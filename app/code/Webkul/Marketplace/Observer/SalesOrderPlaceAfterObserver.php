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

namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Sales\Model\Order\AddressFactory;
use Webkul\Marketplace\Model\SaleslistFactory;
use Magento\Directory\Model\CountryFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Webkul\Marketplace\Helper\Orders as OrdersHelper;
use Webkul\Marketplace\Model\ProductFactory;
use Webkul\Marketplace\Model\OrdersFactory;
use Webkul\Marketplace\Model\OrderPendingMailsFactory;
use Webkul\Marketplace\Helper\Notification as NotificationHelper;
use Webkul\Marketplace\Model\SaleperpartnerFactory;
use Webkul\Marketplace\Model\Product;

/**
 * Webkul Marketplace SalesOrderPlaceAfterObserver Observer Model.
 */
class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var eventManager
     */
    protected $_eventManager;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var MarketplaceHelper
     */
    protected $_marketplaceHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var SaleslistFactory
     */
    protected $saleslistFactory;

    /**
     * @var CountryFactory
     */
    protected $countryModel;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * @var OrdersHelper
     */
    protected $ordersHelper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var OrdersFactory
     */
    protected $ordersFactory;

    /**
     * @var OrderPendingMailsFactory
     */
    protected $orderPendingMailsFactory;

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;

    /**
     * @var SaleperpartnerFactory
     */
    protected $saleperpartnerFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
     */
    protected $salableQty;
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * Construct
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param SessionManager $coreSession
     * @param QuoteRepository $quoteRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param ProductRepositoryInterface $productRepository
     * @param MarketplaceHelper $marketplaceHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param SaleslistFactory $saleslistFactory
     * @param AddressFactory $orderAddressFactory
     * @param CountryFactory $countryModel
     * @param MpEmailHelper $mpEmailHelper
     * @param OrdersHelper $ordersHelper
     * @param ProductFactory $productFactory
     * @param OrdersFactory $ordersFactory
     * @param OrderPendingMailsFactory $orderPendingMailsFactory
     * @param NotificationHelper $notificationHelper
     * @param SaleperpartnerFactory $saleperpartnerFactory
     * @param \Magento\Framework\Module\Manager|null $moduleManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface|null $timezoneInterface
     * @param \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku|null $salableQty
     * @param \Magento\Framework\MessageQueue\PublisherInterface|null $publisher
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        SessionManager $coreSession,
        QuoteRepository $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        MarketplaceHelper $marketplaceHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        SaleslistFactory $saleslistFactory,
        AddressFactory $orderAddressFactory,
        CountryFactory $countryModel,
        MpEmailHelper $mpEmailHelper,
        OrdersHelper $ordersHelper,
        ProductFactory $productFactory,
        OrdersFactory $ordersFactory,
        OrderPendingMailsFactory $orderPendingMailsFactory,
        NotificationHelper $notificationHelper,
        SaleperpartnerFactory $saleperpartnerFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $salableQty,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_coreSession = $coreSession;
        $this->_quoteRepository = $quoteRepository;
        $this->_orderRepository = $orderRepository;
        $this->_customerRepository = $customerRepository;
        $this->_productRepository = $productRepository;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_date = $date;
        $this->saleslistFactory = $saleslistFactory;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->countryModel = $countryModel;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->ordersHelper = $ordersHelper;
        $this->productFactory = $productFactory;
        $this->ordersFactory = $ordersFactory;
        $this->orderPendingMailsFactory = $orderPendingMailsFactory;
        $this->notificationHelper = $notificationHelper;
        $this->saleperpartnerFactory = $saleperpartnerFactory;
        $this->_timezoneInterface = $timezoneInterface;
        $this->moduleManager = $moduleManager;
        $this->salableQty = $salableQty;
        $this->publisher = $publisher;
    }

    /**
     * Sales Order Place After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isMultiShipping = $this->_checkoutSession->getQuote()->getIsMultiShipping();
        if (!$isMultiShipping) {
            /** @var $orderInstance Order */
            $order = $observer->getOrder();
            $lastOrderId = $observer->getOrder()->getId();
            $this->orderPlacedOperations($order, $lastOrderId);
        } else {
            $quoteId = $this->_checkoutSession->getLastQuoteId();
            $quote = $this->_quoteRepository->get($quoteId);
            if ($quote->getIsMultiShipping() == 1 || $isMultiShipping == 1) {
                $orderIds = $this->_coreSession->getOrderIds();
                foreach ($orderIds as $ids => $orderIncId) {
                    $lastOrderId = $ids;
                    /** @var $orderInstance Order */
                    $order = $this->_orderRepository->get($lastOrderId);
                    $this->orderPlacedOperations($order, $lastOrderId);
                }
            }
        }
    }

    /**
     * Order Place Operation method.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int $lastOrderId
     * @return void
     */
    public function orderPlacedOperations($order, $lastOrderId)
    {
        $this->productSalesCalculation($order);

        /*send placed order mail notification to seller*/

        $paymentCode = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }

        $shippingInfo = '';
        $shippingDes = '';
        $billingId = $order->getBillingAddress()->getId();
        $billaddress = $this->orderAddressFactory->create()->load($billingId);
        $billinginfo = $billaddress['firstname'] . '<br/>' .
            $billaddress['street'] . '<br/>' .
            $billaddress['city'] . ' ' .
            $billaddress['region'] . ' ' .
            $billaddress['postcode'] . '<br/>' .
            $this->countryModel->create()->load($billaddress['country_id'])->getName() . '<br/>T:' .
            $billaddress['telephone'];
        $order->setOrderApprovalStatus(1)->save();
        $payment = $order->getPayment()->getMethodInstance()->getTitle();

        if ($order->getShippingAddress()) {
            $shippingId = $order->getShippingAddress()->getId();
            $address = $this->orderAddressFactory->create()->load($shippingId);
            $shippingInfo = $address['firstname'] . '<br/>' .
                $address['street'] . '<br/>' .
                $address['city'] . ' ' .
                $address['region'] . ' ' .
                $address['postcode'] . '<br/>' .
                $this->countryModel->create()->load($address['country_id'])->getName() . '<br/>T:' .
                $address['telephone'];
            $shippingDes = $order->getShippingDescription();
        }

        $adminStoremail = $this->_marketplaceHelper->getAdminEmailId();
        $defaultTransEmailId = $this->_marketplaceHelper->getDefaultTransEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $defaultTransEmailId;
        $adminUsername = $this->_marketplaceHelper->getAdminName();

        $sellerOrder = $this->ordersFactory->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $lastOrderId)
            ->addFieldToFilter('seller_id', ['neq' => 0]);
        foreach ($sellerOrder as $info) {
            $userdata = $this->_customerRepository->getById($info['seller_id']);
            $currencyRate = $this->_marketplaceHelper->getCurrentCurrencyRate();
            $username = $userdata->getFirstname();
            $useremail = $userdata->getEmail();

            $senderInfo = [];
            $receiverInfo = [];

            $receiverInfo = [
                'name' => $username,
                'email' => $useremail,
            ];
            $senderInfo = [
                'name' => $adminUsername,
                'email' => $adminEmail,
            ];
            $totalprice = 0;
            $totalTaxAmount = 0;
            $shippingCharges = 0;
            $mailData = [];
            $codChargeAmount = 0;
            $saleslistIds = [];
            $collectionSales = $this->saleslistFactory->create()
                ->getCollection()
                ->addFieldToFilter('order_id', $lastOrderId)
                ->addFieldToFilter('seller_id', $info['seller_id'])
                ->addFieldToFilter('magerealorder_id', ['neq' => 0])
                ->addFieldToSelect('entity_id');

            $saleslistIds = $collectionSales->getData();

            $fetchsale = $this->saleslistFactory->create()
                ->getCollection()
                ->addFieldToFilter(
                    'entity_id',
                    ['in' => $saleslistIds]
                );
            $fetchsale->getData();
            $fetchsale->getSellerOrderCollection();

            foreach ($fetchsale as $res) {
                // $currencyRate = $res->getCurrencyRate();
                $product = $this->_productRepository->getById($res['mageproduct_id']);

                /* product name */
                $productName = $res->getMageproName();
                $result = [];
                $result = $this->getProductOptionData($res, $result);
                $productName = $this->getProductNameHtml($result, $productName);
                /* end */
                if ($res->getProductType() == PRODUCT::PRODUCT_TYPE_CONFIGURABLE) {
                    $configurableSalesItem = $this->saleslistFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('order_id', $lastOrderId)
                        ->addFieldToFilter('seller_id', $info['seller_id'])
                        ->addFieldToFilter('parent_item_id', $res->getOrderItemId());
                    $configurableItemArr = $configurableSalesItem->getOrderedProductId();
                    $configurableItemId = $res['mageproduct_id'];
                    if (!empty($configurableItemArr)) {
                        $configurableItemId = $configurableItemArr[0];
                    }
                    $product = $this->_productRepository->getById($configurableItemId);
                } else {
                    $product = $this->_productRepository->getById($res['mageproduct_id']);
                }

                $sku = $product->getSku();
                $mailData["items"][] = [
                    "productName" => $productName,
                    "sku" => $sku,
                    "qty" => ($res['magequantity'] * 1),
                    "price" => strip_tags($order->formatPrice($this->_marketplaceHelper
                        ->getCurrentCurrencyPrice($currencyRate, $res['magepro_price'] * $res['magequantity']))),
                ];
                $totalTaxAmount = $totalTaxAmount + $res['total_tax'];
                $totalprice = $totalprice + ($res['magepro_price'] * $res['magequantity']);

                /*
                * Low Stock Notification mail to seller
                */
                $lowStockQty = $this->_marketplaceHelper->getlowStockQty();
                $seller = $this->_marketplaceHelper->getSellerCollectionObj($info['seller_id'])->getFirstItem();
                $lowStockQty = (!empty($seller->getLowStockQuantity())) ? $seller->getLowStockQuantity() : $lowStockQty;
                if ($lowStockQty) {
                    $stockState = $this->salableQty;
                    $salable = $stockState->execute($sku);
                    if (isset($salable[0]['qty'])) {
                        $stockItemQty = $salable[0]['qty'];
                    } elseif (!empty($product['quantity_and_stock_status']['qty'])) {
                        $stockItemQty = $product['quantity_and_stock_status']['qty'];
                    } else {
                        $stockItemQty = $product->getQty();
                    }
                    if ($stockItemQty <= $lowStockQty) {
                        $emailTemplateVariables = [];
                        $emailTemplateVariables['productName'] = $productName;
                        $emailTemplateVariables['sku'] = $sku;
                        $emailTemplateVariables['qty'] = ($stockItemQty * 1);
                        $emailTemplateVariables['sellerName'] = $username;
                        $lowStockData = [
                            "emailTemplateVariables" => $emailTemplateVariables,
                            "senderInfo" => $senderInfo,
                            "receiverInfo" => $receiverInfo
                        ];
                        $this->publisher->publish(
                            \Webkul\Marketplace\Model\Consumer\LowStockConsumer::QUEUE_NAME,
                            $this->_marketplaceHelper->arrayToJson($lowStockData)
                        );
                    }
                }
            }
            $shippingCharges = $info->getShippingCharges();
            $couponAmount = $info->getCouponAmount();
            $totalCod = 0;
            if ($paymentCode == 'mpcashondelivery') {
                $totalCod = $info->getCodCharges();
                $codChargeAmount = strip_tags($order->formatPrice($this->_marketplaceHelper
                    ->getCurrentCurrencyPrice($currencyRate, $totalCod)));
            }
            $sellerShipping = $this->getSellerShippingCharges($order, $info['seller_id']);
            $mailData["shipping"] = strip_tags($order->formatPrice($sellerShipping));
            $mailData["discount"] = strip_tags($order->formatPrice($this->_marketplaceHelper
                ->getCurrentCurrencyPrice($currencyRate, $couponAmount)));
            $mailData["taxAmount"] = strip_tags($order->formatPrice($this->_marketplaceHelper
                ->getCurrentCurrencyPrice($currencyRate, $totalTaxAmount)));
            $mailData["codChargeAmount"] = strip_tags($codChargeAmount);
            $mailData["grandTotal"] = strip_tags($order->formatPrice($this->_marketplaceHelper->getCurrentCurrencyPrice(
                $currencyRate,
                $totalprice +
                        $totalTaxAmount +
                        $shippingCharges +
                        $totalCod -
                        $couponAmount
            )));
            $emailTemplateVariables = [];
            if ($shippingInfo != '') {
                $isNotVirtual = 1;
            } else {
                $isNotVirtual = 0;
            }
            $emailTempVariables['realOrderId'] = $pendingMail['real_order_id'] = $order->getRealOrderId();
            $emailTempVariables['createdAt'] = $pendingMail['order_created_at'] =  $this->_timezoneInterface
                ->date(new \DateTime($order['created_at']))
                ->format('Y-m-d H:i:s');

            $emailTempVariables['username'] = $pendingMail['username'] = $username;
            $emailTempVariables['billingInfo'] = $pendingMail['billing_info'] = $billinginfo;
            $emailTempVariables['payment'] = $pendingMail['payment'] = $payment;
            $emailTempVariables['shippingInfo'] = $pendingMail['shipping_info'] = $shippingInfo;
            $emailTempVariables['isNotVirtual'] = $pendingMail['isNotVirtual'] = $isNotVirtual;
            $emailTempVariables['mailData'] = $mailData;
            $pendingMail['mail_content'] = $this->_marketplaceHelper->arrayToJson($mailData);
            $emailTempVariables['shippingDes'] = $pendingMail['shipping_description'] = $shippingDes;
            if ($this->_marketplaceHelper->getOrderApprovalRequired()) {
                $pendingMail['seller_id'] = $info['seller_id'];
                $pendingMail['order_id'] = $lastOrderId;
                $pendingMail['sender_name'] = $senderInfo['name'];
                $pendingMail['sender_email'] = $senderInfo['email'];
                $pendingMail['receiver_name'] = $receiverInfo['name'];
                $pendingMail['receiver_email'] = $receiverInfo['email'];

                $orderPendingMailsCollection = $this->orderPendingMailsFactory->create();
                $orderPendingMailsCollection->setData($pendingMail);
                $orderPendingMailsCollection->setCreatedAt($this->_date->gmtDate());
                $orderPendingMailsCollection->save();
                $order->setOrderApprovalStatus(0)->save();
            } else {
                $this->mpEmailHelper->sendPlacedOrderEmail(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo
                );
            }
        }
    }

    /**
     * Get Order Product Option Data Method.
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array $result
     * @return array
     */
    public function getProductOptionData($item, $result = [])
    {
        $productOptionsData = $this->ordersHelper->getProductOptions(
            $item->getProductOptions()
        );
        if ($options = $productOptionsData) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Get Order Product Name Html Data Method.
     *
     * @param array  $result
     * @param string $productName
     *
     * @return string
     */
    public function getProductNameHtml($result, $productName)
    {
        if ($_options = $result) {
            $proOptionData = '<dl class="item-options">';
            foreach ($_options as $_option) {
                $proOptionData .= '<dt>' . $_option['label'] . '</dt>';

                $proOptionData .= '<dd>' . $_option['value'];
                $proOptionData .= '</dd>';
            }
            $proOptionData .= '</dl>';
            $productName = $productName . '<br/>' . $proOptionData;
        } else {
            $productName = $productName . '<br/>';
        }

        return $productName;
    }

    /**
     * Seller Product Sales Calculation Method.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    public function productSalesCalculation($order)
    {
        /*
        * Marketplace Order details save before Observer
        */
        $this->_eventManager->dispatch(
            'mp_order_save_before',
            ['order' => $order]
        );

        /*
        * Get Current Store Currency Rate
        */
        $currentCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $allowedCurrencies = $this->_marketplaceHelper->getConfigAllowCurrencies();
        $rates = $this->_marketplaceHelper->getCurrencyRates(
            $baseCurrencyCode,
            array_values($allowedCurrencies)
        );
        if (empty($rates[$currentCurrencyCode])) {
            $rates[$currentCurrencyCode] = 1;
        }

        $lastOrderId = $order->getId();

        /*
        * Marketplace Credit Management module Observer
        */
        $this->_eventManager->dispatch(
            'mp_discount_manager',
            ['order' => $order]
        );

        $this->_eventManager->dispatch(
            'mp_advance_commission_rule',
            ['order' => $order]
        );

        $sellerData = $this->getSellerProductData($order, $rates[$currentCurrencyCode]);
        $shippingTax = $order->getBaseShippingTaxAmount();
        $sellerProArr = $sellerData['seller_pro_arr'];
        $sellerTaxArr = $sellerData['seller_tax_arr'];
        $sellerCouponArr = $sellerData['seller_coupon_arr'];

        $taxToSeller = $this->_marketplaceHelper->getConfigTaxManage();
        $shippingAll = $this->_coreSession->getData('shippinginfo');
        try {
            $shippingAllCount = ($shippingAll == null) ? false : count($shippingAll);
        } catch (\Exception $e) {
            $this->_marketplaceHelper->logDataInLogger(
                "Observer_SalesOrderPlaceAfterObserver productSalesCalculation : " . $e->getMessage()
            );
            $shippingAllCount = false;
        }
        foreach ($sellerProArr as $key => $value) {
            if ($key) {
                $productIds = implode(',', $value);
                $data = [
                    'order_id' => $lastOrderId,
                    'product_ids' => $productIds,
                    'seller_id' => $key,
                    'total_tax' => $sellerTaxArr[$key],
                    'tax_to_seller' => $taxToSeller
                ];
                if (!$shippingAllCount && $key == 0) {
                    $shippingCharges = $order->getBaseShippingAmount();
                    $data = [
                        'order_id' => $lastOrderId,
                        'product_ids' => $productIds,
                        'seller_id' => $key,
                        'shipping_charges' => $shippingCharges,
                        'total_tax' => $sellerTaxArr[$key],
                        'tax_to_seller' => $taxToSeller,
                        'shipping_tax' => $shippingCharges ? $shippingTax : 0
                    ];
                }
                $shippingTax = 0;
                if (!empty($sellerCouponArr) && !empty($sellerCouponArr[$key])) {
                    $data['coupon_amount'] = $sellerCouponArr[$key];
                }
                $collection = $this->ordersFactory->create();
                $collection->setData($data);
                $collection->setCreatedAt($this->_date->gmtDate());
                $collection->setSellerPendingNotification(1);
                $collection->save();
                $sellerOrderId = $collection->getId();
                if (!$this->_marketplaceHelper->getOrderApprovalRequired()) {
                    $this->notificationHelper->saveNotification(
                        \Webkul\Marketplace\Model\Notification::TYPE_ORDER,
                        $sellerOrderId,
                        $lastOrderId
                    );
                }
            }
        }
        /*
        * Marketplace Order details save after Observer
        */
        $this->_eventManager->dispatch(
            'mp_order_save_after',
            ['order' => $order]
        );
    }

    /**
     * Get Seller's Product Data.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int $ratesPerCurrency
     *
     * @return array
     */
    public function getSellerProductData($order, $ratesPerCurrency)
    {
        $lastOrderId = $order->getId();
        /*
        * Get Global Commission Rate for Admin
        */
        $percent = $this->_marketplaceHelper->getConfigCommissionRate();

        $sellerProArr = [];
        $sellerTaxArr = [];
        $sellerCouponArr = [];
        $isShippingFlag = [];
        $totalItemCount = count($order->getAllItems());
        foreach ($order->getAllItems() as $item) {
            $baseDiscountAmount = 0;
            $itemData = $item->getData();
            $sellerId = $this->_marketplaceHelper->getSellerIdByOrderItem($item);
            if ($sellerId && $sellerId != '') {
                $calculationStatus = true;
                if ($itemData['product_type'] == PRODUCT::PRODUCT_TYPE_BUNDLE) {
                    $productOptions = $item->getProductOptions();
                    $calculationStatus = $productOptions['product_calculations'] ? true : false;
                }
                if ($calculationStatus) {
                    $isShippingFlag = $this->getShippingFlag($item, $sellerId, $isShippingFlag);
                    $price = $itemData['base_price'];
                    $taxamount = $itemData['base_tax_amount'] + ($order->getBaseShippingTaxAmount() / $totalItemCount);
                    $qty = $item->getQtyOrdered();
                    $totalamount = $qty * $price;
                    if (!empty($itemData['base_discount_amount'])) {
                        $baseDiscountAmount =  $itemData['base_discount_amount'];
                    } elseif ($itemData['applied_rule_ids']) {
                        $baseDiscountAmount =  abs($order->getBaseDiscountAmount());
                    }

                    $advanceCommissionRule = $this->_customerSession->getData(
                        'advancecommissionrule'
                    );

                    $deductDiscountSetting = $this->_marketplaceHelper->getConfigCommissionWithDiscount();

                    if ($deductDiscountSetting == 1) {
                        // discount will be deduct from both seller amount
                        $commission = $this->getCommission(
                            $sellerId,
                            $totalamount,
                            $item,
                            $advanceCommissionRule,
                            $order
                        );

                        // deducting discount from seller amount
                        $actparterprocost = $totalamount - $commission - $baseDiscountAmount;
                    } elseif ($deductDiscountSetting == 2) {
                        // discount will be deduct from admin amount
                        $commission = $this->getCommission(
                            $sellerId,
                            $totalamount,
                            $item,
                            $advanceCommissionRule,
                            $order
                        );
                        $actparterprocost = $totalamount - $commission;

                        // deducting discount from admin commission
                        if ($baseDiscountAmount <= $commission) {
                            $commission = $commission - $baseDiscountAmount;
                        } else {
                            $actparterprocost = $actparterprocost - ($baseDiscountAmount - $commission);
                            $commission = 0;
                        }
                    } else {
                        // discount will be deduct from both seller and admin amount
                        $totalAfterDiscount = $totalamount - $baseDiscountAmount;

                        $commission = $this->getCommission(
                            $sellerId,
                            $totalAfterDiscount,
                            $item,
                            $advanceCommissionRule,
                            $order
                        );

                        $actparterprocost = $totalAfterDiscount - $commission;
                    }
                } else {
                    if (empty($isShippingFlag[$sellerId])) {
                        $isShippingFlag[$sellerId] = 0;
                    }
                    $price = 0;
                    $taxamount = 0;
                    $qty = $item->getQtyOrdered();
                    $totalamount = 0;
                    $commission = 0;
                    $actparterprocost = 0;
                }

                $collectionsave = $this->saleslistFactory->create();
                $collectionsave->setMageproductId($item->getProductId());
                $collectionsave->setOrderItemId($item->getItemId());
                $collectionsave->setParentItemId($item->getParentItemId());
                $collectionsave->setOrderId($lastOrderId);
                $collectionsave->setMagerealorderId($order->getIncrementId());
                $collectionsave->setMagequantity($qty);
                $collectionsave->setSellerId($sellerId);
                $collectionsave->setCpprostatus(\Webkul\Marketplace\Model\Saleslist::PAID_STATUS_PENDING);
                $collectionsave->setMagebuyerId($this->_customerSession->getCustomerId());
                $collectionsave->setMageproPrice($price);
                $collectionsave->setMageproName($item->getName());
                if ($totalamount != 0) {
                    $totalAfterDiscount = $totalamount - $baseDiscountAmount;
                    $collectionsave->setTotalAmount($totalamount);
                    $commissionRate = ($commission * 100) / $totalAfterDiscount;
                } else {
                    $collectionsave->setTotalAmount($price);
                    $commissionRate = $percent;
                }
                $collectionsave->setTotalTax($taxamount);
                if (!$this->_marketplaceHelper->isSellerCouponModuleInstalled()) {
                    if (isset($itemData['base_discount_amount'])) {
                        $baseDiscountAmount = $itemData['base_discount_amount'];
                        $collectionsave->setIsCoupon(1);
                        $collectionsave->setAppliedCouponAmount($baseDiscountAmount);

                        if (!isset($sellerCouponArr[$sellerId])) {
                            $sellerCouponArr[$sellerId] = 0;
                        }
                        $sellerCouponArr[$sellerId] = $sellerCouponArr[$sellerId] + $baseDiscountAmount;
                    }
                }
                $collectionsave->setTotalCommission($commission);
                $collectionsave->setActualSellerAmount($actparterprocost);
                $collectionsave->setCommissionRate($commissionRate);
                $collectionsave->setCurrencyRate($ratesPerCurrency);
                if (isset($isShippingFlag[$sellerId])) {
                    $collectionsave->setIsShipping($isShippingFlag[$sellerId]);
                }
                $collectionsave->setCreatedAt($this->_date->gmtDate());
                $collectionsave->save();
                $qty = 0;
                if (!isset($sellerTaxArr[$sellerId])) {
                    $sellerTaxArr[$sellerId] = 0;
                }
                $sellerTaxArr[$sellerId] = $sellerTaxArr[$sellerId] + $taxamount;
                if ($price != 0.0000) {
                    if (!isset($sellerProArr[$sellerId])) {
                        $sellerProArr[$sellerId] = [];
                    }
                    array_push($sellerProArr[$sellerId], $item->getProductId());
                } else {
                    if (!$item->getParentItemId()) {
                        if (!isset($sellerProArr[$sellerId])) {
                            $sellerProArr[$sellerId] = [];
                        }
                        array_push($sellerProArr[$sellerId], $item->getProductId());
                    }
                }
            }
        }

        return [
            'seller_pro_arr' => $sellerProArr,
            'seller_tax_arr' => $sellerTaxArr,
            'seller_coupon_arr' => $sellerCouponArr
        ];
    }

    /**
     * Get Commission Amount.
     *
     * @param int                             $sellerId
     * @param float                           $totalamount
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array                           $advanceCommissionRule
     * @param \Magento\Sales\Model\Order      $order
     * @return float
     */
    public function getCommission($sellerId, $totalamount, $item, $advanceCommissionRule, $order)
    {
        /*
        * Get Global Commission Rate for Admin
        */
        $percent = $this->_marketplaceHelper->getConfigCommissionRate();

        $commission = 0;
        $qty = $order->getTotalQtyOrdered();
        $commissionRate = $this->saleperpartnerFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'seller_id',
                $sellerId
            )
            ->addFieldToFilter(
                'commission_status',
                1
            )
            ->addFieldToSelect(
                'commission_rate'
            )->getFirstItem()
            ->getCommissionRate();

        if ($commissionRate != null) {
            $commission = ($totalamount * $commissionRate) / 100;
        } else {
            $commission = ($totalamount * $percent) / 100;
        }
        if ($this->moduleManager->isOutputEnabled('Webkul_MpAdvancedCommission')) {
            if (!$this->_marketplaceHelper->getUseCommissionRule()) {
                $flag = 0;
                if ($item->getProductType() != PRODUCT::PRODUCT_TYPE_CONFIGURABLE) {
                    $wholedata['id'] = $item->getProductId();
                    $this->_eventManager->dispatch(
                        'mp_advance_commission',
                        [$wholedata]
                    );
                    $flag = 1;
                } elseif ($item->getProductType() == PRODUCT::PRODUCT_TYPE_CONFIGURABLE) {
                    $sku = $item->getSku();
                    try {
                        $product = $this->_productRepository->get($sku);
                        $wholedata['id'] = $product->getId();
                    } catch (\Exception $e) {
                        $wholedata['id'] = $item->getProductId();
                    }
                    $this->_eventManager->dispatch(
                        'mp_advance_commission',
                        [$wholedata]
                    );
                    $flag = 1;
                }

                if ($flag) {
                    $advancecommission = $this->_customerSession->getData('commission');
                    if ($advancecommission != '') {
                        $percent = $advancecommission;
                        $commType = $this->_marketplaceHelper->getCommissionType();
                        if ($commType == 'fixed') {
                            $commission = $percent * $qty;
                        } else {
                            $commission = ($totalamount * $advancecommission) / 100;
                        }
                        if ($commission > $totalamount) {
                            $commission = $totalamount * $this->_marketplaceHelper->getConfigCommissionRate() / 100;
                        }
                    }
                }
            } else {
                if (count($advanceCommissionRule) && isset($advanceCommissionRule[$item->getId()])) {
                    if ($advanceCommissionRule[$item->getId()]['type'] == 'fixed') {
                        $commission = $advanceCommissionRule[$item->getId()]['amount'];
                    } else {
                        $commission =
                            ($totalamount * $advanceCommissionRule[$item->getId()]['amount']) / 100;
                    }
                }
            }
        }
        return $commission;
    }

    /**
     * Get Shipping Flag Per Seller Method.
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param int                             $sellerId
     * @param array                           $isShippingFlag
     *
     * @return array
     */
    public function getShippingFlag($item, $sellerId, $isShippingFlag = [])
    {
        $productDownloadable = Product::PRODUCT_TYPE_DOWNLOADABLE;
        $productVirtual = Product::PRODUCT_TYPE_VIRTUAL;
        if (($item->getProductType() != $productVirtual) && ($item->getProductType() != $productDownloadable)) {
            if (!isset($isShippingFlag[$sellerId])) {
                $isShippingFlag[$sellerId] = 1;
            } else {
                $isShippingFlag[$sellerId] = 0;
            }
        }

        return $isShippingFlag;
    }
    /**
     * Add shipping charges
     *
     * @param \Magento\Sales\Model\Order $order
     * @param int $sellerId
     * @return int|void
     */
    public function getSellerShippingCharges($order, $sellerId)
    {
        $shippingMethod = $order->getShippingMethod();
        $lastOrderId = $order->getId();
        $shippingInformation = $this->_customerSession->getShippingInformation();
        if (empty($shippingInformation)) {
            return 0;
        }
        foreach (array_keys($shippingInformation) as $carrierCode) {
            if (strpos($shippingMethod, $carrierCode) !== false) {
                $shipMethod = explode('_', $shippingMethod, 2);

                foreach ((array)$shippingInformation[$carrierCode] as $key => $shipData) {
                    if ($sellerId != $shipData['seller_id']) {
                        continue;
                    }
                    /*** Backward Compatibility Start ***/
                    $flag = false;
                    if (!isset($shipData['sellerCredentials'])) {
                        $flag = true;
                    } elseif ($shipData['sellerCredentials']) {
                        $flag = true;
                    }
                    /*** Backward Compatibility End ***/

                    if ($flag) {
                        $mpOrderCollection = $this->ordersFactory->create()->getCollection()
                            ->addFieldToFilter('order_id', ['eq' => $lastOrderId])
                            ->addFieldToFilter('seller_id', ['eq' => $shipData['seller_id']])
                            ->setPageSize(1);

                        if ($mpOrderCollection->getSize()) {
                            $mpOrder = $mpOrderCollection->getLastItem();
                            $shipping = $this->getShippingMethod($shipData, $shipMethod);
                            return $shipping;
                        }
                    }
                }
            }
        }
    }

    /**
     * Save Shipping Carrier and Charges to Marketplace Orders
     *
     * @param array $shipData
     * @param array $shipMethod
     * @return int|float
     */
    protected function getShippingMethod($shipData, $shipMethod)
    {
        if (isset($shipData['submethod'][$shipMethod[1]])) {
            return $shipData['submethod'][$shipMethod[1]]['cost'];
        }
        return 0;
    }
}
