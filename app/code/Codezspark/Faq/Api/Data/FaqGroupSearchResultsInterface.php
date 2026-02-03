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

use Magento\Framework\Api\SearchResultsInterface;

interface FaqGroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get FaqGroup list.
     *
     * @return \Codezspark\Faq\Api\Data\FaqGroupInterface[]
     */
    public function getItems();

    /**
     * Set FaqGroup list.
     *
     * @param \Codezspark\Faq\Api\Data\FaqGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
