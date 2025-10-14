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
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;
use Webkul\Marketplace\Model\Saleslist;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory as SalesListCollection;
use Magento\Framework\Session\SessionManager;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\OrdersFactory;
use Webkul\Marketplace\Model\SaleperpartnerFactory;

/**
 * Webkul Marketplace OrderCancelAfter Observer.
 */
class OrderCancelAfter implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var SalesListCollection
     */
    protected $salesListCollection;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var OrdersFactory
     */
    protected $ordersFactory;

    /**
     * @var SaleperpartnerFactory
     */
    protected $saleperpartnerFactory;

    /**
     * Construct
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param SessionManager $coreSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CollectionFactory $collectionFactory
     * @param SalesListCollection $salesListCollection
     * @param MpHelper $mpHelper
     * @param OrdersFactory $ordersFactory
     * @param SaleperpartnerFactory $saleperpartnerFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        SessionManager $coreSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CollectionFactory $collectionFactory,
        SalesListCollection $salesListCollection,
        MpHelper $mpHelper,
        OrdersFactory $ordersFactory,
        SaleperpartnerFactory $saleperpartnerFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreSession = $coreSession;
        $this->_collectionFactory = $collectionFactory;
        $this->_date = $date;
        $this->salesListCollection = $salesListCollection;
        $this->mpHelper = $mpHelper;
        $this->ordersFactory = $ordersFactory;
        $this->saleperpartnerFactory = $saleperpartnerFactory;
    }

    /**
     * Get refunded item flag
     *
     * @param int $orderId
     * @param int $mageproductId
     * @param int $orderItemId
     * @return bool
     */
    public function getRefundedItemSellerFlag(
        $orderId,
        $mageproductId,
        $orderItemId
    ) {
        $sellerOrderslist = $this->salesListCollection->create()
                              ->addFieldToFilter('order_id', $orderId)
                              ->addFieldToFilter('mageproduct_id', $mageproductId)
                              ->addFieldToFilter('order_item_id', $orderItemId)
                              ->setOrder('order_id', 'DESC');
        return $sellerOrderslist->getSize() > 0 ? 1 : 0;
    }

    /**
     * Sales Order Cancle After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $refundedShippingCharges = 0;
        $orderId = $order->getEntityId();

        $paymentCode = '';
        $paymentMethod = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }

        // refund calculation check
        $refundQtyArr = [];
        $orderItemsIds = [];
        $orderItemsQty = [];
        $ordeItemsPrice = [];

        foreach ($order->getAllItems() as $item) {
            $refundQtyArr[$item->getItemId()] = $item->getQtyCanceled();
            if ($item->getQtyCanceled()) {
                $availableSellerFlag = $this->getRefundedItemSellerFlag(
                    $item->getOrderId(),
                    $item->getProductId(),
                    $item->getItemId()
                );
                if ($availableSellerFlag == 1) {
                    $orderItemsIds[$item->getItemId()] = $item->getProductId();
                    $orderItemsQty[$item->getItemId()] = $item->getQtyCanceled();
                    $ordeItemsPrice[$item->getItemId()] = $item->getBasePrice() * $item->getQtyCanceled();
                }
            }
        }
        arsort($ordeItemsPrice);
        $orderItemCommissionRateArr = [];
        foreach ($ordeItemsPrice as $key => $item) {
            $refundedQty = $orderItemsQty[$key];
            $refundedPrice = $ordeItemsPrice[$key];
            $productId = $orderItemsIds[$key];
            $sellerProducts = $this->salesListCollection->create()
                              ->addFieldToFilter('order_id', $orderId)
                              ->addFieldToFilter('order_item_id', $key)
                              ->addFieldToFilter('mageproduct_id', $productId);

            foreach ($sellerProducts as $sellerProduct) {
                $updatedQty = $sellerProduct['magequantity'] - $refundedQty;
                $updatedPrice = $sellerProduct['total_amount'] - $refundedPrice;
                if (!($sellerProduct['total_amount'] * 1)) {
                    $sellerProduct['total_amount'] = 1;
                }
                if ($sellerProduct['total_commission'] * 1) {
                    $commissionPercentage = ($sellerProduct['total_commission'] * 100) / $sellerProduct['total_amount'];
                } else {
                    $commissionPercentage = 0;
                }
                if (empty($orderItemCommissionRateArr[$key])) {
                    $orderItemCommissionRateArr[$key] = [];
                }
                $orderItemCommissionRateArr[$key] = $sellerProduct->getData();
                $updatedCommission = ($updatedPrice * $commissionPercentage) / 100;
                $updatedSellerAmount = $updatedPrice - $updatedCommission;

                $updatedQty = ($updatedQty < 0) ? 0 : $updatedQty;
                $updatedPrice = ($updatedPrice < 0) ? 0 : $updatedPrice;
                $updatedCommission = ($updatedCommission < 0) ? 0 : $updatedCommission;
                $updatedSellerAmount = ($updatedSellerAmount < 0) ? 0 : $updatedSellerAmount;

                if ($refundedQty && ($sellerProduct['total_tax'] && $sellerProduct['magequantity'])) {
                    $taxAmount = ($sellerProduct['total_tax'] / $sellerProduct['magequantity']) * $refundedQty;
                    $remainTaxAmount = $sellerProduct['total_tax'] - $taxAmount;

                    $appliedCouponAmount =
                    ($sellerProduct['applied_coupon_amount'] / $sellerProduct['magequantity']) * $refundedQty;
                    $remainAppliedCouponAmount = $sellerProduct['applied_coupon_amount'] - $appliedCouponAmount;
                } else {
                    $taxAmount = 0;
                    $remainTaxAmount = 0;
                    $appliedCouponAmount = 0;
                    $remainAppliedCouponAmount = 0;
                }
                $taxToSeller = $this->mpHelper->getConfigTaxManage();
                $marketplaceOrders = $this->ordersFactory->create()
                                      ->getCollection()
                                      ->addFieldToFilter('order_id', $sellerProduct['order_id'])
                                      ->addFieldToFilter('seller_id', $sellerProduct['seller_id'])
                                      ->setPageSize(1)->setCurPage(1)->getFirstItem();
                $taxToSeller = $marketplaceOrders->getEntityId() ?
                    $marketplaceOrders->getTaxToSeller() : $this->mpHelper->getConfigTaxManage();
                $taxAmount = $taxToSeller ? $taxAmount : 0;
                
                $refundedPrice = $refundedPrice + $taxAmount - $appliedCouponAmount;
                $partnerRemainSeller = ($sellerProduct->getActualSellerAmount() + $taxAmount) -
                $updatedSellerAmount - $appliedCouponAmount;

                $sellerArr[$sellerProduct['seller_id']]['updated_commission'] = $updatedCommission;
                if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE
                    && $sellerProduct['paid_status'] == Saleslist::PAID_STATUS_PENDING
                ) {
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['total_sale'])) {
                        $sellerArr[$sellerProduct['seller_id']]['total_sale'] = 0;
                    }
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['totalremain'])) {
                        $sellerArr[$sellerProduct['seller_id']]['totalremain'] = 0;
                    }
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['totalcommission'])) {
                        $sellerArr[$sellerProduct['seller_id']]['totalcommission'] = 0;
                    }
                    $sellerArr[$sellerProduct['seller_id']]['total_sale'] =
                        $sellerArr[$sellerProduct['seller_id']]['total_sale'] + $refundedPrice;
                    $sellerArr[$sellerProduct['seller_id']]['totalremain'] =
                        $sellerArr[$sellerProduct['seller_id']]['totalremain'] + $partnerRemainSeller;
                    $sellerArr[$sellerProduct['seller_id']]['totalcommission'] =
                        $sellerArr[$sellerProduct['seller_id']]['totalcommission'] +
                        ($refundedPrice - $partnerRemainSeller);
                } elseif ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE
                    && $sellerProduct['paid_status'] == Saleslist::PAID_STATUS_COMPLETE
                ) {
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['total_sale'])) {
                        $sellerArr[$sellerProduct['seller_id']]['total_sale'] = 0;
                    }
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['totalpaid'])) {
                        $sellerArr[$sellerProduct['seller_id']]['totalpaid'] = 0;
                    }
                    if (!isset($sellerArr[$sellerProduct['seller_id']]['totalcommission'])) {
                        $sellerArr[$sellerProduct['seller_id']]['totalcommission'] = 0;
                    }
                    $sellerArr[$sellerProduct['seller_id']]['total_sale'] =
                        $sellerArr[$sellerProduct['seller_id']]['total_sale'] + $refundedPrice;
                    $sellerArr[$sellerProduct['seller_id']]['totalpaid'] =
                        $sellerArr[$sellerProduct['seller_id']]['totalpaid'] + $partnerRemainSeller;
                    $sellerArr[$sellerProduct['seller_id']]['totalcommission'] =
                        $sellerArr[$sellerProduct['seller_id']]['totalcommission'] +
                        ($refundedPrice - $partnerRemainSeller);
                }
                if ($sellerProduct['is_shipping'] == 1) {
                    $sellerArr[$sellerProduct['seller_id']]['is_shipping'] = 1;
                } else {
                    $sellerArr[$sellerProduct['seller_id']]['is_shipping'] = 0;
                }
                $sellerProduct->setMagequantity($updatedQty);
                $sellerProduct->setTotalAmount($updatedPrice);
                $sellerProduct->setTotalCommission($updatedCommission);
                $sellerProduct->setActualSellerAmount($updatedSellerAmount);
                $sellerProduct->setTotalTax($remainTaxAmount);
                $sellerProduct->setAppliedCouponAmount($remainAppliedCouponAmount);
                if ($updatedSellerAmount == 0) {
                    $sellerProduct->setPaidStatus(Saleslist::PAID_STATUS_REFUNDED);
                    if ($paymentCode == 'mpcashondelivery') {
                        $sellerProduct->setCollectCodStatus(Saleslist::PAID_STATUS_REFUNDED);
                    }
                }
                $sellerProduct->save();
            }
        }
        $this->_coreSession->setMpCreditmemoCommissionRate(
            $orderItemCommissionRateArr
        );

        if (!isset($sellerArr)) {
            $sellerArr = $this->getOrderItemSellersData($orderId);
        }

        foreach ($sellerArr as $sellerId => $value) {
            $shippingCharges = 0;
            $codCharges = 0;
            $sellerRefundedShipping = 0;
            //update records
            $trackingcoll = $this->ordersFactory->create()->getCollection()
                                    ->addFieldToFilter('order_id', $orderId)
                                    ->addFieldToFilter('seller_id', $sellerId);
            foreach ($trackingcoll as $tracking) {
                if ($paymentCode == 'mpcashondelivery') {
                    $codCharges = $tracking->getCodCharges();
                }
                $shippingCharges = $tracking->getShippingCharges();
               
                $sellerRefundedShipping = $refundedShippingCharges >= $shippingCharges ?
                    $shippingCharges : $refundedShippingCharges;
                
                $isAllRefunded = 1;
                // check for if all products are refunded
                $saleslistColl = $this->salesListCollection->create()
                                  ->addFieldToFilter('seller_id', $sellerId)
                                  ->addFieldToFilter('order_id', $orderId)
                                  ->addFieldToFilter('parent_item_id', ['eq'=>null]);
                foreach ($saleslistColl as $saleslist) {
                    if ($saleslist->getPaidStatus() != Saleslist::PAID_STATUS_REFUNDED) {
                        $isAllRefunded = 0;
                        break;
                    }
                }
                if ($isAllRefunded && $tracking->getShipmentId() && $tracking->getCreditMemoId()) {
                    $tracking->setOrderStatus('closed');
                } elseif ($isAllRefunded && $tracking->getShipmentId()) {
                    $tracking->setOrderStatus('complete');
                }
                
                $tracking->setRefundedShippingCharges(
                    $sellerRefundedShipping + $tracking->getRefundedShippingCharges()
                )->save();
                $refundedShippingCharges = $refundedShippingCharges - $sellerRefundedShipping;
            }
            $collectionverifyread = $this->saleperpartnerFactory->create()->getCollection()
                                            ->addFieldToFilter('seller_id', $sellerId);
            foreach ($collectionverifyread as $verifyrow) {
                if (isset($sellerArr[$sellerId]['total_sale'])) {
                    $verifyrow->setTotalSale(
                        $verifyrow->getTotalSale() - (
                            $sellerArr[$sellerId]['total_sale'] + $codCharges + $sellerRefundedShipping
                        )
                    );
                }
                if (isset($sellerArr[$sellerId]['totalremain'])) {
                    $sellerRemainRefundedShipping = 0;
                    // in case of seller is not paid and shipping is also not paid
                    if (isset($sellerArr[$sellerId]['is_shipping']) && ($sellerArr[$sellerId]['is_shipping'] == 1)) {
                        $sellerRemainRefundedShipping = $sellerRefundedShipping;
                        $sellerRefundedShipping = 0;
                    }
                    $verifyrow->setAmountRemain(
                        $verifyrow->getAmountRemain() - (
                            $sellerArr[$sellerId]['totalremain'] + $codCharges + $sellerRemainRefundedShipping
                        )
                    );
                } else {
                    // in case of seller is paid but shipping is not paid
                    if (isset($sellerArr[$sellerId]['is_shipping']) && ($sellerArr[$sellerId]['is_shipping'] == 0)) {
                        $verifyrow->setAmountRemain($verifyrow->getAmountRemain() - $sellerRefundedShipping);
                        $sellerRefundedShipping = 0;
                    }
                }
                // in case of seller and shipping both are paid
                if (isset($sellerArr[$sellerId]['totalpaid'])) {
                    $verifyrow->setAmountReceived(
                        $verifyrow->getAmountReceived() - (
                            $sellerArr[$sellerId]['totalpaid'] + $codCharges + $sellerRefundedShipping
                        )
                    );
                }
                if (isset($sellerArr[$sellerId]['totalcommission'])) {
                    $verifyrow->setTotalCommission(
                        $verifyrow->getTotalCommission() - $sellerArr[$sellerId]['totalcommission']
                    );
                }
                $verifyrow->setLastAmountPaid(0);
                $verifyrow->save();
            }
        }
    }

    /**
     * Sales Order seller's total data.
     *
     * @param int $orderId
     * @return array
     */
    private function getOrderItemSellersData($orderId)
    {
        $sellerArr = [];
        $trackingcoll = $this->ordersFactory->create()->getCollection()
                                ->addFieldToFilter('order_id', $orderId)
                                ->addFieldToFilter('invoice_id', ['neq' => 0]);
        foreach ($trackingcoll as $tracking) {
            $sellerId = $tracking->getSellerId();
            $shippingamount = $tracking->getShippingCharges();
            $refundedShippingAmount = $tracking->getRefundedShippingCharges();
            $shippingCharges = $shippingamount - $refundedShippingAmount;
            if ($shippingCharges * 1) {
                if ($tracking->getShipmentId()) {
                    $sellerArr = $this->calculateSellerTotals($sellerArr, $sellerId, $orderId);
                }
            }
        }

        return $sellerArr;
    }

    /**
     * Calculate seller's totals data.
     *
     * @param array $sellerArr
     * @param int $sellerId
     * @param int $orderId
     * @return array
     */
    private function calculateSellerTotals($sellerArr, $sellerId, $orderId)
    {
        $sellerProducts = $this->salesListCollection->create()
                                ->addFieldToFilter('is_shipping', 1)
                                ->addFieldToFilter('seller_id', $sellerId)
                                ->addFieldToFilter('order_id', $orderId);
        foreach ($sellerProducts as $sellerProduct) {
            if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE
                && $sellerProduct['paidstatus'] == Saleslist::PAID_STATUS_PENDING
            ) {
                if (!isset($sellerArr[$sellerId]['total_sale'])) {
                    $sellerArr[$sellerId]['total_sale'] = 0;
                }
                if (!isset($sellerArr[$sellerId]['totalremain'])) {
                    $sellerArr[$sellerId]['totalremain'] = 0;
                }
                $sellerArr[$sellerId]['total_sale'] = 0;
                $sellerArr[$sellerId]['totalremain'] = 0;
            } else {
                if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE
                    && $sellerProduct['paidstatus'] == Saleslist::PAID_STATUS_COMPLETE
                ) {
                    if (!isset($sellerArr[$sellerId]['total_sale'])) {
                        $sellerArr[$sellerId]['total_sale'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalpaid'])) {
                        $sellerArr[$sellerId]['totalpaid'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalcommission'])) {
                        $sellerArr[$sellerId]['totalcommission'] = 0;
                    }
                    $sellerArr[$sellerId]['total_sale'] = 0;
                    $sellerArr[$sellerId]['totalpaid'] = 0;
                    $sellerArr[$sellerId]['totalcommission'] = 0;
                } elseif ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE
                    && $sellerProduct['paidstatus'] != Saleslist::PAID_STATUS_COMPLETE
                    && $sellerProduct['is_paid'] == Saleslist::PAID_STATUS_COMPLETE
                ) {
                    if (!isset($sellerArr[$sellerId]['total_sale'])) {
                        $sellerArr[$sellerId]['total_sale'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalpaid'])) {
                        $sellerArr[$sellerId]['totalpaid'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalcommission'])) {
                        $sellerArr[$sellerId]['totalcommission'] = 0;
                    }
                    $sellerArr[$sellerId]['total_sale'] = 0;
                    $sellerArr[$sellerId]['totalpaid'] = 0;
                    $sellerArr[$sellerId]['totalcommission'] = 0;
                } else {
                    if (!isset($sellerArr[$sellerId]['total_sale'])) {
                        $sellerArr[$sellerId]['total_sale'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalremain'])) {
                        $sellerArr[$sellerId]['totalremain'] = 0;
                    }
                    if (!isset($sellerArr[$sellerId]['totalcommission'])) {
                        $sellerArr[$sellerId]['totalcommission'] = 0;
                    }
                    $sellerArr[$sellerId]['total_sale'] = 0;
                    $sellerArr[$sellerId]['totalremain'] = 0;
                    $sellerArr[$sellerId]['totalcommission'] = 0;
                }
            }
        }
        return $sellerArr;
    }
}
