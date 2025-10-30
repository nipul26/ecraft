<?php
namespace Codezspark\Mobilelogin\Model\Api;
use Magento\Framework\Exception\AuthenticationException;


use Codezspark\Mobilelogin\Helper\Data;


class Mobilelogin implements \Codezspark\Mobilelogin\Api\MobileloginInterface
{

    protected $_helperdata;
    public function __construct(Data $helper
    ) {
          $this->_helperdata=$helper;   
    }

    public function getConfiguration($storeid)
    {
        try {
            if (empty($storeid)) {
                return array(array("status"=>false, "message"=>__("Invalid parameter list.")));
            }
            if(!$this->_helperdata->isEnabled($storeid))
            {
                $response=['status'=>false,'message'=>'Please Enable Mobile login extension.'];
            }
            else{
                $optionName = "";
                if($this->_helperdata->getLoginMode($storeid) == '1')
                {
                    $optionName = $this->_helperdata->getLoginMode($storeid)." => Mobile Number and Password";
                }
                else if($this->_helperdata->getLoginMode($storeid) == '2')
                {
                    $optionName = $this->_helperdata->getLoginMode($storeid)." => Mobile Number/Email and Password";
                }
                else
                {
                    $optionName = "Please Select Option";
                }
                $data=[
                    'status' =>true,
                    'check_module_status' => $this->_helperdata->isEnabled($storeid),
                    'customerloginwith' =>$optionName
                ];
                return json_encode($data);
            }
            return json_encode($response);
        } catch (\Exception $e) {
            $data=[
                'status' => false,
                'message' =>$e->getMessage()
            ];
            return json_encode($data);
          
        }
    }   
}
