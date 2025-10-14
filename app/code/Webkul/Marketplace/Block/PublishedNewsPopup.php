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
use Webkul\Marketplace\Model\ControllersRepository;

class PublishedNewsPopup extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $publishedNews;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param PublishedNewsCollectionFactory $publishedNewsCollection
     * @param MpHelper $mpHelper
     * @param ControllersRepository $controllersRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        protected PublishedNewsCollectionFactory $publishedNewsCollection,
        protected MpHelper $mpHelper,
        protected ControllersRepository $controllersRepository,
        array $data = []
    ) {
        $this->publishedNewsCollection = $publishedNewsCollection;
        $this->mpHelper = $mpHelper;
        $this->controllersRepository = $controllersRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get all published news collection
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\PublishedNews\Collection
     */
    public function getCollection()
    {
        if (!$this->publishedNews) {
            $helper = $this->mpHelper;
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
            $collection->addFieldToFilter("is_read", 0);
            $this->publishedNews = $collection;
        }

        return $this->publishedNews;
    }
    /**
     * Get more news
     *
     * @return int
     */
    public function getMoreNews()
    {
        $collection = $this->getCollection();
        $counter = 0;
        if ($collection->getSize() > 3) {
            $counter = $collection->getSize() - 3;
        }
        return $counter;
    }
    /**
     * Check if popup is available on the page
     *
     * @return bool
     */
    public function isPopupAvailable()
    {
        $action = $this->getRequest()->getFullActionName();
        $action = str_replace("_", "/", $action);
        if (count($this->controllersRepository->getByPath($action))) {
            return true;
        }
        return false;
    }
}
