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
use Webkul\Marketplace\Api\Data\SaleperpartnerInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Saleperpartner Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleperpartner _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Saleperpartner getResource()
 */
class Saleperpartner extends AbstractModel implements SaleperpartnerInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Marketplace Saleperpartner cache tag.
     */
    public const CACHE_TAG = 'marketplace_saleperpartner';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_saleperpartner';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_saleperpartner';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Saleperpartner::class
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
            return $this->noRouteSaleperpartner();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Saleperpartner.
     *
     * @return \Webkul\Marketplace\Model\Saleperpartner
     */
    public function noRouteSaleperpartner()
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
     * Set id
     *
     * @param int $id
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
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
     * Set TotalSale
     *
     * @param float $totalSale
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setTotalSale($totalSale)
    {
        return $this->setData(self::TOTAL_SALE, $totalSale);
    }

    /**
     * Get TotalSale
     *
     * @return float
     */
    public function getTotalSale()
    {
        return parent::getData(self::TOTAL_SALE);
    }

    /**
     * Set AmountReceived
     *
     * @param float $amountReceived
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setAmountReceived($amountReceived)
    {
        return $this->setData(self::AMOUNT_RECEIVED, $amountReceived);
    }

    /**
     * Get AmountReceived
     *
     * @return float
     */
    public function getAmountReceived()
    {
        return parent::getData(self::AMOUNT_RECEIVED);
    }

    /**
     * Set LastAmountPaid
     *
     * @param float $lastAmountPaid
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setLastAmountPaid($lastAmountPaid)
    {
        return $this->setData(self::LAST_AMOUNT_PAID, $lastAmountPaid);
    }

    /**
     * Get LastAmountPaid
     *
     * @return float
     */
    public function getLastAmountPaid()
    {
        return parent::getData(self::LAST_AMOUNT_PAID);
    }

    /**
     * Set AmountRemain
     *
     * @param float $amountRemain
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setAmountRemain($amountRemain)
    {
        return $this->setData(self::AMOUNT_REMAIN, $amountRemain);
    }

    /**
     * Get AmountRemain
     *
     * @return float
     */
    public function getAmountRemain()
    {
        return parent::getData(self::AMOUNT_REMAIN);
    }

    /**
     * Set TotalCommission
     *
     * @param float $totalCommission
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
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
     * Set CommissionRate
     *
     * @param float $commissionRate
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
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
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
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
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
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
     * Set MinOrderAmount
     *
     * @param float $minOrderAmount
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setMinOrderAmount($minOrderAmount)
    {
        return $this->setData(self::MIN_ORDER_AMOUNT, $minOrderAmount);
    }

    /**
     * Get MinOrderAmount
     *
     * @return float
     */
    public function getMinOrderAmount()
    {
        return parent::getData(self::MIN_ORDER_AMOUNT);
    }

    /**
     * Set MinOrderStatus
     *
     * @param int $minOrderStatus
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setMinOrderStatus($minOrderStatus)
    {
        return $this->setData(self::MIN_ORDER_STATUS, $minOrderStatus);
    }

    /**
     * Get MinOrderStatus
     *
     * @return int
     */
    public function getMinOrderStatus()
    {
        return parent::getData(self::MIN_ORDER_STATUS);
    }

    /**
     * Set CommissionStatus
     *
     * @param int $commissionStatus
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setCommissionStatus($commissionStatus)
    {
        return $this->setData(self::COMMISSION_STATUS, $commissionStatus);
    }

    /**
     * Get CommissionStatus
     *
     * @return int
     */
    public function getCommissionStatus()
    {
        return parent::getData(self::COMMISSION_STATUS);
    }

    /**
     * Set AnalyticId
     *
     * @param string $analyticId
     * @return Webkul\Marketplace\Model\SaleperpartnerInterface
     */
    public function setAnalyticId($analyticId)
    {
        return $this->setData(self::ANALYTIC_ID, $analyticId);
    }

    /**
     * Get AnalyticId
     *
     * @return string
     */
    public function getAnalyticId()
    {
        return parent::getData(self::ANALYTIC_ID);
    }
}
