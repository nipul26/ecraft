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
namespace Webkul\Marketplace\Model\ResourceModel\AdminNews;

use Webkul\Marketplace\Model\ResourceModel\AbstractCollection;

/**
 * AdminNews Collection Class
 */
class Collection extends AbstractCollection
{
    /**
     * Admin news id field name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Webkul\Marketplace\Model\AdminNews::class,
            \Webkul\Marketplace\Model\ResourceModel\AdminNews::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
