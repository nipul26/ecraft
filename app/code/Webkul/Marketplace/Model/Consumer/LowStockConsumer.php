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
namespace Webkul\Marketplace\Model\Consumer;

use Magento\Framework\MessageQueue\ConsumerConfiguration;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Consumer used to process low stock messages.
 */
class LowStockConsumer extends ConsumerConfiguration
{
    public const CONSUMER_NAME = "seller.product.lowstock";

    public const QUEUE_NAME = "seller.product.lowstock";
    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;
    /**
     * @var MpHelper
     */
    protected $mpHelper;
    /**
     * @param MpEmailHelper $mpEmailHelper
     * @param MpHelper $mpHelper
     */
    public function __construct(
        MpEmailHelper $mpEmailHelper,
        MpHelper $mpHelper
    ) {
        $this->mpEmailHelper = $mpEmailHelper;
        $this->mpHelper = $mpHelper;
    }

    /**
     * Consumer process start
     *
     * @param string $request
     * @return string|void
     */
    public function process($request)
    {
        try {
            $lowStock = $this->mpHelper->jsonToArray($request);
            if (empty($lowStock["emailTemplateVariables"]) ||
                empty($lowStock["senderInfo"]) ||
                empty($lowStock["receiverInfo"])
            ) {
                throw new LocalizedException(
                    __('Please verify the provided data of low stock quantity')
                );
            }
            $this->mpEmailHelper->sendLowStockNotificationMail(
                $lowStock["emailTemplateVariables"],
                $lowStock["senderInfo"],
                $lowStock["receiverInfo"]
            );
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger($e->getMessage());
        }
    }
}
