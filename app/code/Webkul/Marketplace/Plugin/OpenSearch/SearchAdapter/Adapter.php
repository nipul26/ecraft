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
namespace Webkul\Marketplace\Plugin\OpenSearch\SearchAdapter;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Framework\Search\RequestInterface;
use Magento\Elasticsearch\SearchAdapter\Aggregation\Builder as AggregationBuilder;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch\SearchAdapter\ResponseFactory;
use Magento\Elasticsearch\SearchAdapter\QueryContainerFactory;
use Magento\OpenSearch\SearchAdapter\Mapper;

class Adapter
{

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var QueryContainerFactory
     */
    private $queryContainerFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Product\Collection
     */
    protected $collection;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var array
     */
    private static $emptyRawResponse = [
        "hits" =>
            [
                "hits" => []
            ],
        "aggregations" =>
            [
                "price_bucket" => [],
                "category_bucket" =>
                    [
                        "buckets" => []

                    ]
            ]
    ];

    /**
     * __construct
     *
     * @param ConnectionManager $connectionManager
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     * @param Mapper $mapper
     * @param QueryContainerFactory $queryContainerFactory
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Webkul\Marketplace\Model\ResourceModel\Product\Collection $collection
     */
    public function __construct(
        ConnectionManager $connectionManager,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        Mapper $mapper,
        QueryContainerFactory $queryContainerFactory,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\Marketplace\Model\ResourceModel\Product\Collection $collection
    ) {
        $this->connectionManager = $connectionManager;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->mapper = $mapper;
        $this->queryContainerFactory = $queryContainerFactory;
        $this->helper = $helper;
        $this->request = $request;
        $this->collection = $collection;
    }
    
    /**
     * Add seller product to seller collection
     *
     * @param \Magento\Framework\Search\AdapterInterface $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return \Magento\Elasticsearch\SearchAdapter\Response
     */
    public function aroundQuery(
        \Magento\OpenSearch\SearchAdapter\Adapter $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        if ($this->request->getFullActionName() == 'marketplace_seller_collection' ||
        $this->request->getFullActionName() == 'marketplace_seller_profile') {
            $sellerId = $this->getProfileDetail()->getSellerId();
            $client = $this->connectionManager->getConnection();
            $aggregationBuilder = $this->aggregationBuilder;
            $updatedQuery = $this->mapper->buildQuery($request);
            $productIds = $this->helper->getSellerProducts($sellerId);
            $updatedQuery['body']['query']['bool']['filter'] = ['ids' => ['values' => $productIds]];
            $aggregationBuilder->setQuery($this->queryContainerFactory->create(['query' => $updatedQuery]));
            try {
                $rawResponse = $client->query($updatedQuery);
            } catch (\Exception $e) {
                $this->helper->logDataInLogger("Opensearch_SearchAdapter_Adapter aroundQuery ".$e->getMessage());
                // return empty search result in case an exception is thrown from Elasticsearch
                $rawResponse = self::$emptyRawResponse;
            }
            $rawDocuments = $rawResponse['hits']['hits'] ?? [];
            $queryResponse = $this->responseFactory->create(
                [
                    'documents' => $rawDocuments,
                    'aggregations' => $aggregationBuilder->build($request, $rawResponse),
                    'total' => $rawResponse['hits']['total']['value'] ?? 0
                ]
            );
            return $queryResponse;
        }
        return $proceed($request);
    }

    /**
     * Get Seller Profile Details
     *
     * @return \Webkul\Marketplace\Model\Seller | bool
     */
    public function getProfileDetail()
    {
        return $this->helper->getProfileDetail(MpHelper::URL_TYPE_COLLECTION);
    }
}
