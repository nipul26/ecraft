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

namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\OrdersFactory as MpOrdersModel;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Directory\Model\CountryFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Webkul\Marketplace\Helper\Data;

class AdminhtmlSalesOrderShipmentAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var MpOrdersModel
     */
    protected $mpOrdersModel;
    /**
     * @var AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var CountryFactory
     */
    protected $countryModel;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;
    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory
     */
    protected $mpSaleslistCollectionFactory;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * Construct
     *
     * @param MpOrdersModel $mpOrdersModel
     * @param \Magento\Framework\App\Request\Http $request
     * @param AddressFactory $orderAddressFactory
     * @param CountryFactory $countryModel
     * @param MpEmailHelper $mpEmailHelper
     * @param \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory $mpSaleslistCollectionFactory
     * @param Data $helper
     */
    public function __construct(
        MpOrdersModel $mpOrdersModel,
        \Magento\Framework\App\Request\Http $request,
        AddressFactory $orderAddressFactory,
        CountryFactory $countryModel,
        MpEmailHelper $mpEmailHelper,
        \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory $mpSaleslistCollectionFactory,
        Data $helper
    ) {
        $this->mpOrdersModel = $mpOrdersModel;
        $this->request = $request;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->countryModel = $countryModel;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->mpSaleslistCollectionFactory = $mpSaleslistCollectionFactory;
        $this->helper = $helper;
    }
    /**
     * Admin sales order shipment after
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $paramData = $this->request->getParams();
        $paramData["orderId"] = $order->getId();
        $shipmentItems = $paramData["shipment"]["items"] ?? [];
        $sellerList = [];
        $sellerCollection = $this->mpOrdersModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'order_id',
                ['eq' => $order->getId()]
            )
            ->addFieldToFilter(
                'seller_id',
                ['neq' => 0]
            );
        $shipmentIds = [];
        if ($sellerCollection->getSize()) {
            foreach ($sellerCollection as $row) {
                if ($shipment->getId() != '') {
                    if ($row->getShipmentId()) {
                        $shipmentIds = explode(',', $row->getShipmentId());
                    }
                    array_push($shipmentIds, $shipment->getId());
                    $shipmentIds = array_unique($shipmentIds);
                    $allShipmentIds = implode(',', $shipmentIds);
                    $row->setShipmentId($allShipmentIds);
                    if ($row->getInvoiceId()) {
                        $row->setOrderStatus('complete');
                    } else {
                        $row->setOrderStatus('processing');
                    }
                    $row->save();
                }
            }
            foreach ($shipmentItems as $itemId => $ship) {
                $salesList = $this->mpSaleslistCollectionFactory->create()
                    ->addFieldToFilter("order_item_id", $itemId)->getFirstItem();
                if ($salesList->getSellerId()) {
                    foreach ($order->getAllItems() as $item) {
                        if ($item->getItemId() == $itemId) {
                            $sellerList[$salesList->getSellerId()]["shipmentData"]["items"][] = [
                                "productName" => $item->getName(),
                                "sku" => $item->getSku(),
                                "qty" => $paramData["shipment"]["items"][$item->getItemId()]
                            ];
                        }
                    }
                }
            }
            foreach ($sellerList as $sellerId => $seller) {
                $this->sendShipmentMail($shipment->getIncrementId(), $order, $sellerId, $sellerList[$sellerId]);
            }
        }
    }

    /**
     * Send shipment mail to seller
     *
     * @param int $shipmentId
     * @param \Magento\Sales\Model\Order $order
     * @param int $sellerId
     * @param array $shipmentData
     * @return void
     */
    public function sendShipmentMail($shipmentId, $order, $sellerId, $shipmentData)
    {
        $paramData = $this->request->getParams();
        $billingId = $order->getBillingAddress()->getId();
        $shippingId = $order->getShippingAddress()->getId();
        $seller = $this->helper->getCustomerData($sellerId);
        $adminStoremail = $this->helper->getAdminEmailId();
        $defaultTransEmailId = $this->helper->getDefaultTransEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $defaultTransEmailId;
        $adminUsername = $this->helper->getAdminName();
        $trackingNumber = $shippedBy = "";
        $receiverInfo = [
            'name' =>  $seller->getFirstname(),
            'email' =>  $seller->getEmail()
        ];
        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail
        ];
        if (!empty($paramData["tracking"][1]["number"])) {
            $trackingNumber = $paramData["tracking"][1]["number"];
        }
        if (!empty($paramData["tracking"][1]["title"])) {
            $shippedBy = $paramData["tracking"][1]["title"];
        }
        $emailTempVariables = [];
        $emailTempVariables["sellerName"] = $seller->getFirstname() . " " . $seller->getLastname();
        $emailTempVariables["shippingInfo"] = $this->getAddress($shippingId);
        $emailTempVariables["billingInfo"] = $this->getAddress($billingId);
        $emailTempVariables["shippedBy"] = $shippedBy;
        $emailTempVariables["trackingNumber"] = $trackingNumber;
        $emailTempVariables["comment"] = $paramData["shipment"]["comment_text"];
        $emailTempVariables["payment"] = $order->getPayment()->getMethodInstance()->getTitle();
        $emailTempVariables["shippingDes"] = $order->getShippingDescription();
        $emailTempVariables["shipmentData"] = $shipmentData["shipmentData"];
        $emailTempVariables["orderId"] = $order->getIncrementId();
        $emailTempVariables["shipmentId"] = $shipmentId;
        $this->mpEmailHelper->sendShipmentOrderEmail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
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
        $billinginfo = $billaddress['firstname'] . '<br/>' .
            $billaddress['street'] . '<br/>' .
            $billaddress['city'] . ' ' .
            $billaddress['region'] . ' ' .
            $billaddress['postcode'] . '<br/>' .
            $this->countryModel->create()->load($billaddress['country_id'])->getName() . '<br/>T:' .
            $billaddress['telephone'];
        return $billinginfo;
    }
}
