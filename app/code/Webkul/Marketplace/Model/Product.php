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
use Webkul\Marketplace\Api\Data\ProductInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Product Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Product _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Product getResource()
 */
class Product extends AbstractModel implements ProductInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Product's Statuses
     */
    public const STATUS_PENDING = 0;
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;
    public const STATUS_DENIED = 3;
    public const PRODUCT_TYPE_SIMPLE = "simple";
    public const PRODUCT_TYPE_CONFIGURABLE = "configurable";
    public const PRODUCT_TYPE_DOWNLOADABLE = "downloadable";
    public const PRODUCT_TYPE_GROUPED = "grouped";
    public const PRODUCT_TYPE_BUNDLE = "bundle";
    public const PRODUCT_TYPE_VIRTUAL = "virtual";

    /**
     * Product's type used in product list cards
     */
    public const FILTER_ENABLE = "enable";
    public const FILTER_DISABLE = "disable";
    public const FILTER_LOW_STOCK = "low_stock";
    public const FILTER_OUT_OF_STOCK = "out_of_stock";
    public const FILTER_PENDING = "pending";
    public const FILTER_DENY = "deny";

    /**
     * Marketplace Product cache tag.
     */
    public const CACHE_TAG = 'marketplace_product';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_product';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_product';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Product::class
        );
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteProduct();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Product.
     *
     * @return \Webkul\Marketplace\Model\Product
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Prepare product's statuses.Available event marketplace_product_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ENABLED => __('Approved'),
            self::STATUS_DISABLED => __('Disapproved'),
            self::STATUS_DENIED => __('Denied'),
        ];
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
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\Marketplace\Api\Data\ProductInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set MageproductId
     *
     * @param int $mageproductId
     * @return Webkul\Marketplace\Model\ProductInterface
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
     * Set Adminassign
     *
     * @param int $adminassign
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setAdminassign($adminassign)
    {
        return $this->setData(self::ADMINASSIGN, $adminassign);
    }

    /**
     * Get Adminassign
     *
     * @return int
     */
    public function getAdminassign()
    {
        return parent::getData(self::ADMINASSIGN);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\ProductInterface
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
     * Set StoreId
     *
     * @param int $storeId
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get StoreId
     *
     * @return int
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\ProductInterface
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
     * @return Webkul\Marketplace\Model\ProductInterface
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

    /**
     * Set SellerPendingNotification
     *
     * @param int $sellerPendingNotification
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setSellerPendingNotification($sellerPendingNotification)
    {
        return $this->setData(self::SELLER_PENDING_NOTIFICATION, $sellerPendingNotification);
    }

    /**
     * Get SellerPendingNotification
     *
     * @return int
     */
    public function getSellerPendingNotification()
    {
        return parent::getData(self::SELLER_PENDING_NOTIFICATION);
    }

    /**
     * Set AdminPendingNotification
     *
     * @param int $adminPendingNotification
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setAdminPendingNotification($adminPendingNotification)
    {
        return $this->setData(self::ADMIN_PENDING_NOTIFICATION, $adminPendingNotification);
    }

    /**
     * Get AdminPendingNotification
     *
     * @return int
     */
    public function getAdminPendingNotification()
    {
        return parent::getData(self::ADMIN_PENDING_NOTIFICATION);
    }

    /**
     * Set IsApproved
     *
     * @param int $isApproved
     * @return Webkul\Marketplace\Model\ProductInterface
     */
    public function setIsApproved($isApproved)
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }

    /**
     * Get IsApproved
     *
     * @return int
     */
    public function getIsApproved()
    {
        return parent::getData(self::IS_APPROVED);
    }
}
