<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CodezSparkMobile\ProductlistingApi\Api;

use CodezSparkMobile\ProductlistingApi\Api\Data\SettingDataInterface;

interface ProductlistInterface
{
    /**
     * Get product list
     *
     * @param string $storeId
     * @param string|null $filterData
     * @param int|null $currentPage
     * @param string|null $position
     * @return array
     */
    public function getList($storeId, $filterData = null, $currentPage = null, $position = null);
}
