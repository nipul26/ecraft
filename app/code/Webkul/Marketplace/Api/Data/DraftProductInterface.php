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
 * DraftProduct Model Interface
 */
interface DraftProductInterface
{
    public const ENTITY_ID = 'entity_id';

    public const SELLER_ID = 'seller_id';

    public const NAME = 'name';

    public const SKU = 'sku';

    public const PRICE = 'price';

    public const QUANTITY = 'quantity';

    public const CONTENT = 'content';

    public const CREATED_AT = 'created_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set Name
     *
     * @param string $name
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setName($name);
    /**
     * Get Name
     *
     * @return string
     */
    public function getName();
    /**
     * Set Sku
     *
     * @param string $sku
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setSku($sku);
    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();
    /**
     * Set Price
     *
     * @param float $price
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setPrice($price);
    /**
     * Get Price
     *
     * @return float
     */
    public function getPrice();
    /**
     * Set Quantity
     *
     * @param string $quantity
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setQuantity($quantity);
    /**
     * Get Quantity
     *
     * @return string
     */
    public function getQuantity();
    /**
     * Set Content
     *
     * @param string $content
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setContent($content);
    /**
     * Get Content
     *
     * @return string
     */
    public function getContent();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
}
