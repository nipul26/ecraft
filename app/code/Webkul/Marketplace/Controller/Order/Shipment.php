<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Order;

/**
 * Webkul Marketplace Order Shipment Controller.
 */
class Shipment extends \Webkul\Marketplace\Controller\Order
{
    /**
     * Prepare shipment.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $items
     * @param array $trackingData
     *
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _prepareShipment($order, $items, $trackingData)
    {
        $shipment = $this->_shipmentFactory->create(
            $order,
            $items,
            $trackingData
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }

    /**
     * Create shipmentData
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            if ($order = $this->_initOrder()) {
                $this->doShipmentExecution($order);

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/view',
                    [
                        'id' => $order->getEntityId(),
                        '_secure' => $this->getRequest()->isSecure(),
                    ]
                );
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/history',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * Do shipment execution
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function doShipmentExecution($order)
    {
        try {
            $sellerId = $this->_customerSession->getCustomerId();
            $orderId = $order->getId();
            $trackingid = '';
            $carrier = '';
            $trackingData = [];
            $paramData = $this->getRequest()->getParams();
            if (!empty($paramData['tracking_id'])) {
                $trackingid = $paramData['tracking_id'];
                $trackingData[1]['number'] = $trackingid;
                $trackingData[1]['carrier_code'] = 'custom';
            }
            if (!empty($paramData['carrier'])) {
                $carrier = $paramData['carrier'];
                $trackingData[1]['title'] = $carrier;
            }
            $shippingLabel = '';
            if (!empty($paramData['api_shipment'])) {
                $packageDetails = [];
                if (!empty($paramData['package'])) {
                    $packageDetails = $this->helper->jsonToArray($paramData['package']);
                }
                $this->_eventManager->dispatch(
                    'generate_api_shipment',
                    [
                        'api_shipment' => $paramData['api_shipment'],
                        'order_id' => $orderId,
                        'package_details' => $packageDetails
                    ]
                );
                $shipmentData = $this->_customerSession->getData('shipment_data');
                $trackingid = '';
                if (!empty($shipmentData['tracking_number'])) {
                    $trackingid = $shipmentData['tracking_number'];
                }
                $shippingLabel = '';
                if (!empty($shipmentData['shipping_label'])) {
                    $shippingLabel = $shipmentData['shipping_label'];
                }
                $trackingData[1]['number'] = $trackingid;
                if (array_key_exists('carrier_code', $shipmentData)) {
                    $trackingData[1]['carrier_code'] = $shipmentData['carrier_code'];
                } else {
                    $trackingData[1]['carrier_code'] = 'custom';
                }
                $this->_customerSession->unsetData('shipment_data');
            }

            if (empty($paramData['api_shipment']) || $trackingid != '') {
                if ($order->canUnhold()) {
                    $this->messageManager->addError(
                        __('Can not create shipment as order is in HOLD state')
                    );
                } else {
                    $this->processShipment(
                        $orderId,
                        $sellerId,
                        $trackingid,
                        $carrier,
                        $order,
                        $trackingData,
                        $shippingLabel
                    );
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order_Shipment LocalizedException doShipmentExecution : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Controller_Order_Shipment doShipmentExecution : ".$e->getMessage()
            );
            $this->messageManager->addError(
                __('We can\'t save the shipment right now.')
            );
            $this->messageManager->addError($e->getMessage());
        }
    }

    /**
     * Process Shipment Data.
     *
     * @param int $orderId
     * @param int $sellerId
     * @param int $trackingid
     * @param string $carrier
     * @param \Magento\Sales\Model\Order $order
     * @param array $trackingData
     * @param string $shippingLabel
     * @return void
     */
    private function processShipment(
        $orderId,
        $sellerId,
        $trackingid,
        $carrier,
        $order,
        $trackingData,
        $shippingLabel
    ) {
        $data = $this->getRequest()->getParam('shipment', []);
        $shipmentItems = isset($data['items']) ? $data['items'] : [];
        $paramData = $this->getRequest()->getParams();
        $paramItems = [];
        foreach ($shipmentItems as $shipmentItemId => $shipmentItemQty) {
            if ($shipmentItemQty) {
                array_push($paramItems, $shipmentItemId);
            }
        }

        $items = [];

        $collection = $this->saleslistFactory->create()
        ->getCollection()
        ->addFieldToFilter(
            'order_id',
            $orderId
        )
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'order_item_id',
            ['in' => $paramItems]
        );
        foreach ($collection as $saleproduct) {
            $orderItemId = $saleproduct['order_item_id'];
            if (isset($shipmentItems[$orderItemId])) {
                $items[$saleproduct['order_item_id']] = $shipmentItems[$orderItemId];
            }
        }

        $itemsarray = $this->_getShippingItemQtys($order, $items);

        $itemsToShip = $itemsarray['data'];

        if (count($itemsToShip) > 0) {
            $shipment = false;
            $shipmentId = 0;
            if (!empty($paramData['shipment_id'])) {
                $shipmentId = $paramData['shipment_id'];
            }
            if ($shipmentId) {
                $shipment = $this->_shipment->load($shipmentId);
            } elseif ($orderId) {
                if ($order->getForcedDoShipmentWithInvoice()) {
                    $this->messageManager
                    ->addError(
                        __('Cannot do shipment for the order separately from invoice.')
                    );
                    $this->helper->logDataInLogger(
                        "Cannot do shipment for the order separately from invoice."
                    );
                }
                if (!$order->canShip()) {
                    $this->messageManager->addError(
                        __('Cannot do shipment for the order.')
                    );
                    $this->helper->logDataInLogger(
                        "Cannot do shipment for the order."
                    );
                }

                $shipment = $this->_prepareShipment(
                    $order,
                    $itemsToShip,
                    $trackingData
                );
                if ($shippingLabel!='') {
                    $shipment->setShippingLabel($shippingLabel);
                }
            }
            if ($shipment) {
                if (!empty($data['comment_text'])) {
                    $shipment->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
    
                    $shipment->setCustomerNote($data['comment_text']);
                    $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                }
                $isNeedCreateLabel=!empty($shippingLabel) && $shippingLabel;
                $shipment->getOrder()->setIsInProcess(true);

                $transactionSave = $this->_objectManager->create(
                    \Magento\Framework\DB\Transaction::class
                )->addObject(
                    $shipment
                )->addObject(
                    $shipment->getOrder()
                );
                $transactionSave->save();

                $shipmentId = $shipment->getId();

                $sellerCollection = $this->mpOrdersModel->create()
                ->getCollection()
                ->addFieldToFilter(
                    'order_id',
                    ['eq' => $orderId]
                )
                ->addFieldToFilter(
                    'seller_id',
                    ['eq' => $sellerId]
                );
                $shipmentIds = [];
                foreach ($sellerCollection as $row) {
                    if ($shipment->getId() != '') {
                        if ($row->getShipmentId()) {
                            $shipmentIds = explode(',', $row->getShipmentId());
                        }
                        array_push($shipmentIds, $shipment->getId());
                        $shipmentIds = array_unique($shipmentIds);
                        $allShipmentIds = implode(',', $shipmentIds);
                        $row->setShipmentId($allShipmentIds);
                        $row->setTrackingNumber($trackingid);
                        $row->setCarrierName($carrier);
                        $mpOrderStatus = $this->helper->getSellerOrderStatus($order);
                        $row->setOrderStatus($mpOrderStatus);
                        $row->save();
                    }
                }

                $this->_shipmentSender->send($shipment);
                $this->sendShipmentMail($shipment->getId());
                $shipmentCreatedMessage = __('The shipment has been created.');
                $labelMessage = __('The shipping label has been created.');
                $this->messageManager->addSuccess(
                    $isNeedCreateLabel ? $shipmentCreatedMessage.' '.$labelMessage
                    : $shipmentCreatedMessage
                );
            }
        }
    }
    /**
     * Get address string
     *
     * @param int $id
     * @return string
     */
    public function getAddress($id)
    {
        $billaddress = $this->orderAddressFactory->create()->load($id);
        $billinginfo = $billaddress['firstname'].'<br/>'.
        $billaddress['street'].'<br/>'.
        $billaddress['city'].' '.
        $billaddress['region'].' '.
        $billaddress['postcode'].'<br/>'.
        $this->countryModel->create()->load($billaddress['country_id'])->getName().'<br/>T:'.
        $billaddress['telephone'];
        return $billinginfo;
    }
    /**
     * Send shipment mail
     *
     * @param int $shipmentId
     * @return void
     */
    public function sendShipmentMail($shipmentId)
    {
        $paramData = $this->getRequest()->getParams();
        $order = $this->_coreRegistry->registry('sales_order');
        $items = [];
        $billingId = $order->getBillingAddress()->getId();
        $shippingId = $order->getShippingAddress()->getId();
        $seller = $this->helper->getCustomer();
        $adminStoremail = $this->helper->getAdminEmailId();
        $defaultTransEmailId = $this->helper->getDefaultTransEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $defaultTransEmailId;
        $adminUsername = $this->helper->getAdminName();

        $receiverInfo = [
            'name' =>  $seller->getFirstname(),
            'email' =>  $seller->getEmail(),
        ];
        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];

        $emailTempVariables = [];
        
        $emailTempVariables["sellerName"] = $seller->getFirstname()." ".$seller->getLastname();
        $emailTempVariables["shippingInfo"] = $this->getAddress($shippingId);
        $emailTempVariables["billingInfo"] = $this->getAddress($billingId);
        $emailTempVariables["shippedBy"] = $paramData["carrier"];
        $emailTempVariables["trackingNumber"] = $paramData["tracking_id"];
        $emailTempVariables["comment"] = $paramData["shipment"]["comment_text"];
        $emailTempVariables["payment"] = $order->getPayment()->getMethodInstance()->getTitle();
        $emailTempVariables["shippingDes"] = $order->getShippingDescription();
        $shipmentItems = $paramData["shipment"]["items"];
        foreach ($shipmentItems as $itemId => $ship) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getItemId() == $itemId) {
                    $items[] = [
                        "productName" => $item->getName(),
                        "sku" => $item->getSku(),
                        "qty" => $paramData["shipment"]["items"][$item->getItemId()]
                    ];
                }
            }
        }
        $emailTempVariables["shipmentData"]["items"] = $items;
        $emailTempVariables["orderId"] = $order->getIncrementId();
        $emailTempVariables["shipmentId"] = $shipmentId;
        $this->mpEmailHelper->sendShipmentOrderEmail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }
}
