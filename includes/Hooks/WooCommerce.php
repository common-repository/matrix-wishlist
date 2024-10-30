<?php

namespace MatrixAddons\MatrixWishlist\Hooks;

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;

class WooCommerce
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));

        if (matrix_wishlist_auto_remove_after_cart()) {
            add_action('woocommerce_add_to_cart', array($this, 'add_to_cart'), 10, 2);
        }
    }

    public function init()
    {
        global $matrix_wishlist_products;
        // added products
        $wishlist_id = isset($_COOKIE['matrix_wishlist_id']) ? sanitize_text_field($_COOKIE['matrix_wishlist_id']) : '#';

         $matrix_wishlist_products = matrix_wishlist_get_products($wishlist_id);


        // add button for archive
        $archive_page_button_position = matrix_wishlist_button_archive_page_position();

        $method = 'add_button';

        switch ($archive_page_button_position) {
            case "below_title":
                add_action('woocommerce_shop_loop_item_title', array($this, $method), 11);
                break;
            case "below_rating":
                add_action('woocommerce_after_shop_loop_item_title', array($this, $method), 6);
                break;
            case "below_price":
                add_action('woocommerce_after_shop_loop_item_title', array(
                    $this,
                    $method
                ), 11);
                break;
            case "above_add_to_cart":
                add_action('woocommerce_after_shop_loop_item', array($this, $method), 9);
                break;
            case "below_add_to_cart":
                add_action('woocommerce_after_shop_loop_item', array($this, $method), 11);
                break;
        }

        $single_page_button_position = matrix_wishlist_button_single_page_position();

        $button_position_single_priority = 0;

        switch ($single_page_button_position) {
            case "below_title":
                $button_position_single_priority = 6;
                break;
            case "below_rating":
                $button_position_single_priority = 11;
                break;
            case "below_excerpt":
                $button_position_single_priority = 21;
                break;
            case "above_add_to_cart":
                $button_position_single_priority = 29;
                break;
            case "below_add_to_cart":
                $button_position_single_priority = 31;
                break;
            case "below_meta":
                $button_position_single_priority = 41;
                break;
            case "below_sharing":
                $button_position_single_priority = 51;
                break;
        }

        if ($button_position_single_priority > 0) {
            add_action('woocommerce_single_product_summary', array(
                $this,
                $method
            ), (int)$button_position_single_priority);
        }
    }

    function add_button()
    {
        echo do_shortcode('[matrix_wishlist_button]');
    }

    function add_to_cart($cart_item_key, $product_id)
    {
        $wishlist_id = GeneralHelper::matrix_wishlist_get_id();

        if ($wishlist_id !== '#') {

            $products = matrix_wishlist_get_products($wishlist_id);

            if (array_key_exists($product_id, $products)) {
                unset($products[$product_id]);
                update_option('matrix_wishlist_list_' . $wishlist_id, $products);
                matrix_wishlist_update_product_count($product_id, 'remove');
            }
        }
    }
}