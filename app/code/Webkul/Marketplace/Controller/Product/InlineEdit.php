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

namespace Webkul\Marketplace\Controller\Product;

class InlineEdit extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;
    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $productPriceIndexerProcessor;
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Eav\Processor
     */
    protected $eavProcessor;
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Eav\Processor $eavProcessor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Eav\Processor $eavProcessor
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->productRepository = $productRepository;
        $this->_stockRegistry = $stockRegistry;
        $this->mpHelper = $mpHelper;
        $this->productAction = $productAction;
        $this->_storeManager = $storeManager;
        $this->mpProductFactory = $mpProductFactory;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->eavProcessor = $eavProcessor;
    }

    /**
     * Save grid inline changes
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        $storeId = $this->mpHelper->getCurrentStoreId();
        $productIds = [];
        foreach ($postItems as $item) {
            try {
                if ($item['entity_id']) {
                    $productIds[] = $item['entity_id'];
                    $product = $this->productRepository->getById($item['entity_id']);
                    $stockItem = $this->_stockRegistry->getStockItem($item['entity_id']);
                    $stockItem->setData('qty', $item['qty']);
                    $allStores = $this->_storeManager->getStores();
                    $product->setPrice($item['price']);
                    $product->setVisibility($item['visibility']);
                    $product->save();
                    if (isset($item['product_status'])) {
                        foreach ($allStores as $id) {
                            $this->productAction->updateAttributes(
                                [$item['entity_id']],
                                ['status' => $item['product_status']],
                                $id
                            );
                        }
                    }
                    $this->productAction
                    ->updateAttributes([$item['entity_id']], ['visibility' => $item['visibility']], $storeId);
                }
            } catch (\Exception $e) {
                $messages[] = "[Error:]  {$e->getMessage()}";
                $error = true;
            }
        }
        $helper = $this->mpHelper;
        if ($helper->getIsProductEditApproval()) {
            $allStores = $this->_storeManager->getStores();
            $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
            $sellerProduct = $this->mpProductFactory->create()->getCollection();
            $coditionArr = [];
            foreach ($productIds as $id) {
                $condition = "`mageproduct_id`=".$id;
                array_push($coditionArr, $condition);
            }
            $coditionData = implode(' OR ', $coditionArr);
            $details = ['status' => $status, 'seller_pending_notification' => 1];
            $sellerProduct->setProductData($coditionData, $details);
            foreach ($allStores as $store) {
                $this->productAction->updateAttributes($productIds, ['status' => $status], $store->getId());
            }
            $this->productAction->updateAttributes($productIds, ['status' => $status], 0);
            $this->productPriceIndexerProcessor->reindexList($productIds);
            $this->eavProcessor->reindexList($productIds);
            $helper->reIndexData();
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
