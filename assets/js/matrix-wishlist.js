(function ($) {
    'use strict';
    var MatrixWishList = {

        init: function init() {

            $(document).ready(MatrixWishList.ready);

        },

        /**
         * Document ready.
         */
        ready: function ready() {
            MatrixWishList.bindEvents();
            if (MatrixWishList.getCookie('matrix_wishlist_id') === '') {
                MatrixWishList.setCookie('matrix_wishlist_id', matrix_wishlist_localize_var.wishlist_id, 7);
            }
            if ($('.matrix-wishlist-custom-menu-item').length) {
                // load the count when having a custom menu item
                MatrixWishList.wishListLoadCount();
            }
            $(document).on('found_variation', MatrixWishList.variationFound);

            $(document).trigger('matrix_wishlist_ready');
        },
        bindEvents: function bindEvents() {
            $(document).on('click touch', '.matrix-wishlist-button', function (e) {
                e.preventDefault();
                MatrixWishList.addToWishList($(this));
            });
            $(document).on('click touch', '.matrix-wishlist-item--remove span', function (e) {
                e.preventDefault();
                MatrixWishList.removeFromWishList($(this));
            });
            $(document).on('woovr_selected', function (e, selected, variations) {
                var id = selected.attr('data-id');
                var pid = selected.attr('data-pid');

                if (id > 0) {
                    $('.matrix-wishlist-button-' + pid).attr('data-id', id).removeClass('matrix-wishlist-button-added matrix-wishlist-added');
                } else {
                    $('.matrix-wishlist-button-' + pid).attr('data-id', pid).removeClass('matrix-wishlist-button-added matrix-wishlist-added');
                }
            });


            // reset data
            $(document).on('reset_data', function (e) {
                var product_id = $(e['target']).attr('data-product-id');

                $('.matrix-wishlist-button-' + product_id).attr('data-id', product_id).removeClass('matrix-wishlist-button-added matrix-wishlist-added');
            });

            // quick view
            $(document).on('click touch',
                '#matrix-wishlist-popup-container .matrix-wishlist-link, #matrix-wishlist-popup-container .matrix-wishlist-btn',
                function (e) {
                    matrix_wishlist_wishlist_hide();
                    e.preventDefault();
                });


            // empty wishlist
            $(document).on('click touch', '.matrix-wishlist-empty', function (e) {
                var $this = $(this);

                if (confirm(matrix_wishlist_localize_var.empty_confirm)) {
                    matrix_wishlist_popup_loading();

                    var key = $this.closest('.matrix-wishlist-popup-inner').data('wishlist-id');
                    var data = {
                        action: 'wishlist_empty', key: key,
                    };

                    $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                        response = JSON.parse(response);

                        if (response['status'] === 1) {
                            $('.matrix-wishlist-button').removeClass('matrix-wishlist-added').html(matrix_wishlist_localize_var.button_text);
                        }

                        if (response['content'] != null) {
                            $('#matrix-wishlist-popup-container').html(response['content']).addClass('matrix-wishlist-loaded');
                        }

                        if (response['notice'] != null) {
                            matrix_wishlist_notice(response['notice']);
                        }

                        if (response['count'] != null) {
                            MatrixWishList.wishListChangeCount(response['count']);
                        }

                        matrix_wishlist_popup_loaded();
                    });
                }

                $(document.body).trigger('matrix_wishlist_empty', [key]);

                e.preventDefault();
            });

            // click on area
            $(document).on('click touch', '.matrix-wishlist-popup', function (e) {
                var matrix_wishlist_content = $('.matrix-wishlist-popup-content');

                if ($(e.target).closest(matrix_wishlist_content).length == 0) {
                    matrix_wishlist_wishlist_hide();
                    matrix_wishlist_manage_hide();
                }
            });

            // continue
            $(document).on('click touch', '.matrix-wishlist-continue', function (e) {
                var url = $(this).attr('data-url');
                matrix_wishlist_wishlist_hide();

                if (url !== '') {
                    window.location.href = url;
                }

                e.preventDefault();
            });

            // close
            $(document).on('click touch', '#matrix-wishlist-popup-container .matrix-wishlist-popup-close', function (e) {
                matrix_wishlist_wishlist_hide();
                e.preventDefault();
            });

            // manage close
            $(document).on('click touch', '#matrix_wishlist_manage .matrix-wishlist-popup-close', function (e) {
                matrix_wishlist_manage_hide();
                e.preventDefault();
            });

            // manage wishlists
            $(document).on('click touch', '.matrix-wishlist-manage', function (e) {
                e.preventDefault();
                matrix_wishlist_popup_loading();

                var data = {
                    action: 'manage_wishlists',
                };

                $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                    matrix_wishlist_wishlist_hide();
                    $('#matrix_wishlist_manage').html(response);
                    matrix_wishlist_manage_show();
                    matrix_wishlist_popup_loaded();
                });
            });

            // add wishlist
            $(document).on('click touch', '#matrix_wishlist_add_wishlist', function (e) {
                e.preventDefault();
                matrix_wishlist_popup_loading();

                var name = $('#matrix_wishlist_wishlist_name').val();
                var data = {
                    action: 'add_wishlist', name: name,
                };

                $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                    $('#matrix_wishlist_manage').html(response);
                    $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-loaded');
                    matrix_wishlist_popup_loaded();
                });
            });

            // set default
            $(document).on('click touch', '.matrix-wishlist-set-default', function (e) {
                e.preventDefault();
                matrix_wishlist_popup_loading();

                var key = $(this).data('wishlist-id');
                var data = {
                    action: 'set_default', key: key,
                };

                $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                    response = JSON.parse(response);

                    if (response['count'] != null) {
                        MatrixWishList.wishListChangeCount(response['count']);
                    }

                    $('.matrix-wishlist-button').removeClass('matrix-wishlist-added').html(matrix_wishlist_localize_var.button_text);

                    if ((response['products'] != null) && response['products'].length) {
                        response['products'].forEach((product_id) => {
                            $('.matrix-wishlist-button-' + product_id).addClass('matrix-wishlist-added').html(matrix_wishlist_localize_var.button_text_added);
                        });
                    }

                    $('#matrix_wishlist_manage').html(response['content']);
                    $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-loaded');
                    matrix_wishlist_popup_loaded();
                });
            });

            // delete wishlist
            $(document).on('click touch', '.matrix-wishlist-delete-wishlist', function (e) {
                e.preventDefault();

                if (confirm(matrix_wishlist_localize_var.delete_confirm)) {
                    matrix_wishlist_popup_loading();

                    var key = $(this).data('wishlist-id');
                    var data = {
                        action: 'delete_wishlist', key: key,
                    };

                    $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                        $('#matrix_wishlist_manage').html(response);
                        $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-loaded');
                        matrix_wishlist_popup_loaded();
                    });
                }
            });

            // view wishlist
            $(document).on('click touch', '.matrix-wishlist-view-wishlist', function (e) {
                e.preventDefault();
                matrix_wishlist_popup_loading();

                var key = $(this).data('wishlist-id');
                var data = {
                    action: 'view_wishlist', key: key,
                };

                $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                    matrix_wishlist_manage_hide();
                    $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-loaded').html(response);
                    MatrixWishList.showWishList();
                    matrix_wishlist_popup_loaded();
                });
            });

            // menu item
            $(document).on('click touch', '.matrix-wishlist-menu-item a, .matrix-wishlist-menu a', function (e) {
                e.preventDefault();

                if (matrix_wishlist_localize_var.menu_action === 'open_popup') {
                    if ($('#matrix-wishlist-popup-container').hasClass('matrix-wishlist-loaded')) {
                        MatrixWishList.showWishList();
                    } else {
                        MatrixWishList.loadWishList();
                    }
                }
            });


            // add note
            $(document).on('click touch', '.matrix-wishlist-item--note', function () {
                if ($(this).closest('.matrix-wishlist-item').find('.matrix-wishlist-item--note-add').length) {
                    $(this).closest('.matrix-wishlist-item').find('.matrix-wishlist-item--note-add').show();
                    $(this).hide();
                }
            });


        },
        addToWishList: function addToWishList($this) {
            var id = $this.attr('data-id');
            var pid = $this.attr('data-pid');
            var product_id = $this.attr('data-product-id');
            if (typeof pid !== typeof undefined && pid !== false) {
                id = pid;
            }
            if (typeof product_id !== typeof undefined && product_id !== false) {
                id = product_id;
            }
            var data = {
                action: 'wishlist_add',
                product_id: id,
            };
            if ($this.hasClass('matrix-wishlist-added')) {

                if (matrix_wishlist_localize_var.button_action_after === 'page') {
                    // open wishlist page
                    window.location.href = matrix_wishlist_localize_var.wishlist_url;
                } else {
                    // open wishlist popup
                    if ($('#matrix-wishlist-popup-container').hasClass('matrix-wishlist-loaded')) {
                        MatrixWishList.showWishList();
                    } else {
                        MatrixWishList.loadWishList(true);
                    }
                }
            } else {
                $this.addClass('matrix-wishlist-adding');

                $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {

                    $this.removeClass('matrix-wishlist-adding');

                    MatrixWishList.handleWishListResponse(response);

                    $(document.body).trigger('matrix_wishlist_add', [id]);
                });
            }


            $('.matrix-wishlist-button-' + id).addClass('matrix-wishlist-added').html(matrix_wishlist_localize_var.button_text_added);

            // $this.addClass('matrix-wishlist-added');

        },
        removeFromWishList: function removeFromWishList($this) {
            var wishlist_id = $this.closest('.matrix-wishlist-popup-inner').data('wishlist-id');
            var $this_item = $this.closest('.matrix-wishlist-item');
            var product_id = $this_item.attr('data-id');
            var data = {
                action: 'wishlist_remove', product_id: product_id, wishlist_id: wishlist_id,
            };

            $this.addClass('matrix-wishlist-removing');

            $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {

                $this.removeClass('matrix-wishlist-removing');
                $this_item.remove();
                response = JSON.parse(response);
                if (response['status'] === 1) {
                    $('.matrix-wishlist-button-' + product_id).removeClass('matrix-wishlist-added').html(matrix_wishlist_localize_var.button_text);
                }

                if (response['content'] != null) {
                    $('#matrix-wishlist-popup-container').html(response['content']).addClass('matrix-wishlist-loaded');
                }

                if (response['notice'] != null) {
                    matrix_wishlist_notice(response['notice']);
                }

                if (response['count'] != null) {
                    MatrixWishList.wishListChangeCount(response['count']);
                }

                $(document.body).trigger('matrix_wishlist_remove', [product_id]);
            });
        },
        getCookie: function getCookie(cname) {
            var name = cname + '=';
            var ca = document.cookie.split(';');

            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];

                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }

                if (c.indexOf(name) == 0) {
                    return decodeURIComponent(c.substring(name.length, c.length));
                }
            }

            return '';
        },
        setCookie: function setCookie(cname, cvalue, exdays) {
            var d = new Date();

            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));

            var expires = 'expires=' + d.toUTCString();

            document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/';
        },
        wishListLoadCount: function wishListLoadCount() {
            var data = {
                action: 'wishlist_load_count',
            };

            $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {
                response = JSON.parse(response);

                if (response['count'] != null) {
                    var count = response['count'];

                    MatrixWishList.wishListChangeCount(count);
                    $(document.body).trigger('matrix_wishlist_load_count', [count]);
                }
            });
        },
        wishListChangeCount: function wishListChangeCount(count) {
            $('#matrix-wishlist-popup-container .matrix-wishlist-count').html(count);

            if (parseInt(count) > 0) {
                $('.matrix-wishlist-empty').show();
            } else {
                $('.matrix-wishlist-empty').hide();
            }

            if ($('.matrix-wishlist-menu-item .matrix-wishlist-menu-item-inner').length) {
                $('.matrix-wishlist-menu-item .matrix-wishlist-menu-item-inner').attr('data-count', count);
            } else {
                $('.matrix-wishlist-menu-item a').html('<span class="matrix-wishlist-menu-item-inner" data-count="' + count +
                    '"><i class="matrix-wishlist-icon"></i><span>' + matrix_wishlist_localize_var.menu_text +
                    '</span></span>');
            }

            $(document.body).trigger('matrix_wishlist_change_count', [count]);
        },
        variationFound: function variationFound(event) {

            var t = event.target;

            var product_id = $(event['target']).attr('data-product-id');

            $('.matrix-wishlist-button-' + product_id).attr('data-id', t.variation_id).removeClass('matrix-wishlist-button-added matrix-wishlist-added');

        },
        loadWishList: function loadWishList(is_browse = false) {
            var data = {
                action: 'wishlist_load',
            };
            $.post(matrix_wishlist_localize_var.ajax_url, data, function (response) {

                MatrixWishList.handleWishListResponse(response, is_browse);
            });
        },
        handleWishListResponse: function handleWishListResponse(response, is_browse = false) {

            $('#matrix-wishlist-popup-container').html(response).removeClass('matrix-wishlist-loaded');

            MatrixWishList.showWishList();

            matrix_wishlist_notice();

            if ($('#matrix-wishlist-popup-container').find('.matrix-wishlist-popup-body-content').length > 0) {
                $('#matrix-wishlist-popup-container').addClass('matrix-wishlist-loaded');
            }

            if (matrix_wishlist_localize_var.button_action === 'list' || is_browse) {
                $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-message');
                MatrixWishList.initPerfectScrollbar();

            } else if (matrix_wishlist_localize_var.button_action === 'message') {
                $('#matrix-wishlist-popup-container').addClass('matrix-wishlist-message');

                $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-loaded');

            }
        },

        showWishList: function showWishList() {
            $('#matrix-wishlist-popup-container').addClass('matrix-wishlist-show');

            if ($('#matrix-wishlist-popup-container').hasClass('matrix-wishlist-message')) {
                // timer
                var matrix_wishlist_counter = 6;
                var matrix_wishlist_interval = setInterval(function () {
                    matrix_wishlist_counter--;
                    if (matrix_wishlist_counter === 0) {
                        matrix_wishlist_wishlist_hide();
                        clearInterval(matrix_wishlist_interval);
                    }
                }, 1000);
            }

            $(document.body).trigger('MatrixWishList.showWishList');
        },
        initPerfectScrollbar: function initPerfectScrollbar() {
            jQuery('#matrix-wishlist-popup-container .matrix-wishlist-popup-body').perfectScrollbar({theme: 'wpc'});

        }
    };
    MatrixWishList.init();

    //window.MatrixWishList = MatrixWishList;


    function matrix_wishlist_wishlist_hide() {
        $('#matrix-wishlist-popup-container').removeClass('matrix-wishlist-show');
        $(document.body).trigger('matrix_wishlist_wishlist_hide');
    }

    function matrix_wishlist_manage_show() {
        $('#matrix_wishlist_manage').addClass('matrix-wishlist-show');
        $(document.body).trigger('matrix_wishlist_manage_show');
    }

    function matrix_wishlist_manage_hide() {
        $('#matrix_wishlist_manage').removeClass('matrix-wishlist-show');
        $(document.body).trigger('matrix_wishlist_manage_hide');
    }

    function matrix_wishlist_popup_loading() {
        $('.matrix-wishlist-popup').addClass('matrix-wishlist-loading');
    }

    function matrix_wishlist_popup_loaded() {
        $('.matrix-wishlist-popup').removeClass('matrix-wishlist-loading');
    }


    function matrix_wishlist_notice() {
        matrix_wishlist_notice_show();
        setTimeout(function () {
            matrix_wishlist_notice_hide();
        }, 3000);
    }

    function matrix_wishlist_notice_show() {
        $('#matrix-wishlist-popup-container .matrix-wishlist-notice').addClass('matrix-wishlist-notice-show');
    }

    function matrix_wishlist_notice_hide() {
        $('#matrix-wishlist-popup-container .matrix-wishlist-notice').removeClass('matrix-wishlist-notice-show');
    }


})(jQuery);