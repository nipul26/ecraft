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
namespace Webkul\Marketplace\Model\ResourceModel\DraftProduct;

use \Webkul\Marketplace\Model\ResourceModel\AbstractCollection;

/**
 * DraftProduct Collection Class
 */
class Collection extends AbstractCollection
{
    /**
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
            \Webkul\Marketplace\Model\DraftProduct::class,
            \Webkul\Marketplace\Model\ResourceModel\DraftProduct::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
