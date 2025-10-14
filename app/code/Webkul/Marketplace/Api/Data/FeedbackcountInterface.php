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
 * Marketplace Feedbackcount Interface
 * @api
 */
interface FeedbackcountInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const SELLER_ID = 'seller_id';

    public const BUYER_ID = 'buyer_id';

    public const ORDER_COUNT = 'order_count';

    public const FEEDBACK_COUNT = 'feedback_count';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

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
     * @return \Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setId($id);
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
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
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setBuyerId($buyerId);
    /**
     * Get BuyerId
     *
     * @return int
     */
    public function getBuyerId();
    /**
     * Set OrderCount
     *
     * @param int $orderCount
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setOrderCount($orderCount);
    /**
     * Get OrderCount
     *
     * @return int
     */
    public function getOrderCount();
    /**
     * Set FeedbackCount
     *
     * @param int $feedbackCount
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setFeedbackCount($feedbackCount);
    /**
     * Get FeedbackCount
     *
     * @return int
     */
    public function getFeedbackCount();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
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
     * @return Webkul\Marketplace\Api\Data\FeedbackcountInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
}
