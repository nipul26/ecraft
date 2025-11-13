<?php
/**
 * Copyright © CodezSparkMobile All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\CustomerLogout\Api\Data;

/**
 * Logout Response Interface
 */
interface LogoutResponseInterface
{
    const SUCCESS = 'success';
    const MESSAGE = 'message';

    /**
     * Get success status
     *
     * @return bool
     */
    public function getSuccess();

    /**
     * Set success status
     *
     * @param bool $success
     * @return $this
     */
    public function setSuccess($success);

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
}

