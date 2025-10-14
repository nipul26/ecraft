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
use Webkul\Marketplace\Api\Data\FeedbackcountInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Feedbackcount Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Feedbackcount _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Feedbackcount getResource()
 */
class Feedbackcount extends AbstractModel implements FeedbackcountInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**#@-*/

    /**
     * Marketplace Feedbackcount cache tag.
     */
    public const CACHE_TAG = 'marketplace_feedbackcount';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_feedbackcount';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_feedbackcount';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Marketplace\Model\ResourceModel\Feedbackcount::class);
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
            return $this->noRouteFeedbackcount();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Feedbackcount.
     *
     * @return \Webkul\Marketplace\Model\Feedbackcount
     */
    public function noRouteFeedbackcount()
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
     * @return \Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
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
     * Set BuyerId
     *
     * @param int $buyerId
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
     */
    public function setBuyerId($buyerId)
    {
        return $this->setData(self::BUYER_ID, $buyerId);
    }

    /**
     * Get BuyerId
     *
     * @return int
     */
    public function getBuyerId()
    {
        return parent::getData(self::BUYER_ID);
    }

    /**
     * Set OrderCount
     *
     * @param int $orderCount
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
     */
    public function setOrderCount($orderCount)
    {
        return $this->setData(self::ORDER_COUNT, $orderCount);
    }

    /**
     * Get OrderCount
     *
     * @return int
     */
    public function getOrderCount()
    {
        return parent::getData(self::ORDER_COUNT);
    }

    /**
     * Set FeedbackCount
     *
     * @param int $feedbackCount
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
     */
    public function setFeedbackCount($feedbackCount)
    {
        return $this->setData(self::FEEDBACK_COUNT, $feedbackCount);
    }

    /**
     * Get FeedbackCount
     *
     * @return int
     */
    public function getFeedbackCount()
    {
        return parent::getData(self::FEEDBACK_COUNT);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
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
     * @return Webkul\Marketplace\Model\FeedbackcountInterface
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
}
