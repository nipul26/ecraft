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
 * Marketplace Feedback Interface
 * @api
 */
interface FeedbackInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const SELLER_ID = 'seller_id';

    public const BUYER_ID = 'buyer_id';

    public const BUYER_EMAIL = 'buyer_email';

    public const STATUS = 'status';

    public const FEED_PRICE = 'feed_price';

    public const FEED_VALUE = 'feed_value';

    public const FEED_QUALITY = 'feed_quality';

    public const FEED_NICKNAME = 'feed_nickname';

    public const FEED_SUMMARY = 'feed_summary';

    public const FEED_REVIEW = 'feed_review';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const SELLER_PENDING_NOTIFICATION = 'seller_pending_notification';

    public const ADMIN_NOTIFICATION = 'admin_notification';

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
     * @return \Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setId($id);

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set BuyerId
     *
     * @param int $buyerId
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setBuyerId($buyerId);
    /**
     * Get BuyerId
     *
     * @return int
     */
    public function getBuyerId();
    /**
     * Set BuyerEmail
     *
     * @param string $buyerEmail
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setBuyerEmail($buyerEmail);
    /**
     * Get BuyerEmail
     *
     * @return string
     */
    public function getBuyerEmail();
    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();
    /**
     * Set FeedPrice
     *
     * @param int $feedPrice
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedPrice($feedPrice);
    /**
     * Get FeedPrice
     *
     * @return int
     */
    public function getFeedPrice();
    /**
     * Set FeedValue
     *
     * @param int $feedValue
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedValue($feedValue);
    /**
     * Get FeedValue
     *
     * @return int
     */
    public function getFeedValue();
    /**
     * Set FeedQuality
     *
     * @param int $feedQuality
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedQuality($feedQuality);
    /**
     * Get FeedQuality
     *
     * @return int
     */
    public function getFeedQuality();
    /**
     * Set FeedNickname
     *
     * @param string $feedNickname
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedNickname($feedNickname);
    /**
     * Get FeedNickname
     *
     * @return string
     */
    public function getFeedNickname();
    /**
     * Set FeedSummary
     *
     * @param string $feedSummary
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedSummary($feedSummary);
    /**
     * Get FeedSummary
     *
     * @return string
     */
    public function getFeedSummary();
    /**
     * Set FeedReview
     *
     * @param string $feedReview
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setFeedReview($feedReview);
    /**
     * Get FeedReview
     *
     * @return string
     */
    public function getFeedReview();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
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
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
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
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setSellerPendingNotification($sellerPendingNotification);
    /**
     * Get SellerPendingNotification
     *
     * @return int
     */
    public function getSellerPendingNotification();
    /**
     * Set AdminNotification
     *
     * @param int $adminNotification
     * @return Webkul\Marketplace\Api\Data\FeedbackInterface
     */
    public function setAdminNotification($adminNotification);
    /**
     * Get AdminNotification
     *
     * @return int
     */
    public function getAdminNotification();
}
