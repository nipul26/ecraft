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
namespace Webkul\Marketplace\Controller\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\StateException;
use Magento\Framework\App\RequestInterface;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Customer\Model\Url as CustomerUrl;
use Webkul\Marketplace\Model\OrdersFactory as MpOrdersModel;

class UpdateNotification extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var MpHelper
     */
    protected $mpHelper;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerUrl
     */
    protected $customerUrl;
    /**
     * @var MpOrdersModel
     */
    protected $mpOrdersModel;
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Framework\Json\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param Session $customerSession
     * @param MpHelper $mpHelper
     * @param CustomerUrl $customerUrl
     * @param MpOrdersModel $mpOrdersModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Framework\Json\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        Session $customerSession,
        MpHelper $mpHelper,
        CustomerUrl $customerUrl,
        MpOrdersModel $mpOrdersModel
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->session = $customerSession;
        $this->mpHelper = $mpHelper;
        $this->customerUrl = $customerUrl;
        $this->mpOrdersModel = $mpOrdersModel;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->session->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Update order Notification
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $data = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $data = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "Controller_Order_UpdateNotification execute : ".$e->getMessage()
            );
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$data || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors' => false,
            'message' => __('Notification marked as read.')
        ];
        try {
            $marketplaceOrder = $this->mpOrdersModel->create()->load($data['entityId']);

            if ($marketplaceOrder) {
                $marketplaceOrder->setSellerPendingNotification(0);
                $marketplaceOrder->setId($data['entityId'])->save();
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        } catch (StateException $e) {
            $this->mpHelper->logDataInLogger(
                "Controller_Order_UpdateNotification execute : ".$e->getMessage()
            );
            $message = __(
                'Something went wrong.'
            );
            $response = [
                'errors' => true,
                'message' => $message,
            ];
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        }
    }
}
