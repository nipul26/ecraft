<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url\DecoderInterface;

class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Security\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Security\Model\AdminSessionInfoFactory
     */
    protected $currentSession;
    /**
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param DecoderInterface $urlDecoder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Security\Model\Config $config
     * @param \Magento\Security\Model\AdminSessionInfoFactory $currentSession
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        DecoderInterface $urlDecoder,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Security\Model\Config $config,
        \Magento\Security\Model\AdminSessionInfoFactory $currentSession
    ) {
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory);
        $this->urlDecoder = $urlDecoder;
        $this->viewHelper = $viewHelper;
        $this->date = $date;
        $this->config = $config;
        $this->currentSession = $currentSession;
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if ($this->isAdminloggedIn()) {
            // Get initial data from request
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $productId = (int) $this->getRequest()->getParam('id');
            $specifyOptions = $this->getRequest()->getParam('options');

            // Prepare helper and params
            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);
            $params->setSpecifyOptions($specifyOptions);

            // Render page
            try {
                $page = $this->resultPageFactory->create();
                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                return $page;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return $this->noProductRedirect();
            } catch (\Exception $e) {
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
    /**
     * Check if admin logged in or not
     *
     * @return boolean
     */
    public function isAdminloggedIn()
    {
        $sessionId = $this->getRequest()->getParam('SID');
        $dateTime = $this->date;
        $adminConfig = $this->config;
        $lifetime = $adminConfig->getAdminSessionLifetime();
        $currentTime = $dateTime->gmtTimestamp();
        $currentSession = $this->getCurrentSession($this->urlDecoder->decode($sessionId));
        $lastUpdatedTime = $dateTime->gmtTimestamp($currentSession->getUpdatedAt());
        if (!is_numeric($lastUpdatedTime)) {
            $lastUpdatedTime = strtotime($lastUpdatedTime);
        }
        if ($lastUpdatedTime >= ($currentTime - $lifetime) &&
            $currentSession->getStatus() == 1
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * GetCurrentSession
     *
     * @param  string $sessionId [admin session id]
     * @return \Magento\Security\Model\AdminSessionInfo
     */
    protected function getCurrentSession($sessionId)
    {
        $this->currentSession = $this->currentSession->create();
        $this->currentSession->load($sessionId, 'id');
        return $this->currentSession;
    }
}
