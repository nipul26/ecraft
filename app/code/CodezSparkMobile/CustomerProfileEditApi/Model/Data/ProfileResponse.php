<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Model\Data;

use CodezSparkMobile\CustomerProfileEditApi\Api\Data\ProfileResponseInterface;
use Magento\Framework\DataObject;

/**
 * Profile response implementation
 */
class ProfileResponse extends DataObject implements ProfileResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData('message');
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData('message', $message);
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        return $this->getData('response');
    }

    /**
     * @inheritDoc
     */
    public function setResponse($response)
    {
        return $this->setData('response', $response);
    }
}