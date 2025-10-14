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

namespace Webkul\Marketplace\Controller\Order\Shipment\Tracking;

class Add extends \Webkul\Marketplace\Controller\Order
{
    /**
     * Add new tracking number action
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number = $this->getRequest()->getPost('number');
            $title = $this->getRequest()->getPost('title');
            $orderId = $this->getRequest()->getParam('order_id');
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            if (empty($carrier)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please specify a carrier.')
                );
            }
            if (empty($number)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a tracking number.')
                );
            }
            if ($shipment = $this->_initShipment()) {
                $track = $this->shipmentTrack->setNumber(
                    $number
                )->setCarrierCode(
                    $carrier
                )->setTitle(
                    $title
                );
                $shipment->addTrack($track)->save();
                $trackId  = $track->getId();
                if ($track->isCustom()) {
                    $numberclass = 'display';
                    $numberclasshref = 'no-display';
                    $trackingPopupUrl = '';
                } else {
                    $numberclass = 'no-display';
                    $numberclasshref = 'display';
                    $trackingPopupUrl =$this->shippingHelper
                    ->getTrackingPopupUrlBySalesModel($track);
                }
                $response = [
                    'error' => false,
                    'carrier' => $this->helper->getCarrierTitle($carrier),
                    'title' => $title,
                    'number' => $number,
                    'numberclass' => $numberclass,
                    'numberclasshref' => $numberclasshref,
                    'trackingPopupUrl' => $trackingPopupUrl,
                    'trackingDeleteUrl' =>  $this->helper->buildUrl(
                        'marketplace/order_shipment_tracking/delete',
                        [
                            'order_id' => $orderId,
                            'shipment_id' => $shipmentId,
                            'id' => $trackId,
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    )
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => __(
                        'We can\'t initialize shipment for adding tracking number.'
                    ),
                ];
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order_Shipment_Tracking_Add execute : ".$e->getMessage()
            );
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Controller_Order_Shipment_Tracking_Add execute : ".$e->getMessage()
            );
            $response = [
                'error' => true,
                'message' => __('Cannot add tracking number.%1', $e->getMessage())
            ];
        }
        if (is_array($response)) {
            $response = $this->helper->arrayToJson($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
