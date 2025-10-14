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
namespace Webkul\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\Marketplace\Api\Data\CreditMemoListInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace CreditMemoList Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleslist _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleslist getResource()
 */
class CreditMemoList extends AbstractModel implements CreditMemoListInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';
 
    /**
     * Marketplace Saleslist cache tag.
     */
    public const CACHE_TAG = 'marketplace_creditmemolist';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_creditmemolist';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_creditmemolist';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\CreditMemoList::class
        );
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSaleslist();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Saleslist.
     *
     * @return \Webkul\Marketplace\Model\Saleslist
     */
    public function noRouteSaleslist()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Gets the seller id.
     *
     * @return int
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * Sets the seller id.
     *
     * @param string $sellerId
     * @return $this
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Gets the order id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Sets the order id.
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Gets the magento product quantity.
     *
     * @return int
     */
    public function getMagequantity()
    {
        return $this->getData(self::MAGEQUANTITY);
    }

    /**
     * Sets the magento product quantity.
     *
     * @param string $magequantity
     * @return $this
     */
    public function setMagequantity($magequantity)
    {
        return $this->setData(self::MAGEQUANTITY, $magequantity);
    }

    /**
     * Gets the total commission.
     *
     * @return float
     */
    public function getTotalCommission()
    {
        return $this->getData(self::TOTAL_COMMISSION);
    }

    /**
     * Sets the total commission.
     *
     * @param string $totalCommission
     * @return $this
     */
    public function setTotalCommission($totalCommission)
    {
        return $this->setData(self::TOTAL_COMMISSION, $totalCommission);
    }

    /**
     * Gets the actual seller amount.
     *
     * @return float
     */
    public function getActualSellerAmount()
    {
        return $this->getData(self::ACTUAL_SELLER_AMOUNT);
    }

    /**
     * Sets the actual seller amount.
     *
     * @param string $actualSellerAmount
     * @return $this
     */
    public function setActualSellerAmount($actualSellerAmount)
    {
        return $this->setData(self::ACTUAL_SELLER_AMOUNT, $actualSellerAmount);
    }

    /**
     * Gets the total tax.
     *
     * @return float
     */
    public function getTotalTax()
    {
        return $this->getData(self::TOTAL_TAX);
    }

    /**
     * Sets the total tax.
     *
     * @param string $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax)
    {
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Gets the total amount.
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->getData(self::TOTAL_AMOUNT);
    }

    /**
     * Sets the total amount.
     *
     * @param string $totalAmount
     * @return $this
     */
    public function setTotalAmount($totalAmount)
    {
        return $this->setData(self::TOTAL_AMOUNT, $totalAmount);
    }

    /**
     * Get Created Time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set Created Time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated Time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set Updated Time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Coupon Amount
     *
     * @return float
     */
    public function getCouponAmount()
    {
        return parent::getData(self::COUPON_AMOUNT);
    }

    /**
     * Set Coupon Amount
     *
     * @param float $couponAmount
     * @return $this
     */
    public function setCouponAmount($couponAmount)
    {
        return $this->setData(self::COUPON_AMOUNT, $couponAmount);
    }

    /**
     * Get Coupon Amount
     *
     * @return float
     */
    public function getShippingCharges()
    {
        return parent::getData(self::SHIPPING_CHARGES);
    }

    /**
     * Set Coupon Amount
     *
     * @param float $shippingCharges
     * @return $this
     */
    public function setShippingCharges($shippingCharges)
    {
        return $this->setData(self::SHIPPING_CHARGES, $shippingCharges);
    }
}
