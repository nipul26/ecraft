<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CustomerBillingAddressResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Response $response,
        LoggerInterface $logger
    ) {
        $this->response = $response;
        $this->logger   = $logger;
    }

    public function aftergetDefaultBillingAddress(
        AccountManagementInterface $subject,
        AddressInterface $result
    ) {
        try {
            if (!$result || !$result instanceof AddressInterface) {
                $responseData = [
                    'status' => false,
                    'message' => 'Customer default billing address not found.',
                    'response' => []
                ];

                return $this->response->setBody(json_encode($responseData))->sendResponse();
            }

            $customerBillingAddressData = [
                'id' => $result->getId(),
                'customer_id' => $result->getCustomerId(),
                'region' => [
                    'region_code' => $result->getRegion()->getRegionCode(),
                    'region' => $result->getRegion()->getRegion(),
                    'region_id' => $result->getRegionId()
                ],
                'region_id' => $result->getRegionId(),
                'country_id' => $result->getCountryId(),
                'street' => $result->getStreet(),
                'telephone' => $result->getTelephone(),
                'postcode' => $result->getPostcode(),
                'city' => $result->getCity(),
                'firstname' => $result->getFirstname(),
                'lastname' => $result->getLastname(),
                'default_shipping' => $result->isDefaultShipping(),
                'default_billing' => $result->isDefaultBilling()
            ];

            $responseData = [
                'status' => true,
                'message' => 'Customer billing address data fetched successfully.',
                'response' => $customerBillingAddressData
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();
        } catch (\Exception $e) {
            $this->logger->error("Customer billing address API Error: " . $e->getMessage());

            $responseData = [
                'status' => false,
                'message' => 'Error fetching billing address: ' . $e->getMessage(),
                'response' => []
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();
        }
    }
}