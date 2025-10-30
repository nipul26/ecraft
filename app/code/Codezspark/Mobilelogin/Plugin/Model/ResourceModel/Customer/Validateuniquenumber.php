<?php
namespace Codezspark\Mobilelogin\Plugin\Model\ResourceModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as ResourceModel;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Codezspark\Mobilelogin\Helper\Data as Config;
use Codezspark\Mobilelogin\Setup\Patch\Data\Addmobileno;


class Validateuniquenumber
{
    private $customerCollectionFactory;
    private $config;

    public function __construct(CustomerCollectionFactory $customerCollectionFactory,Config $config) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->config = $config;
    }

    public function beforeSave(ResourceModel $subject, Customer $customer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $collection = $this->customerCollectionFactory
            ->create()
            ->addAttributeToFilter(Addmobileno::MOBILENUMBER, $customer->getData(Addmobileno::MOBILENUMBER));

        // If the customer already exists, exclude them from the query
        if ($customer->getId()) {
            $collection->addAttribuTeToFilter(
                'entity_id',
                [
                    'neq' => (int) $customer->getId(),
                ]
            );
        }

        if ($collection->getSize() > 0) {
            throw new LocalizedException(
                __('A customer with the same mobile number already exists in an associated website.')
            );
        }
    }
}
