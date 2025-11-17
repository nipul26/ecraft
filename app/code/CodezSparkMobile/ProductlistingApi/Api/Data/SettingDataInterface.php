<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\ProductlistingApi\Api\Data;

/**
 * Interface SettingDataInterface
 *
 * @api
 */
interface SettingDataInterface
{
    public const DATA_SETTINGS = 'settings';
    public const DATA = 'response_data';

    /**
     * Get settings
     *
     * @api
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterface|null
     */
    public function getSettings();

    /**
     * Set settings
     *
     * @api
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterface $settings
     * @return $this
     */
    public function setSettings(\CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterface $settings);

    /**
     * Get data
     *
     * @api
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterface|null
     */
    public function getResponseData();

    /**
     * Set data
     *
     * @api
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterface $data
     * @return $this
     */
    public function setResponseData(object $data);
}
