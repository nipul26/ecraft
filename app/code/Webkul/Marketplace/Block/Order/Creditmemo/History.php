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

namespace Webkul\Marketplace\Block\Order\Creditmemo;

/**
 * Webkul Marketplace Order Creditmemo History Block.
 */
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order\Creditmemo;
use Webkul\Marketplace\Model\CreditMemoListFactory;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $Customer;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $Order;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\Marketplace\Helper\Orders
     */
    protected $ordersHelper;

    /**
     * @var Creditmemo
     */
    protected $creditmemoModel;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CreditMemoListFactory
     */
    protected $creditMemoListFactory;

    /**
     * @var \Webkul\Marketplace\Helper\Orders\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Order $order
     * @param Customer $customer
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Marketplace\Helper\Orders $ordersHelper
     * @param Creditmemo $creditmemoModel
     * @param CreditMemoListFactory $creditMemoListFactory
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Order $order,
        Customer $customer,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Orders $ordersHelper,
        Creditmemo $creditmemoModel,
        CreditMemoListFactory $creditMemoListFactory,
        \Webkul\Marketplace\Helper\Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->Customer = $customer;
        $this->Order = $order;
        $this->_customerSession = $customerSession;
        $this->ordersHelper = $ordersHelper;
        $this->creditmemoModel = $creditmemoModel;
        $this->creditMemoListFactory = $creditMemoListFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance.
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
     * Set title
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders'));
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_customerSession->getCustomerId();
    }

    /**
     * Get collection
     *
     * @return bool|\Magento\Sales\Model\Order\Creditmemo\Collection
     */
    public function getCollection()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $tracking = $this->ordersHelper->getOrderinfo($orderId);
        $creditmemo = [];
        if ($tracking) {
            $creditmemoIds = [];
            $creditmemoIds = explode(',', $tracking->getCreditmemoId());
            $creditmemo = $this->creditmemoModel->getCollection()
                          ->addFieldToFilter(
                              'entity_id',
                              ['in' => $creditmemoIds]
                          );
        }
        return $creditmemo;
    }

    /**
     * Set collection
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'marketplace.order.creditmemo.pager'
            )->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * Get Pager
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl(); // Give the current url of recently viewed page
    }

    /**
     * Get Creditmemo amount
     *
     * @param int $orderId
     * @return float
     */
    public function getAmount($orderId)
    {
        $creditmemoAmount = 0;
        $creditmemo = $this->creditMemoListFactory->create()->getCollection()
        ->addFieldToFilter("order_id", $orderId)
        ->addFieldToFilter('seller_id', $this->helper->getCustomerId())
        ->getFirstItem();
        $creditmemoAmount = $creditmemo->getTotalAmount();
        $discount = $creditmemo->getCouponAmount() ?? 0;
        $sellerShippingAmount = $creditmemo->getShippingCharges() ?? 0;
        $sellerTax = $creditmemo->getTotalTax() ?? 0;
        if ($discount) {
            $creditmemoAmount -= $discount;
        }
        if ($sellerShippingAmount) {
            $creditmemoAmount += $sellerShippingAmount;
        }
        if ($sellerTax) {
            $creditmemoAmount += $sellerTax;
        }
        return $creditmemoAmount;
    }
}
