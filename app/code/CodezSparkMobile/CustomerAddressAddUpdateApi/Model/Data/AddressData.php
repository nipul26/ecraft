<?php

namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Model\Data;

use CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressDataInterface;
use Magento\Framework\DataObject;

/**
 * Address data implementation
 */
class AddressData extends DataObject implements AddressDataInterface
{
    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getAddresses()
    {
        return $this->getData('addresses');
    }

    /**
     * @inheritDoc
     */
    public function setAddresses($addresses)
    {
        return $this->setData('addresses', $addresses);
    }
}