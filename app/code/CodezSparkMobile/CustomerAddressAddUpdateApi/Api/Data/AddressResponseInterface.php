<?php

namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data;

/**
 * Address response interface
 */
interface AddressResponseInterface
{
    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get response data
     *
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressDataInterface|null
     */
    public function getResponse();

    /**
     * Set response data
     *
     * @param \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressDataInterface|null $response
     * @return $this
     */
    public function setResponse($response);
}