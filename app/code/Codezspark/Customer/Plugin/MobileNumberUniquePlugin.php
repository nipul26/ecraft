<?php
namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class MobileNumberUniquePlugin
{
    protected $customerCollectionFactory;

    public function __construct(CollectionFactory $customerCollectionFactory)
    {
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    public function beforeSave(
        CustomerRepositoryInterface $subject,
        CustomerInterface $customer,
        $passwordHash = null
    ) {

        // Get mobile number customer attribute value
        $mobileNumberAttribute = $customer->getCustomAttribute('mobile_number');
        $mobileNumber = $mobileNumberAttribute ? $mobileNumberAttribute->getValue() : null;

        if ($mobileNumber) {
            $collection = $this->customerCollectionFactory->create();
            $collection->addAttributeToFilter('mobile_number', $mobileNumber);

            // Exclude same customer if updating
            if ($customer->getId()) {
                $collection->addFieldToFilter('entity_id', ['neq' => $customer->getId()]);
            }

            if ($collection->getSize() > 0) {
                throw new LocalizedException(
                    __('A customer with the same phone number already exists in an associated website.')
                );
            }
        }

        return [$customer, $passwordHash];
    }
}
