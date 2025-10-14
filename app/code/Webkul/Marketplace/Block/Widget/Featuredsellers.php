<?php
/**
 * webkul
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Widget;

use Webkul\Marketplace\Model\SellerFactory;
use Magento\Framework\View\Element\Template\Context;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Framework\View\Asset\Repository;

class Featuredsellers extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     *
     * @var sellerfactory
     */
    protected $sellerFactory;

    /**
     *
     * @var context
     */
    protected $context;

    /**
     *
     * @var helper
     */
    protected $mphelper;

    /**
     *
     * @var asset repository
     */
    protected $assetRepository;

    /**
     * Construct
     *
     * @param Context $context
     * @param SellerFactory $sellerFactory
     * @param MpHelper $mphelper
     * @param Repository $assetRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        SellerFactory $sellerFactory,
        MpHelper $mphelper,
        Repository $assetRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->mphelper = $mphelper;
        $this->assetRepository = $assetRepository;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * GetAllowedStatus is used to check the status of featured seller slider
     *
     * @return bool
     */
    public function getAllowedStatus()
    {
        $helper = $this->mphelper;
        $profileDisplayFlag = $helper->getSellerProfileDisplayFlag();
        $featuredSellerFlag = $helper->getConfigValue('profile_settings', 'vendor_featured');
        if ($profileDisplayFlag && $featuredSellerFlag) {
            return true;
        }
        return false;
    }

    /**
     * Set the template file
     *
     * @return string
     */
    public function _toHtml()
    {
        if ($this->getAllowedStatus()) {
            $this->setTemplate('featuredsellers.phtml');
        }
        return parent::_toHtml();
    }

    /**
     * GetAssetUrl to include css file in phtml.
     *
     * @param  mixed $asset
     * @return Repository
     */
    public function getAssetUrl($asset)
    {
        return $this->assetRepository->createAsset($asset)->getUrl();
    }

    /**
     * Get seller Ids
     *
     * @return array
     */
    public function getSellerIds()
    {
        $sellerIds = $this->getData('sellerids');
        $sellerIdsArray = explode(',', $sellerIds);
        return $sellerIdsArray;
    }

    /**
     * Get Seller Details By Id get selller details by the sellerids
     *
     * @return array
     */
    public function getSellerDetailsById()
    {
        $sellerDetails =[];
        $helper= $this->mphelper;
        $sellerIds = $this->getSellerIds();
        $storeId = $helper->getCurrentStoreId();
        $collection = $this->sellerFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('is_seller', \Webkul\Marketplace\Model\Seller::STATUS_ENABLED)
                        ->addFieldToFilter(
                            ['store_id','store_id'],
                            [
                                 ['eq'=>$storeId],
                                 ['eq'=>0]
                             ]
                        )
                        ->addFieldToFilter('seller_id', ['in'=>$sellerIds]);
        foreach ($collection as $sellerData) {
            $shopTitle = $sellerData->getShopTitle() ? $sellerData->getShopTitle() : $sellerData->getShopUrl();
            $sellerDetails[$sellerData->getSellerId()] = [
            'logo_pic' => $sellerData->getLogoPic(),
            'shop_url' => $sellerData->getShopUrl(),
            'shop_title' => $shopTitle
            ];
        }
        return $sellerDetails;
    }

    /**
     * Get Logo Url get seller logo image path
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->mphelper->getMediaUrl().'avatar/';
    }

    /**
     * Get Transition Time used to get the Transition time
     *
     * @return string
     */
    public function getTransitionTime()
    {
        return $this->getData('transitionTime');
    }

    /**
     * Get Slider Width used to get the slider width
     *
     * @return string
     */
    public function getSliderWidth()
    {
        return $this->getData('sliderwidth');
    }

    /**
     * Get Image Height used to get the seller logo height
     *
     * @return string
     */
    public function getImageHeight()
    {
        return $this->getData('imageheight');
    }
    
    /**
     * Function Get Marketplace helper
     *
     * @return MpHelper
     */
    public function getMarketplacehelper()
    {
        return $this->mphelper;
    }
}
