<?php
namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

class MobileNumberUniquePlugin
{
    protected $logger;
    protected $resourceConnection;
    protected $inputParamsResolverPlugin;

    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        InputParamsResolverPlugin $inputParamsResolverPlugin
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->inputParamsResolverPlugin = $inputParamsResolverPlugin;
    }

    public function beforeSave(
        CustomerRepositoryInterface $subject,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $mobileNumber = $this->inputParamsResolverPlugin->getMobileNumberFromRequest();

        if ($mobileNumber) {
            $this->validateMobileNumberUniqueness($mobileNumber, $customer->getId());
        }

        return [$customer, $passwordHash];
    }

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
