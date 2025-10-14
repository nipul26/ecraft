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
use Magento\Framework\DataObject\IdentityInterface;
use Webkul\Marketplace\Api\Data\PublishedNewsInterface;

/**
 * PublishedNews Model Class
 */
class PublishedNews extends AbstractModel implements IdentityInterface, PublishedNewsInterface
{
    public const NOROUTE_ENTITY_ID = 'no-route';

    public const CACHE_TAG = 'webkul_marketplace_publishednews';

    /**
     * @var string
     */
    protected $_cacheTag = 'webkul_marketplace_publishednews';
    /**
     * @var string
     */
    protected $_eventPrefix = 'webkul_marketplace_publishednews';

    public const IS_READ_YES = 1;

    public const IS_READ_NO = 0;

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\Marketplace\Model\ResourceModel\PublishedNews::class);
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
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
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
     * Set NewsId
     *
     * @param int $newsId
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
     */
    public function setNewsId($newsId)
    {
        return $this->setData(self::NEWS_ID, $newsId);
    }

    /**
     * Get NewsId
     *
     * @return int
     */
    public function getNewsId()
    {
        return parent::getData(self::NEWS_ID);
    }

    /**
     * Set IsRead
     *
     * @param int $isRead
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
     */
    public function setIsRead($isRead)
    {
        return $this->setData(self::IS_READ, $isRead);
    }

    /**
     * Get IsRead
     *
     * @return int
     */
    public function getIsRead()
    {
        return parent::getData(self::IS_READ);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
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
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
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
     * @return Webkul\Marketplace\Model\PublishedNewsInterface
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
