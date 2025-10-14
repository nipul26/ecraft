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
 * Marketplace Sellertransaction Interface.
 * @api
 */
interface SellertransactionInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const TRANSACTION_ID = 'transaction_id';

    public const ONLINETR_ID = 'onlinetr_id';

    public const SELLER_ID = 'seller_id';

    public const TRANSACTION_AMOUNT = 'transaction_amount';

    public const TYPE = 'type';

    public const METHOD = 'method';

    public const CUSTOM_NOTE = 'custom_note';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const SELLER_PENDING_NOTIFICATION = 'seller_pending_notification';

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
     * @return \Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setId($id);
    /**
     * Set TransactionId
     *
     * @param string $transactionId
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setTransactionId($transactionId);
    /**
     * Get TransactionId
     *
     * @return string
     */
    public function getTransactionId();
    /**
     * Set OnlinetrId
     *
     * @param string $onlinetrId
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setOnlinetrId($onlinetrId);
    /**
     * Get OnlinetrId
     *
     * @return string
     */
    public function getOnlinetrId();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set TransactionAmount
     *
     * @param float $transactionAmount
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setTransactionAmount($transactionAmount);
    /**
     * Get TransactionAmount
     *
     * @return float
     */
    public function getTransactionAmount();
    /**
     * Set Type
     *
     * @param string $type
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setType($type);
    /**
     * Get Type
     *
     * @return string
     */
    public function getType();
    /**
     * Set Method
     *
     * @param string $method
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setMethod($method);
    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod();
    /**
     * Set CustomNote
     *
     * @param string $customNote
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setCustomNote($customNote);
    /**
     * Get CustomNote
     *
     * @return string
     */
    public function getCustomNote();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
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
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set SellerPendingNotification
     *
     * @param int $sellerPendingNotification
     * @return Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setSellerPendingNotification($sellerPendingNotification);
    /**
     * Get SellerPendingNotification
     *
     * @return int
     */
    public function getSellerPendingNotification();
}
