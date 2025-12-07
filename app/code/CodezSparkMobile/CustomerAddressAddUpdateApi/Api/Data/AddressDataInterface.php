<?php
namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data;

/**
 * Address data interface
 */
interface AddressDataInterface
{
    /**
     * Get customer ID
     *
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get addresses
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]|null
     */
    public function getAddresses();

    /**
     * Set addresses
     *
     * @param \Magento\Customer\Api\Data\AddressInterface[] $addresses
     * @return $this
     */
    public function setAddresses($addresses);
}
