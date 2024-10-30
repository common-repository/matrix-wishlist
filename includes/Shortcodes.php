<?php

namespace MatrixAddons\MatrixWishlist;

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;

class Shortcodes
{
    public function __construct()
    {

        add_action('init', [$this, 'register_shortcode']);

    }

    public function register_shortcode()
    {
        add_shortcode('matrix_wishlist_button', array($this, 'wishlist_button'));
        add_shortcode('matrix_wishlist', array($this, 'wishlist_product_list'));
    }

    public function wishlist_button($attrs)
    {

        global $matrix_wishlist_products;

        $output = '';

        $attrs = shortcode_atts(array(
            'id' => null,
            'type' => matrix_wishlist_button_type()
        ), $attrs, 'matrix_wishlist_button');

        if (!$attrs['id']) {
            global $product;

            if ($product) {
                $attrs['id'] = $product->get_id();
            }
        }

        if ($attrs['id']) {
            // check cats
            $selected_cats = matrix_wishlist_button_show_only_on_categories();

            if (!empty($selected_cats) && ($selected_cats[0] !== '0')) {
                if (!has_term($selected_cats, 'product_cat', $attrs['id'])) {
                    return '';
                }
            }

            $class = 'button matrix-wishlist-button matrix-wishlist-button-' . esc_attr($attrs['id']);

            if (is_array($matrix_wishlist_products) && array_key_exists($attrs['id'], $matrix_wishlist_products)) {
                $class .= ' matrix-wishlist-added';
                $button_label = StringHelper::get_browse_wishlist_text();
            } else {
                $button_label = StringHelper::get_button_text();
            }

            if (matrix_wishlist_button_class() !== '') {
                $class .= ' ' . esc_attr(matrix_wishlist_button_class());
            }
            $button_label = '<span>' . esc_html($button_label) . '</span>';

            if ($attrs['type'] === 'link') {
                $output = '<a href="' . esc_url('?add-to-wishlist=' . $attrs['id']) . '" class="' . esc_attr($class) . '" data-id="' . esc_attr($attrs['id']) . '">' . $button_label . '</a>';
            } else {
                $output = '<button class="' . esc_attr($class) . '" data-id="' . esc_attr($attrs['id']) . '">' . $button_label . '</button>';
            }
        }

        return apply_filters('matrix_wishlist_button_html', $output, $attrs['id'], $attrs);
    }

    public function wishlist_product_list()
    {
        if (get_query_var('matrix_wishlist_id')) {
            $wishlist_id = get_query_var('matrix_wishlist_id');
        } elseif (isset($_REQUEST['matrix_wishlist_id']) && !empty($_REQUEST['matrix_wishlist_id'])) {
            $wishlist_id = sanitize_text_field($_REQUEST['matrix_wishlist_id']);
        } else {
            $wishlist_id = GeneralHelper::matrix_wishlist_get_id();
        }


        $share_url_raw = GeneralHelper::get_wishlist_page($wishlist_id, true);
        $share_url = urlencode($share_url_raw);
        $return_html = '<div class="matrix-wishlist-list">';
        $return_html .= matrix_wishlist_get_wishlist_product_items($wishlist_id);
        $return_html .= '<div class="matrix-wishlist-actions">';

        $share_items = array();//array('facebook', 'twitter', 'mail');

        if (!empty($share_items)) {
            $return_html .= '<div class="matrix-wishlist-share">';
            $return_html .= '<span class="matrix-wishlist-share-label">' . esc_html__('Share on:', 'matrix-wishlist') . '</span>';
            $return_html .= (in_array('facebook', $share_items)) ? '<a class="matrix-wishlist-share-facebook" href="https://www.facebook.com/sharer.php?u=' . $share_url . '" target="_blank"><i class="matrix-wishlist-icon fa-brands fa-facebook-f"></i></a>' : '';
            $return_html .= (in_array('twitter', $share_items)) ? '<a class="matrix-wishlist-share-twitter" href="https://twitter.com/share?url=' . $share_url . '" target="_blank"><i class="matrix-wishlist-icon fa-brands fa-twitter"></i></a>' : '';
            $return_html .= (in_array('mail', $share_items)) ? '<a class="matrix-wishlist-share-mail" href="mailto:?body=' . $share_url . '" target="_blank"><i class="matrix-wishlist-icon fa-solid fa-envelope"></i></a>' : '';
            $return_html .= '</div><!-- /matrix-wishlist-share -->';
        }


        $copy = false;
        if ($copy) {
            $return_html .= '<div class="matrix-wishlist-copy">';
            $return_html .= '<span class="matrix-wishlist-copy-label">' . esc_html__('Wishlist link:', 'matrix-wishlist') . '</span>';
            $return_html .= '<span class="matrix-wishlist-copy-url"><input id="matrix_wishlist_copy_url" type="url" value="' . esc_attr($share_url_raw) . '" readonly/></span>';
            $return_html .= '<span class="matrix-wishlist-copy-button"><span class="fa-brands fas fa-copy" id="matrix_wishlist_copy_button"  title="' . esc_html__('Copy', 'matrix-wishlist') . '"/></span>';
            $return_html .= '</div><!-- /matrix-wishlist-copy -->';
        }

        $return_html .= '</div><!-- /matrix-wishlist-actions -->';
        $return_html .= '</div><!-- /matrix-wishlist-list -->';

        return $return_html;
    }
}
