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
namespace Webkul\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\Marketplace\Api\Data\OrderPendingMailsInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace OrderPendingMails Model.
 *
 * @method \Webkul\Marketplace\Model\ResourceModel\OrderPendingMails _getResource()
 * @method \Webkul\Marketplace\Model\ResourceModel\OrderPendingMails getResource()
 */
class OrderPendingMails extends AbstractModel implements OrderPendingMailsInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Paid Order status.
     */
    public const PAID_STATUS_PENDING = '0';
    public const PAID_STATUS_COMPLETE = '1';
    public const PAID_STATUS_HOLD = '2';
    public const PAID_STATUS_REFUNDED = '3';
    public const PAID_STATUS_CANCELED = '4';

    /**
     * Marketplace OrderPendingMails cache tag.
     */
    public const CACHE_TAG = 'marketplace_order_pendingemails';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_order_pendingemails';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_order_pendingemails';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\ResourceModel\OrderPendingMails::class
        );
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteOrderPendingMails();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route OrderPendingMails.
     *
     * @return \Webkul\Marketplace\Model\OrderPendingMails
     */
    public function noRouteOrderPendingMails()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId()
    {
        return parent::getData(self::SELLER_ID);
    }

    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDER_ID);
    }

    /**
     * Set Myvar1
     *
     * @param string $myvar1
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar1($myvar1)
    {
        return $this->setData(self::MYVAR1, $myvar1);
    }

    /**
     * Get Myvar1
     *
     * @return string
     */
    public function getMyvar1()
    {
        return parent::getData(self::MYVAR1);
    }

    /**
     * Set Myvar2
     *
     * @param string $myvar2
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar2($myvar2)
    {
        return $this->setData(self::MYVAR2, $myvar2);
    }

    /**
     * Get Myvar2
     *
     * @return string
     */
    public function getMyvar2()
    {
        return parent::getData(self::MYVAR2);
    }

    /**
     * Set Myvar3
     *
     * @param string $myvar3
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar3($myvar3)
    {
        return $this->setData(self::MYVAR3, $myvar3);
    }

    /**
     * Get Myvar3
     *
     * @return string
     */
    public function getMyvar3()
    {
        return parent::getData(self::MYVAR3);
    }

    /**
     * Set Myvar4
     *
     * @param string $myvar4
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar4($myvar4)
    {
        return $this->setData(self::MYVAR4, $myvar4);
    }

    /**
     * Get Myvar4
     *
     * @return string
     */
    public function getMyvar4()
    {
        return parent::getData(self::MYVAR4);
    }

    /**
     * Set Myvar5
     *
     * @param string $myvar5
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar5($myvar5)
    {
        return $this->setData(self::MYVAR5, $myvar5);
    }

    /**
     * Get Myvar5
     *
     * @return string
     */
    public function getMyvar5()
    {
        return parent::getData(self::MYVAR5);
    }

    /**
     * Set Myvar6
     *
     * @param string $myvar6
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar6($myvar6)
    {
        return $this->setData(self::MYVAR6, $myvar6);
    }

    /**
     * Get Myvar6
     *
     * @return string
     */
    public function getMyvar6()
    {
        return parent::getData(self::MYVAR6);
    }

    /**
     * Set Myvar8
     *
     * @param string $myvar8
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar8($myvar8)
    {
        return $this->setData(self::MYVAR8, $myvar8);
    }

    /**
     * Get Myvar8
     *
     * @return string
     */
    public function getMyvar8()
    {
        return parent::getData(self::MYVAR8);
    }

    /**
     * Set Myvar9
     *
     * @param string $myvar9
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMyvar9($myvar9)
    {
        return $this->setData(self::MYVAR9, $myvar9);
    }

    /**
     * Get Myvar9
     *
     * @return string
     */
    public function getMyvar9()
    {
        return parent::getData(self::MYVAR9);
    }

    /**
     * Set IsNotVirtual
     *
     * @param string $isNotVirtual
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setIsNotVirtual($isNotVirtual)
    {
        return $this->setData(self::ISNOTVIRTUAL, $isNotVirtual);
    }

    /**
     * Get IsNotVirtual
     *
     * @return string
     */
    public function getIsNotVirtual()
    {
        return parent::getData(self::ISNOTVIRTUAL);
    }

    /**
     * Set SenderName
     *
     * @param string $senderName
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * Get SenderName
     *
     * @return string
     */
    public function getSenderName()
    {
        return parent::getData(self::SENDER_NAME);
    }

    /**
     * Set SenderEmail
     *
     * @param string $senderEmail
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * Get SenderEmail
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return parent::getData(self::SENDER_EMAIL);
    }

    /**
     * Set ReceiverName
     *
     * @param string $receiverName
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setReceiverName($receiverName)
    {
        return $this->setData(self::RECEIVER_NAME, $receiverName);
    }

    /**
     * Get ReceiverName
     *
     * @return string
     */
    public function getReceiverName()
    {
        return parent::getData(self::RECEIVER_NAME);
    }

    /**
     * Set ReceiverEmail
     *
     * @param string $receiverEmail
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setReceiverEmail($receiverEmail)
    {
        return $this->setData(self::RECEIVER_EMAIL, $receiverEmail);
    }

    /**
     * Get ReceiverEmail
     *
     * @return string
     */
    public function getReceiverEmail()
    {
        return parent::getData(self::RECEIVER_EMAIL);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set realOrderId
     *
     * @param string $realOrderId
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setRealOrderId($realOrderId)
    {
        return $this->setData(self::REAL_ORDER_ID, $realOrderId);
    }

    /**
     * Get realOrderId
     *
     * @return string
     */
    public function getRealOrderId()
    {
        return parent::getData(self::REAL_ORDER_ID);
    }

    /**
     * Set orderCreatedAt
     *
     * @param string $orderCreatedAt
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setOrderCreatedAt($orderCreatedAt)
    {
        return $this->setData(self::ORDER_CREATED_AT, $orderCreatedAt);
    }

    /**
     * Get orderCreatedAt
     *
     * @return string
     */
    public function getOrderCreatedAt()
    {
        return parent::getData(self::ORDER_CREATED_AT);
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setUsername($username)
    {
        return $this->setData(self::USERNAME, $username);
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return parent::getData(self::USERNAME);
    }

    /**
     * Set billingInfo
     *
     * @param string $billingInfo
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setBillingInfo($billingInfo)
    {
        return $this->setData(self::UPDATED_AT, $billingInfo);
    }

    /**
     * Get billingInfo
     *
     * @return string
     */
    public function getBillingInfo()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set payment
     *
     * @param string $payment
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setPayment($payment)
    {
        return $this->setData(self::PAYMENT, $payment);
    }

    /**
     * Get payment
     *
     * @return string
     */
    public function getPayment()
    {
        return parent::getData(self::PAYMENT);
    }

    /**
     * Set shippingInfo
     *
     * @param string $shippingInfo
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setShippingInfo($shippingInfo)
    {
        return $this->setData(self::SHIPPING_INFO, $shippingInfo);
    }

    /**
     * Get shippingInfo
     *
     * @return string
     */
    public function getShippingInfo()
    {
        return parent::getData(self::SHIPPING_INFO);
    }

    /**
     * Set mailContent
     *
     * @param string $mailContent
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setMailContent($mailContent)
    {
        return $this->setData(self::MAIL_CONTENT, $mailContent);
    }

    /**
     * Get mailContent
     *
     * @return string
     */
    public function getMailContent()
    {
        return parent::getData(self::MAIL_CONTENT);
    }

    /**
     * Set shippingDescription
     *
     * @param string $shippingDescription
     * @return Webkul\Marketplace\Model\OrderPendingMailsInterface
     */
    public function setShippingDescription($shippingDescription)
    {
        return $this->setData(self::SHIPPING_DESCRIPTION, $shippingDescription);
    }

    /**
     * Get shippingDescription
     *
     * @return string
     */
    public function getShippingDescription()
    {
        return parent::getData(self::SHIPPING_DESCRIPTION);
    }
}
