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

namespace Webkul\Marketplace\Plugin\Block;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelperData;
use Webkul\Marketplace\Model\Product;

class ProductView
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var MarketplaceHelperData
     */
    private $marketplaceHelperData;

    /**
     * Construct
     *
     * @param StockRegistryInterface $stockRegistry
     * @param MarketplaceHelperData $marketplaceHelperData
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        MarketplaceHelperData $marketplaceHelperData
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->marketplaceHelperData = $marketplaceHelperData;
    }

    /**
     * Set Max limit on cart
     *
     * @param \Magento\Catalog\Block\Product\View $block
     * @param array $validators
     * @return array
     */
    public function afterGetQuantityValidators(
        \Magento\Catalog\Block\Product\View $block,
        array $validators
    ) {
        $stockItem = $this->stockRegistry->getStockItem(
            $block->getProduct()->getId(),
            $block->getProduct()->getStore()->getWebsiteId()
        );
        $mpProductCartLimit = $this->checkAndUpdateProductCartLimit($block->getProduct());
        $params = [];
        $params['minAllowed']  = (float)$stockItem->getMinSaleQty();
        if ($mpProductCartLimit && $mpProductCartLimit != "") {
            $params['maxAllowed'] = (float)$mpProductCartLimit;
        } elseif ($stockItem->getMaxSaleQty()) {
            $params['maxAllowed'] = (float)$stockItem->getMaxSaleQty();
        }
        if ($stockItem->getQtyIncrements() > 0) {
            $params['qtyIncrements'] = (float)$stockItem->getQtyIncrements();
        }
        $validators['validate-item-quantity'] = $params;

        return $validators;
    }
    /**
     * Check And Update Product Cart Limit is used to check cart items limit
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return bool|float
     */
    public function checkAndUpdateProductCartLimit($product)
    {
        try {
            $allowProductLimit = $this->marketplaceHelperData->getAllowProductLimit();
            if ($allowProductLimit) {
                $sellerProductDataColl = $this->marketplaceHelperData->getSellerProductDataByProductId(
                    $product->getId()
                );
              
                $productTypeId = $product['type_id'];
                $productDownloadable = Product::PRODUCT_TYPE_DOWNLOADABLE;
                $productVirtual = Product::PRODUCT_TYPE_VIRTUAL;
                if ($productTypeId != $productDownloadable && $productTypeId != $productVirtual) {
                    $mpProductCartLimit = $product['mp_product_cart_limit'];
                    if (!$mpProductCartLimit) {
                        $mpProductCartLimit = $this->marketplaceHelperData->getGlobalProductLimitQty();
                    }
                    return $mpProductCartLimit;
                }
            }
        } catch (\Exception $e) {
            $this->marketplaceHelperData->logDataInLogger(
                "Plugin_IsCorrectQtyCondition checkAndUpdateProductCartLimit : " . $e->getMessage()
            );
        }
        return false;
    }
}
