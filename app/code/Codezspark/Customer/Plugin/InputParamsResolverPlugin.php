<?php

namespace Codezspark\Customer\Plugin;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Webapi\Controller\Rest\InputParamsResolver;
use Psr\Log\LoggerInterface;

class InputParamsResolverPlugin
{
    const MOBILENUMBER = 'mobile_number';

    /** @var Request */
    protected $request;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        Request $request,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Before resolve - extract and store mobile_number in the Request object, then remove it from content.
     *
     * @param InputParamsResolver $subject
     * @return array
     */
    public function beforeResolve(InputParamsResolver $subject)
    {
        $route = $this->request->getPathInfo();
        
        if (strpos($route, '/V1/customers') !== false && $this->request->isPost()) {
            $content = $this->request->getContent();
            
            if ($content) {
                $data = json_decode($content, true);
                
                if (isset($data['customer']['mobile_number'])) {
                    $mobileNumber = $data['customer']['mobile_number'];
                    
                    $this->request->setParam(self::MOBILENUMBER, $mobileNumber);
                    
                    // Remove mobile_number from request content to pass validation
                    unset($data['customer']['mobile_number']);
                    
                    $modifiedContent = json_encode($data);
                    
                    $this->request->setContent($modifiedContent);
                }
            }
        }
        
        return [];
    }

    /**
     * Retrieve the mobile number from the Request object.
     * * NOTE: This is accessed in the CreateAccountPlugin via dependency injection.
     */
    public function getMobileNumberFromRequest()
    {
        return $this->request->getParam(self::MOBILENUMBER);
    }
}