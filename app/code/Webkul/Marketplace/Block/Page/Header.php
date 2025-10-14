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
namespace Webkul\Marketplace\Block\Page;

class Header extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * @var string
     */
    protected $_template = 'Webkul_Marketplace::layout2/page/header.phtml';

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct(
            $context,
            $fileStorageHelper,
            $data
        );
    }

    /**
     * Get seller shop name
     *
     * @return string
     */
    public function getSellerShopName()
    {
        $sellerId = $this->helper->getCustomerId();
        $collection = $this->helper->getSellerCollectionObj($sellerId);
        $shopName = '';
        foreach ($collection as $value) {
            $shopName = $value->getShopTitle();
            if (empty($value->getShopTitle())) {
                $shopName = $value->getShopUrl();
            }
        }
        return $shopName;
    }

    /**
     * Get seller logo
     *
     * @return string
     */
    public function getSellerLogo()
    {
        $sellerId = $this->helper->getCustomerId();
        $collection = $this->helper->getSellerCollectionObj($sellerId);
        $logoPic = 'default/noimage.png';
        foreach ($collection as $value) {
            $logoPic = $value->getLogoPic();
            if (empty($logoPic)) {
                $logoPic = 'default/noimage.png';
            }
        }
        return $logoPic;
    }

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getSellerDashboardLogoSrc()
    {
        if ($logo = $this->helper->getSellerDashboardLogoUrl()) {
            return $logo;
        }
        return $this->getLogoSrc();
    }

    /**
     * Get Helper Data
     *
     * @return \Webkul\Marketplace\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
