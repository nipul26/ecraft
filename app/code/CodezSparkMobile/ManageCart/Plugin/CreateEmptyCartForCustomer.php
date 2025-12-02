<?php
namespace CodezSparkMobile\ManageCart\Plugin;

use Magento\Quote\Api\CartManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Webapi\Rest\Response as HttpResponse;
use CodezSparkMobile\Logger\Logger\Logger as MobileLogger;

class CreateEmptyCartForCustomer
{
    protected $customerRepository;
    
    protected $response;

    protected $mobileLogger;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        HttpResponse $response,
        MobileLogger $mobileLogger
    ) {
        $this->customerRepository = $customerRepository;
        $this->response = $response;
        $this->mobileLogger = $mobileLogger;
    }

    /**
     * After plugin for createEmptyCartForCustomer
     *
     * @param CartManagementInterface $subject
     * @param int $result
     * @param int $customerId
     * @return int
     */
    public function afterCreateEmptyCartForCustomer(
        CartManagementInterface $subject,
        $result,
        $customerId
    ) {
        try {
            $responseArray = [
                'status' => true,
                'message' => 'Customer cart created successfully.',
                'response' => [
                    'quote_id' => $result
                ]
            ];
        } catch (\Exception $e) {
            $responseArray = [
                'status' => false,
                'message' => 'Something went wrong.',
                'response' => [
                    'quote_id' => ''
                ]
            ];

            $this->mobileLogger->info('Error creating customer cart: ' . $e->getMessage());
        }

        return $this->response->setBody(json_encode($responseArray))->sendResponse();
    }
}
