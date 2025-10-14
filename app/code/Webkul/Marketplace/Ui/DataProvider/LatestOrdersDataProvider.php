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
namespace Webkul\Marketplace\Ui\DataProvider;

use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory;
use Webkul\Marketplace\Helper\Data as HelperData;

class LatestOrdersDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Saleslist Orders collection
     *
     * @var \Webkul\Marketplace\Model\ResourceModel\Saleslist\Collection
     */
    protected $collection;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param HelperData $helperData
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        HelperData $helperData,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $sellerId = $helperData->getCustomerId();
        $collection = $collectionFactory->create();
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
        $collection->getSelect()->limit(5);
        $this->collection = $collection;
    }
}
