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

namespace Codezspark\Faq\Block;

use Magento\Framework\View\Element\Html\Link;
use Magento\Store\Model\ScopeInterface;
use Codezspark\Faq\Model\Config\DefaultConfig;

class FooterLink extends Link
{
    /**
     * Render footer link HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $isEnable = $this->_scopeConfig->isSetFlag(
            DefaultConfig::CONFIG_PATH_IS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
        $isFooterLinkEnable = $this->_scopeConfig->isSetFlag(
            DefaultConfig::CONFIG_PATH_FOOTER_LINK,
            ScopeInterface::SCOPE_STORE
        );

        if (!$isEnable || !$isFooterLinkEnable) {
            return '';
        }
        return parent::_toHtml();
    }
}
