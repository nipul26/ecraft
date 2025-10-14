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

use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Api\DraftProductRepositoryInterface;

/**
 * Webkul Marketplace Product Attribute Delete Controller.
 */
class DeleteDraftProduct extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var HelperData
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var DraftProductRepositoryInterface
     */
    protected $draftProductRepository;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param DraftProductRepositoryInterface $draftProductRepository
     * @param HelperData $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        DraftProductRepositoryInterface $draftProductRepository,
        HelperData $helper
    ) {
        parent::__construct($context);
        $this->messageManager= $messageManager;
        $this->draftProductRepository = $draftProductRepository;
        $this->helper = $helper;
    }

    /**
     * Create attribute pageFactory
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $isPartner = $this->helper->isSeller();
            $draftId = $this->getRequest()->getParam('draft_id');
            if ($isPartner == 1) {
                $this->draftProductRepository->deleteById($draftId);
                $this->messageManager->addSuccess(__('Draft Product has been successfully deleted'));
            } else {
                $this->messageManager->addWarning(__('You are not authorized to perform this action'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath(
            'marketplace/product/draftproduct',
            ['_secure' => $this->getRequest()->isSecure()]
        );
    }
}
