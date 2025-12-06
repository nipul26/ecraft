<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Api\Data;

/**
 * Customer data interface
 */
interface CustomerDataInterface
{
    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set customer ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get firstname
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname);

    /**
     * Get lastname
     *
     * @return string|null
     */
    public function getLastname();

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get mobile number
     *
     * @return string|null
     */
    public function getMobileNumber();

    /**
     * Set mobile number
     *
     * @param string $mobileNumber
     * @return $this
     */
    public function setMobileNumber($mobileNumber);
}