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

class Creditmemo extends \Magento\Framework\View\Element\Template
{
    /**
     * Framework class for Core Registry
     *
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        array $data = []
    ) {
        $this->mpHelper = $mpHelper;
        parent::__construct($context, $data);
    }
    /**
     * Get credit memo data
     *
     * @return array
     */
    public function getCreditMemoData()
    {
        $mailData = $this->getData("mail_data");
        return $mailData;
    }
    /**
     * Get Formatted price
     *
     * @param int $price
     * @return string
     */
    public function getFormatedPrice($price = 0)
    {
        return $this->mpHelper->getFormatedPrice($price);
    }
}
