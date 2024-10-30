<?php

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;

function matrix_wishlist_content($content_props = array())
{
    $content_props_real = wp_parse_args($content_props, array(
        'message' => '',
        'notice' => '',
        'product_count' => 0,
        'wishlist_id' => false
    ));
    $wishlist_id = !$content_props_real['wishlist_id'] ? GeneralHelper::matrix_wishlist_get_id() : $content_props_real['wishlist_id'];
    ob_start();

    wc_get_template('wishlist-popup.php', array(
        'matrix_wishlist_id' => $wishlist_id,
        'matrix_wishlist_count' => $content_props_real['product_count'],
        'matrix_wishlist_body_message' => $content_props_real['message'],
        'matrix_wishlist_notice' => $content_props_real['notice'],
    ), '', MATRIX_WISHLIST_PLUGIN_DIR . 'templates/');

    return ob_get_clean();
}

function matrix_wishlist_get_wishlist_product_items($wishlist_id)
{
    ob_start();

    $products = matrix_wishlist_get_products($wishlist_id);

    do_action('matrix_wishlist_before_items', $wishlist_id, $products);

    if (is_array($products) && (count($products) > 0)) {

        wc_get_template('wishlist-table.php', array(
            'matrix_wishlist_product_items' => $products,
            'matrix_wishlist_id' => $wishlist_id,
            'matrix_wishlist_link_to' => matrix_wishlist_button_link_to_individual_product()
        ), '', MATRIX_WISHLIST_PLUGIN_DIR . 'templates/');

    } else { ?>
        <div class="matrix-wishlist-popup-body-massage">
            <?php echo StringHelper::get_empty_wishlist_text(); ?>
        </div>
    <?php }

    do_action('matrix_wishlist_after_items', $wishlist_id, $products);

    return apply_filters('matrix_wishlist_wishlist_items', ob_get_clean(), $wishlist_id, $products);

}