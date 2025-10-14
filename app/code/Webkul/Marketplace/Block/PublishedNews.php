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
namespace Webkul\Marketplace\Block;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\ResourceModel\PublishedNews\CollectionFactory as PublishedNewsCollectionFactory;
use Webkul\Marketplace\Model\ResourceModel\AdminNews\CollectionFactory as AdminNewsCollectionFactory;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Webkul Marketplace Published News Block.
 */
class PublishedNews extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PublishedNewsCollectionFactory
     */
    protected $publishedNewsCollection;

    /**
     * @var AdminNewsCollectionFactory
     */
    protected $adminNewsCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var array
     */
    protected $publishedNews;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param PublishedNewsCollectionFactory $publishedNewsCollection
     * @param AdminNewsCollectionFactory $adminNewsCollectionFactory
     * @param TimezoneInterface $timezone
     * @param MpHelper $mpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PublishedNewsCollectionFactory $publishedNewsCollection,
        AdminNewsCollectionFactory $adminNewsCollectionFactory,
        TimezoneInterface $timezone,
        MpHelper $mpHelper,
        array $data = []
    ) {
        $this->publishedNewsCollection = $publishedNewsCollection;
        $this->adminNewsCollectionFactory = $adminNewsCollectionFactory;
        $this->mpHelper = $mpHelper;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    /**
     * Get published news collection
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\PublishedNews\Collection
     */
    public function getCollection()
    {
        if (!$this->publishedNews) {
            $helper = $this->mpHelper;
            $paramData = $this->getRequest()->getParams();
            $adminNews = $this->publishedNewsCollection->create()->getTable('marketplace_admin_news');
            $sellerId = $helper->getCustomerId();
            $collection = $this->publishedNewsCollection
            ->create()
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'seller_id',
                ['eq' => $sellerId]
            )->setOrder(
                'main_table.entity_id',
                'desc'
            );
            $collection->getSelect()->join(
                $adminNews.' as adminNews',
                'main_table.news_id = adminNews.entity_id',
                [
                    "creation_date" => "main_table.created_at",
                    "content" => "adminNews.content",
                ]
            );
            $collection->addFieldToFilter("status", 1);
            $collection->setPageSize($paramData["limit"] ?? 4);
            $collection->setCurPage($paramData["p"] ?? 1);
            $this->publishedNews = $collection;
        }

        return $this->publishedNews;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'marketplace.seller.list.pager'
            )
            ->setAvailableLimit([4 => 4, 8 => 8, 16 => 16])
            ->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }

        return $this;
    }

    /**
     * Get Pager
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Convert date
     *
     * @param string $date
     * @return string
     */
    public function convertDate($date)
    {
        return $this->timezone->date($date)->format('d F y H:i:s');
    }
}
