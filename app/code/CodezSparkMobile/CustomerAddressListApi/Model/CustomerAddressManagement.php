<?php
namespace CodezSparkMobile\CustomerAddressListApi\Model;

use CodezSparkMobile\CustomerAddressListApi\Api\CustomerAddressManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Webapi\Rest\Response;
use CodezSparkMobile\Logger\Logger\Logger;

class CustomerAddressManagement implements CustomerAddressManagementInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserContextInterface $userContext,
        DataObjectProcessor $dataObjectProcessor,
        Response $response,
        Logger $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->userContext = $userContext;
        $this->dataObjectProcessor = $dataObjectProcessor;        
        $this->response = $response;
        $this->logger = $logger;
    }

    public function getMyAddresses()
    {
        $customerId = $this->userContext->getUserId();
        // if (!$customerId) {
        //     $this->logger->error('Customer not authenticated.');
        //     throw new LocalizedException(__('Customer not authenticated.'));
        // }

        if ($customerId === null || $this->userContext->getUserType() !== UserContextInterface::USER_TYPE_CUSTOMER) {
            $this->logger->warning('CustomerAddressListApi: Attempt to access addresses without a valid customer token.');
            // Throwing a WebapiException with 401 Unauthorized for better API response handling
            throw new WebapiException(
                __('Authentication failed. Please ensure you are logged in and provide a valid Customer Token.'),
                0,
                WebapiException::HTTP_UNAUTHORIZED
            );
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $addresses = $customer->getAddresses();

            $formattedAddresses = [];
            foreach ($addresses as $address) {
                $formattedAddresses[] = $this->dataObjectProcessor->buildOutputDataArray(
                    $address,
                    AddressInterface::class
                );
            }

            $responseData = [
                'status' => true,
                'message' => 'Customer addresses fetched successfully.',
                'response' => [
                    'addresses' => $formattedAddresses
                ]
            ];

            return $this->response->setBody(json_encode($responseData))->sendResponse();
        } catch (NoSuchEntityException $e) {
            $this->logger->error('CustomerAddressListApi: Customer not found for ID: ' . $customerId . '. Error: ' . $e->getMessage());
            
            throw new WebapiException(
                __('The customer record could not be found.'),
                0,
                WebapiException::HTTP_NOT_FOUND
            );

        } catch (\Exception $e) {
            $this->logger->critical('CustomerAddressListApi: Unexpected error for customer ID: ' . $customerId . '. Error: ' . $e->getMessage());
    
            throw new WebapiException(
                __('We encountered an unexpected error while retrieving your addresses. Please try again later.'),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
        }
    }
}