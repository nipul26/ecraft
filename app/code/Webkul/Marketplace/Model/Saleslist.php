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
use Webkul\Marketplace\Api\Data\SaleslistInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Saleslist Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleslist _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleslist getResource()
 */
class Saleslist extends AbstractModel implements SaleslistInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Paid Order status.
     */
    public const PAID_STATUS_PENDING = '0';
    public const PAID_STATUS_COMPLETE = '1';
    public const PAID_STATUS_HOLD = '2';
    public const PAID_STATUS_REFUNDED = '3';
    public const PAID_STATUS_CANCELED = '4';

    /**
     * Marketplace Saleslist cache tag.
     */
    public const CACHE_TAG = 'marketplace_saleslist';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_saleslist';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_saleslist';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Saleslist::class
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
     * @return \Webkul\Marketplace\Api\Data\SaleslistInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMageproductId($mageproductId)
    {
        return $this->setData(self::MAGEPRODUCT_ID, $mageproductId);
    }

    /**
     * Get MageproductId
     *
     * @return int
     */
    public function getMageproductId()
    {
        return parent::getData(self::MAGEPRODUCT_ID);
    }

    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDER_ID);
    }

    /**
     * Set OrderItemId
     *
     * @param int $orderItemId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * Get OrderItemId
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return parent::getData(self::ORDER_ITEM_ID);
    }

    /**
     * Set ParentItemId
     *
     * @param int $parentItemId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setParentItemId($parentItemId)
    {
        return $this->setData(self::PARENT_ITEM_ID, $parentItemId);
    }

    /**
     * Get ParentItemId
     *
     * @return int
     */
    public function getParentItemId()
    {
        return parent::getData(self::PARENT_ITEM_ID);
    }

    /**
     * Set MagerealorderId
     *
     * @param string $magerealorderId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMagerealorderId($magerealorderId)
    {
        return $this->setData(self::MAGEREALORDER_ID, $magerealorderId);
    }

    /**
     * Get MagerealorderId
     *
     * @return string
     */
    public function getMagerealorderId()
    {
        return parent::getData(self::MAGEREALORDER_ID);
    }

    /**
     * Set Magequantity
     *
     * @param string $magequantity
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMagequantity($magequantity)
    {
        return $this->setData(self::MAGEQUANTITY, $magequantity);
    }

    /**
     * Get Magequantity
     *
     * @return string
     */
    public function getMagequantity()
    {
        return parent::getData(self::MAGEQUANTITY);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId()
    {
        return parent::getData(self::SELLER_ID);
    }

    /**
     * Set TransId
     *
     * @param int $transId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setTransId($transId)
    {
        return $this->setData(self::TRANS_ID, $transId);
    }

    /**
     * Get TransId
     *
     * @return int
     */
    public function getTransId()
    {
        return parent::getData(self::TRANS_ID);
    }

    /**
     * Set Cpprostatus
     *
     * @param int $cpprostatus
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setCpprostatus($cpprostatus)
    {
        return $this->setData(self::CPPROSTATUS, $cpprostatus);
    }

    /**
     * Get Cpprostatus
     *
     * @return int
     */
    public function getCpprostatus()
    {
        return parent::getData(self::CPPROSTATUS);
    }

    /**
     * Set PaidStatus
     *
     * @param int $paidStatus
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setPaidStatus($paidStatus)
    {
        return $this->setData(self::PAID_STATUS, $paidStatus);
    }

    /**
     * Get PaidStatus
     *
     * @return int
     */
    public function getPaidStatus()
    {
        return parent::getData(self::PAID_STATUS);
    }

    /**
     * Set MagebuyerId
     *
     * @param int $magebuyerId
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMagebuyerId($magebuyerId)
    {
        return $this->setData(self::MAGEBUYER_ID, $magebuyerId);
    }

    /**
     * Get MagebuyerId
     *
     * @return int
     */
    public function getMagebuyerId()
    {
        return parent::getData(self::MAGEBUYER_ID);
    }

    /**
     * Set MageproName
     *
     * @param string $mageproName
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMageproName($mageproName)
    {
        return $this->setData(self::MAGEPRO_NAME, $mageproName);
    }

    /**
     * Get MageproName
     *
     * @return string
     */
    public function getMageproName()
    {
        return parent::getData(self::MAGEPRO_NAME);
    }

    /**
     * Set MageproPrice
     *
     * @param float $mageproPrice
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setMageproPrice($mageproPrice)
    {
        return $this->setData(self::MAGEPRO_PRICE, $mageproPrice);
    }

    /**
     * Get MageproPrice
     *
     * @return float
     */
    public function getMageproPrice()
    {
        return parent::getData(self::MAGEPRO_PRICE);
    }

    /**
     * Set TotalAmount
     *
     * @param float $totalAmount
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setTotalAmount($totalAmount)
    {
        return $this->setData(self::TOTAL_AMOUNT, $totalAmount);
    }

    /**
     * Get TotalAmount
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return parent::getData(self::TOTAL_AMOUNT);
    }

    /**
     * Set TotalTax
     *
     * @param float $totalTax
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setTotalTax($totalTax)
    {
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Get TotalTax
     *
     * @return float
     */
    public function getTotalTax()
    {
        return parent::getData(self::TOTAL_TAX);
    }

    /**
     * Set TotalCommission
     *
     * @param float $totalCommission
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setTotalCommission($totalCommission)
    {
        return $this->setData(self::TOTAL_COMMISSION, $totalCommission);
    }

    /**
     * Get TotalCommission
     *
     * @return float
     */
    public function getTotalCommission()
    {
        return parent::getData(self::TOTAL_COMMISSION);
    }

    /**
     * Set ActualSellerAmount
     *
     * @param float $actualSellerAmount
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setActualSellerAmount($actualSellerAmount)
    {
        return $this->setData(self::ACTUAL_SELLER_AMOUNT, $actualSellerAmount);
    }

    /**
     * Get ActualSellerAmount
     *
     * @return float
     */
    public function getActualSellerAmount()
    {
        return parent::getData(self::ACTUAL_SELLER_AMOUNT);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set IsShipping
     *
     * @param int $isShipping
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setIsShipping($isShipping)
    {
        return $this->setData(self::IS_SHIPPING, $isShipping);
    }

    /**
     * Get IsShipping
     *
     * @return int
     */
    public function getIsShipping()
    {
        return parent::getData(self::IS_SHIPPING);
    }

    /**
     * Set IsCoupon
     *
     * @param int $isCoupon
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setIsCoupon($isCoupon)
    {
        return $this->setData(self::IS_COUPON, $isCoupon);
    }

    /**
     * Get IsCoupon
     *
     * @return int
     */
    public function getIsCoupon()
    {
        return parent::getData(self::IS_COUPON);
    }

    /**
     * Set IsPaid
     *
     * @param int $isPaid
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setIsPaid($isPaid)
    {
        return $this->setData(self::IS_PAID, $isPaid);
    }

    /**
     * Get IsPaid
     *
     * @return int
     */
    public function getIsPaid()
    {
        return parent::getData(self::IS_PAID);
    }

    /**
     * Set CommissionRate
     *
     * @param float $commissionRate
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setCommissionRate($commissionRate)
    {
        return $this->setData(self::COMMISSION_RATE, $commissionRate);
    }

    /**
     * Get CommissionRate
     *
     * @return float
     */
    public function getCommissionRate()
    {
        return parent::getData(self::COMMISSION_RATE);
    }

    /**
     * Set CurrencyRate
     *
     * @param float $currencyRate
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setCurrencyRate($currencyRate)
    {
        return $this->setData(self::CURRENCY_RATE, $currencyRate);
    }

    /**
     * Get CurrencyRate
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        return parent::getData(self::CURRENCY_RATE);
    }

    /**
     * Set AppliedCouponAmount
     *
     * @param float $appliedCouponAmount
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setAppliedCouponAmount($appliedCouponAmount)
    {
        return $this->setData(self::APPLIED_COUPON_AMOUNT, $appliedCouponAmount);
    }

    /**
     * Get AppliedCouponAmount
     *
     * @return float
     */
    public function getAppliedCouponAmount()
    {
        return parent::getData(self::APPLIED_COUPON_AMOUNT);
    }

    /**
     * Set IsWithdrawalRequested
     *
     * @param int $isWithdrawalRequested
     * @return Webkul\Marketplace\Model\SaleslistInterface
     */
    public function setIsWithdrawalRequested($isWithdrawalRequested)
    {
        return $this->setData(self::IS_WITHDRAWAL_REQUESTED, $isWithdrawalRequested);
    }

    /**
     * Get IsWithdrawalRequested
     *
     * @return int
     */
    public function getIsWithdrawalRequested()
    {
        return parent::getData(self::IS_WITHDRAWAL_REQUESTED);
    }
}
