<?php

namespace CodezSparkMobile\CustomerAddressAddUpdateApi\Model;

use CodezSparkMobile\CustomerAddressAddUpdateApi\Api\AddressManagementInterface;
use CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterfaceFactory;
use CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressDataInterfaceFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class AddressManagement implements AddressManagementInterface
{
    protected $request;
    protected $customerRepository;
    protected $addressRepository;
    protected $addressFactory;
    protected $regionFactory;
    protected $userContext;
    protected $logger;
    protected $addressResponseFactory;
    protected $addressDataFactory;

    public function __construct(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressFactory,
        RegionInterfaceFactory $regionFactory,
        UserContextInterface $userContext,
        LoggerInterface $logger,
        AddressResponseInterfaceFactory $addressResponseFactory,
        AddressDataInterfaceFactory $addressDataFactory
    ) {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->userContext = $userContext;
        $this->logger = $logger;
        $this->addressResponseFactory = $addressResponseFactory;
        $this->addressDataFactory = $addressDataFactory;
    }

    /**
     * Add new customer address
     *
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterface
     */
    public function addAddress()
    {
        $response = $this->addressResponseFactory->create();

        try {
            // Get authenticated customer ID from token
            $customerId = $this->userContext->getUserId();
            $userType = $this->userContext->getUserType();

            if (!$customerId || $userType !== UserContextInterface::USER_TYPE_CUSTOMER) {
                throw new LocalizedException(__('Customer authentication is required.'));
            }

            // Parse request body
            $data = $this->getRequestData();

            // Extract address data from object format
            if (isset($data['address'][0])) {
                $this->logger->error('Array format not supported. Use object format.');
                throw new LocalizedException(__('Array format not supported. Use object format.'));
            }

            $addressData = $data['address'] ?? [];

            if (empty($addressData)) {
                throw new LocalizedException(__('Address data cannot be empty.'));
            }

            // Validate required fields
            $this->validateAddressData($addressData);

            // Create new address
            $address = $this->addressFactory->create();
            $address->setCustomerId($customerId);

            // Set address fields using helper method
            $this->setAddressFields($address, $addressData);

            // Save address
            $savedAddress = $this->addressRepository->save($address);

            // Prepare response
            $responseData = $this->prepareResponse($customerId);

            $response->setStatus(true)
                ->setMessage(__('Customer address added successfully.'))
                ->setResponse($responseData);

            return $response;

        } catch (LocalizedException $e) {
            $this->logger->error('Address add error: ' . $e->getMessage());
            $response->setStatus(false)->setMessage($e->getMessage());
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Address add unexpected error: ' . $e->getMessage());
            $this->logger->error('Exception trace: ' . $e->getTraceAsString());
            $response->setStatus(false)->setMessage(__('An error occurred while adding address: %1', $e->getMessage()));
            return $response;
        }
    }

    /**
     * Update existing customer address
     *
     * @param int $addressId
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressResponseInterface
     */
    public function updateAddress($addressId)
    {
        $response = $this->addressResponseFactory->create();

        try {
            // Get authenticated customer ID from token
            $customerId = $this->userContext->getUserId();
            $userType = $this->userContext->getUserType();

            if (!$customerId || $userType !== UserContextInterface::USER_TYPE_CUSTOMER) {
                throw new LocalizedException(__('Customer authentication is required.'));
            }

            // Parse request body
            $data = $this->getRequestData();

            // Extract address data from object format
            if (isset($data['address'][0])) {
                $this->logger->error('Array format not supported. Use object format.');
                throw new LocalizedException(__('Array format not supported. Use object format.'));
            }

            $addressData = $data['address'] ?? [];

            if (empty($addressData)) {
                throw new LocalizedException(__('Address data cannot be empty.'));
            }

            try {
                $address = $this->addressRepository->getById($addressId);
                
                if ($address->getCustomerId() != $customerId) {
                    throw new LocalizedException(__('You are not authorized to modify this address.'));
                }
            } catch (\Exception $e) {
                throw new LocalizedException(__('Address not found or you do not have permission to update it.'));
            }

            // Update address fields using helper method
            $this->setAddressFields($address, $addressData);

            // Save address
            $savedAddress = $this->addressRepository->save($address);

            // Prepare response
            $responseData = $this->prepareResponse($customerId);

            $response->setStatus(true)
                ->setMessage(__('Customer address updated successfully.'))
                ->setResponse($responseData);

            return $response;

        } catch (LocalizedException $e) {
            $this->logger->error('Address update error: ' . $e->getMessage());
            $response->setStatus(false)->setMessage($e->getMessage());
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Address update unexpected error: ' . $e->getMessage());
            $response->setStatus(false)->setMessage(__('An error occurred while updating address.'));
            return $response;
        }
    }

    /**
     * Get request data from body params or raw JSON
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getRequestData(): array
    {
        $data = $this->request->getBodyParams();
        
        if (empty($data)) {
            $raw = $this->request->getContent();
            $data = json_decode($raw, true);
        }

        if (!is_array($data)) {
            $this->logger->error('Invalid request data received');
            throw new LocalizedException(__('Invalid request body.'));
        }

        return $data;
    }

    /**
     * Validate address data
     *
     * @param array $addressData
     * @throws LocalizedException
     */
    protected function validateAddressData(array $addressData): void
    {
        $requiredFields = ['firstname', 'lastname', 'street', 'city', 'postcode', 'telephone', 'country_id'];
        
        foreach ($requiredFields as $field) {
            if (empty($addressData[$field])) {
                $this->logger->error("Missing required field: {$field}. Available fields: " . implode(', ', array_keys($addressData)));
                throw new LocalizedException(__('Field "%1" is required.', $field));
            }
        }

        if (!is_array($addressData['street']) || empty($addressData['street'][0])) {
            throw new LocalizedException(__('Street must be an array with at least one line.'));
        }
    }

    /**
     * Set address fields from data array
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @param array $addressData
     * @return void
     */
    protected function setAddressFields($address, array $addressData): void
    {
        if (isset($addressData['firstname'])) {
            $address->setFirstname($addressData['firstname']);
        }
        if (isset($addressData['lastname'])) {
            $address->setLastname($addressData['lastname']);
        }
        if (isset($addressData['street'])) {
            $street = is_array($addressData['street']) 
                ? $addressData['street'] 
                : [$addressData['street']];
            $address->setStreet($street);
        }
        if (isset($addressData['city'])) {
            $address->setCity($addressData['city']);
        }
        if (isset($addressData['postcode'])) {
            $address->setPostcode($addressData['postcode']);
        }
        if (isset($addressData['telephone'])) {
            $address->setTelephone($addressData['telephone']);
        }
        if (isset($addressData['country_id'])) {
            $address->setCountryId($addressData['country_id']);
        }

        if (isset($addressData['region_id'])) {
            $address->setRegionId($addressData['region_id']);
        }
        if (isset($addressData['region'])) {
            $region = $this->regionFactory->create();
            if (is_array($addressData['region'])) {
                if (isset($addressData['region']['region'])) {
                    $region->setRegion($addressData['region']['region']);
                }
                if (isset($addressData['region']['region_code'])) {
                    $region->setRegionCode($addressData['region']['region_code']);
                }
                if (isset($addressData['region']['region_id'])) {
                    $region->setRegionId($addressData['region']['region_id']);
                }
            } else {
                $region->setRegion($addressData['region']);
            }
            $address->setRegion($region);
        }

        if (isset($addressData['default_billing'])) {
            $address->setIsDefaultBilling($addressData['default_billing']);
        }
        if (isset($addressData['default_shipping'])) {
            $address->setIsDefaultShipping($addressData['default_shipping']);
        }
    }

    /**
     * Prepare response with all customer addresses
     *
     * @param int $customerId
     * @return \CodezSparkMobile\CustomerAddressAddUpdateApi\Api\Data\AddressDataInterface
     */
    protected function prepareResponse(int $customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $addresses = $customer->getAddresses();

        $addressDataObj = $this->addressDataFactory->create();
        $addressDataObj->setCustomerId($customerId)
            ->setAddresses($addresses);

        return $addressDataObj;
    }
}
