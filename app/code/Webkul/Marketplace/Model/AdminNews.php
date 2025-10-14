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

use Magento\Framework\DataObject\IdentityInterface;
use Webkul\Marketplace\Api\Data\AdminNewsInterface;

/**
 * AdminNews Model Class
 */
class AdminNews extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, AdminNewsInterface
{
    public const NOROUTE_ENTITY_ID = 'no-route';

    public const CACHE_TAG = 'webkul_marketplace_adminnews';

    /**
     * @var string
     */
    protected $_cacheTag = 'webkul_marketplace_adminnews';

    /**
     * @var string
     */
    protected $_eventPrefix = 'webkul_marketplace_adminnews';

    public const IS_PUBISH_YES = 1;
    
    public const IS_PUBISH_NO = 0;

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\Marketplace\Model\ResourceModel\AdminNews::class);
    }

    /**
     * Load No-Route Indexer.
     *
     * @return $this
     */
    public function noRouteReasons()
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
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\Marketplace\Model\AdminNewsInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set Content
     *
     * @param string $content
     * @return Webkul\Marketplace\Model\AdminNewsInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Get Content
     *
     * @return string
     */
    public function getContent()
    {
        return parent::getData(self::CONTENT);
    }

    /**
     * Set IsPublish
     *
     * @param int $isPublish
     * @return Webkul\Marketplace\Model\AdminNewsInterface
     */
    public function setIsPublish($isPublish)
    {
        return $this->setData(self::IS_PUBLISH, $isPublish);
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
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Model\AdminNewsInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get IsPublish
     *
     * @return int
     */
    public function getIsPublish()
    {
        return parent::getData(self::IS_PUBLISH);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\AdminNewsInterface
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
     * @return Webkul\Marketplace\Model\AdminNewsInterface
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
