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

namespace Codezspark\Faq\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Store\Model\ScopeInterface;
use Codezspark\Faq\Model\Config\DefaultConfig;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ActionFactory $actionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Faq router
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $isModuleEnabled = $this->scopeConfig->getValue(
            DefaultConfig::CONFIG_PATH_IS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
        if ($isModuleEnabled) {
            $identifier = trim($request->getPathInfo(), '/');
            $faqUrl = $this->scopeConfig->getValue(DefaultConfig::FAQ_URL_CONFIG_PATH);
            if ($identifier == $faqUrl) {
                $request->setModuleName('faq');
                $request->setControllerName('index');
                $request->setActionName('index');
                return $this->actionFactory->create(
                    Forward::class
                );
            }
        }
        return null;
    }
}
