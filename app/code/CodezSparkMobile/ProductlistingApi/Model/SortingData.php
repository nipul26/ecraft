<?php

namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\SortingDataInterface;
use Magento\Framework\DataObject;

class SortingData extends DataObject implements SortingDataInterface
{

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData(SortingDataInterface::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(SortingDataInterface::CODE, $code);
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(SortingDataInterface::LABEL);
    }

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData(SortingDataInterface::LABEL, $label);
    }
}
