<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Webapi\Rest\Response;

class CustomerAddressResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function afterSave(
        CustomerRepositoryInterface $subject, 
        $result
    ) {
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
    }
}
