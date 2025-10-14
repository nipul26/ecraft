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
namespace Webkul\Marketplace\Model\Rewrite\Sitemap;

use Magento\Framework\App\ObjectManager;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapConfigReaderInterface;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    /**
     * @var \Magento\Sitemap\Helper\Data
     */
    protected $_sitemapData;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_directory;
    /**
     * @var \Webkul\Marketplace\Model\Seller
     */
    protected $_cmsFactory;
    /**
     * @var \\Magento\Sitemap\Model\ResourceModel\Cms\PageFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateModel;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    /**
     * @var ItemProviderInterface|null
     */
    protected $itemProvider;
    /**
     * @var SitemapConfigReaderInterface|null
     */
    protected $configReader;
    /**
     * @var \Magento\Sitemap\Model\SitemapItemInterfaceFactory|null
     */
    protected $sitemapItemFactory;
    
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $modelDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param MpHelper $mpHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot|null $documentRoot
     * @param ItemProviderInterface|null $itemProvider
     * @param SitemapConfigReaderInterface|null $configReader
     * @param \Magento\Sitemap\Model\SitemapItemInterfaceFactory|null $sitemapItemFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory $categoryFactory,
        \Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Sitemap\Model\ResourceModel\Cms\PageFactory $cmsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $modelDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        MpHelper $mpHelper,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        ?\Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot $documentRoot = null,
        ?ItemProviderInterface $itemProvider = null,
        ?SitemapConfigReaderInterface $configReader = null,
        ?\Magento\Sitemap\Model\SitemapItemInterfaceFactory $sitemapItemFactory = null,
        array $data = []
    ) {
        $this->mpHelper = $mpHelper;
        $this->_escaper = $escaper;
        $this->_sitemapData = $sitemapData;
        $this->filesystem = $filesystem;
        $this->_categoryFactory = $categoryFactory;
        $this->_directory = $filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::PUB
        );
        $this->_cmsFactory = $cmsFactory;
        $this->_productFactory = $productFactory;
        $this->_dateModel = $modelDate;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->dateTime = $dateTime;
        $this->itemProvider = $itemProvider ?: ObjectManager::getInstance()->get(ItemProviderInterface::class);
        $this->configReader = $configReader ?: ObjectManager::getInstance()->get(SitemapConfigReaderInterface::class);
        $this->sitemapItemFactory = $sitemapItemFactory ?: ObjectManager::getInstance()->get(
            \Magento\Sitemap\Model\SitemapItemInterfaceFactory::class
        );
        parent::__construct(
            $context,
            $registry,
            $escaper,
            $sitemapData,
            $filesystem,
            $categoryFactory,
            $productFactory,
            $cmsFactory,
            $modelDate,
            $storeManager,
            $request,
            $dateTime,
            $resource,
            $resourceCollection,
            $data,
            $documentRoot,
            $itemProvider,
            $configReader,
            $sitemapItemFactory
        );
    }
    /**
     * Initialize sitemap
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        $helper = $this->mpHelper;
        if (!$helper->includeSellerUrlInSitemap()) {
            parent::_initSitemapItems();
        }

        parent::_initSitemapItems();
        try {
            $includeProfileUrl = $helper->includeProfileUrlInSitemap();
            $profileFrequency = $helper->getFrequencyOfProfileUrlInSitemap();
            $profilePriority = $helper->getPriorityOfProfileUrlInSitemap();
            $includeCollectionUrl = $helper->includeCollectionUrlInSitemap();
            $collectionFrequency = $helper->getFrequencyOfCollectionUrlInSitemap();
            $collectionPriority = $helper->getPriorityOfCollectionUrlInSitemap();
            $fields = ["shop_url", "shop_title", "updated_at"];
            $sellerCollection = $helper->getSellerCollection();
            $sellerCollection->resetColumns();
            $sellerCollection->addFieldsToCollection($fields);
            $sellerCollection->addStoreWiseSellerColumns();
            $sellerCollection->addAllSellerUrls();

            foreach ($sellerCollection as $seller) {
                $updatedAt = $seller->getUpdatedAt();
                if ($includeProfileUrl) {
                    $this->_sitemapItems[] = $this->sitemapItemFactory->create([
                        'url' => $seller->getProfileUrl(),
                        'updatedAt' => $updatedAt,
                        'images' => [],
                        'priority' => $profilePriority,
                        'changeFrequency' => $profileFrequency,
                    ]);
                }

                if ($includeCollectionUrl) {
                    $this->_sitemapItems[] = $this->sitemapItemFactory->create([
                        'url' => $seller->getCollectionUrl(),
                        'updatedAt' => $updatedAt,
                        'images' => [],
                        'priority' => $collectionPriority,
                        'changeFrequency' => $collectionFrequency,
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger("sitemap ".$e->getMessage());
        }
    }
}
