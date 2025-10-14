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
use Webkul\Marketplace\Api\Data\DraftProductInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * DraftProduct Model Class
 */
class DraftProduct extends AbstractModel implements IdentityInterface, DraftProductInterface
{
    public const NOROUTE_ENTITY_ID = 'no-route';

    public const CACHE_TAG = 'webkul_marketplace_draftproduct';

    /**
     * @var string
     */
    protected $_cacheTag = 'webkul_marketplace_draftproduct';
    /**
     * @var string
     */
    protected $_eventPrefix = 'webkul_marketplace_draftproduct';

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Webkul\Marketplace\Model\ResourceModel\DraftProduct::class);
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
     * @return Webkul\Marketplace\Model\DraftProductInterface
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
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\DraftProductInterface
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
     * Set Name
     *
     * @param string $name
     * @return Webkul\Marketplace\Model\DraftProductInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * Set Sku
     *
     * @param string $sku
     * @return Webkul\Marketplace\Model\DraftProductInterface
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku()
    {
        return parent::getData(self::SKU);
    }

    /**
     * Set Price
     *
     * @param float $price
     * @return Webkul\Marketplace\Model\DraftProductInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get Price
     *
     * @return float
     */
    public function getPrice()
    {
        return parent::getData(self::PRICE);
    }

    /**
     * Set Quantity
     *
     * @param string $quantity
     * @return Webkul\Marketplace\Model\DraftProductInterface
     */
    public function setQuantity($quantity)
    {
        return $this->setData(self::QUANTITY, $quantity);
    }

    /**
     * Get Quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return parent::getData(self::QUANTITY);
    }

    /**
     * Set Content
     *
     * @param string $content
     * @return Webkul\Marketplace\Model\DraftProductInterface
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
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\DraftProductInterface
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
