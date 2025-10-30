<?php
namespace Codezspark\Mobilelogin\Api;

/**
 * Interface Mobilelogininterface
 * Codezspark\Mobilelogin\Api
 */
interface MobileloginInterface
{
    /**
     * @param string $storeid
     * @return string
     */
    public function getConfiguration($storeid);

}
