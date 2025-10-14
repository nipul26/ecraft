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
namespace Webkul\Marketplace\Api\Data;

/**
 * Marketplace OrderPendingMails Interface.
 * @api
 */
interface OrderPendingMailsInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';

    public const SELLER_ID = 'seller_id';

    public const ORDER_ID = 'order_id';

    public const MYVAR1 = 'myvar1';

    public const MYVAR2 = 'myvar2';

    public const MYVAR3 = 'myvar3';

    public const MYVAR4 = 'myvar4';

    public const MYVAR5 = 'myvar5';

    public const MYVAR6 = 'myvar6';

    public const MYVAR8 = 'myvar8';

    public const MYVAR9 = 'myvar9';

    public const ISNOTVIRTUAL = 'isNotVirtual';

    public const SENDER_NAME = 'sender_name';

    public const SENDER_EMAIL = 'sender_email';

    public const RECEIVER_NAME = 'receiver_name';

    public const RECEIVER_EMAIL = 'receiver_email';

    public const STATUS = 'status';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const REAL_ORDER_ID = 'real_order_id';

    public const ORDER_CREATED_AT = 'order_created_at';

    public const USERNAME = 'username';

    public const BILLING_INFO = 'billing_info';

    public const PAYMENT = 'payment';

    public const SHIPPING_INFO = 'shipping_info';

    public const MAIL_CONTENT = 'mail_content';

    public const SHIPPING_DESCRIPTION = 'shipping_description';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setId($id);
    /**
     * Set SellerId
     *
     * @param int $sellerId
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setSellerId($sellerId);
    /**
     * Get SellerId
     *
     * @return int
     */
    public function getSellerId();
    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setOrderId($orderId);
    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId();
    /**
     * Set Myvar1
     *
     * @param string $myvar1
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar1($myvar1);
    /**
     * Get Myvar1
     *
     * @return string
     */
    public function getMyvar1();
    /**
     * Set Myvar2
     *
     * @param string $myvar2
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar2($myvar2);
    /**
     * Get Myvar2
     *
     * @return string
     */
    public function getMyvar2();
    /**
     * Set Myvar3
     *
     * @param string $myvar3
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar3($myvar3);
    /**
     * Get Myvar3
     *
     * @return string
     */
    public function getMyvar3();
    /**
     * Set Myvar4
     *
     * @param string $myvar4
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar4($myvar4);
    /**
     * Get Myvar4
     *
     * @return string
     */
    public function getMyvar4();
    /**
     * Set Myvar5
     *
     * @param string $myvar5
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar5($myvar5);
    /**
     * Get Myvar5
     *
     * @return string
     */
    public function getMyvar5();
    /**
     * Set Myvar6
     *
     * @param string $myvar6
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar6($myvar6);
    /**
     * Get Myvar6
     *
     * @return string
     */
    public function getMyvar6();
    /**
     * Set Myvar8
     *
     * @param string $myvar8
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar8($myvar8);
    /**
     * Get Myvar8
     *
     * @return string
     */
    public function getMyvar8();
    /**
     * Set Myvar9
     *
     * @param string $myvar9
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMyvar9($myvar9);
    /**
     * Get Myvar9
     *
     * @return string
     */
    public function getMyvar9();
    /**
     * Set IsNotVirtual
     *
     * @param string $isNotVirtual
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setIsNotVirtual($isNotVirtual);
    /**
     * Get IsNotVirtual
     *
     * @return string
     */
    public function getIsNotVirtual();
    /**
     * Set SenderName
     *
     * @param string $senderName
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setSenderName($senderName);
    /**
     * Get SenderName
     *
     * @return string
     */
    public function getSenderName();
    /**
     * Set SenderEmail
     *
     * @param string $senderEmail
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setSenderEmail($senderEmail);
    /**
     * Get SenderEmail
     *
     * @return string
     */
    public function getSenderEmail();
    /**
     * Set ReceiverName
     *
     * @param string $receiverName
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setReceiverName($receiverName);
    /**
     * Get ReceiverName
     *
     * @return string
     */
    public function getReceiverName();
    /**
     * Set ReceiverEmail
     *
     * @param string $receiverEmail
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setReceiverEmail($receiverEmail);
    /**
     * Get ReceiverEmail
     *
     * @return string
     */
    public function getReceiverEmail();
    /**
     * Set Status
     *
     * @param int $status
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set realOrderId
     *
     * @param string $realOrderId
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setRealOrderId($realOrderId);
    /**
     * Get realOrderId
     *
     * @return string
     */
    public function getRealOrderId();
    /**
     * Set orderCreatedAt
     *
     * @param string $orderCreatedAt
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setOrderCreatedAt($orderCreatedAt);
    /**
     * Get orderCreatedAt
     *
     * @return string
     */
    public function getOrderCreatedAt();
    /**
     * Set Username
     *
     * @param string $username
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setUsername($username);
    /**
     * Get Username
     *
     * @return string
     */
    public function getUsername();
    /**
     * Set billingInfo
     *
     * @param string $billingInfo
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setBillingInfo($billingInfo);
    /**
     * Get billingInfo
     *
     * @return string
     */
    public function getBillingInfo();
    /**
     * Set payment
     *
     * @param string $payment
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setPayment($payment);
    /**
     * Get payment
     *
     * @return string
     */
    public function getPayment();
    /**
     * Set shippingInfo
     *
     * @param string $shippingInfo
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setShippingInfo($shippingInfo);
    /**
     * Get shippingInfo
     *
     * @return string
     */
    public function getShippingInfo();
    /**
     * Set mailContent
     *
     * @param string $mailContent
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setMailContent($mailContent);
    /**
     * Get mailContent
     *
     * @return string
     */
    public function getMailContent();
    /**
     * Set shippingDescription
     *
     * @param string $shippingDescription
     * @return Webkul\Marketplace\Api\Data\OrderPendingMailsInterface
     */
    public function setShippingDescription($shippingDescription);
    /**
     * Get shippingDescription
     *
     * @return string
     */
    public function getShippingDescription();
}
