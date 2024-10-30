<?php

namespace MatrixAddons\MatrixWishlist\Hooks;

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;

class Template
{
    public function __construct()
    {
        add_filter('query_vars', array($this, 'query_vars'), 1);

        add_action('init', array($this, 'rewrite'));

        add_action('template_redirect', array($this, 'wishlist_add_by_link'));

        add_filter('request', array($this, 'request'));

        add_action('wp_footer', array($this, 'wp_footer'));

        add_action('wp_login', array($this, 'wp_login'), 10, 2);

        add_action('wp_logout', array($this, 'wp_logout'), 10, 1);

        add_filter('wp_dropdown_cats', array($this, 'dropdown_cats_multiple'), 10, 2);

        add_filter('wp_kses_allowed_html', array($this, 'kses_allowed_html'), 99, 2);

        add_filter('wp_nav_menu_items', array($this, 'nav_menu_items'), 99, 2);
    }

    function query_vars($vars)
    {
        $vars[] = 'matrix_wishlist_id';

        return $vars;
    }

    public function rewrite()
    {
        if ($page_id = matrix_wishlist_page_id()) {
            $page_slug = get_post_field('post_name', $page_id);

            if ($page_slug !== '') {
                add_rewrite_rule('^' . $page_slug . '/([\w]+)/?', 'index.php?page_id=' . $page_id . '&matrix_wishlist_id=$matches[1]', 'top');
            }
        }
    }

    public function wishlist_add_by_link()
    {
        if (!isset($_REQUEST['add-to-wishlist']) && !isset($_REQUEST['add_to_wishlist'])) {
            return false;
        }

        $wishlist_id = GeneralHelper::matrix_wishlist_get_id();
        $product_id = absint(isset($_REQUEST['add_to_wishlist']) ? (int)sanitize_text_field($_REQUEST['add_to_wishlist']) : 0);
        $product_id = absint(isset($_REQUEST['add-to-wishlist']) ? (int)sanitize_text_field($_REQUEST['add-to-wishlist']) : $product_id);

        if ($product_id) {
            if ($wishlist_id !== '#' && $wishlist_id !== null) {

                $products = matrix_wishlist_get_products($wishlist_id);

                if (!array_key_exists($product_id, $products)) {
                    // insert if not exists
                    $products = array(
                            $product_id => array(
                                'time' => time(),
                                'note' => ''
                            )
                        ) + $products;
                    update_option('matrix_wishlist_list_' . $wishlist_id, $products);
                }
            }
        }

        // redirect to wishlist page
        wp_safe_redirect(GeneralHelper::get_wishlist_page($wishlist_id, true));
    }

    function request($vars)
    {
        if (isset($vars['orderby']) && 'matrix_wishlist' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => 'matrix_wishlist_count',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }

    function wp_footer()
    {
        if (is_admin()) {
            return;
        }
        echo '<div id="matrix-wishlist-popup-container" class="matrix-wishlist-popup"></div>';
    }

    function wp_login($user_login, $user)
    {
        if (isset($user->data->ID)) {
            $wishlist_id = get_user_meta($user->data->ID, 'matrix_wishlist_id', true);

            if (empty($wishlist_id)) {
                $wishlist_id = GeneralHelper::generate_wishlist_id_hash();

                while (GeneralHelper::is_id_exists($wishlist_id)) {
                    $wishlist_id = GeneralHelper::generate_wishlist_id_hash();
                }

                // set a new key
                update_user_meta($user->data->ID, 'matrix_wishlist_id', $wishlist_id);
            }

            // multiple wishlist
            if (!get_user_meta($user->data->ID, 'matrix_wishlist_multiple_ids', true)) {
                update_user_meta($user->data->ID, 'matrix_wishlist_multiple_ids', array(
                    $wishlist_id => array(
                        'type' => 'primary',
                        'name' => '',
                        'time' => ''
                    )
                ));
            }

            $secure = apply_filters('matrix_wishlist_cookie_secure', wc_site_is_https() && is_ssl());
            $httponly = apply_filters('matrix_wishlist_cookie_httponly', true);

            if (isset($_COOKIE['matrix_wishlist_id']) && !empty($_COOKIE['matrix_wishlist_id'])) {
                wc_setcookie('matrix_wishlist_id_previous', trim(sanitize_text_field($_COOKIE['matrix_wishlist_id'])), time() + 604800, $secure, $httponly);
            }

            wc_setcookie('matrix_wishlist_id', $wishlist_id, time() + 604800, $secure, $httponly);
        }
    }

    function wp_logout($user_id)
    {
        if (isset($_COOKIE['matrix_wishlist_id_previous']) && !empty($_COOKIE['matrix_wishlist_id_previous'])) {
            $secure = apply_filters('matrix_wishlist_cookie_secure', wc_site_is_https() && is_ssl());
            $httponly = apply_filters('matrix_wishlist_cookie_httponly', true);

            wc_setcookie('matrix_wishlist_id', trim(sanitize_text_field($_COOKIE['matrix_wishlist_id_previous'])), time() + 604800, $secure, $httponly);
        } else {
            unset($_COOKIE['matrix_wishlist_id_previous']);
            unset($_COOKIE['matrix_wishlist_id']);
        }
    }

    function dropdown_cats_multiple($output, $r)
    {
        if (isset($r['multiple']) && $r['multiple']) {
            $output = preg_replace('/^<select/i', '<select multiple', $output);
            $output = str_replace("name='{$r['name']}'", "name='{$r['name']}[]'", $output);

            foreach (array_map('trim', explode(",", $r['selected'])) as $value) {
                $output = str_replace("value=\"{$value}\"", "value=\"{$value}\" selected", $output);
            }
        }

        return $output;
    }

    function kses_allowed_html($allowed, $context)
    {
        if ($context === 'matrix_wishlist') {
            return array(
                'img' => array('class' => array(), 'src' => array(), 'alt' => array()),
                'div' => array(
                    'class' => array(),
                    'id' => array(),
                ),
                'a' => array(
                    'class' => array(),
                    'id' => array(),
                    'data-id' => array(),
                    'href' => array(),
                    'title' => array()
                ),
                'span' => array('class' => array(), 'id' => array()),
                'i' => array('class' => array()),
                'u' => array('class' => array()),
                's' => array('class' => array()),
                'strong' => array(),
                'del' => array(),
                'ins' => array(),
            );
        }

        return $allowed;
    }

    function nav_menu_items($items, $args)
    {
        $selected = false;
        $saved_menus = get_option('matrix_wishlist_menus', array());

        if (!is_array($saved_menus) || empty($saved_menus) || !property_exists($args, 'menu')) {
            return $items;
        }

        if ($args->menu instanceof \WP_Term) {
            // menu object
            if (in_array($args->menu->term_id, $saved_menus, false)) {
                $selected = true;
            }
        } elseif (is_numeric($args->menu)) {
            // menu id
            if (in_array($args->menu, $saved_menus, false)) {
                $selected = true;
            }
        } elseif (is_string($args->menu)) {
            // menu slug or name
            $menu = get_term_by('name', $args->menu, 'nav_menu');

            if (!$menu) {
                $menu = get_term_by('slug', $args->menu, 'nav_menu');
            }

            if ($menu && in_array($menu->term_id, $saved_menus, false)) {
                $selected = true;
            }
        }

        if ($selected) {
            $menu_item = '<li class="' . esc_attr(apply_filters('matrix_wishlist_menu_item_class', 'menu-item matrix-wishlist-menu-item menu-item-type-matrix_wishlist')) . '"><a href="' . esc_url(GeneralHelper::get_wishlist_page()) . '"><span class="matrix-wishlist-menu-item-inner" data-count="' . esc_attr(matrix_wishlist_get_count()) . '">' . esc_html(apply_filters('matrix_wishlist_menu_item_label', StringHelper::get_menu_label())) . '</span></a></li>';
            $items .= apply_filters('matrix_wishlist_menu_item', $menu_item);
        }

        return $items;
    }
}
