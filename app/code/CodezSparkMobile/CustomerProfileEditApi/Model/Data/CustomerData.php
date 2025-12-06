<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Model\Data;

use CodezSparkMobile\CustomerProfileEditApi\Api\Data\CustomerDataInterface;
use Magento\Framework\DataObject;

/**
 * Customer data implementation
 */
class CustomerData extends DataObject implements CustomerDataInterface
{
    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        return $this->getData('firstname');
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname)
    {
        return $this->setData('firstname', $firstname);
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        return $this->getData('lastname');
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        return $this->setData('lastname', $lastname);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData('email');
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }

    /**
     * @inheritDoc
     */
    public function getMobileNumber()
    {
        return $this->getData('mobile_number');
    }

    /**
     * @inheritDoc
     */
    public function setMobileNumber($mobileNumber)
    {
        return $this->setData('mobile_number', $mobileNumber);
    }
}