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
namespace Webkul\Marketplace\Controller\Adminhtml\News;

use Webkul\Marketplace\Model\AdminNewsFactory;

class Edit extends \Webkul\Marketplace\Controller\Adminhtml\Sellerflag
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AdminNewsFactory
     */
    protected $adminNewsFactory;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param AdminNewsFactory $adminNewsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        AdminNewsFactory $adminNewsFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->adminNewsFactory = $adminNewsFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id', 0);
        $model = $this->adminNewsFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This news no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('news', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit News') : __('New News'),
            $id ? __('Edit News') : __('New News')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('News'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New News'));
        return $resultPage;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Webkul_Marketplace::managenews');
        return $resultPage;
    }
    /**
     * Check for is allowed.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::managenews');
    }
}
