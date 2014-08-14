jQuery(function ($) {
    //script needs modal
    if (typeof $.fn.modal === 'undefined') {

        return;
    }
    //set up base variables
    var modal,
        mediaInput,
        mediaInputs        = $('[data-btn-media]'),
        modalUrl           = mediaInputs.first().attr('data-btn-media'),
        selectMediaBtnText = mediaInputs.attr('data-btn-media-select'),
        selectMediaBtn     = $('<div />').addClass('btn btn-primary').text(selectMediaBtnText),
        deleteMediaBtn     = $('<div />').addClass('btn btn-danger').text(mediaInputs.attr('data-btn-media-delete')),
        paginationUrl      = '';
    //script needs to have url to $.get content
    if (typeof modalUrl === 'undefined') {
        console.error('BtnMediaBundle: No modal url specified at data-btn-media attr.');

        return;
    }
    //return GET params
    var getPaginationSearchPart = function (el) {

        return $(el).find('a').attr('href').split('?')[1];
    };
    // update media-content html by $.get response
    var updateModalBody = function (url) {
        modal.find('.media-content').slideUp(function () {
            $.get(url, function (response) {
                modal.find('.media-content').html(response).slideDown();
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
            .on('click', '#btn-media-list .item img', function (e) {
                $('#btn-media-list .item img').removeClass('selected');
                $(this).addClass('selected');
            })
            //submit choosen image to binded mediaInput
            .on('click', '[btn-media-submit]', function () {
                var images = $('#btn-media-list .item img.selected');
                if (images.length) {
                    updateMediaInput(mediaInput, images);
                    modal.modal('hide');
                }
            })
            //reload content on pagination link click
            .on('click', '.modal-body .pagination li', function (e) {
                if (!$(this).hasClass('disabled') && !$(this).hasClass('active')) {
                    updateModalBody(paginationUrl + '?' + getPaginationSearchPart(this));
                }

                return false;
            })
            //reload content on category link click
            .on('click', '#tree ul li a', function(event) {
                var category = $(this).attr('data-btn-media-category');
                updateModalBody(category ? (paginationUrl + '/' + category) : paginationUrl);

                return false;
            });
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
        modal.modal('show');
        $('html, body').animate({
            scrollTop : modal.offset().top
        }, 400);
    };
    //get modal if not exist else open it
    var openModal = function () {
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
    }

    var updateMediaButtons = function (input, filename) {
        var selectBtn  = input.data('select-button'),
            deleteBtn  = input.data('delete-button'),
            hideDelete = false;

        if (filename == null) {
            hideDelete = true;
            if (input.is('select')) {
                filename = input.find('option:selected').text();
            } else {
                filename = input.val();
            }
        }
        selectBtn.text(filename ? filename : selectMediaBtnText);
        if (filename) {
            deleteBtn.show();
        } else {
            deleteBtn.hide();
        }

    }
    //create btn select and delete buttons
    mediaInputs.each(function () {
        var self      = $(this).hide(),
            selectBtn = selectMediaBtn.insertAfter(self),
            deleteBtn = deleteMediaBtn.hide().insertAfter(selectBtn);

        self.data('select-button', selectBtn);
        self.data('delete-button', deleteBtn);

        selectBtn.on('click', function (e) {
            mediaInput = self;
            openModal();

            return false;
        });

        // console.log(deleteBtn);

        deleteBtn.on('click', function (e) {
            updateMediaInput(self);
            $(this).hide();

            return false;
        });

        updateMediaButtons(self);
    });
});
