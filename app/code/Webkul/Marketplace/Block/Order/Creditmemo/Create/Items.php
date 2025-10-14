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

namespace Webkul\Marketplace\Block\Order\Creditmemo\Create;

class Items extends \Webkul\Marketplace\Block\Order\Items
{
    /**
     * Set collection to pager
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $itemIds = $this->getCreditmemoItems();

        $this->itemsPerPage = $this->_scopeConfig->getValue('sales/orders/items_per_page');

        $this->itemCollection = $this->itemCollectionFactory->create()
            ->addFieldToFilter('item_id', ['in' => $itemIds]);
        $marketplaceSaleslist = $this->itemCollection->getTable('marketplace_saleslist');
        $this->itemCollection->getSelect()->join(
            $marketplaceSaleslist . ' as msl',
            'msl.order_item_id = main_table.item_id AND msl.order_id = main_table.order_id',
            [
                'msl.seller_id AS seller_id',
                'msl.total_amount AS total_amount',
                'msl.actual_seller_amount AS actual_seller_amount',
                'msl.total_commission AS total_commission',
                'msl.magepro_price AS magepro_price',
                'msl.applied_coupon_amount AS applied_coupon_amount',
                'msl.total_tax AS total_tax'
            ]
        )->where('msl.seller_id = "' . $this->getCustomerId() . '" AND main_table.order_id = ' .
        $this->getOrder()->getId());
        $this->itemCollection = $this->addAdditionalFilters($this->itemCollection);

        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('marketplace_order_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            //here pager updates collection parameters
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve order registered Creditmemo instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Retrieve order registered Creditmemo itemIds
     *
     * @return array $itemIds
     */
    public function getCreditmemoItems()
    {
        $currentCreditmemoItems = $this->getCreditmemo()->getAllItems();
        $itemIds = [];
        foreach ($currentCreditmemoItems as $currentCreditmemoItem) {
            array_push($itemIds, $currentCreditmemoItem->getOrderItemId());
        }
        return $itemIds;
    }

    /**
     * Retrieve order registered Creditmemo itemIds qty array
     *
     * @return array $qtyArr
     */
    public function getCreditmemoItemsQty()
    {
        $currentCreditmemoItems = $this->getCreditmemo()->getAllItems();
        $qtyArr = [];
        foreach ($currentCreditmemoItems as $currentCreditmemoItem) {
            $qtyArr[$currentCreditmemoItem->getOrderItemId()] = $currentCreditmemoItem->getQty();
        }
        return $qtyArr;
    }
}
