<?php
/**
 * Plugin Name: Matrix Wishlist
 * Plugin URI: https://wordpress.org/plugins/matrix-wishlist
 * Description:  Matrix Wishlist plugin allows you to add product on wishlist
 * Author: MatrixAddons
 * Author URI: https://profiles.wordpress.org/matrixaddons
 * Version: 1.0.3
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: matrix-wishlist
 * Domain Path: /languages/
 * WC requires at least: 3.0
 * WC tested up to: 6.5
 *
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Define MATRIX_WISHLIST_PLUGIN_FILE.
if (!defined('MATRIX_WISHLIST_FILE')) {
    define('MATRIX_WISHLIST_FILE', __FILE__);
}

// Define MATRIX_WISHLIST_VERSION.
if (!defined('MATRIX_WISHLIST_VERSION')) {
    define('MATRIX_WISHLIST_VERSION', '1.0.3');
}

// Define MATRIX_WISHLIST_PLUGIN_URI.
if (!defined('MATRIX_WISHLIST_PLUGIN_URI')) {
    define('MATRIX_WISHLIST_PLUGIN_URI', plugins_url('/', MATRIX_WISHLIST_FILE));
}

// Define MATRIX_WISHLIST_PLUGIN_DIR.
if (!defined('MATRIX_WISHLIST_PLUGIN_DIR')) {
    define('MATRIX_WISHLIST_PLUGIN_DIR', plugin_dir_path(MATRIX_WISHLIST_FILE));
}
/**
 * Initializes the main plugin
 *
 * @return \MatrixAddons\MatrixWishlist\Main
 */
if (!function_exists('matrix_wishlist')) {
    function matrix_wishlist()
    {
        return \MatrixAddons\MatrixWishlist\Main::getInstance();
    }
}

matrix_wishlist();
