<?php

namespace Codezspark\Customer\Plugin;

use Magento\Integration\Model\CustomerTokenService;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class CustomerTokenPlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Response $response
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Response $response,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->response = $response;
        $this->customerRepository = $customerRepository;
    }

    /**
     * After plugin to modify the customer access token response format
     *
     * @param CustomerTokenService $subject
     * @param string $result
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function afterCreateCustomerAccessToken(
        CustomerTokenService $subject,
        $result,
        $username,
        $password
    ) {
        try {
            $customer = $this->customerRepository->get($username);

            $mobileNumber = $customer->getCustomAttribute('mobile_number');
            $mobileNumberValue = $mobileNumber ? $mobileNumber->getValue() : null;

            $response = [
                'status' => true,
                'message' => 'Customer token generated successfully.',
                'response' => [
                    'token' => $result,
                    'id' => $customer->getId(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'email' => $customer->getEmail(),
                    'phone_number' => $mobileNumberValue
                ]
            ];
        } catch (LocalizedException $e) {
            $response = [
                'status' => false,
                'message' => 'Unable to retrieve customer data.',
                'response' => [
                    'token' => $result
                ]
            ];
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}