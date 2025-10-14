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

namespace Webkul\Marketplace\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\OrderPendingMailsFactory;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;

/**
 * Class MassApprove used to mass mass approved.
 */
class MassApprove extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var OrderPendingMailsFactory
     */
    protected $orderPendingMails;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;
    /**
     * @var \Webkul\Marketplace\Helper\Notification
     */
    protected $notificationHelper;
    /**
     * @var \Webkul\Marketplace\Model\OrdersFactory
     */
    protected $ordersFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MpHelper $mpHelper
     * @param OrderPendingMailsFactory $orderPendingMails
     * @param MpEmailHelper $mpEmailHelper
     * @param \Webkul\Marketplace\Helper\Notification $notificationHelper
     * @param \Webkul\Marketplace\Model\OrdersFactory $ordersFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MpHelper $mpHelper,
        OrderPendingMailsFactory $orderPendingMails,
        MpEmailHelper $mpEmailHelper,
        \Webkul\Marketplace\Helper\Notification $notificationHelper,
        \Webkul\Marketplace\Model\OrdersFactory $ordersFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->mpHelper = $mpHelper;
        $this->orderPendingMails = $orderPendingMails;
        $this->mpEmailHelper = $mpEmailHelper;
        $this->notificationHelper = $notificationHelper;
        $this->ordersFactory = $ordersFactory;
        parent::__construct($context);
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
            $orderPendingMailsCollection = $this->orderPendingMails->create()
            ->getCollection()
            ->addFieldToFilter('status', 0)
            ->addFieldToFilter(
                'order_id',
                $item->getId()
            );
            
            foreach ($orderPendingMailsCollection as $value) {
                $mpOrderId = $this->ordersFactory->create()->getCollection()
                            ->addFieldToFilter('seller_id', $value->getSellerId())
                            ->addFieldToFilter('order_id', $value->getOrderId())->getFirstItem();
                $emailTempVariables['realOrderId'] = $value['real_order_id'];
                $emailTempVariables['createdAt'] = $value['order_created_at'];
                $emailTempVariables['username'] = $value['username'];
                $emailTempVariables['billingInfo'] = $value['billing_info'];
                $emailTempVariables['payment'] = $value['payment'];
                $emailTempVariables['shippingInfo'] = $value['shipping_info'];
                $emailTempVariables['mailData'] = $this->mpHelper->jsonToArray($value['mail_content']);
                $emailTempVariables['shippingDes'] = $value['shipping_description'];
                $emailTempVariables['isNotVirtual'] = $value['isNotVirtual'];
                $senderInfo = [];
                $senderInfo['name'] = $value['sender_name'];
                $senderInfo['email'] = $value['sender_email'];

                $receiverInfo = [];
                $receiverInfo['name'] = $value['receiver_name'];
                $receiverInfo['email'] = $value['receiver_email'];
                $value->setStatus(1)->save();
                $item->setOrderApprovalStatus(1)->save();
                $this->notificationHelper->saveNotification(
                    \Webkul\Marketplace\Model\Notification::TYPE_ORDER,
                    $mpOrderId->getEntityId(),
                    $value->getOrderId()
                );
                
                $this->mpEmailHelper->sendPlacedOrderEmail(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo
                );
            }

            $this->_eventManager->dispatch(
                'mp_approve_order',
                ['order' => $item]
            );
        }
        $this->messageManager->addSuccess(
            __(
                'A total of %1 record(s) have been approved.',
                $collection->getSize()
            )
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/');
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::seller');
    }
}
