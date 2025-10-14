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
namespace Webkul\Marketplace\Model\Attribute\Backend;

use Webkul\Marketplace\Helper\Data;

class SellerIdAttribute extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public const SELLER_PRODUCT_ATTRIBUTE= 'seller_id';
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }
    /**
     * Get seller list
     *
     * @return array
     */
    public function getAllOptions()
    {
        $sellerData = $this->helper->getSellerList();
        foreach ($sellerData as $key => $seller) {
            $currentSeller = $this->helper->getSellerCollectionObj($seller["value"])
            ->getFirstItem();
            $shop = $currentSeller->getShopTitle()?:$currentSeller->getShopUrl();
            $sellerData[$key]["label"]= $shop;
        }
        $sellerData[0] = [
            'value' => "0",
            'label' => $this->helper->getAdminFilterDisplayName()
        ];
        return $sellerData;
    }
}
