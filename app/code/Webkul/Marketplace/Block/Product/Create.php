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

/*
 * Webkul Marketplace Product Create Block
 */
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\GoogleOptimizer\Model\Code as ModelCode;
use Webkul\Marketplace\Helper\Data as HelperData;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\DB\Helper as FrameworkDbHelper;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Create extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * @var ModelCode
     */
    protected $_modelCode;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var FrameworkDbHelper
     */
    protected $frameworkDbHelper;

    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var CacheInterface
     */
    private $cacheInterface;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var string|null
     */
    protected $filter = null;

    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var \Magento\Cms\Helper\Wysiwyg\Images|null
     */
    protected $wysiwygImages;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;
    /**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param Product $product
     * @param Category $category
     * @param ModelCode $modelCode
     * @param HelperData $helperData
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $categoryCollectionFactory
     * @param FrameworkDbHelper $frameworkDbHelper
     * @param CategoryHelper $categoryHelper
     * @param DataPersistorInterface $dataPersistor
     * @param SerializerInterface $serializer
     * @param \Magento\Cms\Helper\Wysiwyg\Images|null $wysiwygImages
     * @param CacheInterface|null $cacheInterface
     * @param \Magento\Directory\Model\Currency|null $currency
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        Product $product,
        Category $category,
        ModelCode $modelCode,
        HelperData $helperData,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $categoryCollectionFactory,
        FrameworkDbHelper $frameworkDbHelper,
        CategoryHelper $categoryHelper,
        DataPersistorInterface $dataPersistor,
        SerializerInterface $serializer,
        \Magento\Cms\Helper\Wysiwyg\Images $wysiwygImages,
        CacheInterface $cacheInterface,
        \Magento\Directory\Model\Currency $currency,
        array $data = [],
    ) {
        $this->_product = $product;
        $this->_category = $category;
        $this->_modelCode = $modelCode;
        $this->_helperData = $helperData;
        $this->_productRepository = $productRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->frameworkDbHelper = $frameworkDbHelper;
        $this->categoryHelper = $categoryHelper;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        $this->wysiwygImages = $wysiwygImages;
        $this->cacheInterface = $cacheInterface;
        $this->currency = $currency;
        parent::__construct($context, $data);
    }

    /**
     * Get product
     *
     * @param int $id
     * @return product
     */
    public function getProduct($id)
    {
        return $this->_product->load($id);
    }

    /**
     * Get category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Get Googleoptimizer Fields Values.
     *
     * @return array
     */
    public function getGoogleoptimizerFieldsValues()
    {
        $entityId = $this->getRequest()->getParam('id');
        $storeId = $this->_helperData->getCurrentStoreId();
        $experimentCodeModel = $this->_modelCode->loadByEntityIdAndType(
            $entityId,
            'product',
            $storeId
        );
        $result = [];
        $result['experiment_script'] =
        $experimentCodeModel ? $experimentCodeModel->getExperimentScript() : '';
        $result['code_id'] =
        $experimentCodeModel ? $experimentCodeModel->getCodeId() : '';

        return $result;
    }

    /**
     * Get product by sku
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     */
    private function getCacheModel()
    {
        return $this->cacheInterface;
    }

    /**
     * Retrieve categories tree
     *
     * @param string|null $filter
     *
     * @return array
     */
    public function getCategoriesTree($filter = null)
    {
        if (!$this->_helperData->getAllowedCategoryIds()) {
            $categoryTree = $this->getCacheModel()->load('seller_category_tree_' . $this->filter);
            if ($categoryTree) {
                return $categoryTree;
            }
        }

        $this->filter = $filter;
        $shownCategoriesIds = $this->getShownCategoryIds();

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        $sellerCategory = [
            Category::TREE_ROOT_ID => [
                'value' => Category::TREE_ROOT_ID,
                'optgroup' => null,
            ],
        ];

        foreach ($collection as $category) {
            $catId = $category->getId();
            $catParentId = $category->getParentId();
            foreach ([$catId, $catParentId] as $categoryId) {
                if (!isset($sellerCategory[$categoryId])) {
                    $sellerCategory[$categoryId] = ['value' => $categoryId];
                }
            }

            $sellerCategory[$catId]['is_active'] = $category->getIsActive();
            $sellerCategory[$catId]['label'] = $category->getName();
            $sellerCategory[$catParentId]['optgroup'][] = &$sellerCategory[$catId];
        }
        if (!$this->_helperData->getAllowedCategoryIds()) {
            $this->getCacheModel()->save(
                $this->serializer->serialize($sellerCategory[Category::TREE_ROOT_ID]['optgroup']),
                'seller_category_tree_' . $filter,
                [
                    Category::CACHE_TAG,
                    \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
                ]
            );
        }
        $optGroup = $this->_helperData->arrayToJson($sellerCategory[Category::TREE_ROOT_ID]['optgroup']);
        return $optGroup;
    }

    /**
     * Get Shown Category Ids
     *
     * @return array
     */
    public function getShownCategoryIds()
    {
        $storeId = $this->_helperData->getCurrentStoreId();
        $categoryCollection = $this->categoryCollectionFactory->create();
        if ($this->filter !== null) {
            $categoryCollection->addAttributeToFilter(
                'name',
                ['like' => $this->frameworkDbHelper->addLikeEscape($this->filter, ['position' => 'any'])]
            );
        }

        if ($this->_helperData->getAllowedCategoryIds()) {
            $allowedCategoryIds = explode(',', trim($this->_helperData->getAllowedCategoryIds()));
            $categoryCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['in' => $allowedCategoryIds])
            ->setStoreId($storeId);
        } else {
            $categoryCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => Category::TREE_ROOT_ID])
            ->setStoreId($storeId);
        }

        $shownCategoriesIds = [];

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categoryCollection as $category) {
            foreach (explode('/', $category['path']) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        return $shownCategoriesIds;
    }

    /**
     * Get Persistent Data for Product
     *
     * @return array
     */
    public function getPersistentData()
    {
        $draftId = $this->getRequest()->getParam('draft_id', 0);
        $persistentData = (array)$this->dataPersistor->get('seller_catalog_product');
        $fields = [
            "set" => "",
            "type" => "",
            "product" => [
                "name" => "",
                "category_ids" => [],
                "description" => "",
                "short_description" => "",
                "sku" => "",
                "price" => "",
                "special_price" => "",
                "special_from_date" => "",
                "special_to_date" => "",
                "product_has_weight" => 1,
                "weight" => "",
                "mp_product_cart_limit" => "",
                "visibility" => "",
                "tax_class_id" => "",
                "url_key" => "",
                "meta_title" => "",
                "meta_keyword" => "",
                "meta_description" => "",
                "fulfilled_by" => "",
                "quantity_and_stock_status" => [
                    "is_in_stock" => 1,
                    "qty" => ""
                ],
                "image" => "",
                "small_image" => "",
                "thumbnail" => ""
            ],
        ];
        if ($draftId) {
            $draftProd = $this->_helperData->getDraftProductCollection($draftId);
            $draftProdData = $this->_helperData->jsonToArray($draftProd->getContent());
            $persistentData = $this->setFieldsValue($persistentData, $draftProdData);
        } else {
            $persistentData = $this->setFieldsValue($persistentData, $fields);
        }
        $this->dataPersistor->clear('seller_catalog_product');
        return $persistentData;
    }

    /**
     * Validate and Set Default Values for Fields
     *
     * @param array $persistentData
     * @param array $fields
     *
     * @return array
     */
    public function setFieldsValue(&$persistentData, $fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                if (empty($persistentData[$key])) {
                    $persistentData[$key] = [];
                }

                $this->setFieldsValue($persistentData[$key], $fields[$key]);
            } else {
                if (empty($persistentData[$key])) {
                    $persistentData[$key] = $field;
                }
            }
        }

        return $persistentData;
    }
    /**
     * GetWysiwygUrl function
     *
     * @return string
     */
    public function getWysiwygUrl()
    {
        $currentTreePath = $this->wysiwygImages->idEncode(
            \Magento\Cms\Model\Wysiwyg\Config::IMAGE_DIRECTORY
        );
        $url =  $this->getUrl(
            'marketplace/wysiwyg_images/index',
            [
                'current_tree_path' => $currentTreePath
            ]
        );
        return $url;
    }

    /**
     * Get formatted price without currency symbol
     *
     * @param float $price
     * @return float
     */
    public function getFormattedPriceWithoutSymbol($price)
    {
        return $this->currency->format($price, ['display'=>\Magento\Framework\Currency::NO_SYMBOL], false);
    }
    /**
     * Get allowed product types
     *
     * @return array
     */
    public function getAllowedProductType()
    {
        return explode(',', $this->_helperData->getAllowedProductType());
    }
    /**
     * Convert array to json
     *
     * @param array $data
     * @return string
     */
    public function arrayToJson($data)
    {
        return $this->_helperData->arrayToJson($data);
    }
    /**
     * Convert date in proper format
     *
     * @param string $date
     * @return string
     */
    public function formatProductDate($date)
    {
        $dateObject = new \DateTime();
        return $dateObject->modify($date)->format('m/d/Y');
    }
    /**
     * Get fulfilment label
     *
     * @return string
     */
    public function getFulfilmentLabel()
    {
        $fulfilmentPath = \Webkul\Marketplace\Helper\Data::MARKETPLACE_GENERAL_SETTINGS_FULFILMENT_LABEL;
        return $this->_helperData->getConfigurationValue($fulfilmentPath);
    }
}
