<?php

namespace CodezSparkMobile\ProductDetailApi\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;

class ProductDetailResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /** 
     * @var Request 
     */
    protected $request;

    /** 
     * @var State 
     */
    protected $appState;

    /** 
     * @var MarketplaceHelper 
     */
    protected $marketplaceHelper;

    public function __construct(
        Response $response,
        Request $request,
        AppState $appState,
        LoggerInterface $logger,
        DataObjectProcessor $dataObjectProcessor,
        MarketplaceHelper $marketplaceHelper
    ) {
        $this->response = $response;
        $this->request = $request;
        $this->appState  = $appState;
        $this->logger = $logger;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->marketplaceHelper = $marketplaceHelper;
    }

    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $result
    ) {
        try {
            $method = $this->request->getHttpMethod();

            if ($method !== 'GET') {
                return $result;
            }

            $pathInfo = $this->request->getPathInfo();
            if (!preg_match('#^/V1/products/[\w\-\.]+$#', $pathInfo)) {
                return $result;
            }

            if ($result) {
                // Use the DataObjectProcessor to convert the product detail object into an array
                $dataArray = $this->dataObjectProcessor->buildOutputDataArray(
                    $result,
                    ProductInterface::class
                );

                /** Fetch seller ID using Webkul helper */
                $sellerId = $this->marketplaceHelper->getSellerIdByProductId($result->getId());
                $sellerProductCount = $this->marketplaceHelper->getSellerInfo($sellerId);
                $collection = $this->marketplaceHelper->getSellerCollectionObj($sellerId);
                $seller = $collection->getFirstItem();

                $sellerData = [
                    'seller_id'   => $seller->getSellerId() ?? '',
                    'shop_title'  => $seller->getShopTitle() ?? ''
                    // 'shop_url'    => $seller->getShopUrl() ?? '',
                    // 'shop_logo'   => $seller->getLogoPic() ?? '',
                    // 'product_count' => $sellerProductCount['product_count']
                ];
   
                $response = [
                    'status' => true,
                    'message' => 'Product Details fetched successfully.',
                    'response' => array_merge($dataArray, ['seller' => $sellerData]) 
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Unable to retrieve product details',
                    'response' => null
                ];
            }
        } catch (LocalizedException $e) {
             $this->logger->error('Product Details Response Plugin : ' . $e->getMessage());
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}
