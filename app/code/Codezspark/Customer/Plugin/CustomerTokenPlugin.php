<?php

namespace Codezspark\Customer\Plugin;

use Magento\Integration\Model\CustomerTokenService;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

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
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param Response $response
     * @param CustomerRepositoryInterface $customerRepository
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Response $response,
        CustomerRepositoryInterface $customerRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->response = $response;
        $this->customerRepository = $customerRepository;
        $this->resourceConnection = $resourceConnection;
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
            $customerId = $customer->getId();

            // Fetch mobile_number using SQL query
            $mobileNumber = $this->getMobileNumberByCustomerId($customerId);

            $response = [
                'status' => true,
                'message' => 'Customer token generated successfully.',
                'response' => [
                    'token' => $result,
                    'id' => $customer->getId(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'email' => $customer->getEmail(),
                    'phone_number' => $mobileNumber
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

    protected function getMobileNumberByCustomerId($customerId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName  = $this->resourceConnection->getTableName('customer_entity');

        $select = $connection->select()
            ->from($tableName, ['mobile_number'])
            ->where('entity_id = ?', $customerId);

        return $connection->fetchOne($select);
    }
}