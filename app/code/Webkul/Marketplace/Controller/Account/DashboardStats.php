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
namespace Webkul\Marketplace\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Customer\Model\Url as CustomerUrl;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory as MpSaleslistCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Webkul\Marketplace\Model\Orders;

/**
 * Webkul Marketplace Account Dashboard Controller.
 */
class DashboardStats extends Action
{

    /**
     * Construct
     *
     * @param Context $context
     * @param PageFactory $_resultPageFactory
     * @param \Magento\Customer\Model\Session $_customerSession
     * @param MarketplaceHelper $marketplaceHelper
     * @param CustomerUrl $customerUrl
     * @param MpSaleslistCollectionFactory $mpSaleslistCollectionFactory
     * @param \Magento\Sales\Model\Order\ItemRepository $orderItemRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Webkul\Marketplace\Block\Order\OrderListCard $orderListCard
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        protected Context $context,
        protected PageFactory $_resultPageFactory,
        protected \Magento\Customer\Model\Session $_customerSession,
        protected MarketplaceHelper $marketplaceHelper,
        protected CustomerUrl $customerUrl,
        protected MpSaleslistCollectionFactory $mpSaleslistCollectionFactory,
        protected \Magento\Sales\Model\Order\ItemRepository $orderItemRepository,
        protected PriceCurrencyInterface $priceCurrency,
        protected \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        protected \Webkul\Marketplace\Block\Order\OrderListCard $orderListCard,
        protected \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Seller Dashboard page.
     *
     * @return void
     */
    public function execute()
    {
        $stats = [];
        $response = ["status" => false];
        $params = $this->getRequest()->getParams();
        try {
            $dateFrom = date_create($params["dateFrom"]);
            $dateTo = date_create($params["dateTo"]);
            if (!$dateFrom) {
                throw new LocalizedException(__("Invalid date from found"));
            }
            if (!$dateTo) {
                throw new LocalizedException(__("Invalid date to found"));
            }
            $fromDate = date_format($dateFrom, 'Y-m-d 00:00:00');
            $toDate = date_format($dateTo, 'Y-m-d 23:59:59');
            $stats["topCustomer"] = $this->getTopCustomer($fromDate, $toDate);
            $stats["topProduct"] = $this->getTopSaleProducts($fromDate, $toDate);
            $stats["topCategory"] = $this->getTopSaleCategories($fromDate, $toDate);
            $stats["orderStat"] = $this->getOrderStat($fromDate, $toDate);
            $stats["orderSaleData"] = $this->getOrderSaleGraphData($params['dateFrom'], $params['dateTo']);
            $stats["orderGraphData"] = $this->getOrderGraphData($params['dateFrom'], $params['dateTo']);
            $stats["orderValueData"] = $this->getOrderValueGraphData($params['dateFrom'], $params['dateTo']);
            
            $lastFromDate = $params["compFrom"]; //date for growth from last from date
            $lastToDate = $params["compTo"]; //date for growth from last to date
            $stats["saleComparison"] = 0.00;
            if ($this->getOrderSaleGraphData($lastFromDate, $lastToDate)["totalSaleAmount"] > 0) {
                $totalOrder = $this->getOrderSaleGraphData($lastFromDate, $lastToDate);
                $stats["saleComparison"] =  (($stats["orderSaleData"]["totalSaleAmount"] -
                $totalOrder["totalSaleAmount"])/$totalOrder["totalSaleAmount"]) * 100;
            } elseif (count($stats["topCustomer"]) > 0) {
                $stats["saleComparison"] = 100.00;
            }
            $stats["orderCountComparison"] = 0.00;
            if ($this->getOrderStat($lastFromDate, $lastToDate)["all"] > 0) {
                $totalOrder = $this->getOrderStat($lastFromDate, $lastToDate);
                $stats["orderCountComparison"] =  (($stats["orderStat"]["all"] -
                $totalOrder["all"])/$totalOrder["all"]) * 100;
            } elseif (count($stats["topCustomer"]) > 0) {
                $stats["orderCountComparison"] = 100.00;
            }
            $stats["orderValueComparison"] = 0.00;
            if ($this->getOrderValueGraphData($lastFromDate, $lastToDate)["avgOrderValue"] > 0) {
                $totalOrder = $this->getOrderValueGraphData($lastFromDate, $lastToDate);
                $stats["orderValueComparison"] =  (($stats["orderValueData"]["avgOrderValue"] -
                $totalOrder["avgOrderValue"])/$totalOrder["avgOrderValue"]) * 100;
            } elseif (count($stats["topCustomer"]) > 0) {
                $stats["orderValueComparison"] = 100.00;
            }
            $stats["customerComparison"] = 0.00;
            if (count($this->getTopCustomer($lastFromDate, $lastToDate)) > 0) {
                $lastTopCustom = $this->getTopCustomer($lastFromDate, $lastToDate);
                $stats["customerComparison"] =  ((count($stats["topCustomer"]) -
                count($lastTopCustom))/count($lastTopCustom)) * 100;
            } elseif (count($stats["topCustomer"]) > 0) {
                $stats["customerComparison"] = 100.00;
            }

            $response["data"] = $stats;
            $response["status"] = true;
        } catch (\Exception $e) {
            $this->marketplaceHelper->logDataInLogger($e->getMessage());
        }
        $this->getResponse()->representJson(
            $this->marketplaceHelper->arrayToJson($response)
        );
    }
    /**
     * Get order sale stat
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getOrderSaleGraphData($dateFrom, $dateTo)
    {
        $dateFrom = new \DateTime($dateFrom);
        $dateTo = new \DateTime($dateTo);
        $graphXValue = $graphData = [];
        $collection = $this->mpSaleslistCollectionFactory->create();
        $orderValues = $totalPayouts = $remainingPayouts = $commissionPaids = 0;
        $sellerId = $this->marketplaceHelper->getCustomerId();
        $filterType = $this->getRequest()->getParam("filterType");
        $interval = $filterType == "lifetime" ? "+1 year" : "+1 day";
        for ($i = $dateFrom; $i <= $dateTo; $i->modify($interval)) {
            $graphXValue[] = $filterType == "lifetime" ? $i->format("Y") : $i->format("M-d");
            $dateInterval = $filterType == "lifetime" ? $i->format("Y") : $i->format("Y-m-d");
            $salesColl = clone $collection;
            $totalPayoutColl = clone $collection;
            $remainingPayoutColl = clone $collection;
            $commissionPaidColl = clone $collection;

            $salesColl->getSelect()
            ->columns('sum(total_amount) AS order_value')
            ->columns('DATE_FORMAT(created_at, "%y-%m-%d") as sale_date')
            ->where("cpprostatus = 1")
            ->where("created_at like '".$dateInterval."%'")
            ->where("seller_id = ".$sellerId)->group("sale_date");

            $totalPayoutColl->getSelect()
            ->columns('sum(actual_seller_amount) AS total_payout')
            ->columns('DATE_FORMAT(created_at, "%y-%m-%d") as sale_date')
            ->where("cpprostatus = 1")
            ->where("paid_status = 1")
            ->where("created_at like '".$dateInterval."%'")
            ->where("seller_id = ".$sellerId)->group("sale_date");

            $remainingPayoutColl->getSelect()
            ->columns('sum(actual_seller_amount) AS remaining_payout')
            ->columns('DATE_FORMAT(created_at, "%y-%m-%d") as sale_date')
            ->where("cpprostatus = 1")
            ->where("paid_status = 0")
            ->where("created_at like '".$dateInterval."%'")
            ->where("seller_id = ".$sellerId)->group("sale_date");

            $commissionPaidColl->getSelect()
            ->columns('sum(total_commission) AS commission_paid')
            ->columns('DATE_FORMAT(created_at, "%y-%m-%d") as sale_date')
            ->where("cpprostatus = 1")
            ->where("paid_status = 1")
            ->where("created_at like '".$dateInterval."%'")
            ->where("seller_id = ".$sellerId)->group("sale_date");
            $orderValues += $salesColl->getFirstItem()->getOrderValue() ?? 0;
            $totalPayouts += $totalPayoutColl->getFirstItem()->getTotalPayout() ?? 0;
            $remainingPayouts += $remainingPayoutColl->getFirstItem()->getRemainingPayout() ?? 0;
            $commissionPaids += $commissionPaidColl->getFirstItem()->getCommissionPaid() ?? 0;
            
            $graphData[] = $salesColl->getFirstItem()->getOrderValue() ?? 0;
        }

        $barStat["graphXValue"] = $graphXValue;
        $barStat["graphData"] = $graphData;
        $barStat["totalSale"] = $this->priceHelper->currency($orderValues, true, false);
        $barStat["totalSaleAmount"] = $orderValues;
        $barStat["totalPayout"] = $this->priceHelper->currency($totalPayouts, true, false);
        $barStat["remainingPayout"] = $this->priceHelper->currency($remainingPayouts, true, false);
        $barStat["commissionPaid"] = $this->priceHelper->currency($commissionPaids, true, false);
        return $barStat;
    }

    /**
     * Get order stat
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getOrderGraphData($dateFrom, $dateTo)
    {
        $dateFrom = new \DateTime($dateFrom);
        $dateTo = new \DateTime($dateTo);
        $graphXValue = $graphData = [];
        $collection = $this->orderListCard->getOrderCollection();
        $sellerId = $this->marketplaceHelper->getCustomerId();
        $filterType = $this->getRequest()->getParam("filterType");
        $interval = $filterType == "lifetime" ? "+1 year" : "+1 day";
        for ($i = $dateFrom; $i <= $dateTo; $i->modify($interval)) {
            $graphXValue[] = $filterType == "lifetime" ? $i->format("Y") : $i->format("M-d");
            $dateInterval = $filterType == "lifetime" ? $i->format("Y") : $i->format("Y-m-d");
            $newColl = clone $collection;
            $newColl->getSelect()
            ->columns('count(order_id) AS order_count');
            $newColl->getSelect()->where("created_at like '".$dateInterval."%'")
            ->where("seller_id = ".$sellerId);
            $graphData[] = $newColl->getSize();
        }
        $graphStat["graphXValue"] = $graphXValue;
        $graphStat["graphData"] = $graphData;
        return $graphStat;
    }

    /**
     * Get order average value stat
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getOrderValueGraphData($dateFrom, $dateTo)
    {
        $dateFrom = new \DateTime($dateFrom);
        $dateTo = new \DateTime($dateTo);
        $graphXValue = $graphData = [];
        $collection = $this->mpSaleslistCollectionFactory->create();
        $totalOrders = $orderValues = 0;
        $sellerId = $this->marketplaceHelper->getCustomerId();
        for ($i = $dateFrom; $i <= $dateTo; $i->modify('+1 day')) {
            $graphXValue[] = $i->format("M-d");
            $newColl = clone $collection;
            $newColl->getSelect()
            ->columns('sum(actual_seller_amount) AS order_value')
            ->columns('DATE_FORMAT(created_at, "%y-%m-%d") as exec_date')
            ->columns('count(distinct(order_id)) as total_order');
            $newColl->getSelect()->where("created_at like '".$i->format("Y-m-d")."%'")
            ->where("seller_id = ".$sellerId)
            ->group("exec_date");
            $orderValue = $newColl->getFirstItem()->getOrderValue() ?? 0;
            $totalOrder = $newColl->getFirstItem()->getTotalOrder() ?? 0;
            $totalOrders += $orderValue > 0 ? $totalOrder : 0;
            $orderValues += $orderValue > 0 ? $orderValue : 0;
            $graphData[] = $totalOrder > 0 ? ( $orderValue / $totalOrder ) : 0;
        }
        $barStat["graphXValue"] = $graphXValue;
        $barStat["graphData"] = $graphData;
        $avgOrderValue = !empty($totalOrders) ? $orderValues / $totalOrders : 0;
        $barStat["avgOrderValue"] = $this->priceHelper->currency($avgOrderValue, false, false);
        $barStat["avgOrderValueFormatted"] = $this->priceHelper->currency($avgOrderValue, true, false);

        return $barStat;
    }
    /**
     * Get order stat
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getOrderStat($dateFrom, $dateTo)
    {
        $all = $this->orderListCard->getOrderData(Orders::FILTER_ALL, $dateFrom, $dateTo);
        $processing = $this->orderListCard->getOrderData(Orders::FILTER_PROCESSING, $dateFrom, $dateTo);
        $complete = $this->orderListCard->getOrderData(Orders::FILTER_COMPLETE, $dateFrom, $dateTo);
        $cancel = $this->orderListCard->getOrderData(Orders::FILTER_CANCEL, $dateFrom, $dateTo);

        return [
            "all" => $all['count'],
            "processing" => $processing['count'],
            "complete" => $complete['count'],
            "cancel" => $cancel['count']
        ];
    }
    /**
     * Get top customer
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getTopCustomer($dateFrom, $dateTo)
    {
        $customers = [];
        $key = 0;
        $sellerId = $this->marketplaceHelper->getCustomerId();
        $slesCollection = $this->mpSaleslistCollectionFactory->create();
        $customerGridFlat = $slesCollection->getTable('customer_grid_flat');
        $sales = $slesCollection->addFieldToSelect(["entity_id"])
        ->addFieldToFilter('seller_id', $sellerId);

        $sales->getSelect()
        ->columns('SUM(actual_seller_amount) AS customer_base_total')
        ->columns('count(distinct(order_id)) AS order_count')
        ->group('magebuyer_id');

        $sales->getSelect()->join(
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
        $sales->addFieldToFilter(
            'created_at',
            ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
        );
        foreach ($sales as $sale) {
            $custBaseTotal = $sale->getCustomerBaseTotal();
            $customers[$key] = $sale->getData();
            $customers[$key]["name"] = ucfirst($sale->getName());
            $customers[$key]["customer_base_total"] = $this->priceCurrency->format($custBaseTotal, false, 2);
            $key++;
        }
        return $customers;
    }

    /**
     * Get top sale products
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getTopSaleProducts($dateFrom, $dateTo)
    {
        $sellerId = $this->marketplaceHelper->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'parent_item_id',
            ['null' => 'true']
        )
        ->addFieldToFilter(
            'created_at',
            ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
        )
        ->getAllOrderProducts();
        $resultData = [];
        foreach ($collection as $coll) {
            try {
                $item = $this->orderItemRepository->get($coll['order_item_id']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                continue;
            }
            $media = $this->marketplaceHelper->buildUrl("media/catalog/product");
            $image = $media."placeholder/image.jpg";
            $resultData[$coll->getId()]['name'] = $item->getName();
            $resultData[$coll->getId()]['url'] = '';
            $resultData[$coll->getId()]['image'] = $image;
            $resultData[$coll->getId()]['qty'] = $coll['qty'];
            $product = $item->getProduct();
          
            if ($product) {
             
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
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getTopSaleCategories($dateFrom, $dateTo)
    {
        $sellerId = $this->marketplaceHelper->getCustomerId();
        $collection = $this->mpSaleslistCollectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        )
        ->addFieldToFilter(
            'parent_item_id',
            ['null' => 'true']
        )
        ->addFieldToFilter(
            'created_at',
            ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
        );
        $collectionClone = clone $collection;
        $catArr = [];
        $totalOrderedProducts = 0;
        foreach ($collection as $coll) {
            $totalOrderedProducts = $totalOrderedProducts + $coll['magequantity'];
        }
        foreach ($collectionClone as $coll) {
            try {
                $item = $this->orderItemRepository->get($coll['order_item_id']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                continue;
            }
            $product = $item->getProduct();
            if ($product) {
                $productCategories = $product->getCategoryIds();
                foreach ($productCategories as $proCategory) {
                    if (isset($proCategory) && $proCategory != 1) {
                        if (!isset($catArr[$proCategory])) {
                            $catArr[$proCategory] = $coll['magequantity'];
                        } else {
                            $catArr[$proCategory] = $catArr[$proCategory] + $coll['magequantity'];
                        }
                    }
                }
            }
        }
 
        $topCategory = [];
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
                $categories[] = $category->getName();
                   
                $categoryArr["category"] = implode(" > ", $categories);
            } catch (\Exception $e) {
                unset($categoryArr[$key]);
            }
            $topCategory[] = $categoryArr;
        }
        return $topCategory;
    }
}
