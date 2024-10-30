<?php

namespace MatrixAddons\MatrixWishlist\Helpers;

class GeneralHelper
{
    public static function generate_wishlist_id_hash()
    {
        $wishlist_id = '';

        $wishlist_id_str = apply_filters('matrix_wishlist_id_hash_characters', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

        $wishlist_id_str_len = strlen($wishlist_id_str);

        for ($i = 0; $i < apply_filters('matrix_wishlist_id_length', 6); $i++) {

            $wishlist_id .= $wishlist_id_str[random_int(0, $wishlist_id_str_len - 1)];
        }

        $wishlist_id = $wishlist_id . get_current_user_id() . time();

        return apply_filters('matrix_wishlist_generate_wishlist_id_hash', md5($wishlist_id));
    }

    public static function get_wishlist_page($wishlist_id = null, $full = false)
    {
        $url = home_url('/');

        if ($page_id = matrix_wishlist_page_id()) {
            if ($full) {
                if (!$wishlist_id) {
                    $wishlist_id = GeneralHelper::matrix_wishlist_get_id();
                }

                if (get_option('permalink_structure') !== '') {
                    $url = trailingslashit(get_permalink($page_id)) . $wishlist_id;
                } else {
                    $url = get_permalink($page_id) . '&matrix_wishlist_id=' . $wishlist_id;
                }
            } else {
                $url = get_permalink($page_id);
            }
        }

        return esc_url(apply_filters('matrix_wishlist_wishlist_url', $url, $wishlist_id));
    }

    public static function is_id_exists($wishlist_id)
    {
        if (get_option('matrix_wishlist_list_' . $wishlist_id)) {

            return true;
        }

        return false;
    }

    public static function matrix_wishlist_get_id($new_id = false)
    {
        if ($new_id) {
            // get a new key for multiple wishlist
            $wishlist_id = GeneralHelper::generate_wishlist_id_hash();

            while (GeneralHelper::is_id_exists($wishlist_id)) {
                $wishlist_id = GeneralHelper::generate_wishlist_id_hash();
            }

            return $wishlist_id;
        } else {
            if (!is_user_logged_in() && matrix_wishlist_disable_for_non_login()) {
                return '#';
            }


            if (is_user_logged_in() && (($user_id = get_current_user_id()) > 0)) {
                $wishlist_id = get_user_meta($user_id, 'matrix_wishlist_id', true);
                
                if (empty($wishlist_id)) {
                    $wishlist_id = GeneralHelper::generate_wishlist_id_hash();

                    while (GeneralHelper::is_id_exists($wishlist_id)) {
                        $wishlist_id = GeneralHelper::generate_wishlist_id_hash();
                    }

                    update_user_meta($user_id, 'matrix_wishlist_id', $wishlist_id);

                    // multiple wishlist
                    update_user_meta($user_id, 'matrix_wishlist_multiple_ids', array(
                        $wishlist_id => array(
                            'type' => 'primary',
                            'name' => '',
                            'time' => ''
                        )
                    ));
                }

                return $wishlist_id;
            }

            if (isset($_COOKIE['matrix_wishlist_id'])) {
                return trim(sanitize_text_field($_COOKIE['matrix_wishlist_id']));
            }

            return null;
        }
    }
}