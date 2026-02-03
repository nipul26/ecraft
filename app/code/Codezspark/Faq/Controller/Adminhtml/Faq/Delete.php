<?php
/**
 * CodezSpark
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the codezspark.com license that is
 * available through the world-wide-web at this URL:
 * https://codezspark.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    CodezSpark
 * @package     Codezspark_Faq
 * @copyright   Copyright (c) CodezSpark (https://codezspark.com/)
 * @license     https://codezspark.com/end-user-license-agreement
 */

namespace Codezspark\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Codezspark\Faq\Model\FaqRepository;

class Delete extends Faq
{
    /**
     * @var FaqRepository
     */
    protected $faqRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param FaqRepository $faqRepository
     */
    public function __construct(
        Context $context,
        FaqRepository $faqRepository
    ) {
        $this->faqRepository = $faqRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('faq_id');
        if ($id) {
            try {
                $this->faqRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the Faq.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['faq_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Faq to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
