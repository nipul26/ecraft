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

namespace Codezspark\Faq\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Theme\Block\Html\Title as HtmlTitle;
use Codezspark\Faq\Helper\Data;
use Codezspark\Faq\Model\Config\DefaultConfig;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $helper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if ($this->helper->isEnable()) {
            $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
            $pageTitle = $this->helper->getConfig(DefaultConfig::CONFIG_PATH_PAGE_TITLE);

            if ($pageMainTitle && $pageMainTitle instanceof HtmlTitle) {
                $pageMainTitle->setPageTitle($pageTitle);
            }

            $metaTitleConfig = $this->helper->getConfig(DefaultConfig::FAQ_META_TITLE);
            $metaKeywordsConfig = $this->helper->getConfig(DefaultConfig::FAQ_META_KEYWORD);
            $metaDescriptionConfig = $this->helper->getConfig(DefaultConfig::FAQ_META_DESCRIPTION);

            $resultPage->getConfig()->getTitle()->set($metaTitleConfig);
            $resultPage->getConfig()->setDescription($metaDescriptionConfig);
            $resultPage->getConfig()->setKeywords($metaKeywordsConfig);
        }
        return $resultPage;
    }
}
