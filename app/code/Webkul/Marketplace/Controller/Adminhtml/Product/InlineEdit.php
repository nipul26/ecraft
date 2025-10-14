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

namespace Webkul\Marketplace\Controller\Adminhtml\Product;

class InlineEdit extends \Magento\Backend\App\Action
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
     * Contructor function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Catalog\Model\Product\Action $productAction
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Model\Product\Action $productAction
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->productRepository = $productRepository;
        $this->_stockRegistry = $stockRegistry;
        $this->_storeManager = $storeManager;
        $this->productAction = $productAction;
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
         
        foreach ($postItems as $item) {
            try {
                if ($item['mageproduct_id']) {
                    $product = $this->productRepository->getById($item['mageproduct_id']);
                    $stockItem = $this->_stockRegistry->getStockItem($item['mageproduct_id']);
                    $stockItem->setData('qty', $item['qty']);
                    
                    if (isset($item['product_price'])) {
                        $product->setPrice($item['product_price']);
                    }
                    $allStores = $this->_storeManager->getStores();
                    if (isset($item['product_status'])) {
                        foreach ($allStores as $id) {
                            $this->productAction->updateAttributes(
                                [$item['mageproduct_id']],
                                ['status' => $item['product_status']],
                                $id
                            );
                        }
                    }
                    $product->save();
                }
            } catch (\Exception $e) {
                $messages[] = "[Error:]  {$e->getMessage()}";
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
