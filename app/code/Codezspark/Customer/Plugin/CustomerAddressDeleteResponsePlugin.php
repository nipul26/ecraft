<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Webapi\Rest\Response;
use Psr\Log\LoggerInterface;

class CustomerAddressDeleteResponsePlugin
{
    protected $response;
    protected $logger;

    public function __construct(Response $response, LoggerInterface $logger)
    {
        $this->response = $response;
        $this->logger   = $logger;
    }

    public function afterDeleteById(
        AddressRepositoryInterface $subject, 
        $result, 
        $addressId
    ) {
        try {
            if ($result === true) {
                $responseData = [
                    'status'  => true,
                    'message' => 'Customer address deleted successfully.',
                    'response' => [
                        'address_id' => $addressId
                    ]
                ];
            } else {
                $responseData = [
                    'status'  => false,
                    'message' => 'Address could not be deleted.',
                    'response' => [
                        'address_id' => $addressId
                    ]
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error('Customer address delete error: ' . $e->getMessage());
        }

        $this->response->setBody(json_encode($responseData))->sendResponse();
    }
}
