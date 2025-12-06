<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Model;

use CodezSparkMobile\CustomerProfileEditApi\Api\ProfileManagementInterface;
use CodezSparkMobile\CustomerProfileEditApi\Api\Data\ProfileResponseInterfaceFactory;
use CodezSparkMobile\CustomerProfileEditApi\Api\Data\CustomerDataInterfaceFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Authorization\Model\UserContextInterface;
use CodezSparkMobile\Logger\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;

class ProfileManagement implements ProfileManagementInterface
{
    protected $request;
    protected $customerRepository;
    protected $accountManagement;
    protected $resourceConnection;
    protected $logger;
    protected $profileResponseFactory;
    protected $customerDataFactory;
    protected $userContext;

    public function __construct(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        ResourceConnection $resourceConnection,
        Logger $logger,
        ProfileResponseInterfaceFactory $profileResponseFactory,
        CustomerDataInterfaceFactory $customerDataFactory,
        UserContextInterface $userContext
    ) {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->profileResponseFactory = $profileResponseFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->userContext = $userContext;
    }

    /**
     * Read raw JSON and update the logged-in customer's profile.
     *
     * @return \CodezSparkMobile\CustomerProfileEditApi\Api\Data\ProfileResponseInterface
     * @throws LocalizedException
     */
    public function updateProfile()
    {
        $response = $this->profileResponseFactory->create();
        
        try {
            // Get authenticated customer ID from token
            $customerId = $this->userContext->getUserId();
            $userType = $this->userContext->getUserType();

            if (!$customerId || $userType !== UserContextInterface::USER_TYPE_CUSTOMER) {
                throw new LocalizedException(__('Customer authentication is required.'));
            }

            // Load the authenticated customer
            $customer = $this->customerRepository->getById($customerId);

            $raw = $this->request->getContent();
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                throw new LocalizedException(__('Invalid JSON body.'));
            }

            $customerData = $data['customer'] ?? [];
            $currentPassword = $data['currentPassword'] ?? null;
            $newPassword = $data['newPassword'] ?? null;

            if (empty($customerData)) {
                throw new LocalizedException(__('Customer data is required.'));
            }

            if (isset($customerData['firstname'])) {
                $customer->setFirstname($customerData['firstname']);
            }
            if (isset($customerData['lastname'])) {
                $customer->setLastname($customerData['lastname']);
            }
            if (isset($customerData['email'])) {
                $customer->setEmail($customerData['email']);
            }

            $this->customerRepository->save($customer);

            if (isset($customerData['mobile_number'])) {
                $mobile = $customerData['mobile_number'];

                $this->validateMobileNumberUniqueness($mobile, $customer->getId());

                $connection = $this->resourceConnection->getConnection();
                $table = $this->resourceConnection->getTableName('customer_entity');
                $connection->update(
                    $table, 
                    ['mobile_number' => $mobile], 
                    ['entity_id = ?' => $customer->getId()]
                );
            }

            if (!empty($currentPassword) && !empty($newPassword)) {
                try {
                    $this->accountManagement->changePassword(
                        $customer->getEmail(), 
                        $currentPassword, 
                        $newPassword
                    );
                } catch (\Exception $e) {
                    $this->logger->error('Password change failed: ' . $e->getMessage());
                }
            }

            $customerDataObj = $this->customerDataFactory->create();
            $customerDataObj->setId($customer->getId())
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname())
                ->setEmail($customer->getEmail())
                ->setMobileNumber($customerData['mobile_number'] ?? null);

            $response->setStatus(true)
                ->setMessage(__('Customer profile updated successfully.'))
                ->setResponse($customerDataObj);

            return $response;

        } catch (LocalizedException $e) {
            $this->logger->error('Customer profile update error: ' . $e->getMessage());
            $response->setStatus(false)->setMessage($e->getMessage());
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Customer profile update unexpected error: ' . $e->getMessage());
            $response->setStatus(false)->setMessage(__('An error occurred while updating profile.' . $e->getMessage()));
            return $response;
        }
    }

    /**
     * Check if mobile number is unique (not used by another customer)
     *
     * @param string $mobileNumber
     * @param int $currentCustomerId
     * @return bool
     */
    protected function validateMobileNumberUniqueness($mobileNumber, $currentCustomerId = null)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('customer_entity');

        $select = $connection->select()
            ->from($tableName, ['entity_id'])
            ->where('mobile_number = ?', $mobileNumber);

        if ($currentCustomerId) {
            $select->where('entity_id != ?', $currentCustomerId);
        }

        $existingCustomers = $connection->fetchAll($select);

        if (count($existingCustomers) > 0) {
            throw new LocalizedException(
                __('A customer with the same phone number already exists')
            );
        }
    }
}