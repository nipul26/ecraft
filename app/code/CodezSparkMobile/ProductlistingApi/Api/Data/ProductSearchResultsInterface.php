<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\ProductlistingApi\Api\Data;

interface ProductSearchResultsInterface
{
    /**
     * Get total pages.
     *
     * @return int
     */
    public function getTotalPages();

    /**
     * Set total pages.
     *
     * @param int $totalPages
     * @return $this
     */
    public function setTotalPages($totalPages);

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalProductCount();

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalProductCount($totalCount);

    /**
     * Get total count per pages.
     *
     * @return int
     */
    public function getTotalCountPerPage();

    /**
     * Set total count.
     *
     * @param int $getTotalCountPerPage
     * @return $this
     */
    public function setTotalCountPerPage($getTotalCountPerPage);

    /**
     * Get items list.
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterface[]
     */
    public function getProductList();

    /**
     * Set items list.
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setProductList(array $items);

    /**
     * Get filter data.
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterface[]
     */
    public function getFilterData();

    /**
     * Set filter data.
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterface[] $filterData
     * @return $this
     */
    public function setFilterData(array $filterData);

    /**
     * Get sorting data.
     *
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterface[]
     */
    public function getSortingData();

    /**
     * Set sorting data.
     *
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterface[] $sortingData
     * @return $this
     */
    public function setSortingData(array $sortingData);
}
