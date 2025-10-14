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
namespace Webkul\Marketplace\Api\Data;

/**
 * Marketplace Seller interface.
 * @api
 */
interface SellerInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const IS_SELLER = 'is_seller';

    public const SELLER_ID = 'seller_id';

    public const PAYMENT_SOURCE = 'payment_source';

    public const TWITTER_ID = 'twitter_id';

    public const FACEBOOK_ID = 'facebook_id';

    public const YOUTUBE_ID = 'youtube_id';

    public const VIMEO_ID = 'vimeo_id';

    public const INSTAGRAM_ID = 'instagram_id';

    public const PINTEREST_ID = 'pinterest_id';

    public const MOLESKINE_ID = 'moleskine_id';

    public const TIKTOK_ID = 'tiktok_id';

    public const TW_ACTIVE = 'tw_active';

    public const FB_ACTIVE = 'fb_active';

    public const YOUTUBE_ACTIVE = 'youtube_active';

    public const VIMEO_ACTIVE = 'vimeo_active';

    public const INSTAGRAM_ACTIVE = 'instagram_active';

    public const PINTEREST_ACTIVE = 'pinterest_active';

    public const MOLESKINE_ACTIVE = 'moleskine_active';

    public const TIKTOK_ACTIVE = 'tiktok_active';

    public const OTHERS_INFO = 'others_info';

    public const BANNER_PIC = 'banner_pic';

    public const SHOP_URL = 'shop_url';

    public const SHOP_TITLE = 'shop_title';

    public const LOGO_PIC = 'logo_pic';

    public const COMPANY_LOCALITY = 'company_locality';

    public const COUNTRY_PIC = 'country_pic';

    public const COMPANY_DESCRIPTION = 'company_description';

    public const META_KEYWORD = 'meta_keyword';

    public const META_DESCRIPTION = 'meta_description';

    public const BACKGROUND_WIDTH = 'background_width';

    public const STORE_ID = 'store_id';

    public const CONTACT_NUMBER = 'contact_number';

    public const RETURN_POLICY = 'return_policy';

    public const SHIPPING_POLICY = 'shipping_policy';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const ADMIN_NOTIFICATION = 'admin_notification';

    public const PRIVACY_POLICY = 'privacy_policy';

    public const ALLOWED_CATEGORIES = 'allowed_categories';

    public const ALLOWED_ATTRIBUTESET_IDS = 'allowed_attributeset_ids';

    public const IS_SEPARATE_PANEL = 'is_separate_panel';

    public const LOW_STOCK_QUANTITY = 'low_stock_quantity';

    public const FULFILMENT_IMAGE = 'fulfilment_image';

    public const FULFILMENT_TEXT = 'fulfilment_text';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setId($id);
    /**
     * Set IsSeller
     *
     * @param int $isSeller
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setIsSeller($isSeller);
    /**
     * Get IsSeller
     *
     * @return int
     */
    public function getIsSeller();
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set PaymentSource
     *
     * @param string $paymentSource
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setPaymentSource($paymentSource);
    /**
     * Get PaymentSource
     *
     * @return string
     */
    public function getPaymentSource();
    /**
     * Set TwitterId
     *
     * @param string $twitterId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setTwitterId($twitterId);
    /**
     * Get TwitterId
     *
     * @return string
     */
    public function getTwitterId();
    /**
     * Set FacebookId
     *
     * @param string $facebookId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setFacebookId($facebookId);
    /**
     * Get FacebookId
     *
     * @return string
     */
    public function getFacebookId();

    /**
     * Set YoutubeId
     *
     * @param string $youtubeId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setYoutubeId($youtubeId);
    /**
     * Get YoutubeId
     *
     * @return string
     */
    public function getYoutubeId();
    /**
     * Set VimeoId
     *
     * @param string $vimeoId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setVimeoId($vimeoId);
    /**
     * Get VimeoId
     *
     * @return string
     */
    public function getVimeoId();
    /**
     * Set InstagramId
     *
     * @param string $instagramId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setInstagramId($instagramId);
    /**
     * Get InstagramId
     *
     * @return string
     */
    public function getInstagramId();
    /**
     * Set PinterestId
     *
     * @param string $pinterestId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setPinterestId($pinterestId);
    /**
     * Get PinterestId
     *
     * @return string
     */
    public function getPinterestId();
    /**
     * Set MoleskineId
     *
     * @param string $moleskineId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setMoleskineId($moleskineId);
    /**
     * Get MoleskineId
     *
     * @return string
     */
    public function getMoleskineId();
    /**
     * Set TiktokId
     *
     * @param string $tiktokId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setTiktokId($tiktokId);
    /**
     * Get TiktokId
     *
     * @return string
     */
    public function getTiktokId();
    /**
     * Set TwActive
     *
     * @param int $twActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setTwActive($twActive);
    /**
     * Get TwActive
     *
     * @return int
     */
    public function getTwActive();
    /**
     * Set FbActive
     *
     * @param int $fbActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setFbActive($fbActive);
    /**
     * Get FbActive
     *
     * @return int
     */
    public function getFbActive();

    /**
     * Set YoutubeActive
     *
     * @param int $youtubeActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setYoutubeActive($youtubeActive);
    /**
     * Get YoutubeActive
     *
     * @return int
     */
    public function getYoutubeActive();
    /**
     * Set VimeoActive
     *
     * @param int $vimeoActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setVimeoActive($vimeoActive);
    /**
     * Get VimeoActive
     *
     * @return int
     */
    public function getVimeoActive();
    /**
     * Set InstagramActive
     *
     * @param int $instagramActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setInstagramActive($instagramActive);
    /**
     * Get InstagramActive
     *
     * @return int
     */
    public function getInstagramActive();
    /**
     * Set PinterestActive
     *
     * @param int $pinterestActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setPinterestActive($pinterestActive);
    /**
     * Get PinterestActive
     *
     * @return int
     */
    public function getPinterestActive();
    /**
     * Set MoleskineActive
     *
     * @param int $moleskineActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setMoleskineActive($moleskineActive);
    /**
     * Get MoleskineActive
     *
     * @return int
     */
    public function getMoleskineActive();
    /**
     * Set TiktokActive
     *
     * @param int $tiktokActive
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setTiktokActive($tiktokActive);
    /**
     * Get TiktokActive
     *
     * @return int
     */
    public function getTiktokActive();
    /**
     * Set OthersInfo
     *
     * @param string $othersInfo
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setOthersInfo($othersInfo);
    /**
     * Get OthersInfo
     *
     * @return string
     */
    public function getOthersInfo();
    /**
     * Set BannerPic
     *
     * @param string $bannerPic
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setBannerPic($bannerPic);
    /**
     * Get BannerPic
     *
     * @return string
     */
    public function getBannerPic();
    /**
     * Set ShopUrl
     *
     * @param string $shopUrl
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setShopUrl($shopUrl);
    /**
     * Get ShopUrl
     *
     * @return string
     */
    public function getShopUrl();
    /**
     * Set ShopTitle
     *
     * @param string $shopTitle
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setShopTitle($shopTitle);
    /**
     * Get ShopTitle
     *
     * @return string
     */
    public function getShopTitle();
    /**
     * Set LogoPic
     *
     * @param string $logoPic
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setLogoPic($logoPic);
    /**
     * Get LogoPic
     *
     * @return string
     */
    public function getLogoPic();
    /**
     * Set CompanyLocality
     *
     * @param string $companyLocality
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setCompanyLocality($companyLocality);
    /**
     * Get CompanyLocality
     *
     * @return string
     */
    public function getCompanyLocality();
    /**
     * Set CountryPic
     *
     * @param string $countryPic
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setCountryPic($countryPic);
    /**
     * Get CountryPic
     *
     * @return string
     */
    public function getCountryPic();
    /**
     * Set CompanyDescription
     *
     * @param string $companyDescription
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setCompanyDescription($companyDescription);
    /**
     * Get CompanyDescription
     *
     * @return string
     */
    public function getCompanyDescription();
    /**
     * Set MetaKeyword
     *
     * @param string $metaKeyword
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setMetaKeyword($metaKeyword);
    /**
     * Get MetaKeyword
     *
     * @return string
     */
    public function getMetaKeyword();
    /**
     * Set MetaDescription
     *
     * @param string $metaDescription
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setMetaDescription($metaDescription);
    /**
     * Get MetaDescription
     *
     * @return string
     */
    public function getMetaDescription();
    /**
     * Set BackgroundWidth
     *
     * @param string $backgroundWidth
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setBackgroundWidth($backgroundWidth);
    /**
     * Get BackgroundWidth
     *
     * @return string
     */
    public function getBackgroundWidth();
    /**
     * Set StoreId
     *
     * @param int $storeId
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setStoreId($storeId);
    /**
     * Get StoreId
     *
     * @return int
     */
    public function getStoreId();
    /**
     * Set ContactNumber
     *
     * @param string $contactNumber
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setContactNumber($contactNumber);
    /**
     * Get ContactNumber
     *
     * @return string
     */
    public function getContactNumber();
    /**
     * Set ReturnPolicy
     *
     * @param string $returnPolicy
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setReturnPolicy($returnPolicy);
    /**
     * Get ReturnPolicy
     *
     * @return string
     */
    public function getReturnPolicy();
    /**
     * Set ShippingPolicy
     *
     * @param string $shippingPolicy
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setShippingPolicy($shippingPolicy);
    /**
     * Get ShippingPolicy
     *
     * @return string
     */
    public function getShippingPolicy();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set AdminNotification
     *
     * @param int $adminNotification
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setAdminNotification($adminNotification);
    /**
     * Get AdminNotification
     *
     * @return int
     */
    public function getAdminNotification();
    /**
     * Set PrivacyPolicy
     *
     * @param string $privacyPolicy
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setPrivacyPolicy($privacyPolicy);
    /**
     * Get PrivacyPolicy
     *
     * @return string
     */
    public function getPrivacyPolicy();
    /**
     * Set AllowedCategories
     *
     * @param string $allowedCategories
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setAllowedCategories($allowedCategories);
    /**
     * Get AllowedCategories
     *
     * @return string
     */
    public function getAllowedCategories();
    /**
     * Set AllowedAttributesetIds
     *
     * @param string $allowedAttributesetIds
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setAllowedAttributesetIds($allowedAttributesetIds);
    /**
     * Get AllowedAttributesetIds
     *
     * @return string
     */
    public function getAllowedAttributesetIds();
    /**
     * Set IsSeparatePanel
     *
     * @param int $isSeparatePanel
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setIsSeparatePanel($isSeparatePanel);
    /**
     * Get IsSeparatePanel
     *
     * @return int
     */
    public function getIsSeparatePanel();
    /**
     * Set low stock quantity
     *
     * @param int $lowStockQuantity
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setLowStockQuantity($lowStockQuantity);
    /**
     * Get low stock quantity
     *
     * @return int
     */
    public function getLowStockQuantity();
    /**
     * Set fulfilment image
     *
     * @param int $fulfilmentImage
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setFulfilmentImage($fulfilmentImage);
    /**
     * Get fulfilment image
     *
     * @return string
     */
    public function getFulfilmentImage();
    /**
     * Set fulfilment text
     *
     * @param int $fulfilmentText
     * @return Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setFulfilmentText($fulfilmentText);
    /**
     * Get fulfilment text
     *
     * @return string
     */
    public function getFulfilmentText();
}
