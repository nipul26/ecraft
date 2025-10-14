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

namespace Webkul\Marketplace\Plugin\Backend\Controller\Adminhtml\System\Store;

use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\Controller\Adminhtml\System\Store\Save as StoreController;

class Save
{
    /**
     * @var WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Constructor function
     *
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        WebsiteRepositoryInterface $websiteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->reader = $reader;
        $this->messageManager = $messageManager;
    }

    /**
     * After Execute plugin
     *
     * @param \Magento\Backend\Controller\Adminhtml\System\Store\Save $subject
     * @param \Magento\Framework\Controller\Result\RedirectFactory $result
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function afterExecute(StoreController $subject, $result)
    {
        try {
            $params = $subject->getRequest()->getParams();
         
            if (isset($params['store']['code'])) {
                $storecode =  $params['store']['code'];
                $stores = $this->storeManager->getStores(true, true);
                if (isset($stores[$storecode])) {
                    $storeId = (int)$stores[$storecode]->getId();
                    $this->createMediaFiles($storeId, 'stores');
                }
            } elseif (isset($params['website']['code'])) {
                $websiteId = null;
                $website = $this->websiteRepository->get($params['website']['code']);
                $websiteId = (int)$website->getId();
                $this->createMediaFiles($websiteId, 'websites');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $result;
    }

    /**
     * Create Default Media Files
     *
     * @param int $id
     * @param string $name
     * @return void
     */
    public function createMediaFiles($id, $name)
    {
        $mediaDirectories = [];

        $mediaDirectories[] = "avatar/" . $name . "/" . $id;
        $mediaDirectories[] = "marketplace/banner/" . $name . "/" . $id;
        $mediaDirectories[] = "marketplace/icon/" . $name . "/" . $id;
        $mediaDirectories[] = "placeholder" . $name . "/" . $id;

        foreach ($mediaDirectories as $mediaDirectory) {
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $path = $directory->getAbsolutePath($mediaDirectory);
            if (!$this->file->fileExists($path)) {
                $this->file->mkdir($path, 0777, true);
            }
        }
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $ds = "/";
        $baseModulePath = $this->reader->getModuleDir('', 'Webkul_Marketplace');

        $mediaDetails = [];

        $mediaDetails = [
            "avatar/" . $name . "/" . $id => [
                "view/base/web/images/avatar" => [
                    "banner-image.png",
                    "noimage.png"
                ]
            ],
            "marketplace/banner/" . $name . "/" . $id => [
                "view/base/web/images/marketplace/banner" => [
                    "sell-page-banner.png"
                ],
                "view/base/web/images/landingpage1/banner" => [
                    "sell-page-1-hero-banner.jpg"
                ],
                "view/base/web/images/landingpage2/banner" => [
                    "sell-page-2-hero-banner.jpg"
                ]
            ],
            "marketplace/icon/" . $name . "/" . $id => [
                "view/base/web/images/marketplace/icon" => [
                    "icon-add-products.png",
                    "icon-collect-revenues.png",
                    "icon-register-yourself.png",
                    "icon-start-selling.png"
                ],
                "view/base/web/images/landingpage2/icon" => [
                    "sell-page-2-setup-1.png",
                    "sell-page-2-setup-2.png",
                    "sell-page-2-setup-3.png",
                    "sell-page-2-setup-4.png",
                    "sell-page-2-setup-5.png"
                ]
            ],
            "placeholder" . $name . "/" . $id => [
                "view/base/web/images/placeholder" => [
                    "image.jpg"
                ]
            ],
        ];

        foreach ($mediaDetails as $mediaDirectory => $imageDetails) {
            foreach ($imageDetails as $modulePath => $images) {
                foreach ($images as $image) {
                    $path = $directory->getAbsolutePath($mediaDirectory);
                    $mediaFilePath = $path . $ds . $image;
                    $moduleFilePath = $baseModulePath . $ds . $modulePath . $ds . $image;

                    if ($this->file->fileExists($mediaFilePath)) {
                        continue;
                    }

                    if (!$this->file->fileExists($moduleFilePath)) {
                        continue;
                    }

                    $this->file->cp($moduleFilePath, $mediaFilePath);
                }
            }
        }
    }
}
