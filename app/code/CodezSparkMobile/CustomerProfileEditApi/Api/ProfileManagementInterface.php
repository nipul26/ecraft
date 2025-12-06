<?php

namespace CodezSparkMobile\CustomerProfileEditApi\Api;

/**
 * Interface for customer profile update service
 */
interface ProfileManagementInterface
{
    /**
     * Update authenticated customer profile
     *
     * @return \CodezSparkMobile\CustomerProfileEditApi\Api\Data\ProfileResponseInterface
     */
    public function updateProfile();
}