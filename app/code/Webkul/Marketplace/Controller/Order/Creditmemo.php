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
 * Webkul Marketplace Order Creditmemo Controller.
 */
class Creditmemo extends \Webkul\Marketplace\Controller\Order
{
    /**
     * Init creditmemo invoice
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    protected function _initCreditmemoInvoice($order)
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $ordInvoiceIds = explode(",", $invoiceId);
        $orderColl = $order->getCreditmemosCollection();
        if ($orderColl->getData()) {
            $creditMemoInvoiceId = array_column($orderColl->getData(), "invoice_id");
            $newInvoices = array_diff($ordInvoiceIds, $creditMemoInvoiceId);
            $newInvoices = array_values($newInvoices);
            $invoiceId = (!empty($newInvoices[0])) ? $newInvoices[0] : $invoiceId;
        }
        if ($invoiceId) {
            try {
                $invoice = $this->_invoiceRepository->get($invoiceId);
                $invoice->setOrder($order);
                if ($invoice->getId()) {
                    return $invoice;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Initialize creditmemo model instance.
     *
     * @param array $order
     * @return \Magento\Sales\Model\Order\Creditmemo|false
     */
    protected function _initOrderCreditmemo($order)
    {
        $data = $this->getRequest()->getPost('creditmemo');

        $creditmemo = false;

        $sellerId = $this->_customerSession->getCustomerId();
        $orderId = $order->getId();

        $invoice = $this->_initCreditmemoInvoice($order);
        $items = [];
        $shippingAmount = 0;
        $trackingsdata = $this->mpOrdersModel->create()
        ->getCollection()
        ->addFieldToFilter(
            'order_id',
            ['eq' => $orderId]
        )
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        );
        foreach ($trackingsdata as $tracking) {
            $shippingAmount = $tracking->getShippingCharges();
        }
        if (!isset($data['shipping_amount'])) {
            $data['shipping_amount'] = $shippingAmount;
            
        }
        
        $this->getRequest()->setPostValue('creditmemo', $data);
        $refundData = $this->getRequest()->getParams();
        $tax = 0;
        $collection = $this->saleslistFactory->create()
        ->getCollection()
        ->addFieldToFilter(
            'order_id',
            ['eq' => $orderId]
        )
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        );
        foreach ($collection as $saleproduct) {
            $tax = $tax + $saleproduct->getTotalTax();
            array_push($items, $saleproduct['order_item_id']);
        }

        $savedData = $this->_getItemData($order, $items);

        $qtys = [];
        foreach ($savedData as $orderItemId => $itemData) {
            if (isset($itemData['qty']) && $itemData['qty']) {
                $qtys[$orderItemId] = $itemData['qty'];
            }
            if (isset($refundData['creditmemo']['items'][$orderItemId]['back_to_stock'])) {
                $backToStock[$orderItemId] = true;
            }
        }

        if (empty($refundData['creditmemo']['shipping_amount'])) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_positive'])) {
            $refundData['creditmemo']['adjustment_positive'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_negative'])) {
            $refundData['creditmemo']['adjustment_negative'] = 0;
        }
        if (!$shippingAmount >= $refundData['creditmemo']['shipping_amount']) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        $refundData['creditmemo']['qtys'] = $qtys;

        if ($invoice) {
            $creditmemo = $this->_creditmemoFactory->createByInvoice(
                $invoice,
                $refundData['creditmemo']
            );
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()- $order->getShippingTaxAmount());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()- $order->getBaseShippingTaxAmount());
        } else {
            $creditmemo = $this->_creditmemoFactory->createByOrder(
                $order,
                $refundData['creditmemo']
            );
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()- $order->getShippingTaxAmount());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()- $order->getBaseShippingTaxAmount());
        }

        /*
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(
                    $this->_stockConfiguration->isAutoReturnEnabled()
                );
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }

        $this->_coreRegistry->register('current_creditmemo', $creditmemo);

        return $creditmemo;
    }

    /**
     * Save creditmemo.
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            $orderId = $this->getRequest()->getParam('id');
            $sellerId = $this->_customerSession->getCustomerId();
            if ($order = $this->_initOrder()) {
                try {
                    $creditmemo = $this->_initOrderCreditmemo($order);

                    $this->processCreditmemoData($creditmemo, $orderId, $sellerId);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->helper->logDataInLogger(
                        "Controller_Order_Creditmemo execute : ".$e->getMessage()
                    );
                    $this->messageManager->addError(
                        __('We can\'t save the credit memo right now.').$e->getMessage()
                    );
                }

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/view',
                    [
                        'id' => $order->getEntityId(),
                        '_secure' => $this->getRequest()->isSecure(),
                    ]
                );
            } else {
                return $this->resultRedirectFactory->create()
                ->setPath(
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
     * Process Creditmemo Data.
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param int $orderId
     * @param int $sellerId
     * @return void
     */
    private function processCreditmemoData($creditmemo, $orderId, $sellerId)
    {
        if ($creditmemo) {
            if (!$creditmemo->isValidGrandTotal()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The credit memo\'s total must be positive.')
                );
            }
            $data = $this->getRequest()->getParam('creditmemo');

            if (!empty($data['comment_text'])) {
                $creditmemo->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                $creditmemo->setCustomerNote($data['comment_text']);
                $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            if (isset($data['do_offline'])) {
                //do not allow online refund for Refund to Store Credit
                if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Cannot create online refund for Refund to Store Credit.')
                    );
                }
            }
            $creditmemoManagement = $this->creditmemoManagement;
            $creditmemo = $creditmemoManagement
            ->refund($creditmemo, (bool) $data['do_offline'], !empty($data['send_email']));

            /*update records*/
            $creditmemoIds = [];
            $trackingcoll = $this->mpOrdersModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'order_id',
                ['eq' => $orderId]
            )
            ->addFieldToFilter(
                'seller_id',
                ['eq' => $sellerId]
            );
            foreach ($trackingcoll as $tracking) {
                if ($tracking->getCreditmemoId()) {
                    $creditmemoIds = explode(',', $tracking->getCreditmemoId());
                }
                $creditmemoId = $creditmemo->getId();
                if (!in_array($creditmemoId, $creditmemoIds)) {
                    array_push($creditmemoIds, $creditmemo->getId());
                    $creditmemoIds = array_unique($creditmemoIds);
                    $allCreditmemoIds = implode(',', $creditmemoIds);
                    $tracking->setCreditmemoId($allCreditmemoIds);
                    $tracking->save();
                }
            }

            if (!empty($data['send_email'])) {
                $this->_creditmemoSender->send($creditmemo);
            }

            $this->messageManager->addSuccess(__('You created the credit memo.'));
        }
    }

   /**
    * Get requested items qtys.
    *
    * @param \Magento\Sales\Model\Order $order
    * @param array $items
    * @return array
    */
    protected function _getItemData($order, $items)
    {
        $refundData = $this->getRequest()->getParams();
        $data['items'] = [];
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)
                && isset($refundData['creditmemo']['items'][$item->getItemId()]['qty'])) {
                $data['items'][$item->getItemId()]['qty'] = (int)(
                    $refundData['creditmemo']['items'][$item->getItemId()]['qty']
                );

                $_item = $item;
                // for bundle product
                if ($_item->getParentItem()) {
                    continue;
                }
            } else {
                if (!$item->getParentItemId()) {
                    $data['items'][$item->getItemId()]['qty'] = 0;
                }
            }
        }
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = [];
        }
        return $qtys;
    }
    /**
     * Get merged array
     *
     * @param array $arrayFirst
     * @param array $arraySecond
     * @return array
     */
    public function getMergedArray($arrayFirst, $arraySecond)
    {
        return array_merge($arrayFirst, $arraySecond);
    }
}
