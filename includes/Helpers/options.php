<?php
function matrix_wishlist_disable_for_non_login()
{
    return get_option('matrix_wishlist_disable_for_non_login', 'no') === 'yes';
}

function matrix_wishlist_auto_remove_after_cart()
{
    return get_option('matrix_wishlist_auto_remove_after_cart', 'no') === 'yes';
}

function matrix_wishlist_button_type()
{
    return get_option('matrix_wishlist_button_type', 'button');
}

function matrix_wishlist_button_action()
{
    return get_option('matrix_wishlist_button_action', 'list');
}

function matrix_wishlist_button_action_after()
{
    return get_option('matrix_wishlist_button_action_after', 'page');
}

function matrix_wishlist_button_class()
{
    return get_option('matrix_wishlist_button_class', '');
}

function matrix_wishlist_button_archive_page_position()
{
    return get_option('matrix_wishlist_button_archive_page_position', 'below_add_to_cart');
}

function matrix_wishlist_button_single_page_position()
{
    return get_option('matrix_wishlist_button_single_page_position', 'below_add_to_cart');
}

function matrix_wishlist_button_show_only_on_categories()
{
    $cat = get_option('matrix_wishlist_button_show_only_on_categories', array());
    return is_array($cat) ? $cat : array();
}


function matrix_wishlist_message_background_color()
{
    return get_option('matrix_wishlist_message_background_color', '#5fbd74');
}

function matrix_wishlist_popup_header_background_color()
{
    return get_option('matrix_wishlist_popup_header_background_color', '#282727');
}
function matrix_wishlist_popup_footer_background_color()
{
    return get_option('matrix_wishlist_popup_footer_background_color', '#282727');
}

function matrix_wishlist_button_link_to_individual_product()
{
    return get_option('matrix_wishlist_button_link_to_individual_product', 'same_tab');
}



function matrix_wishlist_continue_shopping_link()
{
    return get_option('matrix_wishlist_continue_shopping_link', get_permalink(wc_get_page_id('shop')));
}

function matrix_wishlist_page_id()
{
    if (get_option('matrix_wishlist_page_id')) {
        return absint(get_option('matrix_wishlist_page_id', 0));
    }
    return false;

}
