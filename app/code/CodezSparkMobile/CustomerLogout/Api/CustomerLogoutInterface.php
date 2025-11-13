<?php
/**
 * Copyright © CodezSparkMobile All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodezSparkMobile\CustomerLogout\Api;

/**
 * Customer Logout API Interface
 */
interface CustomerLogoutInterface
{
    /**
     * Logout customer
     *
     * @param int|null $customerId Customer ID to validate (optional, will use authenticated customer if not provided)
     * @param string|null $email Customer email to validate (optional, for additional validation)
     * @return \CodezSparkMobile\CustomerLogout\Api\Data\LogoutResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logout($customerId = null, $email = null);
}

