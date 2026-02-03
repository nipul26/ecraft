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

interface FaqInterface
{
    public const TITLE = 'title';
    public const SORTORDER = 'sortorder';
    public const FAQ_ID = 'faq_id';
    public const CONTENT = 'content';
    public const STATUS = 'status';

    /**
     * Get faq_id
     *
     * @return int
     */
    public function getFaqId();

    /**
     * Set faq_id
     *
     * @param int $faqId
     * @return \Codezspark\Faq\Api\Data\FaqInterface
     */
    public function setFaqId($faqId);

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return \Codezspark\Faq\Api\Data\FaqInterface
     */
    public function setTitle($title);

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return \Codezspark\Faq\Api\Data\FaqInterface
     */
    public function setContent($content);

    /**
     * Get sortorder
     *
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Set sortorder
     *
     * @param string $sortOrder
     * @return \Codezspark\Faq\Api\Data\FaqInterface
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
     * @return \Codezspark\Faq\Api\Data\FaqInterface
     */
    public function setStatus($status);
}
