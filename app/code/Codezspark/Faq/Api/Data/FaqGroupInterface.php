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

namespace Codezspark\Faq\Api\Data;

interface FaqGroupInterface
{
    public const GROUPNAME = 'groupname';
    public const FAQIDS = 'faqids';
    public const FAQGROUP_ID = 'faqgroup_id';
    public const ICON = 'icon';
    public const SORTORDER = 'sortorder';
    public const STATUS = 'status';

    /**
     * Get faqgroup_id
     *
     * @return int
     */
    public function getFaqGroupId();

    /**
     * Set faqgroup_id
     *
     * @param int $faqGroupId
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setFaqGroupId($faqGroupId);

    /**
     * Get groupname
     *
     * @return int|null
     */
    public function getGroupName();

    /**
     * Set groupname
     *
     * @param string $groupName
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setGroupName($groupName);

    /**
     * Get icon
     *
     * @return string|null
     */
    public function getIcon();

    /**
     * Set icon
     *
     * @param string $icon
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setIcon($icon);

    /**
     * Get faq ids
     *
     * @return string|null
     */
    public function getFaqIds();

    /**
     * Set faq ids
     *
     * @param string $faqIds
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setFaqIds($faqIds);

    /**
     * Get sort order
     *
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface
     */
    public function setStatus($status);
}
