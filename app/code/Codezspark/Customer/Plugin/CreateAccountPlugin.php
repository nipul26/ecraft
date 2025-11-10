<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class CreateAccountPlugin
{
    /** @var Response */
    protected $response;

    /** @var ResourceConnection */
    protected $resourceConnection;

    /** @var LoggerInterface */
    protected $logger;
    
    /** @var InputParamsResolverPlugin */
    protected $inputParamsResolverPlugin; 

    public function __construct(
        Response $response,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger,
        InputParamsResolverPlugin $inputParamsResolverPlugin 
    ) {
        $this->response = $response;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->inputParamsResolverPlugin = $inputParamsResolverPlugin;
    }

    /**
     * After Create Account Plugin: Saves the mobile number and customizes response.
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $result The newly created Customer object.
     * @return \Magento\Framework\Webapi\Rest\Response
     */
    public function afterCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $result
    ) {
        $mobileNumber = $this->inputParamsResolverPlugin->getMobileNumberFromRequest();
        
        try {
            if ($mobileNumber && $result->getId()) {
                $this->saveMobileNumberToColumn($result->getId(), $mobileNumber);
            }

            // Customize the response body
            $responseArray = [
                'status' => true,
                'message' => 'Customer account created successfully.',
                'response' => [
                    'id' => $result->getId(),
                    'firstname' => $result->getFirstname(),
                    'lastname' => $result->getLastname(),
                    'email' => $result->getEmail(),
                    'mobile_number' => $mobileNumber 
                ]
            ];
        } catch (\Exception $e) {
            $responseArray = [
                'status' => false,
                'message' => 'Unable to complete customer account creation: ' . $e->getMessage(),
                'response' => null
            ];
        }

        return $this->response->setBody(json_encode($responseArray))->sendResponse();
    }

    /**
     * Executes the raw SQL UPDATE query.
     */
    protected function saveMobileNumberToColumn($customerId, $mobileNumber)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('customer_entity');

            $connection->update(
                $tableName,
                ['mobile_number' => $mobileNumber],
                ['entity_id = ?' => $customerId]
            );
            
        } catch (\Exception $e) {
            $this->logger->error('CRITICAL: Error saving mobile number via direct SQL: ' . $e->getMessage());
        }
    }
}