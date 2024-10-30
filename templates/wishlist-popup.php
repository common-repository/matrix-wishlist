<?php
/**
 * Wishlist Popup.
 *
 * @author  Matrix
 * @version 1.0.1
 */

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;

defined('MATRIX_WISHLIST_VERSION') || exit; // Exit if accessed directly.

?>
<div class="matrix-wishlist-popup-inner" data-wishlist-id="<?php echo esc_attr($matrix_wishlist_id); ?>">
    <div class="matrix-wishlist-popup-content">
        <div class="matrix-wishlist-popup-header">
            <span class="matrix-wishlist-name"><?php echo esc_html(StringHelper::get_popup_heading()); ?></span>
            <?php
            echo '<span class="matrix-wishlist-count-wrapper">';
            echo '[<span class="matrix-wishlist-count" data-matrix-product-count="' . esc_attr($matrix_wishlist_count) . '">' . esc_html($matrix_wishlist_count) . '</span>]';

            echo '<span class="matrix-wishlist-empty"' . ($matrix_wishlist_count ? '' : ' style="display:none"') . '>' . StringHelper::get_remove_all_text() . '</span>';

            echo '</span>';
            ?>
            <span class="matrix-wishlist-popup-close fa-solid fa-xmark"></span>
        </div>
        <div class="matrix-wishlist-popup-body">
            <?php if (!empty($matrix_wishlist_body_message)) {
                echo '<div class="matrix-wishlist-popup-body-massage">' . esc_html($matrix_wishlist_body_message) . '</div>';
            } else {
                echo '<div class="matrix-wishlist-popup-body-content">';
                echo matrix_wishlist_get_wishlist_product_items($matrix_wishlist_id);
                echo '</div>';
            } ?>
        </div>
        <div class="matrix-wishlist-popup-footer">
            <div class="matrix-wishlist-popup-footer-inner">
                <a class="matrix-wishlist-page"
                   href="<?php echo esc_url(GeneralHelper::get_wishlist_page($matrix_wishlist_id, true)); ?>">
                    <?php echo StringHelper::get_open_wishlist_page_text(); ?>
                </a>
                <?php if (matrix_wishlist_continue_shopping_link() !== '') { ?>
                    <span class="matrix-wishlist-continue"
                          data-url="<?php echo esc_url(matrix_wishlist_continue_shopping_link()); ?>">
                                            <?php echo StringHelper::get_continue_shopping_text(); ?>
                                        </span>
                <?php } ?>
            </div>
            <?php if ($matrix_wishlist_notice !== '') { ?>
                <div class="matrix-wishlist-notice"><?php echo esc_html($matrix_wishlist_notice); ?></div>
            <?php } ?>
        </div>
    </div>
</div>