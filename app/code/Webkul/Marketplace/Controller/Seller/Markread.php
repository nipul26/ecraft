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

namespace Webkul\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResourceConnection;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\PublishedNewsFactory;
use Webkul\Marketplace\Model\PublishedNews;

/**
 * Mark admin news read from seller end
 */
class Markread extends Action
{
    /**
     * TABLE_NAME table name
     */
    public const TABLE_NAME = 'marketplace_published_news';
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var ResourceConnection
     */
    protected $connection;

    /**
     * @var PublishedNewsFactory
     */
    protected $publishedNewsFactory;
    /**
     * Construct
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helper
     * @param ResourceConnection $resource
     * @param PublishedNewsFactory $publishedNewsFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HelperData $helper,
        ResourceConnection $resource,
        PublishedNewsFactory $publishedNewsFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->publishedNewsFactory = $publishedNewsFactory;
        parent::__construct($context);
    }

    /**
     * Mark news as read
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->helper;
        $sellerId = $helper->getCustomerId();
        $update = ["is_read" => PublishedNews::IS_READ_YES];
        $where = ["seller_id" => $sellerId];
        $counter = 0;
        if ($this->getRequest()->isAjax()) {
            $publishedId= $this->getRequest()->getParam("publishedId");
            $publishedNews = $this->publishedNewsFactory->create()->load($publishedId);
            $publishedNews->setIsRead(PublishedNews::IS_READ_YES)->save();
            $collection = $this->publishedNewsFactory->create()
            ->getCollection();
            $adminNews = $collection->getTable('marketplace_admin_news');
            $collection->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'seller_id',
                ['eq' => $sellerId]
            );
            $collection->getSelect()->join(
                $adminNews.' as adminNews',
                'main_table.news_id = adminNews.entity_id'
            );
            $collection->addFieldToFilter("status", 1);
            $collection->addFieldToFilter("is_read", 0);
            if ($collection->getSize() > 3) {
                $counter = $collection->getSize() - 3;
            }
            $this->messageManager->addSuccess(
                __(
                    'The news has been marked as read.'
                )
            );
            $this->getResponse()->representJson($helper->arrayToJson(["success" => true, "counter" => $counter]));
            return;
        }
        try {
            $this->connection->beginTransaction();
            $this->connection->update($this->resource->getTableName(self::TABLE_NAME), $update, $where);
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
        }
        $this->messageManager->addSuccess(
            __(
                'All news have been marked as read.'
            )
        );
        return $this->resultRedirectFactory->create()->setPath(
            'marketplace/account/adminnews',
            ['_secure' => $this->getRequest()->isSecure()]
        );
    }
}
