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
use Webkul\Marketplace\Api\Data\ProductRemarkInterface;

/**
 * ProductRemark Model Class
 */
class ProductRemark extends AbstractModel implements IdentityInterface, ProductRemarkInterface
{
    /**
     * No route entity id
     */
    public const NOROUTE_ENTITY_ID = 'no-route';
    /**
     * Remark cache tag
     */
    public const CACHE_TAG = 'webkul_marketplace_productremark';
    /**
     * Remark cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'webkul_marketplace_productremark';
    /**
     * Remark event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'webkul_marketplace_productremark';

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\Marketplace\Model\ResourceModel\ProductRemark::class);
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
     * @return Webkul\Marketplace\Model\ProductRemarkInterface
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
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Model\ProductRemarkInterface
     */
    public function setMageproductId($mageproductId)
    {
        return $this->setData(self::MAGEPRODUCT_ID, $mageproductId);
    }

    /**
     * Get MageproductId
     *
     * @return int
     */
    public function getMageproductId()
    {
        return parent::getData(self::MAGEPRODUCT_ID);
    }

    /**
     * Set Remark
     *
     * @param string $remark
     * @return Webkul\Marketplace\Model\ProductRemarkInterface
     */
    public function setRemark($remark)
    {
        return $this->setData(self::REMARK, $remark);
    }

    /**
     * Get Remark
     *
     * @return string
     */
    public function getRemark()
    {
        return parent::getData(self::REMARK);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\ProductRemarkInterface
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
     * @return Webkul\Marketplace\Model\ProductRemarkInterface
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
}
