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

namespace Webkul\Marketplace\Block\Account;

use Webkul\Marketplace\Model\ResourceModel\Saleperpartner\CollectionFactory as SalesPartnerCollection;

class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Order\ItemRepository $orderItemRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Webkul\Marketplace\Helper\Orders $orderHelper
     * @param \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory $mpSaleslistCollectionFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory $mpOrderCollectionFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $mpProductCollectionFactory
     * @param SalesPartnerCollection $mpSalePerPartnerCollectionFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\Feedback\CollectionFactory $mpFeedbackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        protected \Magento\Framework\View\Element\Template\Context $context,
        protected \Magento\Customer\Model\Customer $customer,
        protected \Magento\Customer\Model\Session $customerSession,
        protected \Magento\Sales\Model\OrderRepository $orderRepository,
        protected \Magento\Sales\Model\Order\ItemRepository $orderItemRepository,
        protected \Magento\Catalog\Model\ProductRepository $productRepository,
        protected \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        protected \Webkul\Marketplace\Helper\Orders $orderHelper,
        protected \Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory $mpSaleslistCollectionFactory,
        protected \Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory $mpOrderCollectionFactory,
        protected \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $mpProductCollectionFactory,
        protected SalesPartnerCollection $mpSalePerPartnerCollectionFactory,
        protected \Webkul\Marketplace\Model\ResourceModel\Feedback\CollectionFactory $mpFeedbackCollectionFactory,
        protected \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        protected \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Set title
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Seller Dashboard'));
    }

    /**
     * Get customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get customerid
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * Get Collection
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\Saleslist\Collection
     */
    public function getCollection()
    {
        if (!($customerId = $this->getCustomerId())) {
            return false;
        }

        $paramData = $this->getRequest()->getParams();
        $filterOrderid = $filterOrderstatus = $filterDataTo = $filterDataFrom = '';
        $from = $to = null;

        if (isset($paramData['s'])) {
            $filterOrderid = $paramData['s'] != '' ? $paramData['s'] : '';
        }
        if (isset($paramData['orderstatus'])) {
            $filterOrderstatus = $paramData['orderstatus'] != '' ? $paramData['orderstatus'] : '';
        }
        if (isset($paramData['from_date'])) {
            $filterDataFrom = $paramData['from_date'] != '' ? $paramData['from_date'] : '';
        }
        if (isset($paramData['to_date'])) {
            $filterDataTo = $paramData['to_date'] != '' ? $paramData['to_date'] : '';
        }

        $orderids = $this->getOrderIdsArray($customerId, $filterOrderstatus);

        $ids = $this->getEntityIdsArray($orderids);

        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'entity_id',
            ['in' => $ids]
        );

        if ($filterDataTo) {
            $todate = date_create($filterDataTo);
            $to = date_format($todate, 'Y-m-d 23:59:59');
        }
        if ($filterDataFrom) {
            $fromdate = date_create($filterDataFrom);
            $from = date_format($fromdate, 'Y-m-d H:i:s');
        }

        if ($filterOrderid) {
            $collection->addFieldToFilter(
                'magerealorder_id',
                ['eq' => $filterOrderid]
            );
        }
        if ($from && $to) {
            $collection->addFieldToFilter(
                'created_at',
                ['datetime' => true, 'from' => $from, 'to' => $to]
            );
        }

        $collection->setOrder(
            'created_at',
            'desc'
        );
        $collection->getSellerOrderCollection();
        $collection->setPageSize(5);

        return $collection;
    }

    /**
     * Get order id array
     *
     * @param int $customerId
     * @param string $filterOrderstatus
     * @return array
     */
    public function getOrderIdsArray($customerId = '', $filterOrderstatus = '')
    {
        $orderids = [];

        $collectionOrders = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $customerId]
        )
        ->addFieldToSelect('order_id')
        ->distinct(true);

        foreach ($collectionOrders as $collectionOrder) {
            $tracking = $this->orderHelper->getOrderinfo($collectionOrder->getOrderId());

            if ($tracking) {
                if ($filterOrderstatus) {
                    if ($tracking->getIsCanceled()) {
                        if ($filterOrderstatus == 'canceled') {
                            array_push($orderids, $collectionOrder->getOrderId());
                        }
                    } else {
                        $tracking = $this->orderRepository->get($collectionOrder->getOrderId());
                        if ($tracking->getStatus() == $filterOrderstatus) {
                            array_push($orderids, $collectionOrder->getOrderId());
                        }
                    }
                } else {
                    array_push($orderids, $collectionOrder->getOrderId());
                }
            }
        }

        return $orderids;
    }

    /**
     * Get entity ids array
     *
     * @param array $orderids
     * @return array
     */
    public function getEntityIdsArray($orderids = [])
    {
        $ids = [];
        foreach ($orderids as $orderid) {
            $collectionIds = $this->mpSaleslistCollectionFactory->create()
            ->addFieldToFilter(
                'order_id',
                ['eq' => $orderid]
            )
            ->addFieldToFilter('parent_item_id', ['null' => 'true'])
            ->setOrder('entity_id', 'DESC')
            ->setPageSize(1);
            foreach ($collectionIds as $collectionId) {
                $autoid = $collectionId->getId();
                array_push($ids, $autoid);
            }
        }

        return $ids;
    }

    /**
     * Get date detials
     *
     * @return array
     */
    public function getDateDetail()
    {
        $sellerId = $this->getCustomerId();

        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        )
        ->addFieldToFilter(
            'order_id',
            ['neq' => 0]
        )
        ->addFieldToFilter(
            'paid_status',
            ['neq' => 2]
        );
        $collectionMonth = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        )
        ->addFieldToFilter(
            'order_id',
            ['neq' => 0]
        )
        ->addFieldToFilter(
            'paid_status',
            ['neq' => 2]
        );
        $collectionWeek = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        )
        ->addFieldToFilter(
            'order_id',
            ['neq' => 0]
        )
        ->addFieldToFilter(
            'paid_status',
            ['neq' => 2]
        );
        $collectionDay = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            ['eq' => $sellerId]
        )
        ->addFieldToFilter(
            'order_id',
            ['neq' => 0]
        )
        ->addFieldToFilter(
            'paid_status',
            ['neq' => 2]
        );

        $firstDayOfWeek = date('Y-m-d', strtotime('Last Monday', time()));

        $lastDayOfWeek = date('Y-m-d', strtotime('Next Sunday', time()));

        $month = $collectionMonth->addFieldToFilter(
            'created_at',
            [
                'datetime' => true,
                'from' => date('Y-m').'-01 00:00:00',
                'to' => date('Y-m').'-31 23:59:59',
            ]
        );

        $week = $collectionWeek->addFieldToFilter(
            'created_at',
            [
                'datetime' => true,
                'from' => $firstDayOfWeek.' 00:00:00',
                'to' => $lastDayOfWeek.' 23:59:59',
            ]
        );

        $day = $collectionDay->addFieldToFilter(
            'created_at',
            [
                'datetime' => true,
                'from' => date('Y-m-d').' 00:00:00',
                'to' => date('Y-m-d').' 23:59:59',
            ]
        );

        $sale = 0;

        $dateDetail['year'] = $sale;

        $saleDay = 0;
        foreach ($day as $recordDay) {
            $saleDay = $saleDay + $recordDay->getActualSellerAmount();
        }
        $dateDetail['day'] = $saleDay;

        $saleMonth = 0;
        foreach ($month as $recordMonth) {
            $saleMonth = $saleMonth + $recordMonth->getActualSellerAmount();
        }
        $dateDetail['month'] = $saleMonth;

        $saleWeek = 0;
        foreach ($week as $recordWeek) {
            $saleWeek = $saleWeek + $recordWeek->getActualSellerAmount();
        }
        $dateDetail['week'] = $saleWeek;

        $temp = 0;
        foreach ($collection as $record) {
            $temp = $temp + $record->getActualSellerAmount();
        }
        $dateDetail['totalamount'] = $temp;

        return $dateDetail;
    }

    /**
     * Get product name
     *
     * @param int $orderId
     * @return string
     */
    public function getProNameByOrder($orderId)
    {
        $orderHelper = $this->orderHelper;
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'order_id',
            $orderId
        )
        ->addFieldToFilter('parent_item_id', ['null' => 'true']);
        $productName = '';
        foreach ($collection as $res) {
            if ($res->getParentItemId()) {
                continue;
            }
            $productName = $orderHelper->getOrderedProductName($res, $productName);
        }

        return $productName;
    }

    /**
     * Get price
     *
     * @param int $orderId
     * @return float
     */
    public function getPricebyorder($orderId)
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create();
        $collection->addFieldToFilter('main_table.seller_id', $sellerId);
        $collection->addFieldToFilter('main_table.order_id', $orderId);
        $collection->getPricebyorderData();
        $actualSellerAmount = 0;
        foreach ($collection as $coll) {
            // calculate order actual_seller_amount in base currency
            $shippingAmount = $coll['shipping_charges']*1;
            $refundedShippingAmount = $coll['refunded_shipping_charges']*1;
            $totalshipping = $shippingAmount - $refundedShippingAmount;
            $vendorTaxAmount = $coll['total_tax']*1;
            if ($coll['actual_seller_amount'] * 1) {
                $taxShippingTotal = $vendorTaxAmount + $totalshipping;
                $actualSellerAmount += $coll['actual_seller_amount'] + $taxShippingTotal;
            } else {
                if ($totalshipping * 1) {
                    $actualSellerAmount += $totalshipping;
                }
            }
        }
        return $actualSellerAmount;
    }

    /**
     * Get top sale products
     *
     * @return array
     */
    public function getTopSaleProducts()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'parent_item_id',
            ['null' => 'true']
        )
        ->getAllOrderProducts();
        $resultData = [];
        foreach ($collection as $coll) {
            try {
                $item = $this->orderItemRepository->get($coll['order_item_id']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                continue;
            }
            $resultData[$coll->getId()]['name'] = $item->getName();
            $resultData[$coll->getId()]['url'] = '';
            $resultData[$coll->getId()]['qty'] = $coll['qty'];
            $product = $item->getProduct();
            if ($product) {
                $media = $this->getUrl("media/catalog/product");
                $image = $media."placeholder/image.jpg";
                if ($product->getCustomAttribute("thumbnail")) {
                    $image = $media.$product->getCustomAttribute("thumbnail")->getValue();
                }
                $resultData[$coll->getId()]['name'] = $product->getName();
                $resultData[$coll->getId()]['url'] = $product->getProductUrl();
                $resultData[$coll->getId()]['image'] = $image;
                $resultData[$coll->getId()]['qty'] = $coll['qty'];
            }
        }
        return $resultData;
    }

    /**
     * Get top sale category
     *
     * @return array
     */
    public function getTopSaleCategories()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'parent_item_id',
            ['null' => 'true']
        );
        $resultData = [];
        $catArr = [];
        $totalOrderedProducts = 0;
        foreach ($collection as $coll) {
           
            $totalOrderedProducts = $totalOrderedProducts + $coll['magequantity'];
        }
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'parent_item_id',
            ['null' => 'true']
        );
        foreach ($collection as $coll) {
            try {
                $item = $this->orderItemRepository->get($coll['order_item_id']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                continue;
            }
            $product = $item->getProduct();
            if ($product) {
                $productCategories = $product->getCategoryIds();
                foreach ($productCategories as $proCategory) {
                    if (!isset($catArr[$proCategory])) {
                        $catArr[$proCategory] = $coll['magequantity'];
                    } else {
                        $catArr[$proCategory] = $catArr[$proCategory] + $coll['magequantity'];
                    }
                }
            }
        }
        $categoryArr = [];
        foreach ($catArr as $key => $value) {
            $percentage = 0;
            $categories = [];
            if ($value) {
                $percentage = round((($value * 100) / $totalOrderedProducts), 2);
            }
            try {
                $categoryArr["id"] = $key;
                $categoryArr["percentage"] = $percentage;
                $category = $this->categoryFactory->create()->load($key);
                if ($category->getParentCategories()) {
                    foreach ($category->getParentCategories() as $parent) {
                        $categories[] = $parent->getName();
                    }
                }
                $categoryArr["category"] = implode(" > ", $categories);
            } catch (\Exception $e) {
                unset($categoryArr[$key]);
            }
        }
        $resultData['category_arr'] = $categoryArr;
        return $resultData;
    }

    /**
     * Get total sale collection
     *
     * @return SalesPartnerCollection
     */
    public function getTotalSaleColl()
    {
        $sellerId = $this->getCustomerId();

        $collection = $this->mpSalePerPartnerCollectionFactory->create();
        $collection->addFieldToFilter('seller_id', ['eq' => $sellerId]);

        return $collection;
    }

    /**
     * Give the current url of recently viewed page.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Get review collection
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\Feedback\Collection
     */
    public function getReviewcollection()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpFeedbackCollectionFactory->create();
        $collection->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        $collection->addFieldToFilter('status', ['eq' => 1]);
        $collection->setOrder('created_at', 'desc');
        $collection->setPageSize(5);
        $collection->setCurPage(1);
        return $collection;
    }

    /**
     * Get order
     *
     * @param int $orderId
     * @return array
     */
    public function getMainOrder($orderId)
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['eq' => $orderId]);
        foreach ($collection as $res) {
            return $res;
        }

        return [];
    }

    /**
     * Get ordered price
     *
     * @param float $currencyRate
     * @param float $basePrice
     * @return float
     */
    public function getOrderedPricebyorder($currencyRate, $basePrice)
    {
        return $basePrice * $currencyRate;
    }

    /**
     * Get total order
     *
     * @return int
     */
    public function getTotalOrders()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpOrderCollectionFactory->create();
        $collection->addFieldToFilter('seller_id', $sellerId);
        $salesOrder = $collection->getTable('sales_order');
        $collection->getSelect()->join(
            $salesOrder.' as so',
            'main_table.order_id = so.entity_id',
            ["order_approval_status" => "order_approval_status"]
        )->where("so.order_approval_status=1");
        return count($collection);
    }

    /**
     * Get pending orders
     *
     * @return int
     */
    public function getPendingOrders()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->getTotalOrders()
        ->getSellerOrderCollection();
        $collection->addFieldToFilter(
            'status',
            'pending'
        );
        return count($collection);
    }

    /**
     * Get processsing order
     *
     * @return int
     */
    public function getProcessingOrders()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->getTotalOrders()
        ->getSellerOrderCollection();
        $collection->addFieldToFilter(
            'status',
            'processing'
        );
        return count($collection);
    }

    /**
     * Get complete order
     *
     * @return int
     */
    public function getCompletedOrders()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->getTotalOrders()
        ->getSellerOrderCollection();
        $collection->addFieldToFilter(
            'status',
            'complete'
        );
        return count($collection);
    }

    /**
     * Get totla products
     *
     * @return int
     */
    public function getTotalProducts()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpProductCollectionFactory->create();
        $collection->addFieldToFilter('seller_id', $sellerId);
        return count($collection);
    }

    /**
     * Get totalcustomers
     *
     * @return int
     */
    public function getTotalCustomers()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )->addFieldToFilter(
            'magebuyer_id',
            ['neq' => 0]
        )
        ->getTotalCustomersCount();
        return count($collection);
    }

    /**
     * Get total customer by month
     *
     * @return int
     */
    public function getTotalCustomersCurrentMonth()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )->addFieldToFilter(
            'created_at',
            [
                'datetime' => true,
                'from' => date('Y-m').'-01 00:00:00',
                'to' => date('Y-m').'-31 23:59:59',
            ]
        )->addFieldToFilter(
            'magebuyer_id',
            ['neq' => 0]
        )->getTotalCustomersCount();
        return count($collection);
    }

    /**
     * Get last month customer
     *
     * @return int
     */
    public function getTotalCustomersLastMonth()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )->addFieldToFilter(
            'created_at',
            [
                'datetime' => true,
                'from' => date('Y-m', strtotime('last month')).'-01 00:00:00',
                'to' => date('Y-m', strtotime('last month')).'-31 23:59:59',
            ]
        )->addFieldToFilter(
            'magebuyer_id',
            ['neq' => 0]
        )->getTotalCustomersCount();
        return count($collection);
    }
    /**
     * Convert date according to timezone
     *
     * @param string $dateTime
     * @return string
     */
    public function convertDate($dateTime)
    {
        return $this->timezone->date(new \DateTime($dateTime))->format('Y-m-d H:i:s');
    }
    /**
     * Get top customer
     *
     * @return array
     */
    public function getTopCustomer()
    {
        $sellerId = $this->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create();
        $customerGridFlat = $collection->getTable('customer_grid_flat');
        $collectionData = $collection->addFieldToSelect(["entity_id"])
        ->addFieldToFilter('seller_id', $sellerId);

        $collectionData->getSelect()
        ->columns('SUM(actual_seller_amount) AS customer_base_total')
        ->columns('count(distinct(order_id)) AS order_count')
        ->group('magebuyer_id');

        $collectionData->getSelect()->join(
            $customerGridFlat.' as cgf',
            'main_table.magebuyer_id = cgf.entity_id',
            [
                'name' => 'name',
                'email' => 'email',
                'billing_telephone' => 'billing_telephone',
                'gender' => 'gender',
                'billing_full' => 'billing_full'
            ]
        );
        return $collectionData;
    }
    /**
     * Get filter options for seller dashboard.
     *
     * @return array
     */
    public function getFilterOptions()
    {
        return [
            ["id" => "", "name" => __('-- Filter --')],
            ["id" => "today", "name" => __('Today')],
            ["id" => "yesterday", "name" => __('Yesterday')],
            ["id" => "7day", "name" => __('Last 7 Days')],
            ["id" => "30day", "name" => __('Last 30 Days')],
            ["id" => "lastmonth", "name" => __('Last Month')],
            ["id" => "lifetime", "name" => __('Life Time')],
            ["id" => "range", "name" => __('Custom Range')]
        ];
    }
}
