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
 * ProductRemark Model Interface
 */
interface ProductRemarkInterface
{
    public const ENTITY_ID = 'entity_id';

    public const MAGEPRODUCT_ID = 'mageproduct_id';

    public const REMARK = 'remark';

    public const SELLER_ID = 'seller_id';

    public const CREATED_AT = 'created_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\Marketplace\Api\Data\ProductRemarkInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Api\Data\ProductRemarkInterface
     */
    public function setMageproductId($mageproductId);
    /**
     * Get MageproductId
     *
     * @return int
     */
    public function getMageproductId();
    /**
     * Set Remark
     *
     * @param string $remark
     * @return Webkul\Marketplace\Api\Data\ProductRemarkInterface
     */
    public function setRemark($remark);
    /**
     * Get Remark
     *
     * @return string
     */
    public function getRemark();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\ProductRemarkInterface
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
     * @return Webkul\Marketplace\Api\Data\ProductRemarkInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
}
