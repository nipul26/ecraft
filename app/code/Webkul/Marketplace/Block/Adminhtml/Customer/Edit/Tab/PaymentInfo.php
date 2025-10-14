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

namespace Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Tab;

class PaymentInfo extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public const COMM_TEMPLATE = 'Webkul_Marketplace::customer/paymentinfo.phtml';
    /**
     * @var \Webkul\Marketplace\Block\Adminhtml\Customer\Edit
     */
    protected $customerEdit;
    /**
     * Construct
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Webkul\Marketplace\Block\Adminhtml\Customer\Edit $customerEdit
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Webkul\Marketplace\Block\Adminhtml\Customer\Edit $customerEdit,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->customerEdit = $customerEdit;
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::COMM_TEMPLATE);
        }

        return $this;
    }

    /**
     * Get payment info
     *
     * @return string
     */
    public function getPaymentInfo()
    {
        $paymentInfo = $this->customerEdit->getPaymentMode();
        return $paymentInfo;
    }
}
