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

namespace Webkul\Marketplace\Block;

/*
 * Webkul Marketplace Sellerblock Block
 */
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\Product;
use Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\CollectionFactory;
use Webkul\Marketplace\Helper\Data;

class Sellerblock extends \Magento\Framework\View\Element\Template
{
    public const FLAG_REASON_ENABLE = 1;
    public const FLAG_REASON_DISABLE = 0;

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $Customer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $Session;

    /**
     * @var Data
     */
    protected $mpHelper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\Collection
     */
    protected $reasonCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Customer $customer
     * @param \Magento\Customer\Model\Session $session
     * @param Data $mpHelper
     * @param CollectionFactory|null $reasonCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Customer $customer,
        \Magento\Customer\Model\Session $session,
        Data $mpHelper,
        CollectionFactory $reasonCollection,
        array $data = []
    ) {
        $this->Customer = $customer;
        $this->Session = $session;
        $this->_coreRegistry = $registry;
        $this->mpHelper = $mpHelper;
        $this->reasonCollection = $reasonCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get product information
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * Get config value
     *
     * @return config value
     */

    /**
     * Get Configuration value
     *
     * @param string $group
     * @param string $field
     * @return string
     */
    public function getConfigValue($group, $field)
    {
        return $this->mpHelper->getConfigValue($group, $field);
    }

    /**
     * GetProductFlagReasons is used to get the product Flag Reasons
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\Collection
     */
    public function getProductFlagReasons()
    {
        $reasonCollection = $this->reasonCollection->create()
                          ->addFieldToFilter('status', self::FLAG_REASON_ENABLE)
                          ->setPageSize(5);
        return $reasonCollection;
    }
    /**
     * Get product fulfilment image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string|null
     */
    public function getFulfilmentImage($product)
    {
        $path = Data::MARKETPLACE_GENERAL_SETTINGS_FULFILMENT_IMAGE;
        $image = $this->mpHelper->getConfigurationValue($path);
        $image = $image?$this->mpHelper->getMediaUrl().'marketplace/logo/'.$image:$image;
        if ($product->getFulfilledBy() != \Webkul\Marketplace\Helper\Data::FULFILLED_BY_MARKETPLACE) {
            $seller = $this->mpHelper->getSellerDataBySellerId($product->getSellerId());
            if (!empty($seller->getFirstItem()->getFulfilmentImage())) {
                $path = $this->mpHelper->getMediaUrl().'avatar/';
                $image = $path.$seller->getFirstItem()->getFulfilmentImage();

            }
        }
        if ($image == "") {
            $image = null;
        }
        return $image;
    }
    /**
     * Get product fulfilment text
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string|null
     */
    public function getFulfilmentText($product)
    {
        $path = Data::MARKETPLACE_GENERAL_SETTINGS_FULFILMENT_TEXT;
        $text = $this->mpHelper->getConfigurationValue($path);
        if ($product->getFulfilledBy() != \Webkul\Marketplace\Helper\Data::FULFILLED_BY_MARKETPLACE) {
            $seller = $this->mpHelper->getSellerDataBySellerId($product->getSellerId());
            $text = __("This product is fulfilled by seller");
            $text = $seller->getFirstItem()->getFulfilmentText()??$text;
            if (!empty($seller->getFirstItem()->getFulfilmentText())) {
                $text = $seller->getFirstItem()->getFulfilmentText();
            }
        }
        return $text;
    }
}
