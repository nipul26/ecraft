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

namespace Webkul\Marketplace\Controller\Order\Creditmemo;

use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul Marketplace Order creditmemo UpdateQty Controller.
 */
class UpdateQty extends \Webkul\Marketplace\Controller\Order
{
    /**
     * Creditmemo Create UpdateQty Action.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $helper = $this->helper;
            $sellerId = $helper->getCustomerId();
            $isPartner = $helper->isSeller();
            if ($isPartner == 1) {
                if ($order = $this->_initOrder()) {
                    $orderId = $order->getId();
                    $creditmemoData = $this->getRequest()->getParam('creditmemo', []);
                    $creditmemoItems = isset($creditmemoData['items']) ? $creditmemoData['items'] : [];

                    $paymentCode = '';
                    if ($order->getPayment()) {
                        $paymentCode = $order->getPayment()->getMethod();
                    }
                    if ($paymentCode == 'mpcashondelivery') {
                        $adminPayStatus = $this->getAdminPayStatus($order->getId());
                        if ($adminPayStatus) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('You can not create credit memo for this order.')
                            );
                        }
                    }

                    if (!$order->canCreditmemo()) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('We can\'t create credit memo for the order.')
                        );
                    }

                    $itemsData = $this->getCurrentSellerItemsRefundDatas(
                        $orderId,
                        $sellerId,
                        $paymentCode,
                        $creditmemoItems
                    );

                    $items = $itemsData['items'];
                    $items['qtys'] = $itemsData['qtys'];
                    $currencyRate = $itemsData['currencyRate'];
                    $codCharges = $itemsData['codCharges'];
                    $tax = $itemsData['tax'];
                    $couponAmount = $itemsData['couponAmount'];
                    $shippingAmount = $itemsData['shippingAmount'];
                    $currentCouponAmount = $currencyRate * $couponAmount;
                    $currentShippingAmount = $currencyRate * $shippingAmount;
                    $currentTaxAmount = $currencyRate * $tax;
                    $currentCodcharges = $currencyRate * $codCharges;
                    if ($order->getId()) {
                        $creditmemo = $this->_creditmemoFactory->createByOrder($order, $items);
                        $creditmemo->setBaseDiscountAmount(-$couponAmount);
                        $creditmemo->setDiscountAmount(-$currentCouponAmount);
                        $creditmemo->setShippingAmount($currentShippingAmount);
                        $creditmemo->setBaseShippingInclTax($shippingAmount);
                        $creditmemo->setBaseShippingAmount($shippingAmount);
                        if ($paymentCode == 'mpcashondelivery') {
                            $creditmemo->setMpcashondelivery($currentCodcharges);
                            $creditmemo->setBaseMpcashondelivery($codCharges);
                        }
                        $creditmemo->setGrandTotal(
                            $creditmemo->getSubtotal() +
                                $currentShippingAmount +
                                $currentCodcharges +
                                $currentTaxAmount -
                                $currentCouponAmount
                        );
                        $creditmemo->setBaseGrandTotal(
                            $creditmemo->getBaseSubtotal() +
                                $shippingAmount +
                                $codCharges +
                                $tax -
                                $couponAmount
                        );
                        $this->_coreRegistry->register('current_creditmemo', $creditmemo);
                    }

                    $resultPage = $this->_resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->prepend(__('Creditmemo'));
                    $responseData = $resultPage->getLayout()->getBlock(
                        'marketplace_order_new_creditmemo_items'
                    )->toHtml();
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('This order is no longer exists.')
                    );
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'marketplace/account/becomeseller',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } catch (LocalizedException $e) {
            $responseData = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $responseData = ['error' => true, 'message' => $e->getMessage()];
        }
        if (is_array($responseData)) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($responseData);
            return $resultJson;
        } else {
            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($responseData);
            return $resultRaw;
        }
    }

    /**
     * Get Admin paystatus
     *
     * @param int $orderId
     * @return int
     */
    public function getAdminPayStatus($orderId)
    {
        $adminPayStatus = 0;
        $collection = $this->saleslistFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'order_id',
                $orderId
            )
            ->addFieldToFilter(
                'seller_id',
                $this->helper->getCustomerId()
            );
        foreach ($collection as $saleproduct) {
            $adminPayStatus = $saleproduct->getAdminPayStatus();
        }
        return $adminPayStatus;
    }
}
