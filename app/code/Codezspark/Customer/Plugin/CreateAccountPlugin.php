<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

class CreateAccountPlugin
{
    protected $response;

    public function __construct(
        Response $response
    ) {
        $this->response = $response;
    }

    /**
     * Customize response after customer account creation
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $result
     * @param CustomerInterface $customer
     * @param string|null $password
     * @param string|null $redirectUrl
     * @return mixed
     */
    public function afterCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $result,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = null
    ) {
        try {
            $mobileNumber = $result->getCustomAttribute('mobile_number');
            $mobileNumberValue = $mobileNumber ? $mobileNumber->getValue() : null;

            $response = [
                'status' => true,
                'message' => 'Customer account created successfully.',
                'response' => [
                    'id' => $result->getId(),
                    'firstname' => $result->getFirstname(),
                    'lastname' => $result->getLastname(),
                    'email' => $result->getEmail(),
                    'phone_number' => $mobileNumberValue
                ]
            ];
        } catch (LocalizedException $e) {
            $response = [
                'status' => false,
                'message' => 'Unable to create customer account.',
                'response' => null
            ];
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}