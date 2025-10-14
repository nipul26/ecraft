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
 * Marketplace creditmemo Interface.
 * @api
 */
interface CreditMemoListInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID  = 'entity_id';

    public const SELLER_ID     = 'seller_id';

    public const ORDER_ID     = 'order_id';

    public const MAGEQUANTITY     = 'magequantity';

    public const TOTAL_COMMISSION     = 'total_commission';

    public const ACTUAL_SELLER_AMOUNT     = 'actual_seller_amount';

    public const TOTAL_TAX     = 'total_tax';

    public const TOTAL_AMOUNT     = 'total_amount';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const COUPON_AMOUNT = 'coupon_amount';

    public const SHIPPING_CHARGES = 'shipping_charges';

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
     * @return $this
     */
    public function setId($id);
    /**
     * Gets the seller id.
     *
     * @return int
     */
    public function getSellerId();

    /**
     * Sets the seller id.
     *
     * @param string $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Gets the order id.
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Sets the order id.
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Gets the magento product quantity.
     *
     * @return int
     */
    public function getMagequantity();

    /**
     * Sets the magento product quantity.
     *
     * @param string $magequantity
     * @return $this
     */
    public function setMagequantity($magequantity);

    /**
     * Gets the total commission.
     *
     * @return float
     */
    public function getTotalCommission();

    /**
     * Sets the total commission.
     *
     * @param string $totalCommission
     * @return $this
     */
    public function setTotalCommission($totalCommission);

    /**
     * Gets the actual seller amount.
     *
     * @return float
     */
    public function getActualSellerAmount();

    /**
     * Sets the actual seller amount.
     *
     * @param string $actualSellerAmount
     * @return $this
     */
    public function setActualSellerAmount($actualSellerAmount);

    /**
     * Gets the total tax.
     *
     * @return float
     */
    public function getTotalTax();

    /**
     * Sets the total tax.
     *
     * @param string $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax);

    /**
     * Gets the total amount.
     *
     * @return float
     */
    public function getTotalAmount();

    /**
     * Sets the total amount.
     *
     * @param string $totalAmount
     * @return $this
     */
    public function setTotalAmount($totalAmount);
    
    /**
     * Get Created Time
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set Created Time
     *
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated Time
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set Updated Time
     *
     * @param int $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Coupon Amount
     *
     * @return string
     */
    public function getCouponAmount();

    /**
     * Set Coupon Amount
     *
     * @param int $couponAmount
     * @return $this
     */
    public function setCouponAmount($couponAmount);

    /**
     * Get shipping charges
     *
     * @return string
     */
    public function getShippingCharges();

    /**
     * Set shipping charges
     *
     * @param int $shippingCharges
     * @return $this
     */
    public function setShippingCharges($shippingCharges);
}
