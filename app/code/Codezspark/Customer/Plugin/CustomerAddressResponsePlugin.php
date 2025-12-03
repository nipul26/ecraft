<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CustomerAddressResponsePlugin
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
        $this->logger = $logger;
    }

    public function afterSave(
        CustomerRepositoryInterface $subject, 
        CustomerInterface $result
    ) { 
        
        try {
            $method = $this->request->getHttpMethod();

            if ($method !== 'PUT') {
                return $result;
            }

            $addresses = [];

            foreach ($result->getAddresses() as $address) {
                $addresses[] = $address->__toArray();
            }

            if (empty($addresses)) {
                $responseData = [
                    'status' => false,
                    'message' => 'No address found or address update failed.',
                    'response' => null
                ];
            } else {
                $responseData = [
                    'status' => true,
                    'message' => 'Customer address updated successfully.',
                    'response' => [
                        'customer_id' => $result->getId(),
                        'addresses' => $addresses
                    ]
                ];
            }

            return $this->response->setBody(json_encode($responseData))->sendResponse();

        } catch (\LocalizedException $e) {
            $this->logger->error('Customer address update error: ' . $e->getMessage());
        }
    }
}
