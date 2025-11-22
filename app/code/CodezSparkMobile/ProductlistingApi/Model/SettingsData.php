<?php

namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\SettingsInterface;
use Magento\Framework\DataObject;

class SettingsData extends DataObject implements SettingsInterface
{

    /**
     * Get code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_getData(self::DATA_CODE);
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData(self::DATA_MESSAGE);
    }

    /**
     * Set code
     *
     * @param int $code
     * @return $this
     */
    public function setCode(int $code)
    {
        return $this->setData(self::DATA_CODE, $code);
    }

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::DATA_MESSAGE, $message);
    }
}
