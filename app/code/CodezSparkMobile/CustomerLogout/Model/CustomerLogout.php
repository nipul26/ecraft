<?php
/**
 * Copyright © CodezSparkMobile All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\CustomerLogout\Model;

use CodezSparkMobile\CustomerLogout\Api\CustomerLogoutInterface;
use CodezSparkMobile\CustomerLogout\Api\Data\LogoutResponseInterface;
use CodezSparkMobile\CustomerLogout\Api\Data\LogoutResponseInterfaceFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\App\ObjectManager;

/**
 * Customer Logout Model
 */
class CustomerLogout implements CustomerLogoutInterface
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var LogoutResponseInterfaceFactory
     */
    protected $logoutResponseFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @param UserContextInterface $userContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param LogoutResponseInterfaceFactory $logoutResponseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserContextInterface $userContext,
        CustomerRepositoryInterface $customerRepository,
        LogoutResponseInterfaceFactory $logoutResponseFactory,
        LoggerInterface $logger
    ) {
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->logoutResponseFactory = $logoutResponseFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function logout($customerId = null, $email = null)
    {
        $response = $this->logoutResponseFactory->create();
        try {
            // Get authenticated customer ID from token (REST API)
            $authenticatedCustomerId = null;
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $authenticatedCustomerId = $this->userContext->getUserId();
            }
            
            if (!$authenticatedCustomerId) {
                $response->setSuccess(false);
                $response->setMessage(__('Customer is not authenticated.'));
                return $response;
            }

            // If customerId is provided in request, validate it matches authenticated customer
            if ($customerId !== null) {
                $customerId = (int)$customerId;
                
                // Verify that the provided customer_id matches the authenticated customer from token
                if ($customerId !== (int)$authenticatedCustomerId) {
                    $response->setSuccess(false);
                    $response->setMessage(__('Invalid customer details'));
                    return $response;
                }
            } else {
                // Use authenticated customer ID from token if not provided
                $customerId = $authenticatedCustomerId;
            }

            // Validate customer exists in database
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $e) {
                $response->setSuccess(false);
                $response->setMessage(__('Customer not found.'));
                return $response;
            }

            // Additional validation with email if provided
            if ($email !== null && !empty($email)) {
                if ($customer->getEmail() !== $email) {
                    $response->setSuccess(false);
                    $response->setMessage(__('Invalid customer email. The provided email does not match the customer.'));
                    return $response;
                }
            }


            // clear customer session cokkie values

            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }

            // Create success response
            $response->setSuccess(true);
            $response->setMessage(__('Customer logged out successfully.'));

            $this->logger->info('Customer logout API: Customer logged out successfully', [
                'customer_id' => $customerId,
                'email' => $customer->getEmail()
            ]);

            return $response;
        } catch (LocalizedException $e) {
            $this->logger->error('Customer logout API error: ' . $e->getMessage());
            $response->setSuccess(false);
            $response->setMessage(__('Something went wrong. Please try again'));
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Customer logout API exception: ' . $e->getMessage());
            $response->setSuccess(false);
            $response->setMessage(__('Something went wrong. Please try again'));
            return $response;
        }
    }


    /**
     * Retrieve cookie manager
     *
     * @deprecated 100.1.0
     * @return PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }
}

