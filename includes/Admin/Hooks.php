<?php

namespace MatrixAddons\MatrixWishlist\Admin;

use MatrixAddons\MatrixWishlist\Admin\Settings\Wishlist;

class Hooks
{
    public function __construct()
    {

        add_filter('woocommerce_get_settings_pages', array($this, 'settings'));
        add_filter('manage_edit-product_columns', array($this, 'product_columns'), 10);
        add_action('manage_product_posts_custom_column', array($this, 'posts_custom_column'), 10, 2);
        add_filter('manage_edit-product_sortable_columns', array($this, 'sortable_columns'));
        add_filter('manage_users_columns', array($this, 'users_columns'));
        add_filter('manage_users_custom_column', array($this, 'users_columns_content'), 10, 3);
        add_filter('display_post_states', array($this, 'display_post_states'), 10, 2);

    }

    public function settings($settings)
    {
        $settings[] = new Wishlist();

        return $settings;
    }

    function product_columns($columns)
    {
        $columns['matrix-wishlist'] = esc_html__('Wishlist', 'matrix-wishlist');

        return $columns;
    }

    function posts_custom_column($column, $postid)
    {
        if ($column == 'matrix-wishlist') {
            if (($count = (int)get_post_meta($postid, 'matrix_wishlist_count', true)) > 0) {
                echo '<span class="matrix_wishlist_actions" data-pid="' . esc_attr($postid) . '">' . esc_html($count) . '</span>';
            }
        }
    }

    function sortable_columns($columns)
    {
        $columns['matrix-wishlist'] = 'matrix-wishlist';

        return $columns;
    }

    function users_columns($column)
    {
        $column['matrix-wishlist'] = esc_html__('Wishlist', 'matrix-wishlist');

        return $column;
    }

    function users_columns_content($val, $column_name, $user_id)
    {
        if ($column_name === 'matrix-wishlist') {
            $key = get_user_meta($user_id, 'matrix_wishlist_id', true);
            if (!empty($key) && ($products = matrix_wishlist_get_products($key))) {
                if (is_array($products) && ($count = count($products))) {
                    $val = '<span class="matrix_wishlist_actions" data-key="' . esc_attr($key) . '">' . esc_html($count) . '</span>';
                }
            }
        }

        return $val;
    }

    function display_post_states($states, $post)
    {
        if ('page' == get_post_type($post->ID) && $post->ID === absint(get_option('matrix_wishlist_page_id'))) {
            $states[] = esc_html__('Wishlist', 'matrix-wishlist');
        }

        return $states;
    }
}