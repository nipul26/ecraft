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

namespace Webkul\Marketplace\Controller\Product\Ui;

use Magento\Framework\App\Action\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as SellerProduct;
use Webkul\Marketplace\Helper\Data as HelperData;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Url as CustomerUrl;

/**
 * Webkul Marketplace Product MassDelete controller.
 */
class MassStatus extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var SellerProduct
     */
    protected $_sellerProductCollectionFactory;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * Construct
     *
     * @param Context $context
     * @param Filter $filter
     * @param Session $customerSession
     * @param Registry $coreRegistry
     * @param CollectionFactory $productCollectionFactory
     * @param SellerProduct $sellerProductCollectionFactory
     * @param HelperData $helper
     * @param ProductRepositoryInterface|null $productRepository
     * @param CustomerUrl $customerUrl
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Session $customerSession,
        Registry $coreRegistry,
        CollectionFactory $productCollectionFactory,
        SellerProduct $sellerProductCollectionFactory,
        HelperData $helper,
        ProductRepositoryInterface $productRepository,
        CustomerUrl $customerUrl
    ) {
        $this->filter = $filter;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $coreRegistry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_sellerProductCollectionFactory = $sellerProductCollectionFactory;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context
        );
        $this->customerUrl = $customerUrl;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Mass delete seller products action.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $sellerId = $this->helper->getCustomer()->getId();
        $status = $this->getRequest()->getParam("status");
        $ids = $this->getRequest()->getParam("selected");
        if ($this->helper->getIsProductApproval() || $this->helper->getIsProductEditApproval()) {
            $this->messageManager->addWarning(
                __('Status could not be updated as product approval is required.')
            );
            return $this->resultRedirectFactory->create()->setPath(
                $this->_redirect->getRefererUrl()
            );
        }
        if (!$sellerId || count($ids) <= 0) {
            return $this->resultRedirectFactory->create()->setPath(
                $this->_redirect->getRefererUrl()
            );
        }
        $sellerColl = $this->helper->getSellerCollectionObj($sellerId);
        if (!$sellerColl->getSize()) {
            return $this->resultRedirectFactory->create()->setPath(
                $this->_redirect->getRefererUrl()
            );
        }
        $isPartner = $sellerColl->getFirstItem()->getIsSeller();
        if ($isPartner == 1) {
            try {
                $registry = $this->_coreRegistry;
                if (!$registry->registry('mp_flat_catalog_flag')) {
                    $registry->register('mp_flat_catalog_flag', 1);
                }
                $this->_coreRegistry->register('isSecureArea', 1);
                $updatedIdsArr = [];
                $sellerProducts = $this->_sellerProductCollectionFactory
                ->create()
                ->addFieldToFilter(
                    'mageproduct_id',
                    ['in' => $ids]
                )->addFieldToFilter(
                    'seller_id',
                    $sellerId
                );
                foreach ($sellerProducts as $sellerProduct) {
                    array_push($updatedIdsArr, $sellerProduct['mageproduct_id']);
                }
                foreach ($updatedIdsArr as $id) {
                    try {
                        $product = $this->productRepository->getById($id);
                        $product->setStatus($status);
                        $product->setStoreId(0);
                        $this->productRepository->save($product);
                    } catch (\Exception $e) {
                        $this->helper->logDataInLogger(
                            "Controller_Product_Ui_MassUpdate execute : ".$e->getMessage()
                        );
                        $this->messageManager->addError($e->getMessage());
                    }
                }

                $unauthIds = array_diff($ids, $updatedIdsArr);
                $this->_coreRegistry->unregister('isSecureArea');
                if (!count($unauthIds)) {
                    // clear cache
                    $this->helper->clearCache();
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been updated.', count($updatedIdsArr))
                    );
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger(
                    "Controller_Product_Ui_MassDelete execute : ".$e->getMessage()
                );
                $this->messageManager->addError($e->getMessage());
            }
            return $this->resultRedirectFactory->create()->setPath(
                $this->_redirect->getRefererUrl()
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                $this->_redirect->getRefererUrl()
            );
        }
    }
}
