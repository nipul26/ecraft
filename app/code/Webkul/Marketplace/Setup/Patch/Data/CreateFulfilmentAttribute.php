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

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\Marketplace\Model\ControllersRepository;

class CreateFulfilmentAttribute implements DataPatchInterface
{
    public const ATTR_FULFILMENT = "fulfilled_by";
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
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
     * @var ControllersRepository
     */
    private $controllersRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param ControllersRepository $controllersRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Module\Dir\Reader $reader,
        ControllersRepository $controllersRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->reader = $reader;
        $this->controllersRepository = $controllersRepository;
    }

    /**
     * Add eav attributes
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            self::ATTR_FULFILMENT,
            [
                'type' => 'int',
                'group' => 'General',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Fulfilled By',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to'     => '',
                'frontend_class' => 'wk_fulfilled_by'
            ]
        );
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $modulePath = "view/base/web/images/avatar";
        $mainDir = "avatar";
        $path = $directory->getAbsolutePath($mainDir);
        if (!$this->file->fileExists($path)) {
            $this->file->mkdir($path, 0777, true);
        }
        $ds = "/";
        $baseModulePath = $this->reader->getModuleDir('', 'Webkul_Marketplace');
        $mediaFilePath = $path . $ds . "fulfilmentImage.png";
        $moduleFilePath = $baseModulePath . $ds . $modulePath . $ds . "fulfilmentImage.png";
        if (!$this->file->fileExists($mediaFilePath) && $this->file->fileExists($moduleFilePath)) {
            $this->file->cp($moduleFilePath, $mediaFilePath);
        }

        if (!count($this->controllersRepository->getByPath('marketplace/product/draftproduct'))) {
            $data[] = [
                'module_name' => 'Webkul_Marketplace',
                'controller_path' => 'marketplace/product/draftproduct',
                'label' => 'Draft Products',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!count($this->controllersRepository->getByPath('marketplace/account/adminnews'))) {
            $data[] = [
                'module_name' => 'Webkul_Marketplace',
                'controller_path' => 'marketplace/account/adminnews',
                'label' => 'Admin News',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!empty($data)) {
            $connection->insertMultiple($this->moduleDataSetup->getTable('marketplace_controller_list'), $data);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
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

    /**
     * Get Aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
