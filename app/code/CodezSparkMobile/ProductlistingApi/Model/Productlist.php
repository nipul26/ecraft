<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CodezSparkMobile\ProductlistingApi\Model;

use Amasty\Shopby\Model\Layer\FilterList;
use CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\OptionInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterface;
use CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\SettingDataInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\SettingDataInterface;
use CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterfaceFactory;
use CodezSparkMobile\ProductlistingApi\Model\ProductFactory;
use CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product\Collection;
use CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\Layer\FilterListFactory;
use Magento\Catalog\Model\Layer\Resolver as layerResolver;
use Magento\Catalog\Model\ProductRepository\MediaGalleryProcessor;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
// use CodezSparkMobile\MobileAppApi\Helper\Data as MobileApiHelperData;
use Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableResource;
use Magento\Framework\Webapi\Rest\Response;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Productlist implements \CodezSparkMobile\ProductlistingApi\Api\ProductlistInterface
{
    public const CONFIG_XML_PATH_PRICE_DISPLAY_TYPE = 'tax/display/type';

    /**
     * @var \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $instances = [];

    /**
     * @var Product[]
     */
    protected $instancesById = [];

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * @var Product\Initialization\Helper\ProductLinks
     */
    protected $linkInitializer;

    /**
     * @var Product\LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $metadataService;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     *
     * @var ImageContentInterfaceFactory
     */
    protected $contentFactory;

    /**
     *
     * @var ImageProcessorInterface
     */
    protected $imageProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     *
     * @var \Magento\Catalog\Model\Product\Gallery\Processor
     */
    protected $mediaGalleryProcessor;

    /**
     * @var MediaGalleryProcessor
     */
    private $mediaProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var int
     */
    private $cacheLimit = 0;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var ReadExtensions
     */
    private $readExtensions;

    /**
     * @var SettingDataInterfaceFactory
     */
    protected $settingDataInterfaceFactory;

    /**
     * @var SettingsInterfaceFactory
     */
    protected $settingsInterfaceFactory;

    /**
     * @var ProductInterfaceFactory
     */
    protected $productInterfaceFactory;

    /**
     * @var FilterDataInterfaceFactory
     */
    protected $filterDataInterfaceFactory;

    /**
     * @var OptionInterfaceFactory
     */
    protected $optionInterfaceFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var SortingDataInterfaceFactory
     */
    protected $sortingDataInterfaceFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \CodezSparkMobile\ProductlistingApi\Api\Data\ProductBadgesInterfaceFactory
     */
    protected $productBadges;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $filterableAttributes;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterListFactory
     */
    protected $filterListFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Resource connection
     */
    protected $resourceConnection;
    
      /**
     * @var MobileApiHelperData
     */
    // protected $mobileApiHelperData;

    /**
     * @var ScopeConfigInterfaces
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    // protected $homeScreenApiHelper;

    /**
     * @var ScopeOverriddenValue
     */
    protected $scopeOverriddenValue;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    protected $configurableFactory;

    protected $configurableResource;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Construct
     *
     * @param ProductFactory $productFactory
     * @param SettingDataInterfaceFactory $settingDataInterfaceFactory
     * @param SettingsInterfaceFactory $settingsInterfaceFactory
     * @param ProductInterfaceFactory $productInterfaceFactory
     * @param FilterDataInterfaceFactory $filterDataInterfaceFactory
     * @param OptionInterfaceFactory $optionInterfaceFactory
     * @param SortingDataInterfaceFactory $sortingDataInterfaceFactory
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param FilterableAttributeList $filterableAttributes
     * @param layerResolver $layerResolver
     * @param FilterListFactory $filterListFactory
     * @param Registry $coreRegistry
     * @param \CodezSparkMobile\HomeScreenApi\Helper\Data $homeScreenApiHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\ProductBadgesInterfaceFactory $productBadges
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product $resourceModel
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param ImageContentInterfaceFactory $contentFactory
     * @param ImageProcessorInterface $imageProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param Response $response
     * @param CollectionProcessorInterface $collectionProcessor = null
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer = null
     * @param CacheLimit $cacheLimit
     * @param ReadExtensions $readExtensions = null
     * @param ScopeOverriddenValue $scopeOverriddenValue = null
     *
     */

    public function __construct(
        ConfigurableFactory $configurableFactory,
        ConfigurableResource $configurableResource,
        ProductFactory $productFactory,
        SettingDataInterfaceFactory $settingDataInterfaceFactory,
        SettingsInterfaceFactory $settingsInterfaceFactory,
        ProductInterfaceFactory $productInterfaceFactory,
        FilterDataInterfaceFactory $filterDataInterfaceFactory,
        OptionInterfaceFactory $optionInterfaceFactory,
        SortingDataInterfaceFactory $sortingDataInterfaceFactory,
        CategoryFactory $categoryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        FilterableAttributeList $filterableAttributes,
        layerResolver $layerResolver,
        FilterListFactory $filterListFactory,
        Registry $coreRegistry,
        // \CodezSparkMobile\HomeScreenApi\Helper\Data $homeScreenApiHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \CodezSparkMobile\ProductlistingApi\Api\Data\ProductBadgesInterfaceFactory $productBadges,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product $resourceModel,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Framework\Filesystem $fileSystem,
        ImageContentInterfaceFactory $contentFactory,
        ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        Response $response,
        \Magento\Framework\Webapi\Rest\Request $request,
        ?CollectionProcessorInterface $collectionProcessor = null,
        ?\Magento\Framework\Serialize\Serializer\Json $serializer = null,
        $cacheLimit = 1000,
        ?ReadExtensions $readExtensions = null,
        ?ScopeOverriddenValue $scopeOverriddenValue = null
    ) {
        $this->configurableFactory = $configurableFactory;
        $this->configurableResource = $configurableResource;
        $this->productFactory = $productFactory;
        $this->settingDataInterfaceFactory = $settingDataInterfaceFactory;
        $this->settingsInterfaceFactory = $settingsInterfaceFactory;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->filterDataInterfaceFactory = $filterDataInterfaceFactory;
        $this->optionInterfaceFactory = $optionInterfaceFactory;
        $this->sortingDataInterfaceFactory = $sortingDataInterfaceFactory;
        $this->categoryFactory = $categoryFactory;
        $this->_eavConfig = $eavConfig;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->filterableAttributes = $filterableAttributes;
        $this->layerResolver = $layerResolver;
        $this->filterListFactory = $filterListFactory;
        $this->coreRegistry = $coreRegistry;
        // $this->homeScreenApiHelper = $homeScreenApiHelper;
        $this->productCollection = $productCollection;
        $this->productBadges = $productBadges;
        $this->collectionFactory = $collectionFactory;
        $this->initializationHelper = $initializationHelper;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceModel = $resourceModel;
        $this->linkInitializer = $linkInitializer;
        $this->linkTypeProvider = $linkTypeProvider;
        $this->storeManager = $storeManager;
        $this->attributeRepository = $attributeRepository;
        $this->filterBuilder = $filterBuilder;
        $this->metadataService = $metadataServiceInterface;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->fileSystem = $fileSystem;
        $this->contentFactory = $contentFactory;
        $this->imageProcessor = $imageProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->resourceConnection = $resourceConnection->getConnection();
        // $this->mobileApiHelperData = $mobileApiHelperData;
        $this->request = $request;
        $this->response = $response;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->cacheLimit = (int) $cacheLimit;
        $this->readExtensions = $readExtensions ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ReadExtensions::class);
        $this->scopeOverriddenValue = $scopeOverriddenValue ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ScopeOverriddenValue::class);
    }

    /**
     * Get Product List
     *
     * @param ProductId $productId
     * @param EditMode $editMode
     * @param StoreId $storeId
     * @param ForceReload $forceReload
     * @return SettingDataInterface
     */
    public function getById($productId, $editMode = false, $storeId = null, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instancesById[$productId][$cacheKey]) || $forceReload) {
            $product = $this->productFactory->create();
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($productId);
            if (!$product->getId()) {
                throw new NoSuchEntityException(
                    __("The product that was requested doesn't exist. Verify the product and try again.")
                );
            }
            $this->cacheProduct($cacheKey, $product);
        }
        return $this->instancesById[$productId][$cacheKey];
    }

    /**
     * Get key for cache
     *
     * @param array $data
     * @return string
     */
    protected function getCacheKey($data)
    {
        $serializeData = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $serializeData[$key] = $value->getId();
            } else {
                $serializeData[$key] = $value;
            }
        }
        $serializeData = $this->serializer->serialize($serializeData);
        return sha1($serializeData);
    }

    /**
     * Add product to internal cache and truncate cache if it has more than cacheLimit elements.
     *
     * @param string $cacheKey
     * @param ProductInterface $product
     * @return void
     */
    private function cacheProduct($cacheKey, ProductInterface $product)
    {
        $this->instancesById[$product->getId()][$cacheKey] = $product;
        $this->saveProductInLocalCache($product, $cacheKey);

        if ($this->cacheLimit && count($this->instances) > $this->cacheLimit) {
            $offset = round($this->cacheLimit / -2);
            $this->instancesById = array_slice($this->instancesById, $offset, null, true);
            $this->instances = array_slice($this->instances, $offset, null, true);
        }
    }

    /**
     * Get product list
     *
     * @param StoreId $storeId
     * @param FilterData $filterData
     * @param CurrentPage $currentPage
     * @param Position $position
     * @return array
     */
    public function getList($storeId, $filterData = null, $currentPage = null, $position = null)
    {
        $filterList = json_decode($filterData,true);
        $settingData = $this->settingDataInterfaceFactory->create();
        $settings = $this->settingsInterfaceFactory->create();
        $storeData = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($storeData);

        try {
            /** @var \CodezSparkMobile\ProductlistingApi\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->collectionFactory->create();
            $this->extensionAttributesJoinProcessor->process($collection);
            $limit = $this->scopeConfig
            ->getValue(
                "homepage_api_configuration/product_list_config/product_limit",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $collection->setPageSize($limit);
            $collection->setCurPage($currentPage ? $currentPage : 1);

            $categoryId = null;
            if (isset($filterList) || $filterList) {
                foreach ($filterList as $filterKey => $filterValue) {
                    if ($filterKey == 'category') {
                        $categoryId = $filterValue;
                    } elseif ($filterKey == 'minPrice') {
                        $collection->addAttributeToFilter('price', ['gteq' => $filterValue]);
                    } elseif ($filterKey == 'maxPrice') {
                        $collection->addAttributeToFilter('price', ['lteq' => $filterValue]);
                    } else {
                        $collection->addAttributeToFilter($filterKey, ['eq' => $filterValue]);
                    }
                }
            }
            $categoryIds = '';
            $category = $this->categoryFactory->create()->load($categoryId);
            $this->coreRegistry->register("current_category", $category);

            if ($categoryId) {
                $collection->addCategoriesFilter(['in' => $categoryId]);
            }
            // if ($category->getLevel() == 4 && $category->hasChildren()) {
            //     $categoryIds = $category->getAllChildren();
            //     $categoryIds = explode(",", $categoryIds);
            // }
            // if ($categoryIds) {
            //     $collection->addCategoriesFilter(['in' => $categoryIds]);
            // } else {
            //     $collection->addCategoriesFilter(['in' => $categoryId]);
            // }
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            );

            // $appVersion = $this->request->getHeader('appVersion');
            // $deviceType = $this->request->getHeader('deviceType');
            // if ((!isset($appVersion) && !isset($deviceType)) || (empty($appVersion) && empty($deviceType))) {
            //     $collection->addAttributeToFilter('type_id', ['neq' => \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD]);
            //     $collection->addAttributeToFilter('type_id', ['neq' => \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE]);
            // }

            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            $this->joinPositionField($collection, $categoryId);

            $collection->setOrder('name', 'asc');
            
            if (isset($position)) {
                if ($position == 'price_high_low') {
                    $collection->setOrder('price', 'desc');
                }
                if ($position == 'price_low_high') {
                    $collection->setOrder('price', 'asc');
                }
                if ($position == 'product_a_z') {
                    $collection->setOrder('name', 'asc');
                }
                if ($position == 'product_z_a') {
                    $collection->setOrder('name', 'desc');
                }
            }

            if (isset($storeId)) {
                $collection->addStoreFilter($storeId);
            }
            $collection->load();
            $totalPage = ceil($collection->getSize() / $collection->getPageSize());
            $collection->addCategoryIds();
            $this->addExtensionAttributes($collection);

            // $searchResult = $this->searchResultsFactory->create();
            $response['total_pages'] = $totalPage;
            $response['total_product_count'] = $collection->getSize();
            $response['total_count_per_page'] = $collection->getPageSize();
            $response['product_list'] = $this->prepareItems($collection->getItems());
            // $searchResult->setTotalPages($totalPage);
            // $searchResult->setTotalProductCount($collection->getSize());
            // $searchResult->setTotalCountPerPage($collection->getPageSize());
            // $searchResult->setProductList($this->prepareItems($collection->getItems()));
            if ($collection->getSize() > 1) {
                $response['filter_data'] = $this->getFilterData($categoryId);
                // $searchResult->setFilterData($this->getFilterData($categoryId));
            } else {
                $response['filter_data'] = [];
                // $searchResult->setFilterData([]);
            }
            $response['sorting_data'] = $this->getSortingData();

            // $searchResult->setSortingData($this->getSortingData());
            $data = [
                "status"  => true,
                "message" => "Product list get successfully.",
                "data"    => $response
            ];

            return $this->response->setBody(json_encode($data))->sendResponse();
            // $settingData->setResponseData($searchResult);
            // $settings->setCode(200);
            // $settings->setMessage(__('Product list get successfully.'));
            // $settingData->setSettings($settings);
        } catch (\Exception $e) {
            $data =  [
                "status"  => false,
                "message" => $e->getMessage(),
                "data"    => []
            ];

            return $this->response->setBody(json_encode($data))->sendResponse();
            // $settings->setCode(400);
            // $settings->setMessage($e->getMessage());
            // $settingData->setSettings($settings);
        }

        return $settingData;
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    private function addExtensionAttributes(Collection $collection) : Collection
    {
        foreach ($collection->getItems() as $item) {
            $this->readExtensions->execute($item);
        }
        return $collection;
    }

    /**
     * Retrieve collection processor
     *
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                // phpstan:ignore "Class Magento\Catalog\Model\Api\SearchCriteria\ProductCollectionProcessor not found."
                \Magento\Catalog\Model\Api\SearchCriteria\ProductCollectionProcessor::class
            );
        }
        return $this->collectionProcessor;
    }

    /**
     * Saves product in the local cache by sku.
     *
     * @param Product $product
     * @param string $cacheKey
     * @return void
     */
    private function saveProductInLocalCache(Product $product, string $cacheKey): void
    {
        $preparedSku = $this->prepareSku($product->getSku());
        $this->instances[$preparedSku][$cacheKey] = $product;
    }

    /**
     * Converts SKU to lower case and trims.
     *
     * @param string $sku
     * @return string
     */
    private function prepareSku(string $sku): string
    {
        return mb_strtolower(trim($sku));
    }

    /**
     * @inheritDoc
     */
    private function joinPositionField(
        Collection $collection,
        $categoryId = null
    ): void {
        $categoryIds = [];
        if ($categoryId) {
            $categoryIds[] = $categoryId;
        }

        if (count($categoryIds) === 1) {
            $collection->joinField(
                'position',
                'catalog_category_product',
                'position',
                'product_id=entity_id',
                ['category_id' => current($categoryIds)],
                'left'
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function prepareItems($items)
    {
        $itemList = [];
        foreach ($items as $item) {

            if ($item->getTypeId() === 'simple') {
                $parentIds = $this->configurableFactory->create()->getParentIdsByChild($item->getId());
                if (!empty($parentIds)) {
                    continue;
                }
            }

            $width = $this->scopeConfig
            ->getValue(
                "homepage_api_configuration/product_list_config/image_width",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $item->getData('store_id')
            );
            $height = $this->scopeConfig
            ->getValue(
                "homepage_api_configuration/product_list_config/image_height",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $item->getData('store_id')
            );
            $products = $this->productInterfaceFactory->create();
            $storeId = $item->getData('store_id');
            $isEnable = $item->getData('is_enable');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $specialPrice = $item->getSpecialPrice();
            $finalPrice = $item->getFinalPrice();
            $isSpecialPrice = false;
            $sPrice = 0;
            //$IsInStock = 0;
            if ($specialPrice) {
                $isSpecialPrice = true;
                $sPrice = $specialPrice;
            }
            if ($finalPrice && $finalPrice < $item->getData('price')) {
                $isSpecialPrice = true;
                $sPrice = $finalPrice;
            }

            $eta = $item->getEta();
            $prodSku = $item->getSku();
            //$stockStatus = $this->checkStock($storeId, $prodSku, $eta);

            // if ($stockStatus) {
            //     $IsInStock = 1;
            // }
            $imageHelper = $objectManager->get(\Magento\Catalog\Helper\Product::class);
            $imageUrl = $imageHelper->getThumbnailUrl($item);
            $products->setData('product_id', $item->getData('entity_id'));
            $products->setData('thumbnail', $imageUrl . '?optimize=high&height=' . $width . '&width=' . $height);
            $products->setData('sku', $item->getSku());
            $products->setData('is_special_price', $item->getData('special_price') ? true : false);
            $sPrice = $this->checkPriceTax($item, $sPrice, $store = null);
            $products->setData('special_price', number_format((float)$sPrice, 2));
            $products->setData('name', $item->getData('name'));
            $productPrice = $this->checkPriceTax($item, $item->getData('price'), $store = null);
            $products->setData('price', number_format((float)$productPrice, 2));
            // $products->setData('is_in_stock', $IsInStock ? true : false);
            // $products->setData('availability', $IsInStock ? 'In Stock' : 'Out Of Stock');
            $products->setData('type_id', $item->getData('type_id'));
            //$itemList[] = $products->getData();

            if ($item->getTypeId() === 'configurable') {

                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType */
                $configurableType = $this->configurableFactory->create();

                /** Correct method: getConfigurableAttributes() */
                $attributes = $configurableType->getConfigurableAttributes($item);

                $configurableOptions = [];

                foreach ($attributes as $attribute) {
                    $values = [];
                    foreach ($attribute->getOptions() as $opt) {
                        
                        $values[] = [
                            'default_label' => $opt['default_label'],
                            'value_index' => $opt['value_index']
                        ];
                    }

                    /** Required format SAME AS YOU WANT */
                    $configurableOptions[] = [
                        'id'           => $attribute->getAttributeId(),
                        'attribute_id' => (string) $attribute->getProductAttribute()->getAttributeId(),
                        'label'        => $attribute->getProductAttribute()->getStoreLabel(),
                        'position'     => (int) $attribute->getPosition(),
                        'values'       => $values,
                        'product_id'   => $item->getId()
                    ];
                }

                $products->setData('configurable_product_options', $configurableOptions);

                $childIds = $configurableType->getChildrenIds($item->getId());

                $childList = [];
                if (isset($childIds[0])) {
                    foreach ($childIds[0] as $childId) {
                        $childList[] = (int)$childId;
                    }
                }

                $products->setData('configurable_product_links', $childList);
            }

            /** Add prepared product */
            $itemList[] = $products->getData();
        }
        return $itemList;
    }

    /**
     * @inheritDoc
     */
    public function getFilterData($categoryId)
    {
        $returnFilters = [];
        $filterableAttributes = $this->filterableAttributes;
        $filterList = $this->filterListFactory->create(['filterableAttributes' => $filterableAttributes]);
        $layer = $this->layerResolver->get();
        $layer->setCurrentCategory($categoryId);
        $layer->getProductCollection();
        $maxPrice = $layer->getProductCollection()->getMaxPrice();
        $minPrice = $layer->getProductCollection()->getMinPrice();
        $filters = $filterList->getFilters($layer);
        foreach ($filters as $filter) {
            $values = [];
            foreach ($filter->getItems() as $item) {
                $values[] = [
                    "display" => strip_tags($item->getLabel()),
                    "value" => $item->getValue(),
                    "count" => $item->getCount(),
                ];
            }
            if (!empty($values)) {
                $returnFilters[] = [
                    "attr_code" => $filter->getRequestVar(),
                    "attr_label" => $filter->getName(),
                    "values" => $values,
                ];
            }
        }

        $prodMinVal = $prodMaxVal = [];
        $filterList = [];
        
        foreach ($returnFilters as $filters) {
            $filterData = $this->filterDataInterfaceFactory->create();
            $filterData->setCode($filters['attr_code'] == 'cat' ? 'category' : $filters['attr_code']);
            $filterData->setLabel($filters['attr_label']);
            $optionList = [];
            if ($filters['attr_code'] == 'price') {
                $filterData->setType('minmax_range');
                $filterData->setOptions([]);
                $prodMinVal = $layer->getProductCollection()->getColumnValues('final_price');
                $prodMaxVal = $layer->getProductCollection()->getColumnValues('final_price');

                $minPrice = $prodMinVal ? min($prodMinVal) : [];
                $maxPrice = $prodMaxVal ? max($prodMaxVal) : [];
                $filterData->setMinRange(number_format((float)$minPrice, 2));
                $filterData->setMaxRange(number_format((float)$maxPrice, 2));

                $filterList[] = $filterData;
            } else {
                $filterData->setType('option');
                foreach ($filters['values'] as $filterValue) {
                    $optionData = $this->optionInterfaceFactory->create();
                    $optionData->setId($filterValue['value']);
                    $optionData->setLabel(htmlspecialchars_decode($filterValue['display']));
                    $optionList[] = $optionData;
                }
                $filterData->setMinRange('');
                $filterData->setMaxRange('');
                $filterData->setOptions($optionList);
                $filterList[] = $filterData;
            }
        }

        return $filterList;
    }

    /**
     * @inheritDoc
     */
    public function getSortingData()
    {
        $priceHighLow = $this->sortingDataInterfaceFactory->create();
        $priceHighLow->setCode('price_high_low');
        $priceHighLow->setLabel('Price: High - Low');
        $sortList[0] = $priceHighLow;
        $priceLowHigh = $this->sortingDataInterfaceFactory->create();
        $priceLowHigh->setCode('price_low_high');
        $priceLowHigh->setLabel('Price: Low - High');
        $sortList[1] = $priceLowHigh;
        $productAz = $this->sortingDataInterfaceFactory->create();
        $productAz->setCode('product_a_z');
        $productAz->setLabel('Product A to Z');
        $sortList[2] = $productAz;
        $priceZa = $this->sortingDataInterfaceFactory->create();
        $priceZa->setCode('product_z_a');
        $priceZa->setLabel('Product Z to A');
        $sortList[3] = $priceZa;
        return $sortList;
    }

    /**
     * Check stock status
     * @param   $storeId
     * @param   $sku
     * @param   $eta
     * @return  bool
     */
    // public function checkStock($storeId, $sku, $eta)
    // {
    //     $sourceCode = $this->scopeConfig->getValue("erpconfig/erp_catalog/source_code", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

    //     $tablename = $this->resourceConnection->getTableName('inventory_source_item');
    //     $query = $this->resourceConnection->select()
    //         ->from(
    //             ['main' => $tablename],
    //             ['quantity', 'sku', 'source_code']
    //         )->where("main.sku = (?)", $sku)
    //         ->where("main.source_code = (?)", $sourceCode);

    //     $fetchData = $this->resourceConnection->fetchAll($query);

    //     foreach ($fetchData as $record) {
    //         $prodQty = $record['quantity'];

    //         if (($prodQty > 0 && !empty($prodQty)) || $eta) {
    //             return true;
    //         }
    //         return false;
    //     }
    //     return false;
    // }

    public function getPriceDisplayType($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    public function checkPriceTax($product, $priceValue, $store = null){
        $getPriceType = $this->getPriceDisplayType($store);
        if($getPriceType == 2 || $getPriceType == 3){
            return $this->catalogHelper->getTaxPrice($product, $priceValue, true, null, null, $store);
        } else {
            return $priceValue;
        }
    }

    public function getPriceIncludingTax($product, $price, $store = null)
    {
        $getPriceType = $this->getPriceDisplayType($store);
        if($getPriceType == 2 || $getPriceType == 3){
            $taxClassId = $product->getTaxClassId();
            $request = $this->taxCalculation->getRateRequest(null, null, null, $product->getStore());
            $rate = $this->taxCalculation->getRate($request->setProductClassId($taxClassId));
            return $price + ($price * $rate / 100);
        } else {
            return $price;
        }
    }

}
