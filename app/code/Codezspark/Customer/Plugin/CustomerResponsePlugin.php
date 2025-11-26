<?php
declare(strict_types=1);

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\App\State;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CustomerResponsePlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var Request
     */
    protected $restRequest;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Response $response,
        State $appState,
        Request $restRequest,
        LoggerInterface $logger
    ) {
        $this->response = $response;
        $this->appState = $appState;
        $this->restRequest = $restRequest;
        $this->logger = $logger;
    }

    /**
     * Modify customer data response for REST API
     *
     * @param CustomerRepositoryInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $result
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface|array
     */
    public function afterGetById(
        CustomerRepositoryInterface $subject,
        $result,
        $customerId
    ) {
        if ($this->appState->getAreaCode() === \Magento\Framework\App\Area::AREA_WEBAPI_REST) {
            $pathInfo = $this->restRequest->getPathInfo();
            
            if ($pathInfo === '/V1/customers/me') {
                return $this->formatCustomerResponse($result);
            }
        }

        // Return original result for other cases
        return $result;
    }

    /**
     * Format customer response in custom format
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array
     */
    private function formatCustomerResponse($customer)
    {
        if (!$customer || !$customer->getId()) {
            $responseData = [
                'status' => false,
                'message' => 'Customer not found.',
                'response' => null
            ];
        }

        try {
            $addresses = [];

            foreach ($customer->getAddresses() as $address) {
                $addresses[] = $address->__toArray();
            }

            $customAttributes = [];
            if ($customer->getCustomAttributes()) {
                foreach ($customer->getCustomAttributes() as $attribute) {
                    $customAttributes[] = [
                        'attribute_code' => $attribute->getAttributeCode(),
                        'value' => $attribute->getValue()
                    ];
                }
            }

            $extensionAttributes = $customer->getExtensionAttributes();
            $extensionAttributesData = [];
            if ($extensionAttributes && $extensionAttributes->getIsSubscribed() !== null) {
                $extensionAttributesData['is_subscribed'] = (bool)$extensionAttributes->getIsSubscribed();
            }

            $customerData = [
                'id' => $customer->getId(),
                'group_id' => $customer->getGroupId(),
                'default_billing' => $customer->getDefaultBilling(),
                'default_shipping' => $customer->getDefaultShipping(),
                'created_at' => $customer->getCreatedAt(),
                'updated_at' => $customer->getUpdatedAt(),
                'created_in' => $customer->getCreatedIn(),
                'email' => $customer->getEmail(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'store_id' => $customer->getStoreId(),
                'website_id' => $customer->getWebsiteId(),
                'addresses' => $addresses,
                'disable_auto_group_change' => $customer->getDisableAutoGroupChange(),
                'extension_attributes' => $extensionAttributesData,
                'custom_attributes' => $customAttributes
            ];

            $responseData = [
                'status' => true,
                'message' => 'Customer data fetched successfully.',
                'response' => $customerData
            ];
        } catch (LocalizedException $e) {
            $this->logger->error('Customer Response Plugin : ' . $e->getMessage());
        }

        return $this->response->setBody(json_encode($responseData))->sendResponse();
    }
}