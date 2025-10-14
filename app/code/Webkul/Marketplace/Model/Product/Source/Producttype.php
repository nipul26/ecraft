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
namespace Webkul\Marketplace\Model\Product\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Webkul\Marketplace\Helper\Data;

/**
 * Class Producttype is used tp get the product type options
 */
class Producttype implements OptionSourceInterface
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
