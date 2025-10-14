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

namespace Webkul\Marketplace\Controller\Product\Attribute;

use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\Product;

/**
 * Webkul Marketplace Product Attribute NewAction Controller.
 */
class NewAction extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var HelperData
     */
    protected $helper;
    /**
     * @var \Webkul\Marketplace\Model\VendorAttributeMappingFactory
     */
    protected $vendorAttributeFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param HelperData $helper
     * @param \Webkul\Marketplace\Model\VendorAttributeMappingFactory $vendorAttributeFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        HelperData $helper,
        \Webkul\Marketplace\Model\VendorAttributeMappingFactory $vendorAttributeFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->vendorAttributeFactory = $vendorAttributeFactory;
    }

    /**
     * Create attribute pageFactory
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        $allowedTypes = explode(',', $helper->getAllowedProductType());
        if ($isPartner == 1 && in_array(Product::PRODUCT_TYPE_CONFIGURABLE, $allowedTypes)) {
            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $sellerId = $this->helper->getCustomerId();
                $attributeCollection = $this->vendorAttributeFactory->create()->getCollection()
                ->addFieldToFilter('attribute_id', $attributeId)->addFieldToFilter('seller_id', $sellerId);
                if (!$attributeCollection->getSize() > 0) {
                    $this->messageManager->addError(
                        __('You are not authorised to edit this attribute.')
                    );
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/new',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            }
            $resultPage = $this->_resultPageFactory->create();
            if ($helper->getIsSeparatePanel()) {
                $resultPage->addHandle('marketplace_layout2_product_attribute_new');
            }
            $resultPage->getConfig()->getTitle()->set(__('Create Attribute'));
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
