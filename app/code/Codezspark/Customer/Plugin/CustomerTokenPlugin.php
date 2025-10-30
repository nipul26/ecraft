<?php

namespace Codezspark\Customer\Plugin;

use Magento\Integration\Model\CustomerTokenService;
use Magento\Framework\Webapi\Rest\Response;

class CustomerTokenPlugin
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(
        Response $response
    ) {
        $this->response = $response;
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

        $response = [
            'status' => true,
            'message' => 'Customer token generated successfully.',
            'response' => [
                'token' => $result
            ]
        ];

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}