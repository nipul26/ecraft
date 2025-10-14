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
use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Helper\Email as MpEmailHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Consumer used to process news published by admin.
 */
class AdminNewsPublishedConsumer extends ConsumerConfiguration
{
    public const CONSUMER_NAME = "admin.news.published";

    public const QUEUE_NAME = "admin.news.published";

    /**
     * @var \Webkul\Marketplace\Model\AdminNewsFactory
     */
    protected $adminNewsFactory;
    /**
     * @var \Webkul\Marketplace\Model\PublishedNewsFactory
     */
    protected $publishedNewsFactory;
    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * @var MpEmailHelper
     */
    protected $mpEmailHelper;

    /**
     * Constructor function
     *
     * @param \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory
     * @param \Webkul\Marketplace\Model\PublishedNewsFactory $publishedNewsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
     * @param MpHelper $mpHelper
     * @param MpEmailHelper $mpEmailHelper
     */
    public function __construct(
        \Webkul\Marketplace\Model\AdminNewsFactory $adminNewsFactory,
        \Webkul\Marketplace\Model\PublishedNewsFactory $publishedNewsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        MpHelper $mpHelper,
        MpEmailHelper $mpEmailHelper
    ) {
        $this->adminNewsFactory = $adminNewsFactory;
        $this->publishedNewsFactory = $publishedNewsFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->mpHelper = $mpHelper;
        $this->mpEmailHelper = $mpEmailHelper;
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
            $newsEntityId = $request;
            $helper = $this->mpHelper;
            $publishedNews = $this->publishedNewsFactory->create();
            $adminNews = $publishedNews->getCollection()->getTable('marketplace_admin_news');
            $date = $this->dateTimeFactory->create()->gmtDate();
            if (empty($newsEntityId)) {
                throw new LocalizedException(
                    __('Please verify the provided data of published news')
                );
            }
            $publishNewsData = [
                "news_id" => $newsEntityId,
                "created_at" => $date,
                "updated_at" => $date,
            ];
            $senderInfo = [
                'name' => $helper->getAdminName(),
                'email' => $helper->getAdminEmailId(),
            ];
            $sellers = $helper->getSellerList();
            foreach ($sellers as $seller) {
                if ($seller["value"]) {
                    $receiverInfo = [
                        'name' => $seller["label"],
                        'email' => $helper->getCustomerData($seller["value"])->getEmail(),
                    ];
                    $publishNewsData["seller_id"] = $seller["value"];
                    $publishedNews->setData($publishNewsData)->save();
                    $collection = $publishedNews->getCollection();
                    $collection->getSelect()->join(
                        $adminNews.' as adminNews',
                        'main_table.news_id = adminNews.entity_id',
                        [
                            "content" => "adminNews.content",
                        ]
                    );
                    $collection->addFieldToFilter("status", 1);
                    $collection->addFieldToFilter("is_read", 0);
                    $collection->addFieldToFilter("adminNews.entity_id", $newsEntityId);
                    $emailTemplateVariables["content"] = $collection->getFirstItem()->getContent();
                    $emailTemplateVariables["sellerName"] = $seller["label"];
                    $emailTemplateVariables["sellerUrl"] = $helper->buildUrl("customer/account/login");
                    $this->mpEmailHelper->sendPublishedNewsMail(
                        $emailTemplateVariables,
                        $senderInfo,
                        $receiverInfo
                    );
                }
            }
        } catch (\Exception $e) {
            $helper->logDataInLogger($e->getMessage());
        }
    }
}
