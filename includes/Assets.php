<?php

namespace MatrixAddons\MatrixWishlist;

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;
use WeDevs\ERP\Settings\General;

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'scripts'));
        add_action('wp_head', array($this, 'dynamic_css'), 10);

    }

    public function scripts()
    {

        wp_enqueue_style('perfect-scrollbar', MATRIX_WISHLIST_PLUGIN_URI . 'assets/vendor/perfect-scrollbar/css/perfect-scrollbar.min.css');
        wp_enqueue_style('perfect-scrollbar-wpc', MATRIX_WISHLIST_PLUGIN_URI . 'assets/vendor/perfect-scrollbar/css/custom-theme.css');
        wp_enqueue_script('perfect-scrollbar', MATRIX_WISHLIST_PLUGIN_URI . 'assets/vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js', array('jquery'), MATRIX_WISHLIST_VERSION, true);


        wp_enqueue_style('matrix-wishlist-fontawesome', MATRIX_WISHLIST_PLUGIN_URI . 'assets/vendor/font-awesome/css/fontawesome.css');

        // main style
        wp_enqueue_style('matrix-wishlist-frontend', MATRIX_WISHLIST_PLUGIN_URI . 'assets/css/matrix-wishlist.css', array(), MATRIX_WISHLIST_VERSION);


        // main js
        wp_enqueue_script('matrix-wishlist-frontend', MATRIX_WISHLIST_PLUGIN_URI . 'assets/js/matrix-wishlist.js', array('jquery'), MATRIX_WISHLIST_VERSION, true);

        // localize
        wp_localize_script('matrix-wishlist-frontend', 'matrix_wishlist_localize_var', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'menu_action' => get_option('matrix_wishlist_menu_action', 'open_page'),
                'wishlist_url' => GeneralHelper::get_wishlist_page(),
                'button_action' => matrix_wishlist_button_action(),
                'button_action_after' => matrix_wishlist_button_action_after(),
                'empty_confirm' => StringHelper::get_empty_confirm(),
                'delete_confirm' => StringHelper::get_delete_confirm(),
                'copied_text' => StringHelper::get_copied_text(),
                'menu_text' => StringHelper::get_menu_text(),
                'button_text' => StringHelper::get_button_text(),
                'button_text_added' => StringHelper::get_browse_wishlist_text(),
                'wishlist_id' => GeneralHelper::generate_wishlist_id_hash(),
            )
        );
    }

    public function minify_css($css = '')
    {

        // Return if no CSS
        if (!$css) return;

        // Normalize whitespace
        $css = preg_replace('/\s+/', ' ', $css);

        // Remove ; before }
        $css = preg_replace('/;(?=\s*})/', '', $css);

        // Remove space after , : ; { } */ >
        $css = preg_replace('/(,|:|;|\{|}|\*\/|>) /', '$1', $css);

        // Remove space before , ; { }
        $css = preg_replace('/ (,|;|\{|})/', '$1', $css);

        // Strips leading 0 on decimal values (converts 0.5px into .5px)
        $css = preg_replace('/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css);

        // Strips units if value is 0 (converts 0px to 0)
        $css = preg_replace('/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css);

        // Trim
        $css = trim($css);

        // Return minified CSS
        return $css;

    }

    public function dynamic_css()
    {

        $all_dynamic_css = '';

        $color = matrix_wishlist_message_background_color();
        $header_bg = matrix_wishlist_popup_header_background_color();
        $footer_bg = matrix_wishlist_popup_footer_background_color();

        if ($color !== '#5fbd74') {
            $all_dynamic_css .= ".matrix-wishlist-popup .matrix-wishlist-popup-inner .matrix-wishlist-popup-content .matrix-wishlist-popup-footer .matrix-wishlist-notice { background-color: {$color}; } ";
        };
        if ($header_bg !== '#1877f2') {
            $all_dynamic_css .= ".matrix-wishlist-popup .matrix-wishlist-popup-inner .matrix-wishlist-popup-content .matrix-wishlist-popup-header { background-color: {$header_bg}; } ";
        };
        if ($footer_bg !== '#1877f2') {
            $all_dynamic_css .= ".matrix-wishlist-popup .matrix-wishlist-popup-inner .matrix-wishlist-popup-content .matrix-wishlist-popup-footer { background-color: {$footer_bg}; } ";
        };

        if ($all_dynamic_css != '') {

            $all_dynamic_css = $this->minify_css($all_dynamic_css);
            ?>

            <style type="text/css" class="matrix-wishlist-dynamic-css">

                <?php echo esc_html($all_dynamic_css) ; ?>

            </style>

            <?php
        }
    }
}