<?php

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;

function matrix_wishlist_get_products($key)
{
    $products = get_option('matrix_wishlist_list_' . sanitize_text_field($key));

    return is_array($products) ? $products : array();
}

function matrix_wishlist_can_edit($key)
{
    if (is_user_logged_in()) {
        if (get_user_meta(get_current_user_id(), 'matrix_wishlist_id', true) === $key) {
            return true;
        }

        if (($keys = get_user_meta(get_current_user_id(), 'matrix_wishlist_multiple_ids', true)) && isset($keys[$key])) {
            return true;
        }
    } else {
        if (isset($_COOKIE['matrix_wishlist_id']) && (sanitize_text_field($_COOKIE['matrix_wishlist_id']) === $key)) {
            return true;
        }
    }

    return false;
}





function matrix_wishlist_get_count($key = false)
{
    $key = !$key ? GeneralHelper::matrix_wishlist_get_id() : $key;

    $products = matrix_wishlist_get_products($key);

    $count = count($products);

    return esc_html(apply_filters('matrix_wishlist_wishlist_count', $count, $key));
}

function matrix_wishlist_update_product_count($product_id, $action = 'add')
{
    $meta_count = 'matrix_wishlist_count';
    $meta_time = ($action === 'add' ? 'matrix_wishlist_add' : 'matrix_wishlist_remove');
    $count = get_post_meta($product_id, $meta_count, true);
    $new_count = 0;

    if ($action === 'add') {
        if ($count) {
            $new_count = absint($count) + 1;
        } else {
            $new_count = 1;
        }
    } elseif ($action === 'remove') {
        if ($count && (absint($count) > 1)) {
            $new_count = absint($count) - 1;
        } else {
            $new_count = 0;
        }
    }

    update_post_meta($product_id, $meta_count, $new_count);
    update_post_meta($product_id, $meta_time, time());
}

