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
namespace Webkul\Marketplace\Block\Product;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as MpProductCollection;
use Webkul\Marketplace\Model\Product as SellerProduct;

class ProductListCard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var MpHelper
     */
    protected $mpHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var MpProductCollection
     */
    protected $mpProductCollectionFactory;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $attribute;
    /**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param MpHelper $mpHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param MpProductCollection $mpProductCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        MpHelper $mpHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        MpProductCollection $mpProductCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute,
        array $data = []
    ) {
        $this->mpHelper = $mpHelper;
        $this->productFactory = $productFactory;
        $this->mpProductCollectionFactory = $mpProductCollectionFactory;
        $this->attribute = $attribute;
        parent::__construct($context, $data);
    }
    /**
     * Get product full collection
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $sellerId = $this->mpHelper->getCustomerId();
        $collection = $this->productFactory->create()->getCollection();
        $collection = $this->mpProductCollectionFactory->create();
        $cpe = $collection->getTable("catalog_product_entity");
        $collection->getSelect()->join(
            ['cpe'=>$cpe],
            "main_table.mageproduct_id = cpe.entity_id",
            ["mpProductStatus" => "main_table.status"]
        );
        $collection->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        return $collection;
    }
    /**
     * Get enable product collection
     *
     * @return int
     */
    public function getEnableProduct()
    {
        $collection = $this->getProductCollection();
        $collection->addFieldToFilter('main_table.status', ['eq' => SellerProduct::STATUS_ENABLED]);

        $proStatusAttrId = $this->attribute->getIdByCode("catalog_product", "status");
        $catalogInventoryStockItem = $collection->getTable('cataloginventory_stock_item');
        $catalogProductEntityInt = $collection->getTable('catalog_product_entity_int');
        $collection->getSelect()->joinLeft(
            $catalogProductEntityInt.' as cpei',
            'main_table.mageproduct_id = cpei.entity_id',
            ["product_status" => "cpei.value"]
        )->where("cpei.store_id = 0 AND cpei.attribute_id = ".$proStatusAttrId);

        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.mageproduct_id = csi.product_id',
            ["qty" => "qty"]
        )->where("csi.website_id = 0 OR csi.website_id = 1");
        return $collection->getSize();
    }
    /**
     * Get denied product collection
     *
     * @return int
     */
    public function getDisableProduct()
    {
        $collection = $this->getProductCollection();
        $catalogProductEntityInt = $collection->getTable('catalog_product_entity_int');
        $proStatusAttrId = $this->attribute->getIdByCode("catalog_product", "status");
        $status = \Webkul\Marketplace\Model\Product::STATUS_DISABLED;
        $collection->getSelect()->joinLeft(
            $catalogProductEntityInt.' as cpei',
            'main_table.mageproduct_id = cpei.entity_id',
            ["product_status" => "cpei.value"]
        )->where("cpei.store_id = 0 AND cpei.attribute_id = ".$proStatusAttrId)
         ->where("cpei.value = ".$status);
        return $collection->getSize();
    }
    /**
     * Get denied product collection
     *
     * @return int
     */
    public function getDeniedProduct()
    {
        $collection = $this->getProductCollection();
        $collection->getSelect()->where('main_table.status = '.SellerProduct::STATUS_DENIED);
        return $collection->getSize();
    }
    /**
     * Get pending product collection
     *
     * @return int
     */
    public function getPendingProduct()
    {
        $collection = $this->getProductCollection();
        $collection->getSelect()->where('main_table.status = '.SellerProduct::STATUS_PENDING);
        return $collection->getSize();
    }
    /**
     * Get pending product collection
     *
     * @return int
     */
    public function getOutOfStockProduct()
    {
        $collection = $this->getProductCollection();
        $collection->addFieldToFilter('main_table.status', ['eq' => SellerProduct::STATUS_ENABLED]);
        $catalogInventoryStockItem = $collection->getTable('cataloginventory_stock_item');
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.mageproduct_id = csi.product_id',
            ["qty" => "qty"]
        )->where("csi.is_in_stock = 0");
        return $collection->getSize();
    }
    /**
     * Get pending product collection
     *
     * @return int
     */
    public function getLowStockProduct()
    {
        $sellerInfo = $this->mpHelper->getSellerData();
        $lowStockQuantity = $sellerInfo->getFirstItem()->getLowStockQuantity();
        $lowStockQuantity = $lowStockQuantity?$lowStockQuantity:$this->mpHelper->getlowStockQty();
        $collection = $this->getProductCollection();
        $collection->addFieldToFilter('main_table.status', ['eq' => SellerProduct::STATUS_ENABLED]);
        $catalogInventoryStockItem = $collection->getTable('cataloginventory_stock_item');
        $collection->getSelect()->join(
            $catalogInventoryStockItem.' as csi',
            'main_table.mageproduct_id = csi.product_id',
            ["qty" => "qty"]
        )->where("csi.qty <= ".$lowStockQuantity)
        ->where("csi.qty > 0")
        ->where("csi.is_in_stock = 1");
        return $collection->getSize();
    }
    /**
     * Check if product approval is enable
     *
     * @return bool
     */
    public function isProductApprovalEnable()
    {
        $status = false;
        if ($this->mpHelper->getIsProductApproval() || $this->mpHelper->getIsProductEditApproval()) {
            $status = true;
        }
        return $status;
    }
}
