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

class Commission extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    public const COMM_TEMPLATE = 'Webkul_Marketplace::customer/commission.phtml';
    /**
     * @var \Webkul\Marketplace\Block\Adminhtml\Customer\Edit
     */
    protected $customerEdit;
    /**
     * Initialization
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Webkul\Marketplace\Block\Adminhtml\Customer\Edit $customerEdit
     * @param \Magento\Framework\Pricing\Helper\Data|null $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Webkul\Marketplace\Block\Adminhtml\Customer\Edit $customerEdit,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = [],
    ) {
        $this->_coreRegistry = $registry;
        $this->customerEdit = $customerEdit;
        $this->pricingHelper = $pricingHelper;
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
     * Get commission
     *
     * @return array
     */
    public function getCommission()
    {
        $rowcom = $this->customerEdit->getSalesPartnerCollection()
                    ->addFieldToFilter(
                        'commission_status',
                        1
                    )
                    ->addFieldToSelect(
                        'commission_rate'
                    )
                    ->getFirstItem()
                    ->getCommissionRate();
        if ($rowcom === null) {
            $rowcom = $this->customerEdit->getConfigCommissionRate();
        }
        $tsale = 0;
        $tcomm = 0;
        $tact = 0;
        $collectionSales = $this->customerEdit->getSalesListCollection();
        foreach ($collectionSales as $key) {
            $tsale += $key->getTotalAmount();
            $tcomm += $key->getTotalCommission();
            $tact += $key->getActualSellerAmount();
        }

        return [
            'total_sale' => $tsale,
            'total_comm' => $tcomm,
            'actual_seller_amt' => $tact,
            'current_val' => $rowcom,
        ];
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        $currencySymbol = $this->customerEdit->getCurrencySymbol();

        return $currencySymbol;
    }

    /**
     * Get currencyprice
     *
     * @param integer $price
     * @return float
     */
    public function getCurrencyPrice($price = 0)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}
