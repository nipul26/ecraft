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

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallCmsBlock implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $block;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Webkul\Marketplace\Model\CmsBlock $block
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Webkul\Marketplace\Model\CmsBlock $block
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->block = $block;
    }

    /**
     * Add eav attributes
     *
     * @return void
     */
    public function apply()
    {
        $this->block->install(['Webkul_Marketplace::fixtures/cms_static_block.csv']);
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
