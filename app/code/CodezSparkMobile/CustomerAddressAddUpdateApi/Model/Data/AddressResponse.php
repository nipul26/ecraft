<?php

namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Model\Data;

use CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterface;
use Magento\Framework\DataObject;

/**
 * Address response implementation
 */
class AddressResponse extends DataObject implements AddressResponseInterface
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