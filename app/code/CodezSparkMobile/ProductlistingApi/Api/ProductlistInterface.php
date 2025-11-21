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
     * Get Product List
     *
     * @param  string $storeId
     * @param  string $filterData
     * @param  string $currentPage
     * @param  string $position
     * @return SettingDataInterface
     */
    public function getList($storeId, $filterData = null, $currentPage = null, $position = null);
}
