<?php
namespace CodezSparkMobile\MobileHomeApi\Model;

use CodezSparkMobile\MobileHomeApi\Api\HomeDataInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Exception\LocalizedException;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Framework\App\ResourceConnection;

class HomeData implements HomeDataInterface
{
    protected $productCollectionFactory;
    protected $blockRepository;
    protected $storeManager;
    protected $urlBuilder;
    protected $response;
    protected $pageRepository;
    protected $_priceCurrency;
    protected $sliderCollectionFactory;
    protected $bannerCollectionFactory;
    protected $resource;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        BlockRepositoryInterface $blockRepository,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Response $response,
        PageRepositoryInterface $pageRepository,
        PriceCurrencyInterface $priceCurrency,
        SliderCollectionFactory $sliderCollectionFactory,
        BannerCollectionFactory $bannerCollectionFactory,
        ResourceConnection $resource
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->blockRepository = $blockRepository;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->response = $response;
        $this->pageRepository = $pageRepository;
        $this->_priceCurrency = $priceCurrency;
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->resource = $resource;
    }

    /**
     * Main method: Get home page data
     */
    public function getHomeData()
    {
        try {
            $mediaBase = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);

            $responseData = [
                'status' => true,
                'message' => 'Home page data fetched successfully',
                'response' => [
                    'home_page_html' => $this->getCmsPageContent('mobile-home-page'),
                    'popular_products' => $this->getProductsByAttribute('is_popular', 1, 8, $mediaBase),
                    'newest_products' => $this->getProductsByAttribute('is_newest', 1, 8, $mediaBase),
                    'suggested_for_you' => $this->getProductsByAttribute('is_suggested', 1, 8, $mediaBase),
                    'home_page_banners' => $this->getSliderWithBanners()
                ]
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();

        } catch (LocalizedException $e) {
            $errorResponse = [
                'status' => false,
                'message' => 'Failed to fetch home data: ' . $e->getMessage(),
                'response' => []
            ];
            return $this->response->setBody(json_encode($errorResponse))->sendResponse();
        }
    }

    /**
     * Get newest products
     */
    private function getNewestProducts($limit, $mediaBase)
    {
        $collection = $this->initProductCollection($limit);
        $collection->setOrder('created_at', 'DESC');
        return $this->formatProductCollection($collection, $mediaBase);
    }

    /**
     * Get products by custom attribute
     */
    private function getProductsByAttribute($attributeCode, $value, $limit, $mediaBase)
    {
        $collection = $this->initProductCollection($limit);
        $collection->addAttributeToFilter($attributeCode, $value)
                   ->setOrder('entity_id', 'DESC');
        return $this->formatProductCollection($collection, $mediaBase);
    }

    /**
     * Initialize product collection with common filters
     */
    private function initProductCollection($limit)
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name', 'price', 'small_image'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->setPageSize($limit)
            ->setCurPage(1);
    }

    /**
     * Format collection into API-friendly array
     */
    private function formatProductCollection($collection, $mediaBase)
    {
        $products = [];
        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();

        foreach ($collection as $product) {
            $price = (int) $product->getPrice();
            $precision = 2;
            $formattedPrice = $this->_priceCurrency->format(
                $price,
                $includeContainer = false,
                $precision,
                $scope = null,
                $currency
            );

            $products[] = [
                //'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $formattedPrice,
                'image_url' => $mediaBase . 'catalog/product' . $product->getSmallImage()
            ];
        }

        return $products;
    }

    private function getCmsPageContent($identifier)
    {
        try {
            $page = $this->pageRepository->getById($identifier);
            return $page->getContent(); // Includes rendered block directives
        } catch (\Exception $e) {
            return '';
        }
    }


    /**
     * Get all active sliders with their mobile-active banners
     *
     * @return array
     */
    public function getSliderWithBanners()
    {
        $connection = $this->resource->getConnection();
        $bannerSliderTable = $connection->getTableName('mageplaza_bannerslider_banner_slider');

        $sliderCollection = $this->sliderCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToSelect(['slider_id', 'name']);

        $homePageBanners = [];
        foreach ($sliderCollection as $slider) {
            $sliderData = $slider->getData();

            $select = $connection->select()
                ->from($bannerSliderTable, ['banner_id'])
                ->where('slider_id = ?', $slider->getId())
                ->order('position ASC');

            $bannerIds = $connection->fetchCol($select);

            $banners = [];
            if (!empty($bannerIds)) {
                $bannerCollection = $this->bannerCollectionFactory->create()
                    ->addFieldToFilter('banner_id', ['in' => $bannerIds])
                    ->addFieldToFilter('is_for_mobile', 1)
                    ->addFieldToFilter('status', 1)
                    ->addFieldToSelect(['banner_id', 'name', 'image', 'entity_id', 'carousel_image_type']);

                foreach ($bannerCollection as $banner) {
                    $data = $banner->getData();
                    $carouselType = $banner->getCarouselImageType();
                    $data['is_clickable'] = ($carouselType === 'none') ? 0 : 1;

                    $banners[] = $data;
                }
            }

            $sliderData['banners'] = $banners;
            $homePageBanners['sliders'][] = $sliderData;
        }

        return $homePageBanners;
    }
}
