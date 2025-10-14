/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
        'Magento_Ui/js/grid/editing/editor'
    ], function(Component){
        'use strict';
        return Component.extend({
            defaults:{
                templates: {
                    record: {  
                        component: 'Webkul_Marketplace/js/grid/editing/record', 
                    }
                }
            }
        })
    }
)