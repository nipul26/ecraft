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
namespace Webkul\Marketplace\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\Product;

class OrderItemAdditionalOptionsObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;
    /**
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     */
    public function __construct(
        \Webkul\Marketplace\Helper\Data $mpHelper
    ) {
        $this->mpHelper = $mpHelper;
    }
    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $quoteItems = [];
            $restrictProductTypes = [
                PRODUCT::PRODUCT_TYPE_CONFIGURABLE,
                PRODUCT::PRODUCT_TYPE_BUNDLE,
                PRODUCT::PRODUCT_TYPE_GROUPED
            ];
            // Assign Quote Item with Quote Item Id from quote
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $quoteItems[$quoteItem->getId()] = $quoteItem;
            }
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $quoteItemId = $orderItem->getQuoteItemId();
                $prodType = $orderItem->getProductType();
                if (in_array($prodType, $restrictProductTypes) || !isset($quoteItems[$quoteItemId])) {
                    continue;
                }
                $quoteItem = $quoteItems[$quoteItemId];
                $additionalOptions = $quoteItem->getOptionByCode('additional_options');
                if ($additionalOptions && $additionalOptions->getValue()) {
                    $options = $orderItem->getProductOptions();
                    $options['additional_options'] = $this->mpHelper->jsonToarray($additionalOptions->getValue());
                    $orderItem->setProductOptions($options);
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }
}
