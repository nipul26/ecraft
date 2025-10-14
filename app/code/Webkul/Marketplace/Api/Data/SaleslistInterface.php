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
namespace Webkul\Marketplace\Api\Data;

/**
 * Marketplace Saleslist Interface.
 * @api
 */
interface SaleslistInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const MAGEPRODUCT_ID = 'mageproduct_id';

    public const ORDER_ID = 'order_id';

    public const ORDER_ITEM_ID = 'order_item_id';

    public const PARENT_ITEM_ID = 'parent_item_id';

    public const MAGEREALORDER_ID = 'magerealorder_id';

    public const MAGEQUANTITY = 'magequantity';

    public const SELLER_ID = 'seller_id';

    public const TRANS_ID = 'trans_id';

    public const CPPROSTATUS = 'cpprostatus';

    public const PAID_STATUS = 'paid_status';

    public const MAGEBUYER_ID = 'magebuyer_id';

    public const MAGEPRO_NAME = 'magepro_name';

    public const MAGEPRO_PRICE = 'magepro_price';

    public const TOTAL_AMOUNT = 'total_amount';

    public const TOTAL_TAX = 'total_tax';

    public const TOTAL_COMMISSION = 'total_commission';

    public const ACTUAL_SELLER_AMOUNT = 'actual_seller_amount';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const IS_SHIPPING = 'is_shipping';

    public const IS_COUPON = 'is_coupon';

    public const IS_PAID = 'is_paid';

    public const COMMISSION_RATE = 'commission_rate';

    public const CURRENCY_RATE = 'currency_rate';

    public const APPLIED_COUPON_AMOUNT = 'applied_coupon_amount';

    public const IS_WITHDRAWAL_REQUESTED = 'is_withdrawal_requested';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setId($id);
    /**
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMageproductId($mageproductId);
    /**
     * Get MageproductId
     *
     * @return int
     */
    public function getMageproductId();
    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setOrderId($orderId);
    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId();
    /**
     * Set OrderItemId
     *
     * @param int $orderItemId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setOrderItemId($orderItemId);
    /**
     * Get OrderItemId
     *
     * @return int
     */
    public function getOrderItemId();
    /**
     * Set ParentItemId
     *
     * @param int $parentItemId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setParentItemId($parentItemId);
    /**
     * Get ParentItemId
     *
     * @return int
     */
    public function getParentItemId();
    /**
     * Set MagerealorderId
     *
     * @param string $magerealorderId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMagerealorderId($magerealorderId);
    /**
     * Get MagerealorderId
     *
     * @return string
     */
    public function getMagerealorderId();
    /**
     * Set Magequantity
     *
     * @param string $magequantity
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMagequantity($magequantity);
    /**
     * Get Magequantity
     *
     * @return string
     */
    public function getMagequantity();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set TransId
     *
     * @param int $transId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setTransId($transId);
    /**
     * Get TransId
     *
     * @return int
     */
    public function getTransId();
    /**
     * Set Cpprostatus
     *
     * @param int $cpprostatus
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setCpprostatus($cpprostatus);
    /**
     * Get Cpprostatus
     *
     * @return int
     */
    public function getCpprostatus();
    /**
     * Set PaidStatus
     *
     * @param int $paidStatus
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setPaidStatus($paidStatus);
    /**
     * Get PaidStatus
     *
     * @return int
     */
    public function getPaidStatus();
    /**
     * Set MagebuyerId
     *
     * @param int $magebuyerId
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMagebuyerId($magebuyerId);
    /**
     * Get MagebuyerId
     *
     * @return int
     */
    public function getMagebuyerId();
    /**
     * Set MageproName
     *
     * @param string $mageproName
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMageproName($mageproName);
    /**
     * Get MageproName
     *
     * @return string
     */
    public function getMageproName();
    /**
     * Set MageproPrice
     *
     * @param float $mageproPrice
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setMageproPrice($mageproPrice);
    /**
     * Get MageproPrice
     *
     * @return float
     */
    public function getMageproPrice();
    /**
     * Set TotalAmount
     *
     * @param float $totalAmount
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setTotalAmount($totalAmount);
    /**
     * Get TotalAmount
     *
     * @return float
     */
    public function getTotalAmount();
    /**
     * Set TotalTax
     *
     * @param float $totalTax
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setTotalTax($totalTax);
    /**
     * Get TotalTax
     *
     * @return float
     */
    public function getTotalTax();
    /**
     * Set TotalCommission
     *
     * @param float $totalCommission
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setTotalCommission($totalCommission);
    /**
     * Get TotalCommission
     *
     * @return float
     */
    public function getTotalCommission();
    /**
     * Set ActualSellerAmount
     *
     * @param float $actualSellerAmount
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setActualSellerAmount($actualSellerAmount);
    /**
     * Get ActualSellerAmount
     *
     * @return float
     */
    public function getActualSellerAmount();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set IsShipping
     *
     * @param int $isShipping
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setIsShipping($isShipping);
    /**
     * Get IsShipping
     *
     * @return int
     */
    public function getIsShipping();
    /**
     * Set IsCoupon
     *
     * @param int $isCoupon
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setIsCoupon($isCoupon);
    /**
     * Get IsCoupon
     *
     * @return int
     */
    public function getIsCoupon();
    /**
     * Set IsPaid
     *
     * @param int $isPaid
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setIsPaid($isPaid);
    /**
     * Get IsPaid
     *
     * @return int
     */
    public function getIsPaid();
    /**
     * Set CommissionRate
     *
     * @param float $commissionRate
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setCommissionRate($commissionRate);
    /**
     * Get CommissionRate
     *
     * @return float
     */
    public function getCommissionRate();
    /**
     * Set CurrencyRate
     *
     * @param float $currencyRate
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setCurrencyRate($currencyRate);
    /**
     * Get CurrencyRate
     *
     * @return float
     */
    public function getCurrencyRate();
    /**
     * Set AppliedCouponAmount
     *
     * @param float $appliedCouponAmount
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setAppliedCouponAmount($appliedCouponAmount);
    /**
     * Get AppliedCouponAmount
     *
     * @return float
     */
    public function getAppliedCouponAmount();
    /**
     * Set IsWithdrawalRequested
     *
     * @param int $isWithdrawalRequested
     * @return Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setIsWithdrawalRequested($isWithdrawalRequested);
    /**
     * Get IsWithdrawalRequested
     *
     * @return int
     */
    public function getIsWithdrawalRequested();
}
