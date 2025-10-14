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

namespace Webkul\Marketplace\Controller\Adminhtml\Productflag;

use Webkul\Marketplace\Api\Data\ProductFlagReasonInterfaceFactory;

class Delete extends \Webkul\Marketplace\Controller\Adminhtml\Productflag
{

    /**
     * @var ProductFlagReasonInterfaceFactory
     */
    protected $productFlagFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param ProductFlagReasonInterfaceFactory $productFlagFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ProductFlagReasonInterfaceFactory $productFlagFactory
    ) {
        $this->productFlagFactory = $productFlagFactory;
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
                $model = $this->productFlagFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Product flag reason.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Product flag reason to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
