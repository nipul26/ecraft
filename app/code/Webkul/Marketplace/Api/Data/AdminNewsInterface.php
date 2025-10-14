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
 * AdminNews Model Interface
 */
interface AdminNewsInterface
{
    public const ENTITY_ID = 'entity_id';

    public const CONTENT = 'content';

    public const IS_PUBLISH = 'is_publish';

    public const STATUS = 'status';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set Content
     *
     * @param string $content
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
     */
    public function setContent($content);
    /**
     * Get Content
     *
     * @return string
     */
    public function getContent();
    /**
     * Set IsPublish
     *
     * @param int $isPublish
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
     */
    public function setIsPublish($isPublish);
    /**
     * Get IsPublish
     *
     * @return int
     */
    public function getIsPublish();
    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
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
     * @return Webkul\Marketplace\Api\Data\AdminNewsInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
}
