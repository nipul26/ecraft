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
use Webkul\Marketplace\Api\Data\SellertransactionInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Sellertransaction Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Sellertransaction _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Sellertransaction getResource()
 */
class Sellertransaction extends AbstractModel implements SellertransactionInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Marketplace Sellertransaction cache tag.
     */
    public const CACHE_TAG = 'marketplace_sellertransaction';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_sellertransaction';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_sellertransaction';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Sellertransaction::class
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
            return $this->noRouteSellertransaction();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Sellertransaction.
     *
     * @return \Webkul\Marketplace\Model\Sellertransaction
     */
    public function noRouteSellertransaction()
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
     * @return \Webkul\Marketplace\Api\Data\SellertransactionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set TransactionId
     *
     * @param string $transactionId
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * Get TransactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return parent::getData(self::TRANSACTION_ID);
    }

    /**
     * Set OnlinetrId
     *
     * @param string $onlinetrId
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setOnlinetrId($onlinetrId)
    {
        return $this->setData(self::ONLINETR_ID, $onlinetrId);
    }

    /**
     * Get OnlinetrId
     *
     * @return string
     */
    public function getOnlinetrId()
    {
        return parent::getData(self::ONLINETR_ID);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\SellertransactionInterface
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
     * Set TransactionAmount
     *
     * @param float $transactionAmount
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setTransactionAmount($transactionAmount)
    {
        return $this->setData(self::TRANSACTION_AMOUNT, $transactionAmount);
    }

    /**
     * Get TransactionAmount
     *
     * @return float
     */
    public function getTransactionAmount()
    {
        return parent::getData(self::TRANSACTION_AMOUNT);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return parent::getData(self::TYPE);
    }

    /**
     * Set Method
     *
     * @param string $method
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setMethod($method)
    {
        return $this->setData(self::METHOD, $method);
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return parent::getData(self::METHOD);
    }

    /**
     * Set CustomNote
     *
     * @param string $customNote
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setCustomNote($customNote)
    {
        return $this->setData(self::CUSTOM_NOTE, $customNote);
    }

    /**
     * Get CustomNote
     *
     * @return string
     */
    public function getCustomNote()
    {
        return parent::getData(self::CUSTOM_NOTE);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\SellertransactionInterface
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
     * @return Webkul\Marketplace\Model\SellertransactionInterface
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
     * Set SellerPendingNotification
     *
     * @param int $sellerPendingNotification
     * @return Webkul\Marketplace\Model\SellertransactionInterface
     */
    public function setSellerPendingNotification($sellerPendingNotification)
    {
        return $this->setData(self::SELLER_PENDING_NOTIFICATION, $sellerPendingNotification);
    }

    /**
     * Get SellerPendingNotification
     *
     * @return int
     */
    public function getSellerPendingNotification()
    {
        return parent::getData(self::SELLER_PENDING_NOTIFICATION);
    }
}
