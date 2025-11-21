<?php

namespace CodezSparkMobile\ProductlistingApi\Model;

use CodezSparkMobile\ProductlistingApi\Api\Data\ProductBadgesInterface;
use Magento\Framework\DataObject;

class ProductBadges extends DataObject implements ProductBadgesInterface
{

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * Get Color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->_getData(self::COLOR);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {

        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set Color
     *
     * @param string $color
     * @return $this
     */
    public function setColor(string $color)
    {

        return $this->setData(self::COLOR, $color);
    }
}
