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

class Invoiceitems extends \Magento\Framework\View\Element\Template
{
    /**
     * Get invoice data
     *
     * @return array
     */
    public function getInvoiceData()
    {
        $mailData = $this->getData("mail_data");
        return $mailData;
    }
}
