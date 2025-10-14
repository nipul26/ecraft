/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    "jquery",
    'mage/template',
    'Magento_Ui/js/modal/alert',
    "mage/translate",
    "Webkul_Marketplace/catalog/base-image-uploader",
    'jquery/uppy-core'
    ], function($, mageTemplate, alert){
        $.widget('mage.imageGallery', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
            _create: function() {
                var self = this;

                // uppy implemetation
          
                let targetElement = $(self.options.uploadId)[0],
                fileId = null,
                arrayFromObj = Array.from,
                fileObj = [],
                uploaderContainer = this.element.find('input[type="file"]').closest('.image-placeholder'),
                options = {
                    debug:true,
                    proudlyDisplayPoweredByUppy: false,
                    target: targetElement,
                    hideUploadButton: false,
                    hideRetryButton: true,
                    hideCancelButton: true,
                    inline: true,
                    showRemoveButtonAfterComplete: true,
                    showProgressDetails: false,
                    showSelectedFiles: false,
                    allowMultipleUploads: false,
                    hideProgressAfterFinish: true
                };

            let dropZone  =  $(self.options.uploadId).closest('.image-placeholder')[0];
    
            dropZone.on("click",function(){
                $(this).find(".uppy-Dashboard-browse").trigger("click");
            });

            const uppy = new Uppy.Uppy({
                autoProceed: true,
                debug:true,
                restrictions: {
                    allowedFileTypes: ['.gif', '.jpeg', '.jpg', '.png'],
                    maxFileSize: this.element.data('maxFileSize')
                },
                onBeforeFileAdded: (currentFile) => {
                    console.log("currentFile",currentFile);
                    var progressTmpl = mageTemplate(self.options.templateId),
                            fileSize,
                            tmpl;

                   
                    fileSize = typeof currentFile.size == 'undefined' ?
                    $.mage.__('We could not detect a size.') :
                    byteConvert(currentFile.size);

                    fileId = Math.random().toString(33).substr(2, 18);

                    tmpl = progressTmpl({
                        data: {
                            name: currentFile.name,
                            size: fileSize,
                            id: fileId
                        }
                    });

                    $(tmpl).appendTo(self.options.contentUploaderId);
                
                    // code to allow duplicate files from same folder
                    const modifiedFile = {
                        ...currentFile,
                        id:  currentFile.id + '-' + fileId,
                        tempFileId:  fileId
                    };

                    return modifiedFile;
                },
                meta: {
                    'form_key': window.FORM_KEY,
                    isAjax : true
                }
            });

            // initialize Uppy upload
            uppy.use(Uppy.Dashboard, options);
 
            // drop area for file upload
            uppy.use(Uppy.DropTarget, {
                target: dropZone,
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
                endpoint: $(self.options.uploadId).data("url"),
                fieldName: 'image',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            uppy.on('upload-progress', (file, progress) => {
                var progressint = parseInt(progress.bytesUploaded / progress.bytesTotal * 100, 10);
                var progressSelector = '#' + fileId + ' .progressbar-container .progressbar';
                $(progressSelector).css('width', progressint + '%');
            });

            uppy.on('upload-success', (file, response) => {
                if (response.body && !response.body.error) {
                    $(self.options.contentId).trigger('addItem', response.body);
                } else {
                    $('#' + response.uploadURL)
                        .delay(2000)
                        .hide('highlight');
                    alert({
                    content: $.mage.__('We don\'t recognize or support this file extension type.')
                    });
                }
                $('#' + fileId).remove();
            });

            uppy.on('complete', () => {
                Array.from = arrayFromObj;
            });

        }
    })
    return $.mage.imageGallery;
});