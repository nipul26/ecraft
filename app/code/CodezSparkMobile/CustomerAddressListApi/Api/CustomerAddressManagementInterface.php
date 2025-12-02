<?php
namespace CodezSparkMobile\CustomerAddressListApi\Api;

interface CustomerAddressManagementInterface
{
    /**
     * Get all addresses for the current authenticated customer.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getMyAddresses();
}
