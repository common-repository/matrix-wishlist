<?php

namespace MatrixAddons\MatrixWishlist;

defined('ABSPATH') || exit;

/**
 * Main Install Class.
 *
 * @class Matrix Quick View
 */
final class Install
{

    private static $update_callbacks = array();

    public static function install()
    {
        if (!is_blog_installed()) {
            return;
        }


        $matrix_wishlist_version = get_option('matrix_wishlist_plugin_version');

        if (empty($matrix_wishlist_version)) {
            self::create_options();

        }
        //save install date
        if (false == get_option('matrix_wishlist_install_date')) {
            update_option('matrix_wishlist_install_date', current_time('timestamp'));
        }

        self::versionwise_update();
        self::update_matrix_wishlist_version();
    }


    private static function create_options()
    {
        $wishlist_page_data = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'post_name' => 'wishlist',
            'post_title' => esc_html__('Wishlist', 'matrix-wishlist'),
            'post_content' => '[matrix_wishlist]',
            'post_parent' => 0,
            'comment_status' => 'closed'
        );
        $wishlist_page_id = wp_insert_post($wishlist_page_data);

        update_option('matrix_wishlist_page_id', $wishlist_page_id);


    }


    private static function versionwise_update()
    {
        $matrix_wishlist_version = get_option('matrix_wishlist_plugin_version', null);

        if ($matrix_wishlist_version == '' || $matrix_wishlist_version == null || empty($matrix_wishlist_version)) {
            return;
        }
        if (version_compare($matrix_wishlist_version, MATRIX_WISHLIST_VERSION, '<')) { // 2.0.15 < 2.0.16

            foreach (self::$update_callbacks as $version => $callbacks) {

                if (version_compare($matrix_wishlist_version, $version, '<')) { // 2.0.15 < 2.0.16

                    self::exe_update_callback($callbacks);
                }
            }
        }
    }

    private static function exe_update_callback($callbacks)
    {
        include_once MATRIX_WISHLIST_ABSPATH . 'includes/Helpers/update.php';

        foreach ($callbacks as $callback) {

            call_user_func($callback);

        }
    }

    /**
     * Update Matrix Quick View version to current.
     */
    private static function update_matrix_wishlist_version()
    {
        delete_option('matrix_wishlist_plugin_version');
        delete_option('matrix_wishlist_plugin_db_version');
        add_option('matrix_wishlist_plugin_version', MATRIX_WISHLIST_VERSION);
        add_option('matrix_wishlist_plugin_db_version', MATRIX_WISHLIST_VERSION);
    }

    public static function init()
    {

        add_action('init', array(__CLASS__, 'check_version'), 5);


    }

    public static function check_version()
    {
        if (!defined('IFRAME_REQUEST') && version_compare(get_option('matrix_wishlist_plugin_version'), MATRIX_WISHLIST_VERSION, '<')) {
            self::install();
            do_action('matrix_wishlist_updated');
        }
    }


}

Install::init();
