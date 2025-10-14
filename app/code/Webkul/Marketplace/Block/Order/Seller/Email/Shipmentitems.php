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

class Shipmentitems extends \Magento\Framework\View\Element\Template
{
    /**
     * Get order data
     *
     * @return array
     */
    public function getShipmentData()
    {
        $shipmentData = $this->getData("shipment_data");
        return $shipmentData;
    }
}
