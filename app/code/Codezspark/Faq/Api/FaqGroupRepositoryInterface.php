<?php
/**
 * CodezSpark
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the codezspark.com license that is
 * available through the world-wide-web at this URL:
 * https://codezspark.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    CodezSpark
 * @package     Codezspark_Faq
 * @copyright   Copyright (c) CodezSpark (https://codezspark.com/)
 * @license     https://codezspark.com/end-user-license-agreement
 */

namespace Codezspark\Faq\Api;

interface FaqGroupRepositoryInterface
{
    /**
     * Save FaqGroup
     *
     * @param \Codezspark\Faq\Api\Data\FaqGroupInterface $faqGroup
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Codezspark\Faq\Api\Data\FaqGroupInterface $faqGroup
    );

    /**
     * Retrieve FaqGroup
     *
     * @param int $faqGroupId
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($faqGroupId);

    /**
     * Retrieve FaqGroup matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Codezspark\Faq\Api\Data\FaqGroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete FaqGroup
     *
     * @param \Codezspark\Faq\Api\Data\FaqGroupInterface $faqGroup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Codezspark\Faq\Api\Data\FaqGroupInterface $faqGroup
    );

    /**
     * Delete FaqGroup by ID
     *
     * @param int $faqGroupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($faqGroupId);
}
