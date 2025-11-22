<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\SettingDataInterface;
use Magento\Framework\DataObject;
use CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterface;

class SettingData extends DataObject implements SettingDataInterface
{

    /**
     * Get settings
     *
     * @api
     * @return SettingsInterface|null
     */
    public function getSettings()
    {
        return $this->_getData(self::DATA_SETTINGS);
    }

    /**
     * Set settings
     *
     * @api
     * @param SettingsInterface $settings
     * @return $this
     */
    public function setSettings(SettingsInterface $settings)
    {
        return $this->setData(self::DATA_SETTINGS, $settings);
    }

    /**
     * Get settings
     *
     * @api
     * @return \CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterface|null
     */
    public function getResponseData()
    {
        return $this->_getData(self::DATA);
    }

    /**
     * Set data
     *
     * @api
     * @param \CodezSparkMobile\ProductlistingApi\Api\Data\ProductSearchResultsInterface $data
     * @return $this
     */
    public function setResponseData(object $data)
    {
        return $this->setData(self::DATA, $data);
    }
}
