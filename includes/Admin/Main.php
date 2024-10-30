<?php

namespace MatrixAddons\MatrixWishlist\Admin;
final class Main
{

    /**
     * The single instance of the class.
     *
     * @var Main
     * @since 1.0.0
     */
    protected static $_instance = null;


    /**
     * Main Main Instance.
     *
     * Ensures only one instance of Yatra_Admin is loaded or can be loaded.
     *
     * @return Main - Main instance.
     * @since 1.0.0
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Main Constructor.
     */
    public function __construct()
    {
        $this->init();
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0.0
     */
    private function init_hooks()
    {


        add_filter("plugin_action_links_" . plugin_basename(MATRIX_WISHLIST_FILE), array($this, 'settings_link'));


    }

    function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=wc-settings&tab=wishlist">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }


    /**
     * Include required core files used in admin.
     */
    public function init()
    {
        new Assets();
        new Hooks();
    }


}
