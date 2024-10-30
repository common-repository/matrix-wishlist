<?php

namespace MatrixAddons\MatrixWishlist;

use MatrixAddons\MatrixWishlist\Hooks\Template;
use MatrixAddons\MatrixWishlist\Hooks\WooCommerce;

final class Main
{
    private static $instances = [];

    protected function __construct()
    {
        $this->define_constant();
        register_activation_hook(__FILE__, [$this, 'activate']);
        $this->load_helpers();
        $this->dispatch_hook();
    }

    public function define_constant()
    {
        define('MATRIX_WISHLIST_ABSPATH', dirname(MATRIX_WISHLIST_FILE) . '/');
        define('MATRIX_WISHLIST_PLUGIN_BASENAME', plugin_basename(MATRIX_WISHLIST_FILE));
        define('MATRIX_WISHLIST_ASSETS_DIR_PATH', MATRIX_WISHLIST_PLUGIN_DIR . 'assets/');
        define('MATRIX_WISHLIST_ASSETS_URI', MATRIX_WISHLIST_PLUGIN_URI . 'assets/');
    }

    public function load_helpers()
    {
        include_once MATRIX_WISHLIST_ABSPATH . 'includes/Helpers/options.php';
        include_once MATRIX_WISHLIST_ABSPATH . 'includes/Helpers/functions.php';
        include_once MATRIX_WISHLIST_ABSPATH . 'includes/Helpers/templates.php';

        //Load Installer
        include_once MATRIX_WISHLIST_ABSPATH . 'includes/Install.php';

    }

    public function init_plugin()
    {
        $this->load_textdomain();
    }

    public function dispatch_hook()
    {
        add_action('init', [$this, 'init_plugin']);


        if (is_admin()) {
            new \MatrixAddons\MatrixWishlist\Admin\Main();
        }
        //Hooks
        new Template();
        new WooCommerce();
        //
        new Shortcodes();
        new Assets();
        new Ajax();
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('matrix-wishlist', false, dirname(MATRIX_WISHLIST_PLUGIN_BASENAME) . '/languages');
    }

    public function activate()
    {
        Install::install();
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(MATRIX_WISHLIST_FILE));
    }

    public function template_path()
    {
        return apply_filters('matrix_wishlist_template_path', 'matrix_wishlist/');
    }


    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}
