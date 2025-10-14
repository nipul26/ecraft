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
use Webkul\Marketplace\Model\CreditMemoListFactory;
use Magento\Framework\Session\SessionManager;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\OrdersFactory;
use Webkul\Marketplace\Model\SaleperpartnerFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Magento\Directory\Model\CountryFactory;
use Webkul\Marketplace\Model\Product;

/**
 * Webkul Marketplace SalesOrderCreditmemoSaveAfterObserver Observer.
 */
class SalesOrderCreditmemoSaveAfterObserver implements ObserverInterface
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
     * @var CreditMemoListFactory
     */
    protected $creditMemoListFactory;

    /**
     * @var AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var CountryFactory
     */
    protected $countryModel;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModel;

    /**
     * @var \Webkul\Marketplace\Helper\Orders
     */
    protected $orderHelper;

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
     * @param CreditMemoListFactory $creditMemoListFactory
     * @param AddressFactory $orderAddressFactory
     * @param MpEmailHelper $mpEmailHelper
     * @param CountryFactory $countryModel
     * @param \Magento\Catalog\Model\ProductFactory $productModel
     * @param \Webkul\Marketplace\Helper\Orders $orderHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        SessionManager $coreSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CollectionFactory $collectionFactory,
        SalesListCollection $salesListCollection,
        MpHelper $mpHelper,
        OrdersFactory $ordersFactory,
        SaleperpartnerFactory $saleperpartnerFactory,
        CreditMemoListFactory $creditMemoListFactory,
        AddressFactory $orderAddressFactory,
        MpEmailHelper $mpEmailHelper,
        CountryFactory $countryModel,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Webkul\Marketplace\Helper\Orders $orderHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreSession = $coreSession;
        $this->_collectionFactory = $collectionFactory;
        $this->_date = $date;
        $this->salesListCollection = $salesListCollection;
        $this->mpHelper = $mpHelper;
        $this->ordersFactory = $ordersFactory;
        $this->saleperpartnerFactory = $saleperpartnerFactory;
        $this->creditMemoListFactory = $creditMemoListFactory;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->countryModel = $countryModel;
        $this->productModel = $productModel;
        $this->orderHelper = $orderHelper;
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
        $flag = 0;
        $sellerOrderslist = $this->salesListCollection->create()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('mageproduct_id', $mageproductId)
            ->addFieldToFilter('order_item_id', $orderItemId)
            ->setOrder('order_id', 'DESC');
        if ($sellerOrderslist->getSize() > 0) {
            $flag = 1;
        }

        return $flag;
    }

    /**
     * Sales Order Creditmemo Save After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $param = $this->mpHelper->getRequest()->getParams();
        $creditmemo = $observer->getCreditmemo();
        $creditmemoId = $creditmemo->getId();
        $refundedShippingCharges = $creditmemo->getBaseShippingAmount();
        $orderId = $creditmemo->getOrderId();
        $order = $creditmemo->getOrder();
        $paymentCode = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }

        $helper = $this->mpHelper;
        // refund calculation check

        $adjustmentPositive = $creditmemo['base_adjustment_positive'];
        $adjustmentNegative = $creditmemo['base_adjustment_negative'];
        if ($adjustmentNegative > $adjustmentPositive) {
            $adjustmentNegative = $adjustmentNegative - $adjustmentPositive;
        } else {
            $adjustmentNegative = 0;
        }

        $refundQtyArr = $creditmemoItemsIds = $creditmemoItemsQty = $creditmemoItemsPrice =
            $creditmemoItemsSku = $creditmemoItemsName = [];
        $emailTempVariables = $prodIds = $emailTempItems = [];
        $creditMemoSellerAmount = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItemId = $item->getOrderItemId();
            $refundQtyArr[$orderItemId] = $item->getQty();
            if ($item->getQty()) {
                $availableSellerFlag = $this->getRefundedItemSellerFlag(
                    $orderId,
                    $item->getProductId(),
                    $orderItemId
                );
                if ($availableSellerFlag == 1) {
                    $creditmemoItemsIds[$orderItemId] = $item->getProductId();
                    $creditmemoItemsQty[$orderItemId] = $item->getQty();
                    $creditmemoItemsSku[$orderItemId] = $item->getSku();
                    $creditmemoItemsName[$orderItemId] = $item->getName();
                    $prodIds[] = $item->getProductId();
                    $creditmemoItemsPrice[$orderItemId] = $item->getBasePrice() * $item->getQty();
                }
            }
        }
        arsort($creditmemoItemsPrice);
        $creditmemoCommissionRateArr = [];
        foreach ($creditmemoItemsPrice as $key => $item) {
            $refundedQty = $creditmemoItemsQty[$key];
            $refundedPrice = $creditmemoItemsPrice[$key];
            $productId = $creditmemoItemsIds[$key];
            $emailTempItems = [
                "productName" =>  $creditmemoItemsName[$key],
                "sku" =>  $creditmemoItemsSku[$key],
                "qty" =>  $creditmemoItemsQty[$key],
                "price" =>  $helper->getFormatedPrice($creditmemoItemsPrice[$key])
            ];
            $sellerProducts = $this->salesListCollection->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('order_item_id', $key)
                ->addFieldToFilter('mageproduct_id', $productId);
            foreach ($sellerProducts as $sellerProduct) {

                $creditMemoTotalAmount = $creditMemoCommission = $creditMemoTaxAmount = 0;

                $mpcreditmemo = $this->creditMemoListFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $sellerProduct['order_id'])
                    ->addFieldToFilter('seller_id', $sellerProduct['seller_id']);
                $marketplaceOrders = $this->ordersFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $sellerProduct['order_id'])
                    ->addFieldToFilter('seller_id', $sellerProduct['seller_id']);
                if ($mpcreditmemo->getSize()) {
                    $currentSeller = $mpcreditmemo->getFirstItem();
                    $creditMemoTotalAmount += $currentSeller->getTotalAmount();
                    $creditMemoCommission += $currentSeller->getTotalCommission();
                    $creditMemoSellerAmount += $currentSeller->getSellerActualAmount();
                    $creditMemoTaxAmount += $currentSeller->getTotalTax();
                }
                $creditMemoTotalAmount += $refundedPrice;
                $updatedQty = $sellerProduct['magequantity'] - $refundedQty;
                if ($adjustmentNegative * 1) {
                    if ($adjustmentNegative >= $refundedPrice) {
                        $adjustmentNegative = $adjustmentNegative - $sellerProduct['total_amount'];
                        $updatedPrice = $sellerProduct['total_amount'];
                        $refundedPrice = 0;
                    } else {
                        $refundedPrice = $refundedPrice - $adjustmentNegative;
                        $updatedPrice = $sellerProduct['total_amount'] - $refundedPrice;
                        $adjustmentNegative = 0;
                    }
                } else {
                    $updatedPrice = $sellerProduct['total_amount'] - $refundedPrice;
                }
                if (!($sellerProduct['total_amount'] * 1)) {
                    $sellerProduct['total_amount'] = 1;
                }
                if ($sellerProduct['total_commission'] * 1) {
                    $commissionPercentage = ($sellerProduct['total_commission'] * 100) / $sellerProduct['total_amount'];
                } else {
                    $commissionPercentage = 0;
                }
                if (empty($creditmemoCommissionRateArr[$key])) {
                    $creditmemoCommissionRateArr[$key] = [];
                }
                $creditmemoCommissionRateArr[$key] = $sellerProduct->getData();
                $updatedCommission = ($updatedPrice * $commissionPercentage) / 100;
                $updatedSellerAmount = $updatedPrice - $updatedCommission;

                $updatedQty = max($updatedQty, 0);
                $updatedPrice = max($updatedPrice, 0);
                $updatedSellerAmount = max($updatedSellerAmount, 0);
                $updatedCommission = ($creditmemo["adjustment_negative"] > 0) ? 0 : max($updatedCommission, 0);

                if ($refundedQty) {
                    $mpOrderShippingCharges = $marketplaceOrders->getFirstItem()->getShippingCharges();
                    $mpOrderShippingTax = $marketplaceOrders->getFirstItem()->getShippingTax();
                    $sellerTotalTax = $sellerProduct['total_tax'] - $mpOrderShippingTax;
                    if ($mpOrderShippingCharges > 0) {
                        $sellerTotalTax = $sellerProduct['total_tax'];
                    }
                    $taxAmount = ($sellerTotalTax / $sellerProduct['magequantity']) * $refundedQty;
                    $appliedCouponAmount =
                        ($sellerProduct['applied_coupon_amount'] / $sellerProduct['magequantity']) * $refundedQty;
                    $remainAppliedCouponAmount = $sellerProduct['applied_coupon_amount'] - $appliedCouponAmount;
                } else {
                    $taxAmount = 0;
                    $appliedCouponAmount = 0;
                    $remainAppliedCouponAmount = 0;
                    $creditMemoCommission += $sellerProduct['total_commission'];
                    $creditMemoSellerAmount += $sellerProduct['actual_seller_amount'];
                }
                $taxToSeller = $helper->getConfigTaxManage();
                foreach ($marketplaceOrders as $tracking) {
                    $taxToSeller = $tracking['tax_to_seller'];
                }
                if (!$taxToSeller) {
                    $taxAmount = 0;
                }
                $refundedPrice = $refundedPrice + $taxAmount - $appliedCouponAmount;
                $partnerRemainSeller = ($sellerProduct->getActualSellerAmount() + $taxAmount) -
                    $updatedSellerAmount - $appliedCouponAmount;

                $sellerArr[$sellerProduct['seller_id']]['updated_commission'] = $updatedCommission;
                if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE &&
                    $sellerProduct['paid_status'] == Saleslist::PAID_STATUS_PENDING
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
                    $sellerArr[$sellerProduct['seller_id']]['total_sale'] += $refundedPrice;
                    $sellerArr[$sellerProduct['seller_id']]['totalremain'] += $partnerRemainSeller;
                    $sellerArr[$sellerProduct['seller_id']]['totalcommission'] +=
                        ($refundedPrice - $partnerRemainSeller);
                } elseif ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE &&
                    $sellerProduct['paid_status'] == Saleslist::PAID_STATUS_COMPLETE
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
                    $sellerArr[$sellerProduct['seller_id']]['total_sale'] += $refundedPrice;
                    $sellerArr[$sellerProduct['seller_id']]['totalpaid'] += $partnerRemainSeller;
                    $sellerArr[$sellerProduct['seller_id']]['totalcommission'] +=
                        ($refundedPrice - $partnerRemainSeller);
                }
                if ($sellerProduct['is_shipping'] == 1) {
                    $sellerArr[$sellerProduct['seller_id']]['is_shipping'] = 1;
                } else {
                    $sellerArr[$sellerProduct['seller_id']]['is_shipping'] = 0;
                }
                if (empty($sellerArr[$sellerProduct['seller_id']]['mailData']["taxAmount"])) {
                    $sellerArr[$sellerProduct['seller_id']]['mailData']["taxAmount"] = 0;
                }
                $sellerArr[$sellerProduct['seller_id']]['mailData']["taxAmount"] += $taxAmount;
                if (empty($emailTempVariables[$sellerProduct['seller_id']]["mailData"]["grandTotal"])) {
                    $emailTempVariables[$sellerProduct['seller_id']]["mailData"]["grandTotal"] = 0;
                }
                $mpOrderShippingCharges = $marketplaceOrders->getFirstItem()->getShippingCharges();
                if ($mpOrderShippingCharges <= 0) {
                    $refundedShippingCharges = 0;
                }

                $emailTempVariables[$sellerProduct['seller_id']]["mailData"]["grandTotal"] +=
                    $refundedPrice + $refundedShippingCharges;
                $creditMemoCommission += $sellerProduct['total_commission'] - $updatedCommission;
                $creditMemoSellerAmount += $sellerProduct['actual_seller_amount'] / $sellerProduct['magequantity'] *
                    $refundedQty;
                $creditMemoTaxAmount += $taxAmount;
                if ($mpcreditmemo->getSize()) {
                    $mpcreditmemo = $mpcreditmemo->getFirstItem()
                        ->setTotalAmount($creditMemoTotalAmount)
                        ->setTotalCommission($creditMemoCommission)
                        ->setActualSellerAmount($creditMemoSellerAmount)
                        ->setTotalTax($creditMemoTaxAmount)
                        ->setMagequantity($refundedQty)
                        ->setCouponAmount($appliedCouponAmount)
                        ->setShippingCharges($refundedShippingCharges)
                        ->save();
                } else {
                    $this->creditMemoListFactory->create()
                        ->setOrderId($sellerProduct['order_id'])
                        ->setSellerId($sellerProduct['seller_id'])
                        ->setTotalAmount($creditMemoTotalAmount)
                        ->setTotalCommission($creditMemoCommission)
                        ->setActualSellerAmount($creditMemoSellerAmount)
                        ->setTotalTax($creditMemoTaxAmount)
                        ->setMagequantity($refundedQty)
                        ->setCouponAmount($appliedCouponAmount)
                        ->setShippingCharges($refundedShippingCharges)
                        ->save();
                }
                if ($updatedSellerAmount == 0) {
                    $sellerProduct->setPaidStatus(Saleslist::PAID_STATUS_REFUNDED);
                    if ($paymentCode == 'mpcashondelivery') {
                        $sellerProduct->setCollectCodStatus(Saleslist::PAID_STATUS_REFUNDED);
                    }
                }

                $sellerProduct->save();
                $emailTempVariables[$sellerProduct['seller_id']]["mailData"]["items"][] = $emailTempItems;
                if ($marketplaceOrders->getSize()) {
                    $emailTempVariables[$sellerProduct['seller_id']]["mailData"]["discount"] = $marketplaceOrders
                        ->getFirstItem()->getCouponAmount();
                } else {
                    $emailTempVariables[$sellerProduct['seller_id']]["mailData"]["discount"] = 0;
                }
                $emailTempVariables[$sellerProduct['seller_id']]["isAddressRequired"] =
                    $this->isAddressRequired($prodIds);
            }
        }
        $this->_coreSession->setMpCreditmemoCommissionRate(
            $creditmemoCommissionRateArr
        );

        if (!isset($sellerArr)) {
            $sellerArr = $this->getCreditmemoSellersData(
                $adjustmentNegative,
                $refundedShippingCharges,
                $orderId
            );
        }
        foreach ($sellerArr as $sellerId => $value) {
            $shippingCharges = 0;
            $codCharges = 0;
            /*update records*/
            $creditmemoIds = [];
            $emailSellerRefundedShipping = 0;
            if ($sellerId) {
                $trackingcoll = $this->ordersFactory->create()
                    ->getCollection()
                    ->addFieldToFilter(
                        'order_id',
                        $orderId
                    )
                    ->addFieldToFilter(
                        'seller_id',
                        $sellerId
                    );
                $sellerRefundedShipping = 0;
                foreach ($trackingcoll as $tracking) {
                    if ($tracking->getCreditmemoId()) {
                        $creditmemoIds = explode(',', $tracking->getCreditmemoId());
                    }
                    if ($creditmemoId && !in_array($creditmemoId, $creditmemoIds)) {
                        array_push($creditmemoIds, $creditmemoId);
                        $creditmemoIds = array_unique($creditmemoIds);
                        $allCreditmemoIds = implode(',', $creditmemoIds);
                        $tracking->setCreditmemoId($allCreditmemoIds);
                    }
                    if ($paymentCode == 'mpcashondelivery') {
                        $codCharges = $tracking->getCodCharges();
                    }
                    $shippingCharges = $tracking->getShippingCharges();
                    $sellerRefundedShipping = 0;
                    if ($refundedShippingCharges >= $shippingCharges) {
                        $sellerRefundedShipping = $shippingCharges;
                    } else {
                        $sellerRefundedShipping = $refundedShippingCharges;
                    }
                    $mpOrderStatus = $this->mpHelper->getSellerOrderStatus($order, $sellerId);
                    $tracking->setOrderStatus($mpOrderStatus)->save();
                    $tracking->setRefundedShippingCharges(
                        $sellerRefundedShipping + $tracking->getRefundedShippingCharges()
                    )->save();
                    $refundedShippingCharges = $refundedShippingCharges - $sellerRefundedShipping;
                    $emailSellerRefundedShipping += $sellerRefundedShipping;
                }
                $collectionverifyread = $this->saleperpartnerFactory->create()
                    ->getCollection()
                    ->addFieldToFilter(
                        'seller_id',
                        $sellerId
                    );
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
                        if (isset($sellerArr[$sellerId]['is_shipping']) &&
                            ($sellerArr[$sellerId]['is_shipping'] == 1)
                        ) {
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
                        if (isset($sellerArr[$sellerId]['is_shipping']) &&
                            ($sellerArr[$sellerId]['is_shipping'] == 0)
                        ) {
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
                $emailTempVariables[$sellerId]["mailData"]["shipping"] =
                    $emailSellerRefundedShipping;
                $emailTempVariables[$sellerId]["mailData"]["codChargeAmount"] = $codCharges;
                $emailTempVariables[$sellerId]["mailData"]["taxAmount"] =
                    $sellerArr[$sellerId]["mailData"]["taxAmount"];
                $this->sendSellerCreditMemoMail($order, $creditmemo, $emailTempVariables[$sellerId], $sellerId);
            }
        }
    }

    /**
     * Sales Order Creditmemo seller's total data.
     *
     * @param float $adjustmentNegative
     * @param float $refundedShippingCharges
     * @param int $orderId
     * @return array
     */
    private function getCreditmemoSellersData(
        $adjustmentNegative,
        $refundedShippingCharges,
        $orderId
    ) {
        if ($adjustmentNegative * 1) {
            if ($adjustmentNegative >= $refundedShippingCharges) {
                $adjustmentNegative = $adjustmentNegative - $refundedShippingCharges;
                $refundedShippingCharges = 0;
            } else {
                $refundedShippingCharges = $refundedShippingCharges - $adjustmentNegative;
                $adjustmentNegative = 0;
            }
        }
        $sellerArr = [];
        $trackingcoll = $this->ordersFactory->create()
            ->getCollection()
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
            if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE &&
                $sellerProduct['paidstatus'] == Saleslist::PAID_STATUS_PENDING
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
                if ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE &&
                    $sellerProduct['paidstatus'] == Saleslist::PAID_STATUS_COMPLETE
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
                } elseif ($sellerProduct['cpprostatus'] == Saleslist::PAID_STATUS_COMPLETE &&
                    $sellerProduct['paidstatus'] != Saleslist::PAID_STATUS_COMPLETE &&
                    $sellerProduct['is_paid'] == Saleslist::PAID_STATUS_COMPLETE
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
    /**
     * Send creditmemo mail
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param array $emailTempVariables
     * @param int $sellerId
     * @return void
     */
    public function sendSellerCreditMemoMail($order, $creditmemo, $emailTempVariables, $sellerId)
    {
        $paramData = $this->mpHelper->getRequest()->getParams();
        $billingId = $shippingId = $order->getBillingAddress()->getId();
        if (!empty($order->getShippingAddress())) {
            $shippingId = $order->getShippingAddress()->getId();
        }
        $seller = $this->mpHelper->getCustomerData($sellerId);
        $adminStoremail = $this->mpHelper->getAdminEmailId();
        $defaultTransEmailId = $this->mpHelper->getDefaultTransEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $defaultTransEmailId;
        $adminUsername = $this->mpHelper->getAdminName();
        $emailTempVariables["creditMemoId"] = $creditmemo->getIncrementId();
        $emailTempVariables["orderId"] = $order->getIncrementId();
        $emailTempVariables["sellerName"] = $seller->getFirstname() . " " . $seller->getLastname();
        $emailTempVariables["shippingInfo"] = $this->getAddress($shippingId);
        $emailTempVariables["billingInfo"] = $this->getAddress($billingId);
        $emailTempVariables["comment"] = $creditmemo->getCustomerNote();
        $emailTempVariables["payment"] = $order->getPayment()->getMethodInstance()->getTitle();
        $emailTempVariables["shippingDes"] = $order->getShippingDescription();
        $receiverInfo = [
            'name' =>  $seller->getFirstname(),
            'email' =>  $seller->getEmail(),
        ];
        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $this->mpEmailHelper->sendSellerCreditMemoMail(
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
    /**
     * Is order virtual or not
     *
     * @param array $ids
     * @return bool
     */
    public function isAddressRequired($ids)
    {
        $isAddressRequired = false;
        $productCollection = $this->productModel->create()
            ->getCollection()
            ->addFieldToFilter("entity_id", ["in" => $ids]);
        $productVirtual = Product::PRODUCT_TYPE_VIRTUAL;
        $productDownloadable = Product::PRODUCT_TYPE_VIRTUAL;
        foreach ($productCollection as $prodColl) {
            if ($prodColl->getTypeId() != $productVirtual && $prodColl->getTypeId() != $productDownloadable) {
                $isAddressRequired = true;
            }
        }
        return $isAddressRequired;
    }
}
