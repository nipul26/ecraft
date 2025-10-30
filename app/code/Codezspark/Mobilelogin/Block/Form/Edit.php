<?php
namespace Codezspark\Mobilelogin\Block\Form;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\SubscriberFactory;
use Codezspark\Mobilelogin\Setup\Patch\Data\Addmobileno;
use Codezspark\Mobilelogin\Helper\Data as HelperData;

class Edit extends \Magento\Customer\Block\Form\Edit
{

    private $helperData;

    public function __construct(
        Context $context,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->helperData = $helperData;
    }

    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }

    public function getMobilenumber()
    {
        $phoneAttribute = $this->getCustomer()
            ->getCustomAttribute(Addmobileno::MOBILENUMBER);
        return $phoneAttribute ? (string) $phoneAttribute->getValue() : '';
    }
}
