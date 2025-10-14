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
 * PublishedNews Model Interface
 */
interface PublishedNewsInterface
{
    public const ENTITY_ID = 'entity_id';

    public const NEWS_ID = 'news_id';

    public const IS_READ = 'is_read';

    public const SELLER_ID = 'seller_id';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set NewsId
     *
     * @param int $newsId
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
     */
    public function setNewsId($newsId);
    /**
     * Get NewsId
     *
     * @return int
     */
    public function getNewsId();
    /**
     * Set IsRead
     *
     * @param int $isRead
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
     */
    public function setIsRead($isRead);
    /**
     * Get IsRead
     *
     * @return int
     */
    public function getIsRead();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
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
     * @return Webkul\Marketplace\Api\Data\PublishedNewsInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
}
