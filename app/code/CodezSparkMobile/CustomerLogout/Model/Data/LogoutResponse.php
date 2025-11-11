<?php
/**
 * Copyright © CodezSparkMobile All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\CustomerLogout\Model\Data;

use CodezSparkMobile\CustomerLogout\Api\Data\LogoutResponseInterface;

/**
 * Logout Response Model
 */
class LogoutResponse implements LogoutResponseInterface
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}

