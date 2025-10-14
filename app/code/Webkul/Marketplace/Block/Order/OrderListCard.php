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
namespace Webkul\Marketplace\Block\Order;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory as OrderCollection;
use Webkul\Marketplace\Model\Orders;
use Magento\Sales\Model\OrderFactory;

class OrderListCard extends \Magento\Framework\View\Element\Template
{
    /**
     * Construct function
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param MpHelper $mpHelper
     * @param OrderCollection $orderCollection
     * @param OrderFactory $order
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        protected MpHelper $mpHelper,
        protected OrderCollection $orderCollection,
        protected OrderFactory $order,
        protected \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
    /**
     * Get order product collection
     *
     * @return OrderCollection
     */
    public function getOrderCollection()
    {
        return $this->orderCollection->create();
    }
    /**
     * Get order product collection of specific type
     *
     * @param string $currentType
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getOrderData(string $currentType, string $dateFrom = "", string $dateTo = "")
    {
        $sellerId = $this->mpHelper->getCustomerId();
        $collection = $this->getOrderCollection()->addFieldToFilter("seller_id", $sellerId);
        $orderGridFlat = $collection->getTable('sales_order_grid');
        $collection->getSelect()->join(
            $orderGridFlat.' as ogf',
            'main_table.order_id = ogf.entity_id'
        );
        
        $type = $this->getRequest()->getParam("type");
        $status = $this->getAllOrderFilter();
        if ($currentType != Orders::FILTER_ALL) {
            $collection->addFieldToFilter("order_status", $currentType);
        }
        if (!empty($dateFrom) && !empty($dateTo)) {
            $collection->addFieldToFilter(
                'created_at',
                ['datetime' => true, 'from' => $dateFrom, 'to' => $dateTo]
            );
        }
        $collection->getSelect()->where(
            'ogf.order_approval_status = 1'
        );
         
        return [
            'url' => $this->getUrl("marketplace/order/history/type/".$currentType),
            'active' => ($type == $currentType) ? "active" :"",
            'count' => $collection->getSize(),
            'label' => $status[$currentType],
        ];
    }
    /**
     * Get all order statuses
     *
     * @return array
     */
    public function getAllOrderFilter()
    {
        return [
            Orders::FILTER_ALL => __("All Orders"),
            Orders::FILTER_PENDING => __("Pending"),
            Orders::FILTER_PROCESSING => __("Processing"),
            Orders::FILTER_HOLDED => __("On Hold"),
            Orders::FILTER_COMPLETE => __("Complete"),
            Orders::FILTER_CLOSED => __("Closed"),
            Orders::FILTER_CANCEL => __("Canceled")
        ];
    }
    /**
     * Get all latest order
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\Orders\Collection
     */
    public function getLatestOrder()
    {
        $sellerId = $this->mpHelper->getCustomerId();
        $collection = $this->orderCollection->create();
        $collection->getLatestOrder($sellerId, [
            "seller_id",
            "order_id",
            "product_ids",
            "shipment_id",
            "invoice_id",
            "creditmemo_id",
            "is_canceled",
            "order_status",
            "shipping_charges",
            "carrier_name",
            "tracking_number",
            "updated_at",
            "tax_to_seller",
            "coupon_amount",
            "refunded_coupon_amount",
            "refunded_shipping_charges",
            "seller_pending_notification"
        ]);
        return $collection;
    }
    /**
     * Get all image of order products
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderImage($orderId)
    {
        $order = $this->order->create()->load($orderId);
        $items = $order->getAllVisibleItems();
        $item =  array_slice($items, 0, 3);
        $images = [];
        foreach ($item as $item):
            $imageContent = [];
            $media = $this->getUrl("media/catalog/product");
            $image = $media."placeholder/image.jpg";
            if ($item->getProduct()) {
                if ($item->getProduct()->getCustomAttribute("thumbnail")) {
                    $image = $media.$item->getProduct()->getCustomAttribute("thumbnail")->getValue();
                }
                $imageContent['name'] = $item->getProduct()->getName();
                $imageContent['url'] = $item->getProduct()->getProductUrl();
                $imageContent['qty'] = round($item->getQtyOrdered());
                $imageContent['image'] = $image;
                $images[] = $imageContent;
            }
        endforeach;
        return $images;
    }
    /**
     * Get formatted curreny price
     *
     * @param int|float $price
     * @return string
     */
    public function getFormatedPrice($price = 0)
    {
        return $this->mpHelper->getFormatedPrice($price);
    }
    /**
     * Get converted date
     *
     * @param string $date
     * @return string
     */
    public function getConvertedDate($date)
    {
        return $this->timezone->date($date)->format('Y-m-d H:i:s');
    }
}
