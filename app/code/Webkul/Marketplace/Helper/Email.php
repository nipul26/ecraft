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

namespace Webkul\Marketplace\Helper;

use Magento\Customer\Model\Session;

/**
 * Webkul Marketplace Helper Email.
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_EMAIL_SELLER_APPROVAL = 'marketplace/email/seller_approve_notification_template';
    public const XML_PATH_EMAIL_BECOME_SELLER = 'marketplace/email/becomeseller_request_notification_template';
    public const XML_PATH_EMAIL_SELLER_DISAPPROVE = 'marketplace/email/seller_disapprove_notification_template';
    public const XML_PATH_EMAIL_SELLER_STATE_PROCESSING = 'marketplace/email/seller_process_notification_template';
    public const XML_PATH_EMAIL_SELLER_DENY = 'marketplace/email/seller_deny_notification_template';
    public const XML_PATH_EMAIL_PRODUCT_DENY = 'marketplace/email/product_deny_notification_template';
    public const XML_PATH_EMAIL_PRODUCT_REMARK = 'marketplace/email/product_remark_notification_template';
    public const XML_PATH_EMAIL_NEW_PRODUCT = 'marketplace/email/new_product_notification_template';
    public const XML_PATH_EMAIL_EDIT_PRODUCT = 'marketplace/email/edit_product_notification_template';
    public const XML_PATH_EMAIL_DENY_PRODUCT = 'marketplace/email/product_deny_notification_template';
    public const XML_PATH_EMAIL_PRODUCT_QUERY = 'marketplace/email/askproductquery_seller_template';
    public const XML_PATH_EMAIL_SELLER_QUERY = 'marketplace/email/askquery_seller_template';
    public const XML_PATH_EMAIL_ADMIN_QUERY = 'marketplace/email/askquery_admin_template';
    public const XML_PATH_EMAIL_APPROVE_PRODUCT = 'marketplace/email/product_approve_notification_template';
    public const XML_PATH_EMAIL_DISAPPROVE_PRODUCT = 'marketplace/email/product_disapprove_notification_template';
    public const XML_PATH_EMAIL_ORDER_PLACED = 'marketplace/email/order_placed_notification_template';
    public const XML_PATH_EMAIL_ORDER_SHIPMENT = 'marketplace/email/order_shipment_notification_template';
    public const XML_PATH_EMAIL_ORDER_CREDITMEMO = 'marketplace/email/order_creditmemo_notification_template';
    public const XML_PATH_EMAIL_ORDER_INVOICED = 'marketplace/email/order_invoiced_notification_template';
    public const XML_PATH_EMAIL_SELLER_TRANSACTION = 'marketplace/email/seller_transaction_notification_template';
    public const XML_PATH_EMAIL_PUBLISHED_NEWS = 'marketplace/email/published_news_template';
    public const XML_PATH_EMAIL_WITHDRAWAL = 'marketplace/email/withdrawal_request_template';
    public const XML_PATH_EMAIL_PRODUCTFLAG = 'marketplace/email/product_flag_template';
    public const XML_PATH_EMAIL_SELLERFLAG = 'marketplace/email/seller_flag_template';
    public const XML_PATH_EMAIL_LOW_STOCK = 'marketplace/email/low_stock_template';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     *
     * @var int
     */
    public $_template;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
    }

    /**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     *
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store.
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Return template id.
     *
     * @param mixed $xmlPath
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Generate Template description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return $this
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $senderEmail = $senderInfo['email'];
        $adminEmail = $this->getConfigValue(
            'trans_email/ident_general/email',
            $this->getStore()->getStoreId()
        );
        $senderInfo['email'] = $adminEmail;
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name'])
            ->setReplyTo($senderEmail, $senderInfo['name']);
        return $this;
    }
    /**
     * Send mail now
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendNow($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
        $this->_inlineTranslation->resume();
    }
    /**
     * Send Query partner Email description.
     *
     * @param Mixed $data
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendQuerypartnerEmail($data, $emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        if (isset($data['product-id']) && $data['product-id']) {
            $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_PRODUCT_QUERY);
        } else {
            $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_QUERY);
        }
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Placed Order Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendPlacedOrderEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_ORDER_PLACED);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Shipment Order Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendShipmentOrderEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_ORDER_SHIPMENT);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }
    /**
     * Send Credit Memo Order Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerCreditMemoMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_ORDER_CREDITMEMO);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }
    /**
     * Send Invoiced Order Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendInvoicedOrderEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_ORDER_INVOICED);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Low Stock Notification Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendLowStockNotificationMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_LOW_STOCK);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Seller Payment Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerPaymentEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_TRANSACTION);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Product Status Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendProductStatusMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_APPROVE_PRODUCT);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Product Unapprove Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendProductUnapproveMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_DISAPPROVE_PRODUCT);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send New Seller Request description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendNewSellerRequest($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_BECOME_SELLER);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Seller Approve Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerApproveMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_APPROVAL);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Seller Disapprove Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerDisapproveMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_DISAPPROVE);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Seller Processing Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerProcessingMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_STATE_PROCESSING);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Seller Deny Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerDenyMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLER_DENY);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Product Deny Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendProductDenyMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_PRODUCT_DENY);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Product Remark Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendProductRemarkMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_PRODUCT_REMARK);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send New Product Mail description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @param bool $editFlag
     * @return void
     */
    public function sendNewProductMail($emailTemplateVariables, $senderInfo, $receiverInfo, $editFlag)
    {
        if ($editFlag == null) {
            $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_NEW_PRODUCT);
        } else {
            $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_EDIT_PRODUCT);
        }
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Query Admin Email description.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function askQueryAdminEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_ADMIN_QUERY);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Withdrawal Request Mail.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendWithdrawalRequestMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_WITHDRAWAL);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send Product Flag Mail.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return bool
     */
    public function sendProductFlagMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_PRODUCTFLAG);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
        return true;
    }

    /**
     * Send Seller Flag Mail.
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendSellerFlagMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_SELLERFLAG);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }

    /**
     * Send published news mail
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function sendPublishedNewsMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_EMAIL_PUBLISHED_NEWS);
        $this->sendNow($emailTemplateVariables, $senderInfo, $receiverInfo);
    }
}
