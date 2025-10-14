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

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Webkul\Marketplace\Model\AdminNewsFactory
     */
    protected $adminNewsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

   /**
    * Construct
    *
    * @param \Magento\Backend\App\Action\Context $context
    * @param \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory
    * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
    * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->adminNewsFactory = $adminNewsFactory;
        $this->_date = $date;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');
            
            $model = $this->adminNewsFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This news no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setContent($data['content']);
            $model->setStatus($data['status']);
            if (!$model->getId()) {
                $model->setCreatedAt($this->_date->gmtDate());
            }

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the news.'));
                $this->dataPersistor->clear('adminnews');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e->getMessage());
            }

            $this->dataPersistor->set('adminnews', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
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
