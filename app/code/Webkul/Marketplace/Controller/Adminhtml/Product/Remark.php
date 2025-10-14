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
namespace Webkul\Marketplace\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Webkul\Marketplace\Model\ProductFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Helper\Notification as NotificationHelper;
use Magento\Catalog\Model\CategoryFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;

/**
 * Class Deny used to deny the product.
 */
class Remark extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var ProductFactory
     */
    protected $productModel;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModel;
    /**
     * @var \Webkul\Marketplace\Model\ProductRemark
     */
    protected $productRemark;

    /**
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param ProductFactory $productModel
     * @param MpHelper $mpHelper
     * @param NotificationHelper $notificationHelper
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerModel
     * @param MpEmailHelper $mpEmailHelper
     * @param \Webkul\Marketplace\Model\ProductRemarkFactory $productRemark
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        ProductFactory $productModel,
        MpHelper $mpHelper,
        NotificationHelper $notificationHelper,
        CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        MpEmailHelper $mpEmailHelper,
        \Webkul\Marketplace\Model\ProductRemarkFactory $productRemark
    ) {
        parent::__construct($context);
        $this->_date = $date;
        $this->dateTime = $dateTime;
        $this->productModel = $productModel;
        $this->mpHelper = $mpHelper;
        $this->notificationHelper = $notificationHelper;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->customerModel = $customerModel;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->productRemark = $productRemark;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $collection = $this->productModel->create()
        ->getCollection()
        ->addFieldToFilter('mageproduct_id', $data['mageproduct_id'])
        ->addFieldToFilter('seller_id', $data['seller_id']);
        if ($collection->getSize()) {
            $sellerProduct = $this->productModel->create()->getCollection();
            $productRemark = $this->productRemark->create();
            $coditionData = "`mageproduct_id`=".$data['mageproduct_id'];
            $sellerProduct->setProductData(
                $coditionData,
                [
                    'seller_pending_notification' => 1
                ]
            );

            $catagoryModel = $this->categoryFactory->create();
            $helper = $this->mpHelper;
            $id = 0;

            foreach ($collection as $item) {
                $id = $item->getId();
                $this->notificationHelper->saveNotification(
                    \Webkul\Marketplace\Model\Notification::TYPE_PRODUCT,
                    $id,
                    $data['mageproduct_id']
                );
            }

            $model = $this->productFactory->create()->load($data['mageproduct_id']);

            $catarray = $model->getCategoryIds();
            $categoryname = '';
            foreach ($catarray as $keycat) {
                $categoriesy = $catagoryModel->load($keycat);
                if ($categoryname == '') {
                    $categoryname = $categoriesy->getName();
                } else {
                    $categoryname = $categoryname.','.$categoriesy->getName();
                }
            }

            $pro = $this->productModel->create()->load($id);
            $seller = $this->customerModel->create()->load($data['seller_id']);
            $productRemark->setMageproductId($data['mageproduct_id'])
            ->setRemark($data['product_deny_reason'])
            ->setSellerId($data['seller_id'])
            ->save();
            if (isset($data['notify_seller']) && $data['notify_seller'] == 1) {
                $helper = $this->mpHelper;
                $adminStoreEmail = $helper->getAdminEmailId();
                $adminEmail = $adminStoreEmail ? $adminStoreEmail : $helper->getDefaultTransEmailId();
                $adminUsername = $helper->getAdminName();
                $emailTempVariables['sellerName'] = $seller->getName();
                $emailTempVariables['remark'] = $data['product_deny_reason'];
                $emailTempVariables['productName'] = $model->getName();
                $emailTempVariables['categoryName'] = $categoryname;
                $emailTempVariables['description'] = $model->getDescription();
                $emailTempVariables['price'] = $model->getPrice();
                $senderInfo = [
                      'name' => $adminUsername,
                      'email' => $adminEmail,
                  ];
                $receiverInfo = [
                  'name' => $seller->getName(),
                  'email' => $seller->getEmail(),
                ];
                $this->mpEmailHelper->sendProductRemarkMail(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo
                );
            }
            $this->_eventManager->dispatch(
                'mp_remark_product',
                ['product' => $pro, 'seller' => $seller]
            );

            $this->messageManager->addSuccess(__('Product has been Remarked.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::product');
    }
}
