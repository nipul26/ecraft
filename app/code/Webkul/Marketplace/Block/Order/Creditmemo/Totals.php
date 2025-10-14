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

use Webkul\Marketplace\Model\ResourceModel\Saleslist\Collection;
use Webkul\Marketplace\Model\CreditMemoListFactory;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var Collection
     */
    protected $orderCollection;

    /**
     * Associated array of seller order totals
     *
     * @var array
     */
    protected $_totals;

    /**
     * @var CreditMemoListFactory
     */
    protected $creditMemoListFactory;

    /**
     * @param \Webkul\Marketplace\Helper\Data                   $helper
     * @param \Magento\Framework\Registry                       $coreRegistry
     * @param Collection                                        $orderCollection
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param CreditMemoListFactory                             $creditMemoListFactory
     * @param array                                             $data
     */
    public function __construct(
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        Collection $orderCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        CreditMemoListFactory $creditMemoListFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->orderCollection = $orderCollection;
        $this->creditMemoListFactory = $creditMemoListFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $data
        );
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Retrieve current creditmemo model instance.
     *
     * @return array
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Init totals
     *
     * @return void
     */
    protected function _initTotals()
    {
        $this->_totals = [];
        $creditmemo = $this->getSource();
        $currencyRate = $creditmemo['base_to_order_rate'];
        $order = $this->getOrder();
        $this->_totals = [];
        $sellerId = $this->helper->getCustomerId();
        $creditMemo = $this->creditMemoListFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('order_id', $order->getId())
                        ->addFieldToFilter('seller_id', $sellerId)
                        ->getFirstItem();
        $subTotal = $creditMemo->getTotalAmount();
        $tax = $creditMemo->getTotalTax();

        $mpOrderData = $this->getMpOrderData();
        $adminSubTotal = $grandTotal = $creditMemo->getTotalCommission();
        $vendorSubTotal = $this->getVendorSubTotal($creditMemo);
        $discountAmount = !empty($mpOrderData[0]["coupon_amount"]) ? $mpOrderData[0]["coupon_amount"] : 0;
        $grandTotal += $vendorSubTotal;
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            [
                'code' => 'subtotal',
                'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $subTotal),
                'label' => __('Subtotal')
            ]
        );

        $this->_totals['discount'] = new \Magento\Framework\DataObject(
            [
                'code' => 'discount',
                'value' => $this->helper->getCurrentCurrencyPrice(
                    $currencyRate,
                    $discountAmount
                ),
                'label' => __('Discount')
            ]
        );

        $this->_totals['tax'] = new \Magento\Framework\DataObject(
            [
                'code' => 'tax',
                'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $tax),
                'label' => __('Total Tax')
            ]
        );

        $this->_totals['shipping'] = new \Magento\Framework\DataObject(
            [
                'code' => 'shipping',
                'value' => $this->helper->getCurrentCurrencyPrice(
                    $currencyRate,
                    $creditMemo->getShippingCharges()
                ),
                'label' => __('Shipping & Handling')
            ]
        );

        $this->_totals['adjustment_positive'] = new \Magento\Framework\DataObject(
            [
                'code' => 'adjustment_positive',
                'value' => $this->helper->getCurrentCurrencyPrice(
                    $currencyRate,
                    $creditmemo->getAdjustmentPositive()
                ),
                'label' => __('Adjustment Refund')
            ]
        );

        $this->_totals['adjustment_negative'] = new \Magento\Framework\DataObject(
            [
                'code' => 'adjustment_negative',
                'value' => $this->helper->getCurrentCurrencyPrice(
                    $currencyRate,
                    $creditmemo->getAdjustmentNegative()
                ),
                'label' => __('Adjustment Fee')
            ]
        );

        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(
            [
                'code' => 'grand_total',
                'strong' => 1,
                'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $grandTotal),
                'label' => __('Total Ordered Amount')
            ]
        );
        if ($order->isCurrencyDifferent()) {
            $this->_totals['base_ordered_total'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'base_ordered_total',
                    'is_base' => 1,
                    'strong' => 1,
                    'value' => $order->formatBasePrice($grandTotal),
                    'label' => __('Total Ordered Amount(in base currency)')
                ]
            );
        }
        $this->_totals['vendor_total'] = new \Magento\Framework\DataObject(
            [
                'code' => 'vendor_total',
                'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $vendorSubTotal),
                'label' => __('Total Vendor Amount')
            ]
        );

        if ($order->isCurrencyDifferent()) {
            $this->_totals['base_vendor_total'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'base_vendor_total',
                    'is_base' => 1,
                    'value' => $vendorSubTotal,
                    'label' => __('Total Vendor Amount(in base currency)')
                ]
            );
        }

        $this->_totals['admin_commission'] = new \Magento\Framework\DataObject(
            [
                'code' => 'admin_commission',
                'value' => $this->helper->getCurrentCurrencyPrice($currencyRate, $adminSubTotal),
                'label' => __('Total Admin Commission')
            ]
        );

        if ($order->isCurrencyDifferent()) {
            $this->_totals['base_admin_commission'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'base_admin_commission',
                    'is_base' => 1,
                    'value' => $adminSubTotal,
                    'label' => __('Total Admin Commission(in base currency)')
                ]
            );
        }
    }

    /**
     * Get seller order totals array
     *
     * @param array|null $area
     * @return array
     */
    public function getOrderTotals($area = null)
    {
        $orderTotals = [];
        if ($area === null) {
            $orderTotals = $this->_totals;
        } else {
            $area = (string)$area;
            foreach ($this->_totals as $orderTotal) {
                $totalArea = (string)$orderTotal->getArea();
                if ($totalArea == $area) {
                    $this->_totals[] = $orderTotal;
                }
            }
        }
        return $orderTotals;
    }

    /**
     * Get label propertis
     *
     * @return string
     */
    public function getLabelProperties()
    {
        $paymentCode = '';
        if ($this->_order->getPayment()) {
            $paymentCode = $this->getOrder()->getPayment()->getMethod();
        }
        if ($paymentCode == 'mpcashondelivery') {
            return 'colspan="7" class="mark"';
        }
        return 'colspan="6" class="mark"';
    }
    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getMpOrderData()
    {
        $collection = $this->orderCollection
        ->addFieldToFilter(
            'main_table.order_id',
            $this->getOrder()->getId()
        )->addFieldToFilter(
            'main_table.seller_id',
            $this->helper->getCustomerId()
        )->getSellerOrderTotals();
        return $collection;
    }
    /**
     * Format total value based on order currency
     *
     * @param   \Magento\Framework\DataObject $total
     * @return  string
     */
    public function formatValue($total)
    {
        if ($total->getIsBase()) {
            if (!$total->getIsFormated()) {
                return $this->getOrder()->formatBasePrice($total->getValue());
            }
        } else {
            if (!$total->getIsFormated()) {
                return $this->getOrder()->formatPrice($total->getValue());
            }
        }
        return $total->getValue();
    }
    /**
     * Get vendor subtotal
     *
     * @param array $creditMemo
     * @return float
     */
    public function getVendorSubTotal($creditMemo)
    {
        $vendorTotal = $creditMemo->getActualSellerAmount();
        if (!empty($creditMemo->getShippingCharges())) {
            $vendorTotal += $creditMemo->getShippingCharges();
        }
        if (!empty($creditMemo->getTotalTax())) {
            $vendorTotal += $creditMemo->getTotalTax();
        }
        if (!empty($this->getSource()->getAdjustmentNegative())) {
            $vendorTotal -= $this->getSource()->getAdjustmentNegative();
        }
        return $vendorTotal;
    }
}
