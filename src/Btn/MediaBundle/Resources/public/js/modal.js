/* global BtnApp, jQuery */
(function (app, $, undefined) {
    'use strict';
    $.fn.btnMediaModal = function () {
        //script needs modal
        if (typeof $.fn.modal === 'undefined') {
            return;
        }

        //set up base variables
        var modal,
            mediaInput         = $(this),
            modalUrl           = mediaInput.attr('btn-media'),
            selectMediaBtnText = mediaInput.attr('btn-media-select'),
            selectMediaBtn     = $('<div />').addClass('btn btn-primary').text(selectMediaBtnText),
            editMediaBtn       = $('<a target="_blank" />').addClass('btn btn-info').text(mediaInput.attr('btn-media-edit')),
            deleteMediaBtn     = $('<div />').addClass('btn btn-danger').attr('btn-remove', true).text(mediaInput.attr('btn-media-delete')),
            paginationUrl      = '',
            laddaButton        = null;

        //script needs to have url to $.get content
        if (typeof modalUrl === 'undefined') {
            app.tools.error('BtnMediaBundle: No modal url specified at btn-media attr.');

            return;
        }
        // update media-content html by $.get response
        var updateModalBody = function (url) {
            var modalContent = modal.find('.modal-content');
            modal.find('[btn-media-submit]').addClass('disabled');
            modal.find('.media-content').slideUp(function () {
                // start spinner
                if ($.fn.spin) {
                    modalContent.spin();
                }
                $.get(url, function (response) {
                    modal.find('.media-content').html(response).slideDown();
                }).always(function(){
                    // stop spinner
                    if ($.fn.spin) {
                        modalContent.spin(false);
                    }
                });
            });
        };
        //bind modal behaviors
        var bindModalBehaviors = function () {
            //append additional modal styles
            modal.find('style, link').appendTo('head');
            //set media-content URL
            paginationUrl = modal.find('#btn-media-list').attr('data-pagination-url');
            //create real modal
            modal
                .modal({
                    show : false,
                    keyboard : true,
                    backdrop : true
                })
                //select image behavior
                .on('click', '#btn-media-list .item[data-id]', function () {
                    $('#btn-media-list .item[data-id]').removeClass('selected');
                    modal.find('[btn-media-submit]').removeClass('disabled');
                    $(this).addClass('selected');
                })
                //submit choosen image to binded mediaInput
                .on('click', '[btn-media-submit]', function () {
                    var media = $('#btn-media-list .item.selected');
                    if (media.length) {
                        updateMediaInput(mediaInput, media);
                        refreshPreview();
                        modal.modal('hide');
                    }
                })
                //reload content on pagination link click
                .on('click', '.modal-body .pagination li a', function (event) {
                    event.preventDefault();
                    updateModalBody($(this).attr('href').replace('modal?', 'modal-content?'));

                    return false;
                })
                //reload content on category link click
                .on('click', '#btn-media-tree ul li a', function () {
                    var category = $(this).attr('btn-media-category');
                    var modalBodyUrl = (category ? (paginationUrl + '/' + category) : paginationUrl)
                    var queryParams = JSON.parse($(this).attr('btn-media-query-params'));
                    modalBodyUrl += (modalBodyUrl.indexOf('?') > 0) ? '&' : '?';
                    modalBodyUrl += $.param(queryParams);
                    updateModalBody(modalBodyUrl);

                    return false;
                })
                // filter form
                .on('submit', 'form[name="filter"]', function(event) {
                    event.preventDefault();
                    var searchUrl = paginationUrl;
                    searchUrl += (searchUrl.indexOf('?') > 0) ? '&' : '?';
                    updateModalBody(searchUrl + $(this).serialize());
                });

                if (BtnApp) {
                    // trigger refresh for nodal to attach custom events
                    BtnApp.refresh(modal);
                }
        };

        //get modal contend and append it to the body
        var getModal = function () {
            var xhr = $.get(modalUrl, function (response) {
                modal = $(response).appendTo('body').show();
            });

            return xhr;
        };
        //if modal is ready show it and animate it
        var onModalReady = function () {
            if (laddaButton) {
                laddaButton.stop();
            }
            modal.modal('show');
            $('html, body').animate({
                scrollTop : modal.offset().top
            }, 400);
        };

        //get modal if not exist else open it
        var openModal = function () {
            if (laddaButton) {
                laddaButton.start();
            }
            if (!modal) {
                getModal().done(function() {
                    onModalReady();
                    bindModalBehaviors();
                });
            } else {
                onModalReady();
            }
        };

        //TODO: check if below 3 methods can better :)
        var updateMediaInput = function (input, image) {
            if (image == null) {
                input.val(null);
            } else if (input.is('select')) {
                input.val(image.data('id'));
            } else if (input.is('input')) {
                input.val(image.data('filename'));
            }

            if (image) {
                updateMediaButtons(input, image.data('filename'));
            } else {
                updateMediaButtons(input);
            }
        };

        var updateMediaButtons = function (input, filename) {
            var selectBtn  = input.data('select-button'),
                editBtn    = input.data('edit-button'),
                deleteBtn  = input.data('delete-button'),
                selectBtnText;

            if (filename == null) {
                if (input.is('select')) {
                    selectBtnText = input.find('option:selected').text();
                } else {
                    selectBtnText = input.val();
                }
            } else {
                selectBtnText = filename;
            }

            selectBtn.text(selectBtnText || selectMediaBtnText);
            editBtn.attr('href', '#');

            if (input.val()) {
                editBtn.show();
                deleteBtn.show();
            } else {
                editBtn.hide();
                deleteBtn.hide();
            }
        };

        var refreshPreview = function() {
            var preview = $('[btn-preview-for="'+mediaInput.attr('id')+'"]');
            if (preview.length) {
                preview.hide();
            }
        };

        // main init function
        var init = function() {
            var self      = mediaInput.hide(),
                selectBtn = selectMediaBtn.clone().insertAfter(self),
                editBtn   = editMediaBtn.clone().hide().insertAfter(selectBtn),
                deleteBtn = deleteMediaBtn.clone().hide().insertAfter(editBtn);

            self.data('select-button', selectBtn);
            self.data('edit-button', editBtn);
            self.data('delete-button', deleteBtn);

            selectBtn.on('click', function () {
                if (BtnApp && BtnApp.tools.loadingButton) {
                    laddaButton = BtnApp.tools.loadingButton(this);
                }
                mediaInput = self;
                openModal();

                return false;
            });

            editBtn.on('click', function() {
                editBtn.attr('href', self.attr('btn-media-edit-url').replace('/0', '/' + self.val()));
                window.setTimeout(function() {
                    editBtn.attr('href', '#');
                });
            });

            deleteBtn.on('click btnRemove', function () {
                updateMediaInput(self);
                refreshPreview();
                $(this).hide();

                return false;
            });

            updateMediaButtons(self);
        };

        // do all the magic
        init();

        // trigger refresh for form row to attach custom events
        app.refresh(mediaInput.parent().get());
    };
})(BtnApp, jQuery);
