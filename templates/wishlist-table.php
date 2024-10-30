<?php
/**
 * Wishlist Table.
 *
 * @author  Matrix
 * @version 1.0.1
 */

defined('MATRIX_WISHLIST_VERSION') || exit; // Exit if accessed directly.
?>
<table class="matrix-wishlist-items-table">
    <?php

    do_action('matrix_wishlist_wishlist_items_before', $matrix_wishlist_id, $matrix_wishlist_product_items);

    foreach ($matrix_wishlist_product_items as $product_id => $product_data) {

        $product = wc_get_product($product_id);

        if (!$product) {
            continue;
        }

        if (is_array($product_data) && isset($product_data['time'])) {
            $product_time = date_i18n(get_option('date_format'), $product_data['time']);
        } else {
            $product_time = date_i18n(get_option('date_format'), $product_data);
        }

        if (is_array($product_data) && !empty($product_data['note'])) {
            $product_note = $product_data['note'];
        } else {
            $product_note = '';
        } ?>
        <tr class="<?php echo esc_attr('matrix-wishlist-item matrix-wishlist-item-' . $product_id); ?>"
            data-id="<?php echo esc_attr($product_id); ?>">

            <?php do_action('matrix_wishlist_wishlist_item_before', $product, $product_id, $matrix_wishlist_id); ?>

            <?php if (matrix_wishlist_can_edit($matrix_wishlist_id)) { ?>
                <td class="matrix-wishlist-item--remove"><span class="fa-solid fa-xmark"></span></td>
            <?php } ?>

            <td class="matrix-wishlist-item--image">
                <?php
                if ($matrix_wishlist_link_to !== 'none') {
                    echo '<a ' . ($matrix_wishlist_link_to === 'yes_popup' ? 'class="matrix-wishlist-link" data-id="' . esc_attr($product_id) . '" data-context="matrix_wishlist"' : '') . ' href="' . esc_url($product->get_permalink()) . '" ' . ($matrix_wishlist_link_to === 'yes_blank' ? 'target="_blank"' : '') . '>';
                    echo wp_kses(apply_filters('matrix_wishlist_item_image', $product->get_image()), 'matrix_wishlist');
                    echo '</a>';
                } else {
                    echo wp_kses(apply_filters('matrix_wishlist_item_image', $product->get_image()), 'matrix_wishlist');
                }

                do_action('matrix_wishlist_wishlist_item_image', $product, $product_id, $matrix_wishlist_id);
                ?>
            </td>

            <td class="matrix-wishlist-item--info">
                <?php if ($matrix_wishlist_link_to !== 'none') {
                    echo '<div class="matrix-wishlist-item--name"><a ' . ($matrix_wishlist_link_to === 'yes_popup' ? 'class="matrix-wishlist-link" data-id="' . esc_attr($product_id) . '" data-context="matrix_wishlist"' : '') . ' href="' . esc_url($product->get_permalink()) . '" ' . ($matrix_wishlist_link_to === 'yes_blank' ? 'target="_blank"' : '') . '>' . esc_html(apply_filters('matrix_wishlist_item_name', $product->get_name(), $product)) . '</a></div>';
                } else {
                    echo '<div class="matrix-wishlist-item--name">' . esc_html(apply_filters('matrix_wishlist_item_name', $product->get_name(), $product)) . '</div>';
                }

                echo '<div class="matrix-wishlist-item--price">' . wp_kses(apply_filters('matrix_wishlist_item_price', $product->get_price_html(), $product), 'matrix_wishlist') . '</div>';

                echo '<div class="matrix-wishlist-item--time">' . esc_html(apply_filters('matrix_wishlist_item_time', $product_time, $product)) . '</div>';

                do_action('matrix_wishlist_wishlist_item_info', $product, $product_id, $matrix_wishlist_id); ?>
            </td>

            <td class="matrix-wishlist-item--actions">
                <div class="matrix-wishlist-item--stock">
                    <?php $product->is_in_stock() ? esc_html_e('In stock', 'matrix-wishlist') : esc_html_e('Out of stock', 'matrix-wishlist'); ?>
                </div>

                <div class="matrix-wishlist-item--add">
                    <?php echo do_shortcode('[add_to_cart style="" show_price="false" id="' . esc_attr($product_id) . '"]'); ?>
                </div>

                <?php do_action('matrix_wishlist_wishlist_item_actions', $product, $product_id, $matrix_wishlist_id); ?>
            </td>

            <?php do_action('matrix_wishlist_wishlist_item_after', $product, $product_id, $matrix_wishlist_id); ?>
        </tr>
    <?php }

    do_action('matrix_wishlist_wishlist_items_after', $matrix_wishlist_id, $matrix_wishlist_product_items);
    ?>
</table>