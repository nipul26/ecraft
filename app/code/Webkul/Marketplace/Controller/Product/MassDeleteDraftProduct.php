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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Marketplace\Model\ResourceModel\DraftProduct\CollectionFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

/**
 * Class MassDelete
 */
class MassDeleteDraftProduct extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var FormKeyValidator
     */
    protected $_formKeyValidator;

     /**
      * @var FormKey
      */
    protected $formKey;

    /**
     * @var request
     */
    protected $request;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FormKeyValidator $formKeyValidator
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_formKeyValidator = $formKeyValidator;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $block) {
            $block->delete();
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/draftproduct');
    }
}
