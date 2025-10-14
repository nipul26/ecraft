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
        'Magento_Ui/js/grid/editing/record'
    ], function(Component){
        'use strict';
        return Component.extend({
            defaults:{
                templates: {
                    fields: {
                        price: {
                            component: 'Webkul_Marketplace/js/form/element/price',
                            template: 'ui/form/element/input'
                        }
                    }
                }
            }
        })
    }
)