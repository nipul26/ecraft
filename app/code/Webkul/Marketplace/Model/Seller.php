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
namespace Webkul\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\Marketplace\Api\Data\SellerInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Seller Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\Seller _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\Seller getResource()
 */
class Seller extends AbstractModel implements SellerInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Seller's Statuses
     */
    public const STATUS_ENABLED    = 1;
    public const STATUS_PENDING    = 0;
    public const STATUS_PROCESSING = 2;
    public const STATUS_DISABLED   = 3;
    public const STATUS_DENIED   = 4;

    /**
     * Marketplace Seller cache tag.
     */
    public const CACHE_TAG = 'marketplace_seller';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_seller';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_seller';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\Seller::class
        );
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSeller();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Seller.
     *
     * @return \Webkul\Marketplace\Model\Seller
     */
    public function noRouteSeller()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Prepare seller's statuses. Available event marketplace_seller_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
          self::STATUS_ENABLED => __('Approved'),
          self::STATUS_PENDING => __('Pending'),
          self::STATUS_PROCESSING => __('Processing'),
          self::STATUS_DISABLED  => __('Disapproved'),
          self::STATUS_DENIED => __('Denied')
        ];
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set IsSeller
     *
     * @param int $isSeller
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setIsSeller($isSeller)
    {
        return $this->setData(self::IS_SELLER, $isSeller);
    }

    /**
     * Get IsSeller
     *
     * @return int
     */
    public function getIsSeller()
    {
        return parent::getData(self::IS_SELLER);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId()
    {
        return parent::getData(self::SELLER_ID);
    }

    /**
     * Set PaymentSource
     *
     * @param string $paymentSource
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setPaymentSource($paymentSource)
    {
        return $this->setData(self::PAYMENT_SOURCE, $paymentSource);
    }

    /**
     * Get PaymentSource
     *
     * @return string
     */
    public function getPaymentSource()
    {
        return parent::getData(self::PAYMENT_SOURCE);
    }

    /**
     * Set TwitterId
     *
     * @param string $twitterId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setTwitterId($twitterId)
    {
        return $this->setData(self::TWITTER_ID, $twitterId);
    }

    /**
     * Get TwitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return parent::getData(self::TWITTER_ID);
    }

    /**
     * Set FacebookId
     *
     * @param string $facebookId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setFacebookId($facebookId)
    {
        return $this->setData(self::FACEBOOK_ID, $facebookId);
    }

    /**
     * Get FacebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return parent::getData(self::FACEBOOK_ID);
    }

    /**
     * Set YoutubeId
     *
     * @param string $youtubeId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setYoutubeId($youtubeId)
    {
        return $this->setData(self::YOUTUBE_ID, $youtubeId);
    }

    /**
     * Get YoutubeId
     *
     * @return string
     */
    public function getYoutubeId()
    {
        return parent::getData(self::YOUTUBE_ID);
    }

    /**
     * Set VimeoId
     *
     * @param string $vimeoId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setVimeoId($vimeoId)
    {
        return $this->setData(self::VIMEO_ID, $vimeoId);
    }

    /**
     * Get VimeoId
     *
     * @return string
     */
    public function getVimeoId()
    {
        return parent::getData(self::VIMEO_ID);
    }

    /**
     * Set InstagramId
     *
     * @param string $instagramId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setInstagramId($instagramId)
    {
        return $this->setData(self::INSTAGRAM_ID, $instagramId);
    }

    /**
     * Get InstagramId
     *
     * @return string
     */
    public function getInstagramId()
    {
        return parent::getData(self::INSTAGRAM_ID);
    }

    /**
     * Set PinterestId
     *
     * @param string $pinterestId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setPinterestId($pinterestId)
    {
        return $this->setData(self::PINTEREST_ID, $pinterestId);
    }

    /**
     * Get PinterestId
     *
     * @return string
     */
    public function getPinterestId()
    {
        return parent::getData(self::PINTEREST_ID);
    }

    /**
     * Set MoleskineId
     *
     * @param string $moleskineId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setMoleskineId($moleskineId)
    {
        return $this->setData(self::MOLESKINE_ID, $moleskineId);
    }

    /**
     * Get MoleskineId
     *
     * @return string
     */
    public function getMoleskineId()
    {
        return parent::getData(self::MOLESKINE_ID);
    }

    /**
     * Set TiktokId
     *
     * @param string $tiktokId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setTiktokId($tiktokId)
    {
        return $this->setData(self::TIKTOK_ID, $tiktokId);
    }

    /**
     * Get TiktokId
     *
     * @return string
     */
    public function getTiktokId()
    {
        return parent::getData(self::TIKTOK_ID);
    }

    /**
     * Set TwActive
     *
     * @param int $twActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setTwActive($twActive)
    {
        return $this->setData(self::TW_ACTIVE, $twActive);
    }

    /**
     * Get TwActive
     *
     * @return int
     */
    public function getTwActive()
    {
        return parent::getData(self::TW_ACTIVE);
    }

    /**
     * Set FbActive
     *
     * @param int $fbActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setFbActive($fbActive)
    {
        return $this->setData(self::FB_ACTIVE, $fbActive);
    }

    /**
     * Get FbActive
     *
     * @return int
     */
    public function getFbActive()
    {
        return parent::getData(self::FB_ACTIVE);
    }

    /**
     * Set YoutubeActive
     *
     * @param int $youtubeActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setYoutubeActive($youtubeActive)
    {
        return $this->setData(self::YOUTUBE_ACTIVE, $youtubeActive);
    }

    /**
     * Get YoutubeActive
     *
     * @return int
     */
    public function getYoutubeActive()
    {
        return parent::getData(self::YOUTUBE_ACTIVE);
    }

    /**
     * Set VimeoActive
     *
     * @param int $vimeoActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setVimeoActive($vimeoActive)
    {
        return $this->setData(self::VIMEO_ACTIVE, $vimeoActive);
    }

    /**
     * Get VimeoActive
     *
     * @return int
     */
    public function getVimeoActive()
    {
        return parent::getData(self::VIMEO_ACTIVE);
    }

    /**
     * Set InstagramActive
     *
     * @param int $instagramActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setInstagramActive($instagramActive)
    {
        return $this->setData(self::INSTAGRAM_ACTIVE, $instagramActive);
    }

    /**
     * Get InstagramActive
     *
     * @return int
     */
    public function getInstagramActive()
    {
        return parent::getData(self::INSTAGRAM_ACTIVE);
    }

    /**
     * Set PinterestActive
     *
     * @param int $pinterestActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setPinterestActive($pinterestActive)
    {
        return $this->setData(self::PINTEREST_ACTIVE, $pinterestActive);
    }

    /**
     * Get PinterestActive
     *
     * @return int
     */
    public function getPinterestActive()
    {
        return parent::getData(self::PINTEREST_ACTIVE);
    }

    /**
     * Set MoleskineActive
     *
     * @param int $moleskineActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setMoleskineActive($moleskineActive)
    {
        return $this->setData(self::MOLESKINE_ACTIVE, $moleskineActive);
    }

    /**
     * Get MoleskineActive
     *
     * @return int
     */
    public function getMoleskineActive()
    {
        return parent::getData(self::MOLESKINE_ACTIVE);
    }

    /**
     * Set TiktokActive
     *
     * @param int $tiktokActive
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setTiktokActive($tiktokActive)
    {
        return $this->setData(self::TIKTOK_ACTIVE, $tiktokActive);
    }

    /**
     * Get TiktokActive
     *
     * @return int
     */
    public function getTiktokActive()
    {
        return parent::getData(self::TIKTOK_ACTIVE);
    }

    /**
     * Set OthersInfo
     *
     * @param string $othersInfo
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setOthersInfo($othersInfo)
    {
        return $this->setData(self::OTHERS_INFO, $othersInfo);
    }

    /**
     * Get OthersInfo
     *
     * @return string
     */
    public function getOthersInfo()
    {
        return parent::getData(self::OTHERS_INFO);
    }

    /**
     * Set BannerPic
     *
     * @param string $bannerPic
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setBannerPic($bannerPic)
    {
        return $this->setData(self::BANNER_PIC, $bannerPic);
    }

    /**
     * Get BannerPic
     *
     * @return string
     */
    public function getBannerPic()
    {
        return parent::getData(self::BANNER_PIC);
    }

    /**
     * Set ShopUrl
     *
     * @param string $shopUrl
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setShopUrl($shopUrl)
    {
        return $this->setData(self::SHOP_URL, $shopUrl);
    }

    /**
     * Get ShopUrl
     *
     * @return string
     */
    public function getShopUrl()
    {
        return parent::getData(self::SHOP_URL);
    }

    /**
     * Set ShopTitle
     *
     * @param string $shopTitle
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setShopTitle($shopTitle)
    {
        return $this->setData(self::SHOP_TITLE, $shopTitle);
    }

    /**
     * Get ShopTitle
     *
     * @return string
     */
    public function getShopTitle()
    {
        return parent::getData(self::SHOP_TITLE);
    }

    /**
     * Set LogoPic
     *
     * @param string $logoPic
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setLogoPic($logoPic)
    {
        return $this->setData(self::LOGO_PIC, $logoPic);
    }

    /**
     * Get LogoPic
     *
     * @return string
     */
    public function getLogoPic()
    {
        return parent::getData(self::LOGO_PIC);
    }

    /**
     * Set CompanyLocality
     *
     * @param string $companyLocality
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setCompanyLocality($companyLocality)
    {
        return $this->setData(self::COMPANY_LOCALITY, $companyLocality);
    }

    /**
     * Get CompanyLocality
     *
     * @return string
     */
    public function getCompanyLocality()
    {
        return parent::getData(self::COMPANY_LOCALITY);
    }

    /**
     * Set CountryPic
     *
     * @param string $countryPic
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setCountryPic($countryPic)
    {
        return $this->setData(self::COUNTRY_PIC, $countryPic);
    }

    /**
     * Get CountryPic
     *
     * @return string
     */
    public function getCountryPic()
    {
        return parent::getData(self::COUNTRY_PIC);
    }

    /**
     * Set CompanyDescription
     *
     * @param string $companyDescription
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setCompanyDescription($companyDescription)
    {
        return $this->setData(self::COMPANY_DESCRIPTION, $companyDescription);
    }

    /**
     * Get CompanyDescription
     *
     * @return string
     */
    public function getCompanyDescription()
    {
        return parent::getData(self::COMPANY_DESCRIPTION);
    }

    /**
     * Set MetaKeyword
     *
     * @param string $metaKeyword
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setMetaKeyword($metaKeyword)
    {
        return $this->setData(self::META_KEYWORD, $metaKeyword);
    }

    /**
     * Get MetaKeyword
     *
     * @return string
     */
    public function getMetaKeyword()
    {
        return parent::getData(self::META_KEYWORD);
    }

    /**
     * Set MetaDescription
     *
     * @param string $metaDescription
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Get MetaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return parent::getData(self::META_DESCRIPTION);
    }

    /**
     * Set BackgroundWidth
     *
     * @param string $backgroundWidth
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setBackgroundWidth($backgroundWidth)
    {
        return $this->setData(self::BACKGROUND_WIDTH, $backgroundWidth);
    }

    /**
     * Get BackgroundWidth
     *
     * @return string
     */
    public function getBackgroundWidth()
    {
        return parent::getData(self::BACKGROUND_WIDTH);
    }

    /**
     * Set StoreId
     *
     * @param int $storeId
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get StoreId
     *
     * @return int
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * Set ContactNumber
     *
     * @param string $contactNumber
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setContactNumber($contactNumber)
    {
        return $this->setData(self::CONTACT_NUMBER, $contactNumber);
    }

    /**
     * Get ContactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return parent::getData(self::CONTACT_NUMBER);
    }

    /**
     * Set ReturnPolicy
     *
     * @param string $returnPolicy
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setReturnPolicy($returnPolicy)
    {
        return $this->setData(self::RETURN_POLICY, $returnPolicy);
    }

    /**
     * Get ReturnPolicy
     *
     * @return string
     */
    public function getReturnPolicy()
    {
        return parent::getData(self::RETURN_POLICY);
    }

    /**
     * Set ShippingPolicy
     *
     * @param string $shippingPolicy
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setShippingPolicy($shippingPolicy)
    {
        return $this->setData(self::SHIPPING_POLICY, $shippingPolicy);
    }

    /**
     * Get ShippingPolicy
     *
     * @return string
     */
    public function getShippingPolicy()
    {
        return parent::getData(self::SHIPPING_POLICY);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set AdminNotification
     *
     * @param int $adminNotification
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setAdminNotification($adminNotification)
    {
        return $this->setData(self::ADMIN_NOTIFICATION, $adminNotification);
    }

    /**
     * Get AdminNotification
     *
     * @return int
     */
    public function getAdminNotification()
    {
        return parent::getData(self::ADMIN_NOTIFICATION);
    }

    /**
     * Set PrivacyPolicy
     *
     * @param string $privacyPolicy
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setPrivacyPolicy($privacyPolicy)
    {
        return $this->setData(self::PRIVACY_POLICY, $privacyPolicy);
    }

    /**
     * Get PrivacyPolicy
     *
     * @return string
     */
    public function getPrivacyPolicy()
    {
        return parent::getData(self::PRIVACY_POLICY);
    }

    /**
     * Set AllowedCategories
     *
     * @param string $allowedCategories
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setAllowedCategories($allowedCategories)
    {
        return $this->setData(self::ALLOWED_CATEGORIES, $allowedCategories);
    }

    /**
     * Get AllowedCategories
     *
     * @return string
     */
    public function getAllowedCategories()
    {
        return parent::getData(self::ALLOWED_CATEGORIES);
    }

    /**
     * Set AllowedAttributesetIds
     *
     * @param string $allowedAttributesetIds
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setAllowedAttributesetIds($allowedAttributesetIds)
    {
        return $this->setData(self::ALLOWED_ATTRIBUTESET_IDS, $allowedAttributesetIds);
    }

    /**
     * Get AllowedAttributesetIds
     *
     * @return string
     */
    public function getAllowedAttributesetIds()
    {
        return parent::getData(self::ALLOWED_ATTRIBUTESET_IDS);
    }

    /**
     * Set IsSeparatePanel
     *
     * @param int $isSeparatePanel
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setIsSeparatePanel($isSeparatePanel)
    {
        return $this->setData(self::IS_SEPARATE_PANEL, $isSeparatePanel);
    }

    /**
     * Get IsSeparatePanel
     *
     * @return int
     */
    public function getIsSeparatePanel()
    {
        return parent::getData(self::IS_SEPARATE_PANEL);
    }
    /**
     * Set LowStockQuantity
     *
     * @param int $lowStockQuantity
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setLowStockQuantity($lowStockQuantity)
    {
        return $this->setData(self::LOW_STOCK_QUANTITY, $lowStockQuantity);
    }

    /**
     * Get LowStockQuantity
     *
     * @return int
     */
    public function getLowStockQuantity()
    {
        return parent::getData(self::LOW_STOCK_QUANTITY);
    }
    /**
     * Set fulfilment Image
     *
     * @param int $fulfilmentImage
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setFulfilmentImage($fulfilmentImage)
    {
        return $this->setData(self::FULFILMENT_IMAGE, $fulfilmentImage);
    }

    /**
     * Get fulfilment Image
     *
     * @return int
     */
    public function getFulfilmentImage()
    {
        return parent::getData(self::FULFILMENT_IMAGE);
    }
    /**
     * Set fulfilment text
     *
     * @param int $fulfilmentText
     * @return Webkul\Marketplace\Model\SellerInterface
     */
    public function setFulfilmentText($fulfilmentText)
    {
        return $this->setData(self::FULFILMENT_TEXT, $fulfilmentText);
    }

    /**
     * Get fulfilment text
     *
     * @return int
     */
    public function getFulfilmentText()
    {
        return parent::getData(self::FULFILMENT_TEXT);
    }
}
