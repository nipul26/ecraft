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
namespace Webkul\Marketplace\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Webkul\Marketplace\Helper\Data;

class FulfilledBy extends AbstractModifier
{

    /**
     * @param ArrayManager $arrayManager
     * @param Data $mpHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        protected ArrayManager $arrayManager,
        protected Data $mpHelper,
        protected \Magento\Framework\Registry $coreRegistry,
    ) {
    }

    /**
     * Modify Data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify Meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $attribute = \Webkul\Marketplace\Setup\Patch\Data\CreateFulfilmentAttribute::ATTR_FULFILMENT;
        $meta = array_replace_recursive(
            $meta,
            [
                'product-details' => [
                    "children" => [
                        "fulfilled_by" => [
                            "arguments" => [
                                "data" => [
                                    "config" => [
                                                "dataType" => "boolean",
                                                "formElement" => "checkbox",
                                                "visible" => "1",
                                                "required" => "0",
                                                "notice" => "",
                                                "default" => "0",
                                                "label" => __("Product Fulfilled By"),
                                                "code" => "fulfilled_by",
                                                "source" => "product-details",
                                                "scopeLabel" => __("[GLOBAL]"),
                                                "globalScope" => 1,
                                                "sortOrder" => 150,
                                                "componentType" => "field",
                                                "prefer" => "toggle",
                                                "valueMap" => [
                                                    "true" => "1",
                                                    "false" => "0"
                                                ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        if (isset($meta['product-details']['children']['fulfilled_by'])) {
            $path = Data::MARKETPLACE_GENERAL_SETTINGS_FULFILMENT_LABEL;
            $fulfilmentLabel = $this->mpHelper->getConfigurationValue($path);
            $path = $this->arrayManager->findPath($attribute, $meta, null, 'children');
            $meta = $this->arrayManager->set(
                "{$path}/arguments/data/config/label",
                $meta,
                __("Product Fulfilled By ( %1 )", $fulfilmentLabel)
            );
            $value = $this->getWkFulfilledBy();
            $meta = $this->arrayManager->set(
                "{$path}/arguments/data/config/value",
                $meta,
                $value
            );
        }
        return $meta;
    }
    /**
     * Get product Fulfilled By
     *
     * @return int
     */
    public function getWkFulfilledBy()
    {
        $product = $this->coreRegistry->registry('product');
        return $product->getFulfilledBy() ?? 0;
    }
}
