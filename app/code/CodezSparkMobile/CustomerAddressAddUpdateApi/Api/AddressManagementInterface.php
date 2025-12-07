<?php

namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Api;

/**
 * Interface for customer address management service
 */
interface AddressManagementInterface
{
    /**
     * Add new customer address
     *
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterface
     */
    public function addAddress();

    /**
     * Update existing customer address
     *
     * @param int $addressId
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterface
     */
    public function updateAddress($addressId);
}