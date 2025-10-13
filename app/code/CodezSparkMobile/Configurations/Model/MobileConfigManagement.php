<?php
namespace CodezSparkMobile\Configurations\Model;

use CodezSparkMobile\Configurations\Api\MobileConfigManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Webapi\Rest\Response;

class MobileConfigManagement implements MobileConfigManagementInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Configuration Paths
     */
    const XML_PATH_SIGN_IN_TEXT         = 'CodezSparkMobileConfig/general/sign_in_text';
    const XML_PATH_SIGN_UP_TEXT         = 'CodezSparkMobileConfig/general/sign_up_text';
    const XML_PATH_FORGET_PASSWORD_TEXT = 'CodezSparkMobileConfig/general/forget_password_text';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Response $response
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Response $response
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->response = $response;
    }

    /**
     * Get Mobile Configuration
     *
     * @return \Magento\Framework\Webapi\Rest\Response
     */
    public function getMobileConfiguration()
    {
        try {
            // Fetch config values
            $data = [
                'sign_in_text'         => $this->getConfigValue(self::XML_PATH_SIGN_IN_TEXT),
                'sign_up_text'         => $this->getConfigValue(self::XML_PATH_SIGN_UP_TEXT),
                'forget_password_text' => $this->getConfigValue(self::XML_PATH_FORGET_PASSWORD_TEXT)
            ];

            $response = [
                'status'  => true,
                'message' => __('Mobile configuration fetched successfully.'),
                'data'    => $data
            ];
        } catch (\Exception $e) {
            $response = [
                'status'  => false,
                'message' => __('Something went wrong while fetching configuration: %1', $e->getMessage()),
                'data'    => []
            ];
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }

    /**
     * Get config value by path
     *
     * @param string $path
     * @return string|null
     */
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}

