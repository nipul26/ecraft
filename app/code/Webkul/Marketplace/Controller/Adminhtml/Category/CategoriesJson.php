<?php
/**
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Adminhtml\Category;

use Magento\Customer\Controller\RegistryConstants;

class CategoriesJson extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
    }
    /**
     * Ajax categories tree loader action
     *
     * @return string
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $categoryId = $this->getRequest()->getParam('id', null);
        $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        $category->setStoreId($this->_storeManager->getStore()->getId());
      
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setJsonData(
            $this->layoutFactory->create()
            ->createBlock(\Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Categories::class)
                ->getTreeJson($category)
        );
    }
    /**
     * Customer initialization.
     *
     * @return string customer id
     */
    protected function initCurrentCustomer()
    {
        $customerId = (int)$this->getRequest()->getParam('customer_id');

        if ($customerId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        return $customerId;
    }
}
