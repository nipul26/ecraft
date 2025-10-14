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
namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Webkul\Marketplace\Model\Product as ProductStatus;
use Webkul\Marketplace\Model\SaleperpartnerFactory as MpSalesPartner;
use Webkul\Marketplace\Model\SellerFactory as MpSeller;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Magento\Catalog\Model\ProductFactory as ProductModel;
use Magento\Framework\Filesystem\Io\File as FilesystemIo;

/**
 * Webkul Marketplace AdminhtmlCustomerSaveAfterObserver Observer.
 */
class AdminhtmlCustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * Media Storage File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Magento Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Product\Collection
     */
    protected $_sellerProduct;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * @var MpSalesPartner
     */
    protected $mpSalesPartner;

    /**
     * @var MpSeller
     */
    protected $mpSeller;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ProductModel
     */
    protected $productModel;

    /**
     * @var FilesystemIo
     */
    protected $_filesystemFile;
    /**
     * @var Filesystem
     */
    protected $_mediaDirectory;

    /**
     * Construct
     *
     * @param Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param CollectionFactory $collectionFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param ProductCollection $sellerProduct
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param MpSalesPartner $mpSalesPartner
     * @param MpSeller $mpSeller
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param MpHelper $mpHelper
     * @param MpEmailHelper $mpEmailHelper
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param ProductModel $productModel
     * @param FilesystemIo|null $filesystemFile
     */
    public function __construct(
        Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        CollectionFactory $collectionFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        ProductCollection $sellerProduct,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        MpSalesPartner $mpSalesPartner,
        MpSeller $mpSeller,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        MpHelper $mpHelper,
        MpEmailHelper $mpEmailHelper,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Magento\Framework\Module\Dir\Reader $reader,
        ProductModel $productModel,
        FilesystemIo $filesystemFile
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_productRepository = $productRepository;
        $this->_messageManager = $messageManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->_date = $date;
        $this->_sellerProduct = $sellerProduct;
        $this->_jsonDecoder = $jsonDecoder;
        $this->mpSalesPartner = $mpSalesPartner;
        $this->mpSeller = $mpSeller;
        $this->customerFactory = $customerFactory;
        $this->mpHelper = $mpHelper;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->mpProductFactory = $mpProductFactory;
        $this->reader = $reader;
        $this->filesystem = $filesystem;
        $this->productModel = $productModel;
        $this->_filesystemFile = $filesystemFile;
    }

    /**
     * Admin customer save after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->moveDirToMediaDir();
        $autoid = 0;
        $customer = $observer->getCustomer();
        $customerid = $customer->getId();
        $postData = $observer->getRequest()->getPostValue();
        $isAllStore = false;
        if (!empty($postData['all_store'])) {
            unset($postData['all_store']);
            $isAllStore = true;
        }
        if ($this->isSeller($customerid)) {
            $validateData = $this->validateprofiledata($observer);
            $errors = $validateData[1];
            $productIds = isset($postData['sellerassignproid']) ?
            $postData['sellerassignproid'] : '';
            $sellerId = $customerid;
            if (isset($postData['is_seller_remove'])
                && $postData['is_seller_remove'] == true) {
                $this->removePartner($sellerId);
                $this->_messageManager->addSuccess(
                    __('You removed the customer from seller.')
                );

                return $this;
            }
            if (!empty($productIds)) {
                $this->assignProduct($sellerId, $productIds);
            }
            if (!empty($postData['commission_enable'])) {
                $collectionselect = $this->mpSalesPartner->create()
                                    ->getCollection()
                                    ->addFieldToFilter(
                                        'seller_id',
                                        $sellerId
                                    );
                if ($collectionselect->getSize() == 1) {
                    foreach ($collectionselect as $verifyrow) {
                        $autoid = $verifyrow->getEntityId();
                    }

                    $collectionupdate = $this->mpSalesPartner->create()->load($autoid);
                    if (!isset($postData['commission'])) {
                        $postData['commission'] = $collectionupdate->getCommissionRate();
                    }
                    $collectionupdate->setCommissionRate($postData['commission']);
                    $collectionupdate->setCommissionStatus(1);
                    $collectionupdate->save();
                } else {
                    if (!isset($postData['commission'])) {
                        $postData['commission'] = 0;
                    }
                    $collectioninsert = $this->mpSalesPartner->create();
                    $collectioninsert->setSellerId($sellerId);
                    $collectioninsert->setCommissionRate($postData['commission']);
                    $collectioninsert->setCommissionStatus(1);
                    $collectioninsert->save();
                }
            }
            if (empty($errors)) {
                $target = $this->_mediaDirectory->getAbsolutePath('avatar/');
                // upload logo file
                $postData['banner_pic'] = $this->uploadSellerProfileImage(
                    $target,
                    'banner_pic'
                );

                // upload logo file
                $postData['logo_pic'] = $this->uploadSellerProfileImage(
                    $target,
                    'logo_pic'
                );

                $autoId = 0;
                $storeId = 0;
                if (!empty($postData['store_id'])) {
                    $storeId = $postData['store_id'];
                }
                $collection = $this->_collectionFactory->create()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('store_id', $storeId);
                if (!count($collection)) {
                    $sellerDefaultData = [];
                    $collection = $this->_collectionFactory->create()
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->addFieldToFilter('store_id', 0);
                    foreach ($collection as $value) {
                        $postData['banner_pic'] = $postData['banner_pic'] ?
                        $postData['banner_pic'] : $value->getBannerPic();
                        $postData['logo_pic'] = $postData['logo_pic'] ?
                        $postData['logo_pic'] : $value->getLogoPic();
                    }
                    foreach ($collection as $value) {
                        $sellerDefaultData = $value->getData();
                        $postData['banner_pic'] = $postData['banner_pic'] ?
                        $postData['banner_pic'] : $value->getBannerPic();
                        $postData['logo_pic'] = $postData['logo_pic'] ?
                        $postData['logo_pic'] : $value->getLogoPic();
                    }
                    foreach ($sellerDefaultData as $key => $value) {
                        if (empty($postData[$key]) && $key != 'entity_id') {
                            $postData[$key] = $value;
                        }
                    }
                } else {
                    foreach ($collection as $value) {
                        $autoId = $value->getId();
                        $postData['banner_pic'] = $postData['banner_pic'] ?
                        $postData['banner_pic'] : $value->getBannerPic();
                        $postData['logo_pic'] = $postData['logo_pic'] ?
                        $postData['logo_pic'] : $value->getLogoPic();
                    }
                }
                $value = $this->mpSeller->create()->load($autoId);
                $value->addData($postData);
                $value->setIsSeller(1);
                $value->save();
                if (array_key_exists("is_separate_panel", $postData)) {
                    $value->setIsSeparatePanel($postData['is_separate_panel']);
                }
                if (isset($postData['seller_category_ids'])) {
                    $value->setAllowedCategories($postData['seller_category_ids']);
                }
              
                if (!empty($postData['attribute_set'])) {
                    $attributeSet = $this->mpHelper->arrayToJson($postData['attribute_set']);
                    $value->setAllowedAttributesetIds($attributeSet);
                }

                if (isset($postData['company_description'])) {
                    $postData['company_description'] = preg_replace(
                        '#<script(.*?)>(.*?)</script>#is',
                        '',
                        $postData['company_description']
                    );
                    $value->setCompanyDescription(
                        $postData['company_description']
                    );
                }

                if (isset($postData['return_policy'])) {
                    $postData['return_policy'] = preg_replace(
                        '#<script(.*?)>(.*?)</script>#is',
                        '',
                        $postData['return_policy']
                    );
                    $value->setReturnPolicy($postData['return_policy']);
                }

                if (isset($postData['shipping_policy'])) {
                    $postData['shipping_policy'] = preg_replace(
                        '#<script(.*?)>(.*?)</script>#is',
                        '',
                        $postData['shipping_policy']
                    );
                    $value->setShippingPolicy($postData['shipping_policy']);
                }

                if (isset($postData['privacy_policy'])) {
                    $postData['privacy_policy'] = preg_replace(
                        '#<script(.*?)>(.*?)</script>#is',
                        '',
                        $postData['privacy_policy']
                    );
                    $value->setPrivacyPolicy($postData['privacy_policy']);
                }

                if (isset($postData['meta_description'])) {
                    $value->setMetaDescription($postData['meta_description']);
                }

                /**
                 * set taxvat number for seller
                 */
                if (isset($postData['taxvat'])) {
                    $customer = $this->customerFactory->create()->load($sellerId);
                    $customer->setTaxvat($postData['taxvat']);
                    $customer->setId($sellerId)->save();
                }

                if (array_key_exists('country_pic', $postData)) {
                    $value->setCountryPic($postData['country_pic']);
                }
                $value->save();
                $this->updateCommonData($value, $isAllStore);
            }
        } else {
            $this->createSellerAccount($customerid, $postData);
        }

        return $this;
    }
    /**
     * Create Seller account
     *
     * @param int $customerid
     * @param array $postData
     *
     * @return string|void
     */
    protected function createSellerAccount($customerid, $postData)
    {
        $profileurl = '';
        if (isset($postData['is_seller_add'])) {
            $isSellerAdd = $postData['is_seller_add'];
            $profileurl = $postData['profileurl'];
        } else {
            $isSellerAdd = false;
        }
        if ($profileurl != '') {
            $profileurlcount = $this->_collectionFactory->create();
            $profileurlcount->addFieldToFilter('shop_url', $profileurl);
            $sellerProfileIds = [];
            $sellerProfileUrl = '';
            $collectionselect = $this->_collectionFactory->create();
            $collectionselect->addFieldToFilter('seller_id', $customerid);
            foreach ($collectionselect as $coll) {
                array_push($sellerProfileIds, $coll->getEntityId());
                $sellerProfileUrl = $coll->getShopUrl();
            }
            if ($profileurlcount->getSize() && ($profileurl != $sellerProfileUrl)) {
                $this->_messageManager->addError(
                    __('This Shop URL already Exists.')
                );
            } else {
                $sellerStatus = $isSellerAdd ? 1 : 0;
                if (!empty($sellerProfileIds)) {
                    foreach ($sellerProfileIds as $sellerProfileId) {
                        $collection = $this->mpSeller->create()->load($sellerProfileId);
                        $collection->setIsSeller($sellerStatus);
                        $collection->setShopUrl($profileurl);
                        $collection->setSellerId($customerid);
                        $collection->setCreatedAt($this->_date->gmtDate());
                        $collection->save();
                    }
                } else {
                    $sellerProfileId = 0;
                    $collection = $this->mpSeller->create()->load($sellerProfileId);
                    $collection->setIsSeller($sellerStatus);
                    $collection->setShopUrl($profileurl);
                    $collection->setStoreId(0);
                    $collection->setSellerId($customerid);
                    $collection->setCreatedAt($this->_date->gmtDate());
                    $collection->save();
                }
                if ($sellerStatus) {
                    $helper = $this->mpHelper;
                    $adminStoreEmail = $helper->getAdminEmailId();
                    $adminEmail = $adminStoreEmail ? $adminStoreEmail :
                    $helper->getDefaultTransEmailId();
                    $adminUsername = $helper->getAdminName();

                    $seller = $this->customerFactory->create()->load($customerid);
                    $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
                    $emailTempVariables['sellerName'] = $seller->getName();
                    $emailTempVariables['sellerUrl'] = $baseUrl.'customer/account/login';
                    $senderInfo = [
                        'name' => $adminUsername,
                        'email' => $adminEmail,
                    ];
                    $receiverInfo = [
                        'name' => $seller->getName(),
                        'email' => $seller->getEmail(),
                    ];
                    $this->mpEmailHelper->sendSellerApproveMail(
                        $emailTempVariables,
                        $senderInfo,
                        $receiverInfo
                    );
                }
                $this->_messageManager->addSuccess(
                    __('You created the customer as seller.')
                );
            }
        }
    }
    /**
     * Update seller data in all store when all store is selected
     *
     * @param \Webkul\Marketplace\Model\Seller $sellerInfo
     * @param bool $updateInAllStore
     *
     * @return void
     */
    protected function updateCommonData($sellerInfo, $updateInAllStore = false)
    {
        $stores = $this->_storeManager->getStores(true);
        foreach ($stores as $mageStore) {
            $mpStore = $this->mpSeller->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $sellerInfo->getSellerId())
            ->addFieldToFilter('store_id', ["eq" => $mageStore->getId()]);
            $this->saveInStores($sellerInfo, $updateInAllStore, $mpStore, $mageStore);
        }
    }
    /**
     * Update seller data in all store when all store is selected
     *
     * @param \Webkul\Marketplace\Model\Seller $sellerInfo
     * @param bool $updateInAllStore
     * @param \Webkul\Marketplace\Model\Seller $mpStore
     * @param \Magento\Store\Model\StoreManagerInterface $mageStore
     *
     * @return void
     */
    public function saveInStores($sellerInfo, $updateInAllStore, $mpStore, $mageStore)
    {
        $storeSeller = [];
        if ($mpStore->getSize()) {
            #will execute if seller detail is stored with the mage store id
            foreach ($mpStore as $storeWise) {
                $storeSeller["allowed_categories"] = $sellerInfo->getAllowedCategories();
                $storeSeller["allowed_attributeset_ids"] = $sellerInfo->getAllowedAttributesetIds();
                #common seller details for all stores
                if ($updateInAllStore) {
                    #update seller details in all stores.
                    $currentSeller = $sellerInfo->getData();
                    $restrictedKey = ["entity_id", "store_id", "created_at"];
                    foreach ($currentSeller as $sellerKey => $sellerValue) {
                        if (!in_array($sellerKey, $restrictedKey)) {
                            $storeSeller[$sellerKey] = $currentSeller[$sellerKey];
                        }
                    }
                }
                $storeWise->addData($storeSeller)->save();
            }
          
        } else {
            if ($updateInAllStore) {
                #update seller details in all stores.
                $currentSeller = $sellerInfo->getData();
                $restrictedKey = [
                    "entity_id",
                    "store_id",
                    "customer",
                    "default_billing_address",
                    "default_shipping_address"
                ];
                foreach ($currentSeller as $sellerKey => $sellerValue) {
                    if (!in_array($sellerKey, $restrictedKey)) {
                        $storeSeller[$sellerKey] = $currentSeller[$sellerKey];
                    }
                }
                $storeSeller["store_id"] = $mageStore->getId();
                $this->mpSeller->create()
                ->setData($storeSeller)->save();
            }
        }
    }
    /**
     * Upload Seller Profile Image.
     *
     * @param string $target
     * @param string $fileName
     *
     * @return string|void
     */
    protected function uploadSellerProfileImage($target, $fileName)
    {
        try {
            $uploader = $this->_fileUploaderFactory->create(
                ['fileId' => $fileName]
            );
            $uploader->setAllowedExtensions(
                ['jpg', 'jpeg', 'gif', 'png']
            );
            $uploader->setAllowRenameFiles(true);
            $resul = $uploader->save($target);
            if ($resul['file']) {
                return $resul['file'];
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "Observer_AdminhtmlCustomerSaveAfterObserver uploadSellerProfileImage : ".$e->getMessage()
            );
            return '';
        }
    }

    /**
     * Check is seller
     *
     * @param int $customerid
     * @return boolean
     */
    public function isSeller($customerid)
    {
        $sellerStatus = 0;
        $model = $this->_collectionFactory->create()
        ->addFieldToFilter('seller_id', $customerid)
        ->addFieldToFilter('store_id', 0);
        foreach ($model as $value) {
            $sellerStatus = $value->getIsSeller();
        }

        return $sellerStatus;
    }

    /**
     * Validate profile data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    private function validateprofiledata($observer)
    {
        $errors = [];
        $data = [];
        $paramData = $observer->getRequest()->getParams();
        foreach ($paramData as $code => $value) {
            switch ($code):
                case 'twitter_id':
                    if (trim($value) != '' && preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $value)) {
                        $errors[] = __(
                            'Twitter Id cannot contain space and special characters'
                        );
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'facebook_id':
                    if (trim($value) != '' && preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $value)) {
                        $errors[] = __(
                            'Facebook Id cannot contain space and special characters'
                        );
                    } else {
                        $data[$code] = $value;
                    }
                    break;
            endswitch;
        }

        return [$data, $errors];
    }

    /**
     * Remove seller
     *
     * @param int $sellerId
     * @return void
     */
    private function removePartner($sellerId)
    {
        $collectionselectdelete = $this->mpSeller->create()->getCollection();
        $collectionselectdelete->addFieldToFilter(
            'seller_id',
            $sellerId
        );
        foreach ($collectionselectdelete as $sellerColl) {
            $sellerColl->delete();
        }
        //Set Produt status disabled
        $sellerProduct = $this->_sellerProduct->create()
                        ->addFieldToFilter(
                            'seller_id',
                            $sellerId
                        );

        foreach ($sellerProduct as $productInfo) {
            $allStores = $this->_storeManager->getStores();
            foreach ($allStores as $_eachStoreId => $val) {
                try {
                    $product = $this->_productRepository->getById(
                        $productInfo->getMageproductId()
                    );
                    $product->setStatus(
                        ProductStatus::STATUS_DISABLED
                    );
                    $this->_productRepository->save($product);
                } catch (\Exception $e) {
                    $this->mpHelper->logDataInLogger(
                        "Observer_AdminhtmlCustomerSaveAfterObserver removePartner : ".$e->getMessage()
                    );
                }
            }

            $productInfo->delete();
        }

        $helper = $this->mpHelper;
        $adminStoreEmail = $helper->getAdminEmailId();
        $adminEmail = $adminStoreEmail ?
        $adminStoreEmail : $helper->getDefaultTransEmailId();
        $adminUsername = $helper->getAdminName();

        $seller = $this->customerFactory->create()->load($sellerId);

        $emailTempVariables['sellerName'] = $seller->getName();
        $emailTempVariables['sellerUrl'] = $this->_storeManager->getStore()->getBaseUrl().'marketplace/account/login';
        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $receiverInfo = [
            'name' => $seller->getName(),
            'email' => $seller->getEmail(),
        ];
        $this->mpEmailHelper->sendSellerDisapproveMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }

    /**
     * Assign product to seller
     *
     * @param int $sellerId
     * @param array $productIds
     * @return void
     */
    public function assignProduct($sellerId, $productIds)
    {
        $productids = array_flip($this->_jsonDecoder->decode($productIds));

        // get all seller's products
        $sellerProductIds = $this->_sellerProduct->create()->getAllAssignProducts(
            '`seller_id`='.$sellerId
        );

        $additionalProductIds = array_diff(array_values($productids), array_values($sellerProductIds));
        $unassignProductIds = array_diff(array_values($sellerProductIds), array_values($productids));

        $helper = $this->mpHelper;
        $allowedProductTypeIds = explode(',', $helper->getAllowedProductType());

        // set product status to 1 to assign selected products from seller
        $productCollection = $this->productModel->create()->getCollection()
        ->addFieldToFilter(
            'entity_id',
            ['in' => $additionalProductIds]
        )->addFieldToFilter(
            'type_id',
            ['in' => $allowedProductTypeIds]
        );
        $successMessage = '';
        foreach ($productCollection as $product) {
            $product->setSellerId($sellerId)->save();
            $proid = $product->getId();
            $collection = $this->_sellerProduct->create()
                          ->addFieldToFilter(
                              'mageproduct_id',
                              $proid
                          );
            $flag = 1;
            foreach ($collection as $coll) {
                $flag = 0;
                if ($sellerId != $coll['seller_id']) {
                    $this->_messageManager->addError(
                        __('The product with id %1 is already assigned to other seller.', $proid)
                    );
                } else {
                    $coll->setAdminassign(1)->save();
                }
            }
            if ($flag) {
                $mpProduct = $this->mpProductFactory->create();
                $mpProduct->setMageproductId($proid);

                $mpProduct->setSellerId($sellerId);
                $mpProduct->setStatus($product->getStatus());
                $mpProduct->setAdminassign(1);
                $isApproved = 1;
                if ($product->getStatus() == 2 && $helper->getIsProductApproval()) {
                    $isApproved = 0;
                }
                $mpProduct->setIsApproved($isApproved);
                $mpProduct->setCreatedAt($this->_date->gmtDate());
                $mpProduct->save();
                $successMessage = __(
                    'Products has been successfully assigned to seller.'
                );
            }
        }

        // remove unassign products from seller
        $this->unassignProduct($sellerId, $unassignProductIds);

        if ($successMessage) {
            $this->_messageManager->addSuccess($successMessage);
        }
    }

    /**
     * Unassign products
     *
     * @param int $sellerId
     * @param array $productIds
     * @return void
     */
    public function unassignProduct($sellerId, $productIds)
    {
        $productCollection = $this->productModel->create()->getCollection()
        ->addFieldToFilter(
            'entity_id',
            ['in' => $productIds]
        );
        foreach ($productCollection as $coll) {
            $coll->setSellerId(0)->save();
        }
        $productids = $productIds;
        $collection = $this->_sellerProduct->create()
                      ->addFieldToFilter(
                          'mageproduct_id',
                          ['in' => $productids]
                      )
                      ->addFieldToFilter(
                          'seller_id',
                          $sellerId
                      );
        foreach ($collection as $coll) {
            $coll->delete();
        }
    }

    /**
     * Move directory to media directory
     *
     * @param string $value
     * @return void
     */
    private function moveDirToMediaDir()
    {
        try {
            $reader = $this->reader;

            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $this->filesystem;

            $mediaAvatarFullPath = $filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('avatar');
            if (!$this->_filesystemFile->fileExists($mediaAvatarFullPath)) {
                $this->_filesystemFile->mkdir($mediaAvatarFullPath, 0777, true);
                $avatarBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/avatar/banner-image.png';
                $this->_filesystemFile->cp($avatarBannerImage, $mediaAvatarFullPath.'/banner-image.png');
                $avatarNoImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/avatar/noimage.png';
                $this->_filesystemFile->cp($avatarNoImage, $mediaAvatarFullPath.'/noimage.png');
            }

            $mediaMarketplaceFullPath = $filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('marketplace');
            if (!$this->_filesystemFile->fileExists($mediaMarketplaceFullPath)) {
                $this->_filesystemFile->mkdir($mediaMarketplaceFullPath, 0777, true);
            }

            $mediaMarketplaceBannerFullPath = $filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('marketplace/banner');
            if (!$this->_filesystemFile->fileExists($mediaMarketplaceBannerFullPath)) {
                $this->_filesystemFile->mkdir($mediaMarketplaceBannerFullPath, 0777, true);
                $marketplaceBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/marketplace/banner/sell-page-banner.png';
                $this->_filesystemFile->cp(
                    $marketplaceBannerImage,
                    $mediaMarketplaceBannerFullPath.'/sell-page-banner.png'
                );
                // for landing page layout 2
                $marketplaceBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage1/banner/sell-page-1-hero-banner.jpg';
                $this->_filesystemFile->cp(
                    $marketplaceBannerImage,
                    $mediaMarketplaceBannerFullPath.'/sell-page-1-hero-banner.jpg'
                );
                // for landing page layout 3
                $marketplaceBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/banner/sell-page-2-hero-banner.jpg';
                $this->_filesystemFile->cp(
                    $marketplaceBannerImage,
                    $mediaMarketplaceBannerFullPath.'/sell-page-2-hero-banner.jpg'
                );
            }

            $mediaMarketplaceIconFullPath = $filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('marketplace/icon');
            if (!$this->_filesystemFile->fileExists($mediaMarketplaceIconFullPath)) {
                $this->_filesystemFile->mkdir($mediaMarketplaceIconFullPath, 0777, true);
                $icon1BannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/marketplace/icon/icon-add-products.png';
                $this->_filesystemFile->cp(
                    $icon1BannerImage,
                    $mediaMarketplaceIconFullPath.'/icon-add-products.png'
                );

                $icon2BannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/marketplace/icon/icon-collect-revenues.png';
                $this->_filesystemFile->cp(
                    $icon2BannerImage,
                    $mediaMarketplaceIconFullPath.'/icon-collect-revenues.png'
                );

                $icon3BannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/marketplace/icon/icon-register-yourself.png';
                $this->_filesystemFile->cp(
                    $icon3BannerImage,
                    $mediaMarketplaceIconFullPath.'/icon-register-yourself.png'
                );

                $icon4BannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/marketplace/icon/icon-start-selling.png';
                $this->_filesystemFile->cp(
                    $icon4BannerImage,
                    $mediaMarketplaceIconFullPath.'/icon-start-selling.png'
                );

                // for landing page layout 3
                $iconBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/icon/sell-page-2-setup-1.png';
                $this->_filesystemFile->cp(
                    $iconBannerImage,
                    $mediaMarketplaceIconFullPath.'/sell-page-2-setup-1.png'
                );
                $iconBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/icon/sell-page-2-setup-2.png';
                $this->_filesystemFile->cp(
                    $iconBannerImage,
                    $mediaMarketplaceIconFullPath.'/sell-page-2-setup-2.png'
                );
                $iconBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/icon/sell-page-2-setup-3.png';
                $this->_filesystemFile->cp(
                    $iconBannerImage,
                    $mediaMarketplaceIconFullPath.'/sell-page-2-setup-3.png'
                );
                $iconBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/icon/sell-page-2-setup-4.png';
                $this->_filesystemFile->cp(
                    $iconBannerImage,
                    $mediaMarketplaceIconFullPath.'/sell-page-2-setup-4.png'
                );
                $iconBannerImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/landingpage2/icon/sell-page-2-setup-5.png';
                $this->_filesystemFile->cp(
                    $iconBannerImage,
                    $mediaMarketplaceIconFullPath.'/sell-page-2-setup-5.png'
                );
            }

            $mediaPlaceholderFullPath = $filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('placeholder');
            if (!$this->_filesystemFile->fileExists($mediaPlaceholderFullPath)) {
                $this->_filesystemFile->mkdir($mediaPlaceholderFullPath, 0777, true);
                $placeholderImage = $reader->getModuleDir(
                    '',
                    'Webkul_Marketplace'
                ).'/view/base/web/images/placeholder/image.jpg';
                $this->_filesystemFile->cp(
                    $placeholderImage,
                    $mediaPlaceholderFullPath.'/image.jpg'
                );
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "Observer_AdminhtmlCustomerSaveAfterObserver moveDirToMediaDir : ".$e->getMessage()
            );
            $this->_messageManager->addError($e->getMessage());
        }
    }
}
