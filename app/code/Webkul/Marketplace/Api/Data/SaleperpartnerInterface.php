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
 * Marketplace Saleperpartner interface.
 * @api
 */
interface SaleperpartnerInterface
{
    public const ENTITY_ID = 'entity_id';

    public const SELLER_ID = 'seller_id';

    public const TOTAL_SALE = 'total_sale';

    public const AMOUNT_RECEIVED = 'amount_received';

    public const LAST_AMOUNT_PAID = 'last_amount_paid';

    public const AMOUNT_REMAIN = 'amount_remain';

    public const TOTAL_COMMISSION = 'total_commission';

    public const COMMISSION_RATE = 'commission_rate';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const MIN_ORDER_AMOUNT = 'min_order_amount';

    public const MIN_ORDER_STATUS = 'min_order_status';

    public const COMMISSION_STATUS = 'commission_status';

    public const ANALYTIC_ID = 'analytic_id';

    /**
     * Set ID
     *
     * @param int $id
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setId($id);
    /**
     * Get ID
     *
     * @return int
     */
    public function getId();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set TotalSale
     *
     * @param float $totalSale
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setTotalSale($totalSale);
    /**
     * Get TotalSale
     *
     * @return float
     */
    public function getTotalSale();
    /**
     * Set AmountReceived
     *
     * @param float $amountReceived
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setAmountReceived($amountReceived);
    /**
     * Get AmountReceived
     *
     * @return float
     */
    public function getAmountReceived();
    /**
     * Set LastAmountPaid
     *
     * @param float $lastAmountPaid
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setLastAmountPaid($lastAmountPaid);
    /**
     * Get LastAmountPaid
     *
     * @return float
     */
    public function getLastAmountPaid();
    /**
     * Set AmountRemain
     *
     * @param float $amountRemain
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setAmountRemain($amountRemain);
    /**
     * Get AmountRemain
     *
     * @return float
     */
    public function getAmountRemain();
    /**
     * Set TotalCommission
     *
     * @param float $totalCommission
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setTotalCommission($totalCommission);
    /**
     * Get TotalCommission
     *
     * @return float
     */
    public function getTotalCommission();
    /**
     * Set CommissionRate
     *
     * @param float $commissionRate
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setCommissionRate($commissionRate);
    /**
     * Get CommissionRate
     *
     * @return float
     */
    public function getCommissionRate();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
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
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set MinOrderAmount
     *
     * @param float $minOrderAmount
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setMinOrderAmount($minOrderAmount);
    /**
     * Get MinOrderAmount
     *
     * @return float
     */
    public function getMinOrderAmount();
    /**
     * Set MinOrderStatus
     *
     * @param int $minOrderStatus
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setMinOrderStatus($minOrderStatus);
    /**
     * Get MinOrderStatus
     *
     * @return int
     */
    public function getMinOrderStatus();
    /**
     * Set CommissionStatus
     *
     * @param int $commissionStatus
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setCommissionStatus($commissionStatus);
    /**
     * Get CommissionStatus
     *
     * @return int
     */
    public function getCommissionStatus();
    /**
     * Set AnalyticId
     *
     * @param string $analyticId
     * @return Webkul\Marketplace\Api\Data\SaleperpartnerInterface
     */
    public function setAnalyticId($analyticId);
    /**
     * Get AnalyticId
     *
     * @return string
     */
    public function getAnalyticId();
}
