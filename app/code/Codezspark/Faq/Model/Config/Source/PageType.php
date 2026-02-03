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

namespace Codezspark\Faq\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Codezspark\Faq\Model\Config\DefaultConfig;

class PageType implements OptionSourceInterface
{
    /**
     * Get page type
     *
     * @return array
     */
    public function toOptionArray()
    {
        return  [[
            'value' => DefaultConfig::FAQ_PAGE_TYPE_SCROLL,
            'label' => 'Scroll'
        ], [
            'value' => DefaultConfig::FAQ_PAGE_TYPE_AJAX,
            'label' => 'Ajax'
        ]];
    }
}
