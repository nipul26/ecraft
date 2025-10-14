<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Adminhtml\News;

use Webkul\Marketplace\Api\Data\AdminNewsInterfaceFactory;

class Delete extends \Webkul\Marketplace\Controller\Adminhtml\Sellerflag
{

    /**
     * @var AdminNewsInterfaceFactory
     */
    protected $adminNewsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param AdminNewsInterfaceFactory $adminNewsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        AdminNewsInterfaceFactory $adminNewsFactory
    ) {
        $this->adminNewsFactory = $adminNewsFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->adminNewsFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the news.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find an news to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
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
