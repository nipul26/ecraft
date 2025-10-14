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
namespace Webkul\Marketplace\Model\Config\Source;

use Webkul\Marketplace\Helper\Data;

/**
 * Used in creating options for getting product type value.
 */
class Producttype
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * Construct
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->helper->getSellerAllowedProduct();
    }
}
