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
use Webkul\Marketplace\Api\Data\FeedbackInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Feedback Model
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Feedback _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Feedback getResource()
 */
class Feedback extends AbstractModel implements FeedbackInterface, IdentityInterface
{
    /**
     * No route page id
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Feedback's Statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    /**
     * Feedback's Rating Options
     */
    public const STAR1 = 20;
    public const STAR2 = 40;
    public const STAR3 = 60;
    public const STAR4 = 80;
    public const STAR5 = 100;

    /**
     * Marketplace Feedback cache tag
     */
    public const CACHE_TAG = 'marketplace_feedback';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_feedback';

    /**
     * @var string
     */
    protected $_eventPrefix = 'marketplace_feedback';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Feedback::class
        );
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteFeedback();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Feedback
     *
     * @return \Webkul\Marketplace\Model\Feedback
     */
    public function noRouteFeedback()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Prepare feedback's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Approved'),
            self::STATUS_DISABLED => __('Disapproved')
        ];
    }

    /**
     * Prepare feedback's rating options
     *
     * @return array
     */
    public function getAllRatingOptions()
    {
        return [
            self::STAR1 => __('1 Star'),
            self::STAR2 => __('2 Star'),
            self::STAR3 => __('3 Star'),
            self::STAR4 => __('4 Star'),
            self::STAR5 => __('5 Star'),
        ];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\FeedbackInterface
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
     * @return Webkul\Marketplace\Model\FeedbackInterface
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
     * Set BuyerEmail
     *
     * @param string $buyerEmail
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setBuyerEmail($buyerEmail)
    {
        return $this->setData(self::BUYER_EMAIL, $buyerEmail);
    }

    /**
     * Get BuyerEmail
     *
     * @return string
     */
    public function getBuyerEmail()
    {
        return parent::getData(self::BUYER_EMAIL);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set FeedPrice
     *
     * @param int $feedPrice
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedPrice($feedPrice)
    {
        return $this->setData(self::FEED_PRICE, $feedPrice);
    }

    /**
     * Get FeedPrice
     *
     * @return int
     */
    public function getFeedPrice()
    {
        return parent::getData(self::FEED_PRICE);
    }

    /**
     * Set FeedValue
     *
     * @param int $feedValue
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedValue($feedValue)
    {
        return $this->setData(self::FEED_VALUE, $feedValue);
    }

    /**
     * Get FeedValue
     *
     * @return int
     */
    public function getFeedValue()
    {
        return parent::getData(self::FEED_VALUE);
    }

    /**
     * Set FeedQuality
     *
     * @param int $feedQuality
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedQuality($feedQuality)
    {
        return $this->setData(self::FEED_QUALITY, $feedQuality);
    }

    /**
     * Get FeedQuality
     *
     * @return int
     */
    public function getFeedQuality()
    {
        return parent::getData(self::FEED_QUALITY);
    }

    /**
     * Set FeedNickname
     *
     * @param string $feedNickname
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedNickname($feedNickname)
    {
        return $this->setData(self::FEED_NICKNAME, $feedNickname);
    }

    /**
     * Get FeedNickname
     *
     * @return string
     */
    public function getFeedNickname()
    {
        return parent::getData(self::FEED_NICKNAME);
    }

    /**
     * Set FeedSummary
     *
     * @param string $feedSummary
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedSummary($feedSummary)
    {
        return $this->setData(self::FEED_SUMMARY, $feedSummary);
    }

    /**
     * Get FeedSummary
     *
     * @return string
     */
    public function getFeedSummary()
    {
        return parent::getData(self::FEED_SUMMARY);
    }

    /**
     * Set FeedReview
     *
     * @param string $feedReview
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setFeedReview($feedReview)
    {
        return $this->setData(self::FEED_REVIEW, $feedReview);
    }

    /**
     * Get FeedReview
     *
     * @return string
     */
    public function getFeedReview()
    {
        return parent::getData(self::FEED_REVIEW);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\FeedbackInterface
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
     * @return Webkul\Marketplace\Model\FeedbackInterface
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
     * @return Webkul\Marketplace\Model\FeedbackInterface
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

    /**
     * Set AdminNotification
     *
     * @param int $adminNotification
     * @return Webkul\Marketplace\Model\FeedbackInterface
     */
    public function setAdminNotification($adminNotification)
    {
        return $this->setData(self::ADMIN_NOTIFICATION, $adminNotification);
    }

    /**
     * Get AdminNotification
     *
     * @return int
     */
    public function getAdminNotification()
    {
        return parent::getData(self::ADMIN_NOTIFICATION);
    }
}
