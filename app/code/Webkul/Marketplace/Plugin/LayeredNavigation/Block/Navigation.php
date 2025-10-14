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
namespace Webkul\Marketplace\Plugin\LayeredNavigation\Block;

use Magento\LayeredNavigation\Block\Navigation as BlockNavigation;

class Navigation
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Webkul\Marketplace\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Webkul\Marketplace\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }
    /**
     * Remove seller filter from collection shop
     *
     * @param BlockNavigation $subject
     * @param array $result
     * @return array $result
     */
    public function afterGetFilters(BlockNavigation $subject, $result)
    {
        $isSellerLayered = $this->helper->allowSellerFilter();
        $actionName = $this->request->getFullActionName();
        $profileType = $this->helper->getSellerProfileLayout();
        $restrictedAction = ["marketplace_seller_collection"];
        if ($profileType == 2) {
            $restrictedAction[] = "marketplace_seller_profile";
        }
        if (in_array($actionName, $restrictedAction) || $isSellerLayered == false) {
            $filters = $result;
            $availableFilters = [];
            foreach ($filters as $filter) {
                if ($filter->getName() == "Seller") {
                    continue;
                }
                $availableFilters[] = $filter;
            }
            return $availableFilters;
        }
        return $result;
    }
}
