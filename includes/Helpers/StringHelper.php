<?php

namespace MatrixAddons\MatrixWishlist\Helpers;
class StringHelper
{
    public static function get_popup_heading()
    {
        return esc_html__('Wishlist', 'matrix-wishlist');
    }

    public static function get_empty_confirm()
    {
        return esc_html__('This action cannot be undone. Are you sure?', 'matrix-wishlist');
    }

    public static function get_delete_confirm()
    {
        return esc_html__('This action cannot be undone. Are you sure?', 'matrix-wishlist');
    }

    public static function get_copied_text()
    {

        return esc_html__('Copied the wishlist link:', 'matrix-wishlist');
    }

    public static function get_menu_text()
    {
        return esc_html__('Wishlist', 'matrix-wishlist');
    }

    public static function get_button_text()
    {
        return esc_html__('Add to wishlist', 'matrix-wishlist');
    }

    public static function get_browse_wishlist_text()
    {
        return esc_html__('Browse wishlist', 'matrix-wishlist');
    }

    public static function get_menu_label()
    {
        return esc_html__('Wishlist', 'matrix-wishlist');
    }

    public static function get_login_message_text()
    {
        return esc_html__('Please log in to use the wishlist!', 'matrix-wishlist');
    }

    public static function get_empty_wishlist_text()
    {
        return esc_html__('There are no products on the wishlist!', 'matrix-wishlist');
    }

    public static function get_product_added_text()
    {
        return esc_html__('Added to the wishlist!', 'matrix-wishlist');
    }

    public static function get_product_already_in_text()
    {
        return esc_html__('Already in the wishlist!', 'matrix-wishlist');
    }

    public static function get_wishlist_error_text()
    {
        return esc_html__('Have an error, please try again!', 'matrix-wishlist');
    }

    public static function get_product_removed_text()
    {
        return esc_html__('Removed from wishlist!', 'matrix-wishlist');
    }

    public static function get_product_doesnt_exist_text()
    {

        return esc_html__('The product does not exist on the wishlist!', 'matrix-wishlist');
    }

    public static function get_all_product_removed_text()
    {
        return esc_html__('All products were removed from your wishlist!', 'matrix-wishlist');
    }

    public static function get_remove_all_text()
    {
        return esc_html__('Clear all', 'matrix-wishlist');
    }

    public static function get_continue_shopping_text()
    {
        return esc_html__('Continue shopping', 'matrix-wishlist');
    }

    public static function get_open_wishlist_page_text()
    {
        return esc_html__('View all wishlist', 'matrix-wishlist');
    }


}