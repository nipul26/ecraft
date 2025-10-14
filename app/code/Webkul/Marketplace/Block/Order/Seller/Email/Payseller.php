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
namespace Webkul\Marketplace\Block\Order\Seller\Email;

class Payseller extends \Magento\Framework\View\Element\Template
{
    /**
     * Get order items
     */
    public function getOrderItems()
    {
        return $this->getData('items');
    }
}
