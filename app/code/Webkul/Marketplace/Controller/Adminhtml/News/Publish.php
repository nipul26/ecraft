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

use Webkul\Marketplace\Model\AdminNews;

class Publish extends \Magento\Backend\App\Action
{
    /**
     * @var AdminNews
     */
    protected $adminNewsFactory;
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher
    ) {
        $this->adminNewsFactory = $adminNewsFactory;
        $this->publisher = $publisher;
        parent::__construct($context);
    }

    /**
     * Publish news
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $this->getRequest()->getParam('entityId');
            $model = $this->adminNewsFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This news no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if (!$model->getStatus()) {
                $this->messageManager->addWarningMessage(__('Please enable news first to publish.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setIsPublish(AdminNews::IS_PUBISH_YES)->save();
            $this->publisher->publish(
                \Webkul\Marketplace\Model\Consumer\AdminNewsPublishedConsumer::QUEUE_NAME,
                $id
            );
            $this->messageManager->addSuccessMessage(__('The news has been published'));
        }
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
