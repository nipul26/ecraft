<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CodezSparkMobile\ProductlistingApi\Model;

/**
 * SearchResults Service Data Object used for the search service requests
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class SearchResults extends \Magento\Framework\Api\AbstractSimpleObject implements
    \CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterface
{
    public const KEY_TOTAL_PAGES = 'totalpages';
    public const KEY_ITEMS = 'productList';
    public const KEY_SEARCH_CRITERIA = 'search_criteria';
    public const KEY_TOTAL_COUNT = 'totalProductCount';
    public const KEY_TOTAL_COUNT_PER_PAGES = 'totalCountPerPage';
    public const KEY_FILTER_DATA = 'filterData';
    public const KEY_SORTING_DATA = 'sortingData';

    /**
     * Get total pages
     *
     * @return int
     */
    public function getTotalPages()
    {
        return $this->_get(self::KEY_TOTAL_PAGES);
    }

    /**
     * Set total pages
     *
     * @param int $totalPages
     * @return $this
     */
    public function setTotalPages($totalPages)
    {
        return $this->setData(self::KEY_TOTAL_PAGES, $totalPages);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalProductCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }

    /**
     * Set total count
     *
     * @param int $count
     * @return $this
     */
    public function setTotalProductCount($count)
    {
        return $this->setData(self::KEY_TOTAL_COUNT, $count);
    }

    /**
     * Get total count per pages
     *
     * @return int
     */
    public function getTotalCountPerPage()
    {
        return $this->_get(self::KEY_TOTAL_COUNT_PER_PAGES);
    }

    /**
     * Set total count per pages
     *
     * @param int $getTotalCountPerPage
     * @return $this
     */
    public function setTotalCountPerPage($getTotalCountPerPage)
    {
        return $this->setData(self::KEY_TOTAL_COUNT_PER_PAGES, $getTotalCountPerPage);
    }

    /**
     * Get items
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterface[]
     */
    public function getProductList()
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * Set items
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setProductList(array $items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

    /**
     * Get filter data
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterface[]
     */
    public function getFilterData()
    {
        return $this->_get(self::KEY_FILTER_DATA) === null ? [] : $this->_get(self::KEY_FILTER_DATA);
    }

    /**
     * Set filter data
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterface[] $filterData
     * @return $this
     */
    public function setFilterData(array $filterData)
    {
        return $this->setData(self::KEY_FILTER_DATA, $filterData);
    }

    /**
     * Get sorting data
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterface[]
     */
    public function getSortingData()
    {
        return $this->_get(self::KEY_SORTING_DATA) === null ? [] : $this->_get(self::KEY_SORTING_DATA);
    }

    /**
     * Set sorting data
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterface[] $sortingData
     * @return $this
     */
    public function setSortingData(array $sortingData)
    {
        return $this->setData(self::KEY_SORTING_DATA, $sortingData);
    }
}
