<?php

namespace Codezspark\Customer\Plugin;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Webapi\Rest\Response;

class CustomerForgotPasswordPlugin
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
     * After plugin to modify the password reset response format
     *
     * @param AccountManagement $subject
     * @param string $result
     * @return mixed
     */
    public function afterInitiatePasswordReset(
        AccountManagement $subject,
        $result
    ) {

        if ($result === true) {
            $response = [
                'status' => true,
                'message' => 'Password reset email sent successfully.',
                'response' => null
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Unable to send password reset email. Please try again later.',
                'response' => null
            ];
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }
}