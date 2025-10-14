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
namespace Webkul\Marketplace\Model\ResourceModel\Orders;

use \Webkul\Marketplace\Model\ResourceModel\AbstractCollection;

/**
 * Webkul Marketplace ResourceModel Orders Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var boolean
     */
    protected $hasJoinedSellerTable = false;

    /**
     * @var string
     */
    protected $sellerTableAlias = 'mu';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\Orders::class,
            \Webkul\Marketplace\Model\ResourceModel\Orders::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
        $this->_map['fields']['created_at'] = 'main_table.created_at';
    }

    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Retrieve all Orders ids for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $collectionIdsSelect = $this->_getClearSelect();
        $collectionIdsSelect->columns('entity_id');
        $collectionIdsSelect->limit($limit, $offset);
        $collectionIdsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($collectionIdsSelect, $this->_bindParams);
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(
            \Magento\Framework\DB\Select::ORDER
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_COUNT
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_OFFSET
        );
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        );

        return $select;
    }

    /**
     * Join seller table
     */
    public function joinSellerTable()
    {
        $this->hasJoinedSellerTable = true;
        $alias = $this->sellerTableAlias;
        $sellerTable = $this->getTable('marketplace_userdata');
        $this->getSelect()->join([$alias => $sellerTable], $alias.'.seller_id = main_table.seller_id', ['*']);
    }

    /**
     * Add seller tabe filter
     *
     * @param string $field
     * @param string $filter
     */
    public function addSellerTableFilter($field, $filter)
    {
        if ($this->hasJoinedSellerTable) {
            $alias = $this->sellerTableAlias;
            $this->addFieldToFilter($alias.".".$field, $filter);
        }
    }

    /**
     * Add active seller filter
     */
    public function addActiveSellerFilter()
    {
        if ($this->hasJoinedSellerTable) {
            $alias = $this->sellerTableAlias;
            $this->addFieldToFilter($alias.".is_seller", 1);
        }
    }

    /**
     * Join with Customer Grid Flat Table
     */
    public function joinCustomer()
    {
        $joinTable = $this->getTable('customer_grid_flat');
        $this->getSelect()->join($joinTable.' as cgf', 'main_table.seller_id = cgf.entity_id');
    }

    /**
     * Get latest orders
     *
     * @param int $sellerId
     * @param array $columns
     * @return void
     */
    public function getLatestOrder($sellerId, array $columns = ["entity_id"])
    {
        $marketplaceSaleslist = $this->getTable('marketplace_saleslist');
        $orderGridFlat = $this->getTable('sales_order_grid');
        $this->addFieldToSelect($columns);
        $this->getSelect()->where('main_table.seller_id = '.$sellerId);
        $this->getSelect()->join(
            $marketplaceSaleslist.' as ms',
            'main_table.order_id = ms.order_id AND main_table.seller_id = ms.seller_id',
            [
                "magerealorder_id" => "magerealorder_id",
                "magebuyer_id" => "magebuyer_id",
                "currency_rate" => "currency_rate",
                'SUM(ms.total_tax) AS total_tax'
            ]
        )
        ->columns(
            [
                'SUM(actual_seller_amount) AS actual_seller_amount',
                'SUM(actual_seller_amount) AS purchased_actual_seller_amount',
                'SUM(applied_coupon_amount) AS applied_coupon_amount'
            ]
        )
        ->group('ms.order_id');
        $this->getSelect()->join(
            $orderGridFlat.' as ogf',
            'main_table.order_id = ogf.entity_id',
            [
                'customer_name' => 'customer_name',
                "status" => "status",
                "created_at" => "created_at",
                "order_currency_code" => "order_currency_code",
                "base_currency_code" => "base_currency_code",
                "shipping_address" => "ogf.shipping_address"
            ]
        );
        $this->getSelect()->where(
            'ogf.order_approval_status = 1'
        );
        $this->setOrder('main_table.order_id', 'DESC');
    }
}
