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

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\Product;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Webkul Marketplace Product Save Controller.
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
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
     * @var SaveProduct
     */
    protected $_saveProduct;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_productResourceModel;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var HelperData
     */
    protected $helper;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @var \Webkul\Marketplace\Api\DraftProductRepositoryInterface
     */
    protected $draftProductRepo;
    /**
     * @var \Webkul\Marketplace\Api\Data\DraftProductInterface
     */
    protected $draftProduct;
    /**
     * @var Filesystem
     */
    protected $_filesystem;
    /**
     * @var int
     */
    protected $draftId = 0;
    /**
     * Construct
     *
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param SaveProduct $saveProduct
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
     * @param HelperData $helper
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Catalog\Model\ProductFactory|null $productFactory
     * @param \Webkul\Marketplace\Api\DraftProductRepositoryInterface $draftProductRepo
     * @param \Webkul\Marketplace\Api\Data\DraftProductInterface $draftProduct
     * @param Filesystem $_filesystem
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        SaveProduct $saveProduct,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        HelperData $helper,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\Marketplace\Api\DraftProductRepositoryInterface $draftProductRepo,
        \Webkul\Marketplace\Api\Data\DraftProductInterface $draftProduct,
        Filesystem $_filesystem
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_saveProduct = $saveProduct;
        $this->_productResourceModel = $productResourceModel;
        $this->helper = $helper;
        $this->dataPersistor = $dataPersistor;
        $this->_productFactory = $productFactory;
        $this->draftProductRepo = $draftProductRepo;
        $this->draftProduct = $draftProduct;
        $this->_filesystem = $_filesystem;
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
     * Seller product save action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        $productSimple = Product::PRODUCT_TYPE_SIMPLE;
        $productConfigurable = Product::PRODUCT_TYPE_CONFIGURABLE;
        if ($isPartner == 1) {
            $productId = $this->getRequest()->getParam('id');
            $wholedata = $this->getRequest()->getParams();
            $this->draftId = !empty($wholedata["draft_product_id"])?$wholedata["draft_product_id"]:0;
            $wholedata["product"]["fulfilled_by"] = empty($wholedata["product"]["fulfilled_by"])?0:1;
            if (isset($wholedata["draft"])) {
                $this->saveDraftProduct($wholedata);
                $this->messageManager->addSuccess(
                    __('The draft product has been successfully saved')
                );
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/draftproduct',
                    [
                        '_secure' => $this->getRequest()->isSecure(),
                    ]
                );
            }
            if (!empty($productId) && (isset($wholedata['type']) && $wholedata['type'] == $productConfigurable)) {
                $productObj = $this->_productFactory->create()->load($productId);
                if ($productObj->getTypeId() == $productSimple) {
                    $productObj->setTypeId($productConfigurable);
                    $productObj->save();
                }
            }
            try {
                $returnArr = [];
                if ($this->getRequest()->isPost()) {
                    if (!$this->_formKeyValidator->validate($this->getRequest())) {
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/create',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }

                    $skuType = $helper->getSkuType();
                    $skuPrefix = $helper->getSkuPrefix();
                    if ($skuType == 'dynamic' && !$productId) {
                        $sku = $skuPrefix.$wholedata['product']['name'];
                        $wholedata['product']['sku'] = $this->checkSkuExist($sku);
                    }
                    list($errors, $wholedata) = $this->validatePost($wholedata);

                    if (empty($errors)) {
                        $returnArr = $this->_saveProduct->saveProductData(
                            $this->_getSession()->getCustomerId(),
                            $wholedata
                        );
                        $productId = $returnArr['product_id'];
                    } else {
                        foreach ($errors as $message) {
                            $this->messageManager->addError($message);
                        }
                        $this->getDataPersistor()->set('seller_catalog_product', $wholedata);
                    }
                }
                if ($productId != '') {
                    // clear cache
                    $helper->clearCache();
                    if (empty($errors)) {
                        $this->messageManager->addSuccess(
                            __('Your product has been successfully saved')
                        );
                        $this->getDataPersistor()->clear('seller_catalog_product');
                        if ($this->draftId) {
                            $this->draftProductRepo->deleteById($this->draftId);
                        }
                    }

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/edit',
                        [
                            'id' => $productId,
                            '_secure' => $this->getRequest()->isSecure(),
                        ]
                    );
                } else {
                    if (isset($returnArr['error']) && isset($returnArr['message'])) {
                        if ($returnArr['error'] && $returnArr['message'] != '') {
                            $this->messageManager->addError($returnArr['message']);
                        }
                    }
                    $this->getDataPersistor()->set('seller_catalog_product', $wholedata);
                    if (isset($wholedata['set']) && isset($wholedata['type'])) {
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/add',
                            [
                                'set' => $wholedata['set'],
                                'type' => $wholedata['type'],
                                '_secure' => $this->getRequest()->isSecure()
                            ]
                        );
                    } else {
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/productlist',
                            [
                                '_secure' => $this->getRequest()->isSecure()
                            ]
                        );
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->helper->logDataInLogger(
                    "Controller_Product_Save execute : ".$e->getMessage()
                );
                $this->messageManager->addError($e->getMessage());
                $this->getDataPersistor()->set('seller_catalog_product', $wholedata);
                if ($productId) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/edit',
                        [
                            'id' => $productId,
                            '_secure' => $this->getRequest()->isSecure(),
                        ]
                    );
                } else {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/add',
                        [
                            'set' => $wholedata['set'],
                            'type' => $wholedata['type'],
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger(
                    "Controller_Product_Save execute : ".$e->getMessage()
                );
                $this->messageManager->addError($e->getMessage());
                $this->getDataPersistor()->set('seller_catalog_product', $wholedata);
                if ($productId) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/edit',
                        [
                            'id' => $productId,
                            '_secure' => $this->getRequest()->isSecure(),
                        ]
                    );
                } elseif (isset($wholedata['set']) && isset($wholedata['type'])) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/add',
                        [
                            'set' => $wholedata['set'],
                            'type' => $wholedata['type'],
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                } else {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/productlist',
                        [
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                }
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * CHeck if sku exist
     *
     * @param string $sku
     * @return string
     */
    private function checkSkuExist($sku)
    {
        try {
            $id = $this->_productResourceModel->getIdBySku($sku);
            if ($id) {
                $avialability = 0;
            } else {
                $avialability = 1;
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Controller_Product_Save checkSkuExist : ".$e->getMessage()
            );
            $avialability = 0;
        }
        if ($avialability == 0) {
            $sku = $sku.rand();
            $sku = $this->checkSkuExist($sku);
        }
        return $sku;
    }
    /**
     * Validate data
     *
     * @param array $wholedata
     * @return array
     */
    private function validatePost(&$wholedata)
    {
        $errors = [];
        $data = [];
        foreach ($wholedata['product'] as $code => $value) {
            switch ($code):
                case 'name':
                    $result = $this->nameValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Name has to be completed');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'description':
                    $result = $this->descriptionValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        // $errors[] = __('Description has to be completed');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'short_description':
                    $result = $this->descriptionValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'price':
                    if ($wholedata['type'] == Product::PRODUCT_TYPE_CONFIGURABLE) {
                        $value = 0;
                    }
                    $result = $this->priceValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Price should contain only decimal numbers');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'weight':
                    $result = $this->weightValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Weight should contain only decimal numbers');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'stock':
                    $result = $this->stockValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Product quantity should contain only decimal numbers');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'sku_type':
                    $result = $this->skuTypeValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Sku Type has to be selected');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'sku':
                    $result = $this->skuValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Sku has to be completed');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'price_type':
                    $result = $this->priceTypeValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Price Type has to be selected');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'weight_type':
                    $result = $this->weightTypeValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Weight Type has to be selected');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'bundle_options':
                    $result = $this->bundleOptionValidateFunction($value, $code, $data);
                    if ($result['error']) {
                        $errors[] = __('Default Title has to be completed');
                        $wholedata['product'][$code] = '';
                    } else {
                        $wholedata['product'][$code] = $result['data'][$code];
                    }
                    break;
                case 'url_key':
                    $result = $this->urlKeyValidateFunction($value, $code, $data);
                    $wholedata['product'][$code] = $result['data'][$code];
                    break;
                case 'meta_title':
                    $result = $this->metaTitleValidateFunction($value, $code, $data);
                    $wholedata['product'][$code] = $result['data'][$code];
                    break;
                case 'meta_keyword':
                    $result = $this->metaKeywordValidateFunction($value, $code, $data);
                    $wholedata['product'][$code] = $result['data'][$code];
                    break;
                case 'meta_description':
                    $result = $this->metaDiscValidateFunction($value, $code, $data);
                    $wholedata['product'][$code] = $result['data'][$code];
                    break;
                case 'mp_product_cart_limit':
                    if (!empty($value)) {
                        $result = $this->stockValidateFunction($value, $code, $data);
                        if ($result['error']) {
                            $errors[] = __('Allowed Product Cart Limit Qty should contain only decimal numbers');
                            $wholedata['product'][$code] = '';
                        } else {
                            $wholedata['product'][$code] = $result['data'][$code];
                        }
                    }
                    break;
            endswitch;
        }

        return [$errors, $wholedata];
    }

    /**
     * Validate name
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function nameValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = strip_tags($value);
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate description
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function descriptionValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $value = preg_replace("/<script.*?\/script>/s", "", $value) ? : $value;
            $helper = $this->helper;
            $value = $helper->validateXssString($value);
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate short description
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function shortDescValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate price
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function priceValidateFunction($value, $code, $data)
    {
        $error = false;
        if (!preg_match('/^\s*[+\-]?(?:\d+(?:\.\d*)?|\.\d+)\s*$/', $value)) {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate weight
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function weightValidateFunction($value, $code, $data)
    {
        $error = false;
        if ($value != "" && !preg_match('/^\s*[+\-]?(?:\d+(?:\.\d*)?|\.\d+)\s*$/', $value)) {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate stock
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function stockValidateFunction($value, $code, $data)
    {
        $error = false;
        if (!preg_match('/^([0-9])+?[0-9.]*$/', $value)) {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate sku type
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function skuTypeValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate Sku
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function skuValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = strip_tags($value);
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate price type
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function priceTypeValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate weight
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function weightTypeValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate bundle options
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function bundleOptionValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
        } else {
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate meta title
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function metaTitleValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
            $data[$code] = '';
        } else {
            $data[$code] = strip_tags($value);
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate meta keyword
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function metaKeywordValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
            $data[$code] = '';
        } else {
            $value = preg_replace("/<script.*?\/script>/s", "", $value) ? : $value;
            $helper = $this->helper;
            $value = $helper->validateXssString($value);
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Validate meta descriotion
     *
     * @param string $value
     * @param string|int $code
     * @param array $data
     * @return array
     */
    private function metaDiscValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
            $data[$code] = '';
        } else {
            $value = preg_replace("/<script.*?\/script>/s", "", $value) ? : $value;
            $helper = $this->helper;
            $value = $helper->validateXssString($value);
            $data[$code] = $value;
        }
        return ['error' => $error, 'data' => $data];
    }

    /**
     * Retrieve data persistor
     *
     * @return \Magento\Framework\App\Request\DataPersistorInterface|mixed
     */
    protected function getDataPersistor()
    {
        return $this->dataPersistor;
    }
    /**
     * UrlKeyValidateFunction function
     *
     * @param string $value
     * @param string $code
     * @param array $data
     * @return array
     */
    private function urlKeyValidateFunction($value, $code, $data)
    {
        $error = false;
        if (trim($value) == '') {
            $error = true;
            $data[$code] = '';
        } else {
            $data[$code] = strip_tags($value);
        }
        return ['error' => $error, 'data' => $data];
    }
    /**
     * Save draft product
     *
     * @param array $draftData
     * @return void
     */
    private function saveDraftProduct($draftData)
    {
        unset($draftData["form_key"]);
        $draftRepo = null;
        $draftRepo = $this->draftProduct;
        $sellerId = $this->helper->getCustomerId();
        if (!empty($draftData["draft"])) {
            $draftRepo = $this->draftProductRepo->getById($draftData["draft"]);
        }
        $draftData["links"]["related"] = $this->getProductIds("related", $draftData);
        $draftData["links"]["crosssell"] = $this->getProductIds("crosssell", $draftData);
        $draftData["links"]["upsell"] = $this->getProductIds("upsell", $draftData);
        $this->getProductImage($draftData);
        $content = $this->helper->arrayToJson($draftData);
        $draftRepo->setData("content", $content)
        ->setSellerId($sellerId)
        ->setName($draftData["product"]["name"])
        ->setSku($draftData["product"]["sku"])
        ->setPrice($draftData["product"]["price"])
        ->setQuantity($draftData["product"]["quantity_and_stock_status"]["qty"]??0);
        $this->draftProductRepo->save($draftRepo);
    }
    /**
     * Get product ids from product data
     *
     * @param string $type
     * @param array $productData
     * @return array
     */
    private function getProductIds($type, $productData)
    {
        $linkType = $productData["links"][$type]??[];
        $productIds = [];
        foreach ($linkType as $product) {
            $productIds[] = $product["id"];
        }
        return $productIds;
    }
    /**
     * Get product image data
     *
     * @param array $productData
     * @return void
     */
    protected function getProductImage(&$productData)
    {
        $productImages = $productData["product"]["media_gallery"]["images"]??[];
        $imageData = [];
        foreach ($productImages as $productImage) {
            $media = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $fileName = explode(".tmp", $productImage["file"]);
            if ($productImage["removed"] == 1) {
                continue;
            }
            $imageData[] = [
                "value_id" => $productImage["value_id"],
                "file" => $fileName[0],
                "media_type" => $productImage["media_type"]??"image",
                "position" => $productImage["position"],
                "disabled" => $productImage["disabled"],
                "url" => $this->helper->getMediaUrl()."tmp/catalog/product".$fileName[0],
                "path" => $media->getAbsolutePath('tmp/catalog/product').$fileName[0]
            ];
        }
        $productData["product"]["product_image"] = $imageData;
        unset($productData["product"]["media_gallery"]);
    }
}
