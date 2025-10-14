/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
// jscs:disable requireDotNotation
// jscs:disable jsDoc
define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/modal/alert'
], function (Component, $, ko, _, alert) {
    'use strict';

    function UserException(message)
    {
        this.message = message;
        this.name = 'UserException';
    }
    UserException.prototype = Object.create(Error.prototype);

    return Component.extend({
        defaults: {
            opened: false,
            attributes: [],
            productMatrix: [],
            variations: [],
            productAttributes: [],
            fullAttributes: [],
            rowIndexToEdit: false,
            productAttributesMap: null,
            modules: {
                associatedProductGrid: '${ $.configurableProductGrid }'
            }
        },
        initialize: function () {
            this._super();

            if (this.variations.length) {
                this.render(this.variations, this.productAttributes);
            }
            this.initProductAttributesMap();
        },
        initObservable: function () {
            this._super().observe('actions opened attributes productMatrix');

            return this;
        },
        showGrid: function (rowIndex) {
            var product = this.productMatrix()[rowIndex],
                attributes = JSON.parse(product.attribute),
                filterModifier = product.productId ? {
                    'entity_id': {
                        'condition_type': 'neq', value: product.productId
                    }
                 } : {};
            this.rowIndexToEdit = rowIndex;

            filterModifier = _.extend(filterModifier, _.mapObject(attributes, function (value) {
                return {
                    'condition_type': 'eq',
                    'value': value
                };
            }));
            this.associatedProductGrid().open(
                {
                    'filters': attributes,
                    'filters_modifier': filterModifier
                },
                'changeProduct',
                false
            );
        },
        changeProduct: function (newProducts) {
            var oldProduct = this.productMatrix()[this.rowIndexToEdit],
                newProduct = this._makeProduct(_.extend(oldProduct, newProducts[0]));
            this.productAttributesMap[this.getVariationKey(newProduct.options)] = newProduct.productId;
            this.productMatrix.splice(this.rowIndexToEdit, 1, newProduct);
        },
        appendProducts: function (newProducts) {
            this.productMatrix.push.apply(
                this.productMatrix,
                _.map(
                    newProducts,
                    _.wrap(
                        this._makeProduct.bind(this),
                        function (func, product) {
                            var newProduct = func(product);
                            this.productAttributesMap[this.getVariationKey(newProduct.options)] = newProduct.productId;

                            return newProduct;
                        }.bind(this)
                    )
                )
            );
        },
        _makeProduct: function (product) {
            var productId = product['entity_id'] || product.productId || null,
                attributes = _.pick(product, this.attributes.pluck('code')),
                options = _.map(attributes, function (option, attribute) {
                    var oldOptions = _.findWhere(this.attributes(), {
                            code: attribute
                        }).options,
                        result;

                    if (_.isFunction(oldOptions)) {
                        result = oldOptions.findWhere({
                            value: option
                        });
                    } else {
                        result = _.findWhere(oldOptions, {
                            value: option
                        });
                    }

                    return result;
                }.bind(this));

            return {
                attribute: JSON.stringify(attributes),
                editable: false,
                images: {
                    preview: product['thumbnail_src']
                },
                name: product.name || product.sku,
                options: options,
                price: parseFloat(product.price.replace(/[^\d.]+/g, '')).toFixed(4),
                productId: productId,
                productUrl: this.buildProductUrl(productId),
                quantity: product.quantity || null,
                sku: product.sku,
                status: product.status === undefined ? 1 : parseInt(product.status, 10),
                variationKey: this.getVariationKey(options),
                weight: product.weight || null
            };
        },
        getProductValue: function (name) {
            return $('[name="product[' + name.split('/').join('][') + ']"]', this.productForm).val();
        },
        getRowId: function (data, field) {
            var key = data.variationKey;

            return 'variations-matrix-' + key + '-' + field;
        },
        getVariationRowName: function (variation, field) {
            var result;

            if (variation.productId) {
                result = 'configurations[' + variation.productId + '][' + field.split('/').join('][') + ']';
            } else {
                result = 'variations-matrix[' + variation.variationKey + '][' + field.split('/').join('][') + ']';
            }

            return result;
        },
        getAttributeRowName: function (attribute, field) {
            return 'product[configurable_attributes_data][' + attribute.id + '][' + field + ']';
        },
        getOptionRowName: function (attribute, option, field) {
            return 'product[configurable_attributes_data][' + attribute.id + '][values][' +
                option.value + '][' + field + ']';
        },
        render: function (variations, attributes) {
            this.changeButtonWizard();
            this.populateVariationMatrix(variations);
            this.attributes(attributes);
            this.initImageUpload();
            this.disableConfigurableAttributes(attributes);
            this.showPrice();
        },
        changeButtonWizard: function () {
            var $button = $('[data-action=open-steps-wizard] [data-role=button-label]');
            $button.text($button.attr('data-edit-label'));
        },

        /**
         * Get attributes options
         * @see use in matrix.phtml
         * @function
         * @event
         * @returns {array}
         */
        getAttributesOptions: function () {
            return this.showVariations() ? this.productMatrix()[0].options : [];
        },
        showVariations: function () {
            return this.productMatrix().length > 0;
        },
        populateVariationMatrix: function (variations) {
            this.productMatrix([]);
            _.each(variations, function (variation) {
                var attributes = _.reduce(variation.options, function (memo, option) {
                    var attribute = {};
                    attribute[option['attribute_code']] = option.value;

                    return _.extend(memo, attribute);
                }, {});
                this.productMatrix.push(_.extend(variation, {
                    productId: variation.productId || null,
                    name: variation.name || variation.sku,
                    weight: variation.weight,
                    attribute: JSON.stringify(attributes),
                    variationKey: this.getVariationKey(variation.options),
                    editable: variation.editable === undefined ? !variation.productId : variation.editable,
                    productUrl: this.buildProductUrl(variation.productId),
                    status: variation.status === undefined ? 1 : parseInt(variation.status, 10)
                }));
            }, this);
        },
        buildProductUrl: function (productId) {
            return this.productUrl.replace('associated_pro_id', productId);
        },
        removeProduct: function (rowIndex) {
            this.opened(false);
            var removedProduct = this.productMatrix.splice(rowIndex, 1);
            delete this.productAttributesMap[this.getVariationKey(removedProduct[0].options)];

            if (this.productMatrix().length === 0) {
                this.attributes.each(function (attribute) {
                    $('[data-attribute-code="' + attribute.code + '"] select').removeProp('disabled');
                });
            }
            this.showPrice();
        },
        toggleProduct: function (rowIndex) {
            var product, row, productChanged = {};

            if (this.productMatrix()[rowIndex].editable) {
                row = $('[data-row-number=' + rowIndex + ']');
                _.each(['name','sku','qty','weight','price'], function (column) {
                    productChanged[column] = $(
                        'input[type=text]',
                        row.find($('[data-column="%s"]'.replace('%s', column)))
                    ).val();
                });
            }
            product = this.productMatrix.splice(rowIndex, 1)[0];
            product = _.extend(product, productChanged);
            product.status = +!product.status;
            this.productMatrix.splice(rowIndex, 0, product);
        },
        toggleList: function (rowIndex) {
            var state = false;

            if (rowIndex !== this.opened()) {
                state = rowIndex;
            }
            this.opened(state);

            return this;
        },
        closeList: function (rowIndex) {
            if (this.opened() === rowIndex()) {
                this.opened(false);
            }

            return this;
        },
        getVariationKey: function (options) {
            return _.pluck(options, 'value').sort().join('-');
        },
        getProductIdByOptions: function (options) {
            return this.productAttributesMap[this.getVariationKey(options)] || null;
        },
        initProductAttributesMap: function () {
            if (this.productAttributesMap === null) {
                this.productAttributesMap = {};
                _.each(this.variations, function (product) {
                    this.productAttributesMap[this.getVariationKey(product.options)] = product.productId;
                }.bind(this));
            }
        },

        /**
         * Is show preview image
         * @see use in matrix.phtml
         * @function
         * @event
         * @param {object} variation
         * @returns {*|boolean}
         */
        isShowPreviewImage: function (variation) {
            return variation.images.preview && (!variation.editable || variation.images.file);
        },
        generateImageGallery: function (variation) {
            var gallery = [],
                imageFields = ['position', 'file', 'disabled', 'label'];
            _.each(variation.images.images, function (image) {
                _.each(imageFields, function (field) {
                    gallery.push(
                        '<input type="hidden" name="' +
                        this.getVariationRowName(variation, 'media_gallery/images/' + image['file_id'] + '/' + field) +
                        '" value="' + (image[field] || '') + '" >'
                    );
                }, this);
                _.each(image.galleryTypes, function (imageType) {
                    gallery.push(
                        '<input type="hidden" name="' + this.getVariationRowName(variation, imageType) +
                        '" value="' + image.file + '" >'
                    );
                }, this);
            }, this);

            return gallery.join('\n');
        },
        initImageUpload: function () {
            require([
                'mage/template',
                'mage/mage',
                'jquery/uppy-core',
                'mage/translate',
                'domReady!'
            ], function (mageTemplate) {
                
                var matrix = $('[data-role=product-variations-matrix]');
                matrix.find('[data-action=upload-image]').find("[data-action=upload-image-uppy]").each(function () {
   
                    var uploadUrl = $(this).find("[name=image]").attr("data-url");
                    var imageColumn = $(this).closest('[data-column=image]');
                    var uploadImage = $(this).closest('[data-action=upload-image]')[0];
                    var target = $(this).find("[data-action=wk-uppy-root]")[0];
  
                    if (imageColumn.find('[data-role=image]').length) {
                        imageColumn.find('[data-toggle=dropdown]').dropdown().show();
                    }

                    uploadImage.on("click",function(){
                      $(this).find('[data-action=upload-image-uppy]').find(".uppy-Dashboard-browse").trigger("click");
                    });

                    // uppy implementation
                    let targetElement = target,
                    fileId = null,
                    arrayFromObj = Array.from,
                    options = {
                        proudlyDisplayPoweredByUppy: false,
                        target: targetElement,
                        hideUploadButton: true,
                        hideRetryButton: true,
                        hideCancelButton: true,
                        inline: true,
                        debug:true,
                        showRemoveButtonAfterComplete: true,
                        showProgressDetails: false,
                        showSelectedFiles: false,
                        allowMultipleUploads: false,
                        hideProgressAfterFinish: true
                    };

                    const uppy = new Uppy.Uppy({
                        debug:false,
                        autoProceed: true,
                        restrictions: {
                            allowedFileTypes: ['.gif', '.jpeg', '.jpg', '.png'],
                            maxFileSize:2000000
                        },    
                        onBeforeFileAdded: (currentFile) => {
                            $(this).closest('[data-action=upload-image]').addClass('loading');
                        },
                        meta: {
                            'form_key': FORM_KEY
                        }
                    });

                    // initialize Uppy upload
                    uppy.use(Uppy.Dashboard, options);

                    // drop area for file upload
                    uppy.use(Uppy.DropTarget, {
                        target: uploadImage,    
                        onDragOver: () => {
                            // override Array.from method of legacy-build.min.js file
                            Array.from = null;
                        },
                        onDragLeave: () => {
                            Array.from = arrayFromObj;
                        }
                    });

                    // upload files on server
                    uppy.use(Uppy.XHRUpload, {
                        endpoint: uploadUrl,
                        fieldName: 'image'
                    });

                    uppy.on('upload-success', (file, response) => {
                        var tmpl, parentElement, uploaderControl, imageElement;

                        if (response.body && !response.body.error) {
                            parentElement = $(this).closest('[data-column=image]');
                            uploaderControl = parentElement.find('[data-action=upload-image]');
                            imageElement = parentElement.find('[data-role=image]');

                            if (imageElement.length) {
                                imageElement.attr('src', response.body.url);
                            } else {
                                tmpl = mageTemplate('[data-template-for=variation-image]');

                                $(tmpl({
                                    data: response.body
                                })).prependTo(uploaderControl);
                            }
                            parentElement.find('[name$="[image]"]').val(response.body.file);
                            parentElement.find('[data-toggle=dropdown]').dropdown().show();
                        } else {
                            alert({
                                content: $.mage.__('We don\'t recognize or support this file extension type.')
                            });
                        }
                        
                    });

                    uppy.on('upload-progress', (file, progress) => {
                        $(this).closest('[data-action=upload-image]').addClass('loading');
                    });

                    uppy.on('complete', () => {
                        $(this).closest('[data-action=upload-image]').removeClass('loading');
                        Array.from = arrayFromObj;
                    });
                
                });
                matrix.find('[data-action=no-image]').click(function (event) {
                    var parentElement = $(event.target).closest('[data-column=image]');
                    parentElement.find('[data-role=image]').remove();
                    parentElement.find('[name$="[image]"]').val('');
                    parentElement.find('[data-toggle=dropdown]').trigger('close.dropdown').hide();
                });
            });
        },
        disableConfigurableAttributes: function (attributes) {
            $('[data-attribute-code] select.disabled-configurable-elements')
                .removeClass('disabled-configurable-elements')
                .prop('disabled', false);
            _.each(attributes, function (attribute) {
                $('[data-attribute-code="' + attribute.code + '"] select')
                    .addClass('disabled-configurable-elements')
                    .prop('disabled', true);
            });
        },
        showPrice: function () {
            var priceContainer = $('[id="attribute-price-container"]');
            if (this.productMatrix().length !== 0) {
                priceContainer.hide();
                priceContainer.find('input').prop('disabled', true);
            } else {
                priceContainer.show();
                priceContainer.find('input').prop('disabled', false);
            }
        },

        /**
         * Get currency symbol
         * @returns {*}
         */
        getCurrencySymbol: function () {
            return this.currencySymbol;
        }
    });
});
