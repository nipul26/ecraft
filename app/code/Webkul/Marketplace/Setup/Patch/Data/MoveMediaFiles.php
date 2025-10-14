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

namespace Webkul\Marketplace\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;

class MoveMediaFiles implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $reader;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor function
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->reader = $reader;
        $this->_storeManager = $storeManager;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->processDefaultImages();
    }

    /**
     * Copy Banner and Icon Images to Media
     */
    private function processDefaultImages()
    {
        $error = false;
        try {
            $mediaDirectories = $this->createDirectories();
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $ds = "/";
            $baseModulePath = $this->reader->getModuleDir('', 'Webkul_Marketplace');

            $mediaDetails = [];

            $mediaDetails = [
                "avatar/default" => [
                    "view/base/web/images/avatar" => [
                        "banner-image.png",
                        "noimage.png"
                    ]
                ],
                "marketplace/banner/default" => [
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
                "marketplace/icon/default" => [
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
                "placeholder" => [
                    "view/base/web/images/placeholder" => [
                        "image.jpg"
                    ]
                ],
            ];

            foreach ($mediaDirectories as $directoryname) {

                if (strpos($directoryname, "avatar") !== false) {
                    $mediaDetails[$directoryname] = [
                        "view/base/web/images/avatar" => [
                            "banner-image.png",
                            "noimage.png"
                        ]
                    ];
                }

                if (strpos($directoryname, "banner") !== false) {
                    $mediaDetails[$directoryname] = [
                        "view/base/web/images/marketplace/banner" => [
                            "sell-page-banner.png"
                        ],
                        "view/base/web/images/landingpage1/banner" => [
                            "sell-page-1-hero-banner.jpg"
                        ],
                        "view/base/web/images/landingpage2/banner" => [
                            "sell-page-2-hero-banner.jpg"
                        ]
                    ];
                }

                if (strpos($directoryname, "icon") !== false) {
                    $mediaDetails[$directoryname] = [
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
                    ];
                }

                if (strpos($directoryname, "placeholder") !== false) {
                    $mediaDetails[$directoryname] = [
                        "view/base/web/images/placeholder" => [
                            "image.jpg"
                        ]
                    ];
                }
            }
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
        } catch (\Exception $e) {
            $error = true;
        }
        return $error;
    }

    /**
     * Create default directories
     */
    private function createDirectories()
    {
        $websites = $this->_storeManager->getWebsites();
        $mediaDirectories = [];
        foreach ($websites as $website) {

            if (!empty($website->getId())) {
                $websiteId = $website->getId();
                $mediaDirectories[] =  'avatar/websites/' . $websiteId;
                $mediaDirectories[] = 'marketplace/banner/websites/' . $websiteId;
                $mediaDirectories[] = 'marketplace/icon/websites/' . $websiteId;
                $mediaDirectories[] = 'placeholder/websites/' . $websiteId;
            }
            $websiteStores = $website->getStores();
            foreach ($websiteStores as $store) {
                if (!empty($store->getId())) {
                    $storeId = $store->getId();
                    $mediaDirectories[] =  'avatar/stores/' . $storeId;
                    $mediaDirectories[] = 'marketplace/banner/stores/' . $storeId;
                    $mediaDirectories[] = 'marketplace/icon/stores/' . $storeId;
                    $mediaDirectories[] = 'placeholder/stores/' . $storeId;
                }
            }
        }
        $mediaDirectories[] = 'avatar/default';
        $mediaDirectories[] = 'marketplace/banner/default/';
        $mediaDirectories[] = 'marketplace/icon/default';
        $mediaDirectories[] = 'placeholder/default';
        foreach ($mediaDirectories as $mediaDirectory) {
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $path = $directory->getAbsolutePath($mediaDirectory);
            if (!$this->file->fileExists($path)) {
                $this->file->mkdir($path, 0777, true);
            }
        }
        return  $mediaDirectories;
    }

    /**
     * Get Aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
