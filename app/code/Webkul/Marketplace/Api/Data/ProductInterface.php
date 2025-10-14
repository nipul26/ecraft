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
 * Marketplace product interface.
 * @api
 */
interface ProductInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const MAGEPRODUCT_ID = 'mageproduct_id';

    public const ADMINASSIGN = 'adminassign';

    public const SELLER_ID = 'seller_id';

    public const STORE_ID = 'store_id';

    public const STATUS = 'status';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const SELLER_PENDING_NOTIFICATION = 'seller_pending_notification';

    public const ADMIN_PENDING_NOTIFICATION = 'admin_pending_notification';

    public const IS_APPROVED = 'is_approved';

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
     * @return \Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setId($id);
    /**
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setMageproductId($mageproductId);
    /**
     * Get MageproductId
     *
     * @return int
     */
    public function getMageproductId();
    /**
     * Set Adminassign
     *
     * @param int $adminassign
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setAdminassign($adminassign);
    /**
     * Get Adminassign
     *
     * @return int
     */
    public function getAdminassign();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set StoreId
     *
     * @param int $storeId
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setStoreId($storeId);
    /**
     * Get StoreId
     *
     * @return int
     */
    public function getStoreId();
    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Api\Data\ProductInterface
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
     * @return Webkul\Marketplace\Api\Data\ProductInterface
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
     * @return Webkul\Marketplace\Api\Data\ProductInterface
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
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setSellerPendingNotification($sellerPendingNotification);
    /**
     * Get SellerPendingNotification
     *
     * @return int
     */
    public function getSellerPendingNotification();
    /**
     * Set AdminPendingNotification
     *
     * @param int $adminPendingNotification
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setAdminPendingNotification($adminPendingNotification);
    /**
     * Get AdminPendingNotification
     *
     * @return int
     */
    public function getAdminPendingNotification();
    /**
     * Set IsApproved
     *
     * @param int $isApproved
     * @return Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setIsApproved($isApproved);
    /**
     * Get IsApproved
     *
     * @return int
     */
    public function getIsApproved();
}
