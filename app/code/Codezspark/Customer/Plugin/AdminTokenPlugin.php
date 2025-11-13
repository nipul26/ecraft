<?php

namespace Codezspark\Customer\Plugin;

use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Exception\LocalizedException;

class AdminTokenPlugin
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
     * @param AdminTokenServiceInterface $subject
     * @param string $result
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function afterCreateAdminAccessToken(
        AdminTokenServiceInterface $subject,
        $result,
        $username,
        $password
    ) {

        try {
            $response = [
                'status' => true,
                'message' => 'Admin token generated successfully.',
                'response' => [
                    'token' => $result
                ]
            ];
        } catch (LocalizedException $e) {
            $response = [
                'status' => false,
                'message' => 'Unable to retrieve admin token',
                'response' => [
                    'token' => null
                ]
            ];
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}
