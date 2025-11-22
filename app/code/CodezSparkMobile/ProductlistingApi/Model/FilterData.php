<?php

namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\FilterDataInterface;
use Magento\Framework\DataObject;

class FilterData extends DataObject implements FilterDataInterface
{

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData(FilterDataInterface::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(FilterDataInterface::CODE, $code);
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(FilterDataInterface::LABEL);
    }

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData(FilterDataInterface::LABEL, $label);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData(FilterDataInterface::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(FilterDataInterface::TYPE, $type);
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->_getData(FilterDataInterface::OPTIONS);
    }

    /**
     * Set options
     *
     * @param string $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this->setData(FilterDataInterface::OPTIONS, $options);
    }

    /**
     * Get minRange
     *
     * @return string
     */
    public function getMinRange()
    {
        return $this->_getData(FilterDataInterface::MIN_RANGE);
    }

    /**
     * Set minRange
     *
     * @param string $minRange
     * @return $this
     */
    public function setMinRange($minRange)
    {
        return $this->setData(FilterDataInterface::MIN_RANGE, $minRange);
    }

    /**
     * Get maxRange
     *
     * @return string
     */
    public function getMaxRange()
    {
        return $this->_getData(FilterDataInterface::MAX_RANGE);
    }

    /**
     * Set maxRange
     *
     * @param string $maxRange
     * @return $this
     */
    public function setMaxRange($maxRange)
    {
        return $this->setData(FilterDataInterface::MAX_RANGE, $maxRange);
    }
}
