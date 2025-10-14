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

namespace Webkul\Marketplace\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Webkul\Marketplace\Model\SellerFactory;

/**
 * Webkul Marketplace Account EditprofilePost Controller.
 */
class EditprofilePost extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * Media Storage File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var SellerFactory
     */
    protected $sellerModel;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerModel;

    /**
     * @param Context                                          $context
     * @param Session                                          $customerSession
     * @param FormKeyValidator                                 $formKeyValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     * @param Filesystem                                       $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Webkul\Marketplace\Helper\Data                  $helper
     * @param DataPersistorInterface                           $dataPersistor
     * @param CustomerUrl                                      $customerUrl
     * @param SellerFactory                                    $sellerModel
     * @param \Magento\Customer\Model\CustomerFactory          $customerModel
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Webkul\Marketplace\Helper\Data $helper,
        DataPersistorInterface $dataPersistor,
        CustomerUrl $customerUrl,
        SellerFactory $sellerModel,
        \Magento\Customer\Model\CustomerFactory $customerModel
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_date = $date;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->helper = $helper;
        $this->dataPersistor = $dataPersistor;
        $this->customerUrl = $customerUrl;
        $this->sellerModel = $sellerModel;
        $this->customerModel = $customerModel;
        parent::__construct(
            $context
        );
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
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
     * Update Seller Profile Informations.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $fields = [];
        if ($this->getRequest()->isPost()) {
            try {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
                $fields = $this->getRequest()->getParams();
                $paymentSource = $fields["payment_source"];
                unset($fields["payment_source"]);
                $sellerId = $this->helper->getCustomerId();
                $storeId = $this->helper->getCurrentStoreId();

                if ($this->helper->getAnalyticStatus()) {
                    $analyticId = $fields["analytic_id"];
                    unset($fields["analytic_id"]);
                    $this->updateAnalyticId($sellerId, $analyticId);
                }
                $this->updatePaymentSource($sellerId, $paymentSource);

                $errors = $this->validateprofiledata($fields);

                if (empty($errors)) {
                    $this->saveSellerProfileInfo($sellerId, $storeId, $fields);

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                } else {
                    foreach ($errors as $message) {
                        $this->messageManager->addError($message);
                    }
                    $this->dataPersistor->set('seller_profile_data', $fields);

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger(
                    "Controller_Account_EditProfilePost execute : " . $e->getMessage()
                );
                $this->messageManager->addError($e->getMessage());
                $this->dataPersistor->set('seller_profile_data', $fields);

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/editProfile',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * Save Seller Profile Info
     *
     * @param int $sellerId
     * @param int $storeId
     * @param array $fields
     * @return void
     */
    protected function saveSellerProfileInfo($sellerId, $storeId, $fields)
    {
        $autoId = 0;
        $collection = $this->sellerModel->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('store_id', $storeId);
        foreach ($collection as $value) {
            $autoId = $value->getId();
        }
        $fields['tw_active'] = (!isset($fields['tw_active'])) ? 0 : $fields['tw_active'];
        $fields['fb_active'] = (!isset($fields['fb_active'])) ? 0 : $fields['fb_active'];
        $fields['youtube_active'] = (!isset($fields['youtube_active'])) ? 0 : $fields['youtube_active'];
        $fields['vimeo_active'] = (!isset($fields['vimeo_active'])) ? 0 : $fields['vimeo_active'];
        $fields['instagram_active'] = (!isset($fields['instagram_active'])) ? 0 : $fields['instagram_active'];
        $fields['pinterest_active'] = (!isset($fields['pinterest_active'])) ? 0 : $fields['pinterest_active'];
        $fields['moleskine_active'] = (!isset($fields['moleskine_active'])) ? 0 : $fields['moleskine_active'];
        $fields['tiktok_active'] = (!isset($fields['tiktok_active'])) ? 0 : $fields['tiktok_active'];
        // If seller data doesn't exist for current store
        if (!$autoId) {
            $sellerDefaultData = [];
            $collection = $this->sellerModel->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('store_id', 0);
            foreach ($collection as $value) {
                $sellerDefaultData = $value->getData();
            }
            foreach ($sellerDefaultData as $key => $value) {
                if (empty($fields[$key]) && $key != 'entity_id') {
                    $fields[$key] = $value;
                }
            }
        }

        // Save seller data for current store
        $value = $this->sellerModel->create()->load($autoId);
        $value->addData($fields);
        if (!$autoId) {
            $value->setCreatedAt($this->_date->gmtDate());
        }

        $value->save();
        if (isset($fields['company_description'])) {
            $fields['company_description'] = $this->escapeString($fields['company_description']);
            $value->setCompanyDescription($fields['company_description']);
        }

        if (isset($fields['return_policy'])) {
            $fields['return_policy'] = $this->escapeString($fields['return_policy']);
            $value->setReturnPolicy($fields['return_policy']);
        }

        if (isset($fields['shipping_policy'])) {
            $fields['shipping_policy'] = $this->escapeString($fields['shipping_policy']);
            $value->setShippingPolicy($fields['shipping_policy']);
        }

        if (isset($fields['privacy_policy'])) {
            $fields['privacy_policy'] = $this->escapeString($fields['privacy_policy']);
            $value->setPrivacyPolicy($fields['privacy_policy']);
        }

        $value->setMetaDescription($fields['meta_description']);

        /**
         * Set taxvat number for seller
         */
        if ($fields['taxvat']) {
            $customer = $this->customerModel->create()->load($sellerId);
            $customer->setTaxvat($fields['taxvat']);
            $customer->setId($sellerId)->save();
        }
        if (!empty($fields['fulfilment_text'])) {
            $value->setFulfilmentText($fields['fulfilment_text']);
        }
        // perform action on seller's images
        $target = $this->_mediaDirectory->getAbsolutePath('avatar/');
        $canfulfilmentDelete = $canBannerPicDelete = $canLogoPicDelete = "";
        if ($value->getFulfilmentImage() && $this->canPicDelete($value->getId(), $value->getFulfilmentImage())) {
            $canfulfilmentDelete = $value->getFulfilmentImage();
        }
        if ($value->getBannerPic() && $this->canPicDelete($value->getId(), $value->getBannerPic())) {
            $canBannerPicDelete = $value->getBannerPic();
        }
        if ($value->getLogoPic() && $this->canPicDelete($value->getId(), $value->getLogoPic())) {
            $canLogoPicDelete = $value->getLogoPic();
        }
        try {
            if (isset($fields['fulfilment_text'])) {
                $uploader = $this->_fileUploaderFactory->create(
                    ['fileId' => 'fulfilment_image']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($target);
                if ($result['file']) {
                    $value->setFulfilmentImage($result['file']);
                }
            }
        } catch (\Exception $e) {
            $canfulfilmentDelete = "";
            $this->helper->logDataInLogger(
                "Controller_Account_EditProfilePost execute : " . $e->getMessage()
            );
            if ($e->getMessage() != 'The file was not uploaded.') {
                $this->messageManager->addError($e->getMessage());
                $this->dataPersistor->set('seller_profile_data', $fields);
            }
        }
        try {
            $uploader = $this->_fileUploaderFactory->create(
                ['fileId' => 'banner_pic']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($target);
            if ($result['file']) {
                $value->setBannerPic($result['file']);
            }
        } catch (\Exception $e) {
            $canBannerPicDelete = "";
            $this->helper->logDataInLogger(
                "Controller_Account_EditProfilePost execute : " . $e->getMessage()
            );
            if ($e->getMessage() != 'The file was not uploaded.') {
                $this->messageManager->addError($e->getMessage());
                $this->dataPersistor->set('seller_profile_data', $fields);
            }
        }
        try {
            $uploaderLogo = $this->_fileUploaderFactory->create(
                ['fileId' => 'logo_pic']
            );
            $uploaderLogo->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $uploaderLogo->setAllowRenameFiles(true);
            $resultLogo = $uploaderLogo->save($target);
            if ($resultLogo['file']) {
                $value->setLogoPic($resultLogo['file']);
            }
        } catch (\Exception $e) {
            $canLogoPicDelete = "";
            $this->helper->logDataInLogger(
                "Controller_Account_EditProfilePost execute : " . $e->getMessage()
            );
            if ($e->getMessage() != 'The file was not uploaded.') {
                $this->messageManager->addError($e->getMessage());
                $this->dataPersistor->set('seller_profile_data', $fields);
            }
        }
        $driverFile = $this->helper->getDriverFile();
        if ($canfulfilmentDelete) {
            if ($driverFile->isExists($target . $canfulfilmentDelete)) {
                $driverFile->deleteFile($target . $canfulfilmentDelete);
            }
        }
        if ($canBannerPicDelete) {
            if ($driverFile->isExists($target . $canBannerPicDelete)) {
                $driverFile->deleteFile($target . $canBannerPicDelete);
            }
        }
        if ($canLogoPicDelete) {
            if ($driverFile->isExists($target . $canLogoPicDelete)) {
                $driverFile->deleteFile($target . $canLogoPicDelete);
            }
        }
        // End of perform action on seller's images
        if (array_key_exists('country_pic', $fields)) {
            $value->setCountryPic($fields['country_pic']);
        }
        $value->save();
        if (array_key_exists('country_pic', $fields)) {
            $value->setCountryPic($fields['country_pic']);
        }
        $value->setStoreId($storeId);
        $value->save();
        $sellerData = $value;
        $this->saveInOtherStores($value->getId(), $sellerData);
        try {
            // clear cache
            $this->helper->clearCache();

            $this->messageManager->addSuccess(
                __('Profile information was successfully saved')
            );
            $this->dataPersistor->clear('seller_profile_data');

            return $this->resultRedirectFactory->create()->setPath(
                '*/*/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Controller_Account_EditProfilePost execute : " . $e->getMessage()
            );
            $this->messageManager->addException($e, __('We can\'t save the customer.'));
        }
    }

    /**
     * Validate profiledata
     *
     * @param array $fields
     * @return array
     */
    protected function validateprofiledata(&$fields)
    {
        $errors = [];
        foreach ($fields as $code => $value) {
            switch ($code):
                case 'twitter_id':
                    $this->validateInput("Twitter Id", $fields, $code, $value, $errors);
                    break;
                case 'facebook_id':
                    $this->validateInput("Facebook Id", $fields, $code, $value, $errors);
                    break;
                case 'instagram_id':
                    $this->validateInput("Instagram ID", $fields, $code, $value, $errors);
                    break;
                case 'youtube_id':
                    $this->validateInput("Youtube ID", $fields, $code, $value, $errors);
                    break;
                case 'vimeo_id':
                    $this->validateInput("Vimeo ID", $fields, $code, $value, $errors);
                    break;
                case 'pinterest_id':
                    $this->validateInput("Pinterest ID", $fields, $code, $value, $errors);
                    break;
                case 'moleskine_id':
                    $this->validateInput("Moleskine ID", $fields, $code, $value, $errors);
                    break;
                case 'tiktok_id':
                    $this->validateInput("Tiktok ID", $fields, $code, $value, $errors);
                    break;
                case 'taxvat':
                    if (trim($value) != '' &&
                        preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $value)
                    ) {
                        $errors[] = __('Tax/VAT Number cannot contain space and special characters');
                    } else {
                        $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                        $fields[$code] = $value;
                    }
                    break;
                case 'shop_title':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $fields[$code] = $value;
                    break;
                case 'contact_number':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $fields[$code] = $value;
                    break;
                case 'company_locality':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $fields[$code] = $value;
                    break;
                case 'company_description':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'meta_keyword':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'meta_description':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'shipping_policy':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'privacy_policy':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'return_policy':
                    $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                    $value = $this->helper->validateXssString($value);
                    $fields[$code] = $value;
                    break;
                case 'background_width':
                    if (trim($value) != '' &&
                        strlen($value) != 6 &&
                        substr($value, 0, 1) != '#'
                    ) {
                        $errors[] = __('Invalid Background Color');
                    } else {
                        $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
                        $fields[$code] = $value;
                    }
            endswitch;
        }

        return $errors;
    }
    /**
     * Escape string
     *
     * @param string $input
     * @return string
     */
    public function escapeString($input)
    {
        return preg_replace(
            '#<script(.*?)>(.*?)</script>#is',
            '',
            $input
        );
    }
    /**
     * Validate Input
     *
     * @param string $id
     * @param array $fields
     * @param string $code
     * @param string $value
     * @param array $errors
     * @return void
     */
    public function validateInput($id, &$fields, $code, $value, &$errors)
    {
        if (trim($value) != '' &&
            preg_match('/[\'^£$%&*()}{~?><>, |=+¬]/', $value)
        ) {
            $errors[] = __($id) . __(' cannot contain space and special characters, ' .
                'allowed special characters are @,#,_,-');
        } else {
            $value = preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;
            $fields[$code] = $value;
        }
    }
    /**
     * Save data in other stores if does not exists
     *
     * @param int $id
     * @param \Webkul\Marketplace\Model\SellerFactory $currentSeller
     * @return void
     */
    public function saveInOtherStores($id, $currentSeller)
    {
        $currentSeller = $currentSeller->getData();
        $AllSellerStores = $this->sellerModel->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $currentSeller["seller_id"])
            ->addFieldToFilter('entity_id', ["neq" => $id]);
        foreach ($AllSellerStores as $storeWise) {
            $storeSeller = [];
            $sellerKeys = $storeWise->getData();
            $restrictedKey = ["entity_id", "store_id", "created_at", "low_stock_quantity"];
            $restricketedValue = [""];
            $storeSeller["low_stock_quantity"] = $currentSeller["low_stock_quantity"];
            foreach ($sellerKeys as $sellerKey => $sellerValue) {
                if (!in_array($sellerKey, $restrictedKey) && !in_array($sellerValue, $restricketedValue)) {
                    $storeSeller[$sellerKey] = $currentSeller[$sellerKey];
                }
            }
            $storeWise->addData($storeSeller)->save();
        }
    }
    /**
     * Can pic delete
     *
     * @param int $id
     * @param string $picName
     * @return bool
     */
    public function canPicDelete($id, $picName)
    {
        $sellerData = $this->sellerModel->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', ["neq" => $id])
            ->addFieldToFilter(
                ["fulfilment_image", "banner_pic", "logo_pic"],
                [
                    ["eq" => $picName],
                    ["eq" => $picName],
                    ["eq" => $picName]
                ]
            );
        if ($sellerData->getSize()) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Update payment source
     *
     * @param int $sellerId
     * @param string $paymentSource
     * @return void
     */
    public function updatePaymentSource($sellerId, $paymentSource)
    {
        $collection = $this->sellerModel->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', $sellerId);
        foreach ($collection as $seller) {
            $seller->setPaymentSource($paymentSource);
            $seller->save();
        }
    }
    /**
     * Update analytic Id
     *
     * @param int $sellerId
     * @param string $analyticId
     * @return void
     */
    public function updateAnalyticId($sellerId, $analyticId)
    {
        if (trim($analyticId)) {
            $this->helper->updateAnalyticId($sellerId, $analyticId);
        }
    }
}
