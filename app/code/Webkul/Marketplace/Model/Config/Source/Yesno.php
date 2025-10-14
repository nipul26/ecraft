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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status is used tp get the seller available status
 */
class Yesno implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Yes')],
        ];
        return $data;
    }
}
