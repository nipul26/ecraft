<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Api\Data;

/**
 * Profile update response interface
 */
interface ProfileResponseInterface
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
     * @return \CodezSparkMobile\CustomerProfileEditApi\Api\Data\CustomerDataInterface|null
     */
    public function getResponse();

    /**
     * Set response data
     *
     * @param \CodezSparkMobile\CustomerProfileEditApi\Api\Data\CustomerDataInterface|null $response
     * @return $this
     */
    public function setResponse($response);
}