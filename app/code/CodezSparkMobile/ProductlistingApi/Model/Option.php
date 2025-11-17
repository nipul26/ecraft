<?php

namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\OptionInterface;
use Magento\Framework\DataObject;

class Option extends DataObject implements OptionInterface
{

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_getData(OptionInterface::ID);
    }

    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(OptionInterface::ID, $id);
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(OptionInterface::LABEL);
    }

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData(OptionInterface::LABEL, $label);
    }
}
