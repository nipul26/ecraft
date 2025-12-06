<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Log\LoggerInterface;

class CustomerAddressDeleteResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /** 
     * @var Request 
     */
    protected $request;

    public function __construct(
        Response $response, 
        Request $request,
        LoggerInterface $logger
    ) {
        $this->response = $response;
        $this->request = $request;
        $this->logger   = $logger;
    }

    public function afterDeleteById(
        AddressRepositoryInterface $subject, 
        $result, 
        $addressId
    ) {
        try {

            $method = $this->request->getHttpMethod();

            if ($method !== 'DELETE') {
                return $result;
            }
            
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
