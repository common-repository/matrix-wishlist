<?php

namespace MatrixAddons\MatrixWishlist;

use MatrixAddons\MatrixWishlist\Helpers\GeneralHelper;
use MatrixAddons\MatrixWishlist\Helpers\StringHelper;

class Ajax
{
    public function __construct()
    {

        add_action('wp_ajax_wishlist_add', array($this, 'add_to_wishlist'));
        add_action('wp_ajax_nopriv_wishlist_add', array($this, 'add_to_wishlist'));

        //
        // remove
        add_action('wp_ajax_wishlist_remove', array($this, 'wishlist_remove'));
        add_action('wp_ajax_nopriv_wishlist_remove', array($this, 'wishlist_remove'));

        // empty
        add_action('wp_ajax_wishlist_empty', array($this, 'wishlist_empty'));
        add_action('wp_ajax_nopriv_wishlist_empty', array($this, 'wishlist_empty'));

        // load
        add_action('wp_ajax_wishlist_load', array($this, 'wishlist_load'));
        add_action('wp_ajax_nopriv_wishlist_load', array($this, 'wishlist_load'));

        // load count
        add_action('wp_ajax_wishlist_load_count', array($this, 'wishlist_load_count'));
        add_action('wp_ajax_nopriv_wishlist_load_count', array($this, 'wishlist_load_count'));

    }

    function add_to_wishlist()
    {
        $wishlist_id = GeneralHelper::matrix_wishlist_get_id();

        $response_data = array(
            'notice' => '',
            'message' => '',
            'product_count' => 0,
            'wishlist_id' => $wishlist_id

        );
        if (($product_id = (int)sanitize_text_field($_POST['product_id'])) > 0) {
            if ($wishlist_id === '#') {
                $response_data['notice'] = StringHelper::get_login_message_text();
                $response_data['notice'] = StringHelper::get_empty_wishlist_text();
            } else {
                $products = matrix_wishlist_get_products($wishlist_id);

                if (!array_key_exists($product_id, $products)) {
                    // insert if not exists
                    $products = array(
                            $product_id => array(
                                'time' => time(),
                                'note' => ''
                            )
                        ) + $products;
                    update_option('matrix_wishlist_list_' . $wishlist_id, $products);
                    matrix_wishlist_update_product_count($product_id, 'add');
                    $response_data['notice'] = StringHelper::get_product_added_text();
                } else {
                    $response_data['notice'] = StringHelper::get_product_already_in_text();
                }

                $response_data['product_count'] = count($products);

            }
        } else {
            $product_id = 0;
            $response_data['notice'] = StringHelper::get_wishlist_error_text();

        }

        do_action('matrix_wishlist_add', $product_id, $wishlist_id);


        $this->render_wishlist_content($response_data);
    }

    public function render_wishlist_content($response_data = array())
    {
        switch (matrix_wishlist_button_action()) {
            case "message":
                $response_data['message'] = $response_data['notice'];
                $response_data['notice'] = '';
                break;
            case "list":
                $response_data['message'] = '';
                break;

        }
        echo matrix_wishlist_content($response_data);
        exit;

    }

    function wishlist_remove()
    {
        $return = array('status' => 0);

        $wishlist_id = sanitize_text_field($_POST['wishlist_id']);

        if (empty($wishlist_id)) {
            $wishlist_id = GeneralHelper::matrix_wishlist_get_id();
        }

        if (($product_id = (int)sanitize_text_field($_POST['product_id'])) > 0) {
            if ($wishlist_id === '#') {
                $return['notice'] = StringHelper::get_login_message_text();
            } else {

                $products = matrix_wishlist_get_products($wishlist_id);

                if (array_key_exists($product_id, $products)) {
                    unset($products[$product_id]);
                    update_option('matrix_wishlist_list_' . $wishlist_id, $products);
                    matrix_wishlist_update_product_count($product_id, 'remove');
                    $return['count'] = count($products);
                    $return['status'] = 1;
                    $return['notice'] = StringHelper::get_product_removed_text();

                    if (empty($products)) {
                        $return['content'] = StringHelper::get_empty_wishlist_text();
                    }
                } else {
                    $return['notice'] = StringHelper::get_product_doesnt_exist_text();
                }
            }
        } else {
            $product_id = 0;
            $return['notice'] = StringHelper::get_wishlist_error_text();
        }

        do_action('matrix_wishlist_remove', $product_id, $wishlist_id);

        echo json_encode($return);
        die();
    }

    function wishlist_empty()
    {
        $return = array('status' => 0);
        $key = sanitize_text_field($_POST['key']);

        if (empty($key)) {
            $key = GeneralHelper::matrix_wishlist_get_id();
        }

        if ($key === '#') {
            $return['notice'] = StringHelper::get_login_message_text();
        } else {

            $products = matrix_wishlist_get_products($key);

            if (!empty($products)) {
                foreach (array_keys($products) as $product_id) {
                    // update count
                    matrix_wishlist_update_product_count($product_id, 'remove');
                }
            }


            // remove option
            update_option('matrix_wishlist_list_' . $key, array());
            $return['status'] = 1;
            $return['count'] = 0;
            $return['notice'] = StringHelper::get_all_product_removed_text();
            $return['content'] = StringHelper::get_empty_wishlist_text();
        }

        do_action('matrix_wishlist_empty', $key);

        echo json_encode($return);
        die();
    }

    function wishlist_load()
    {
        $wishlist_id = GeneralHelper::matrix_wishlist_get_id();

        $response_data = array(
            'notice' => '',
            'message' => '',
            'product_count' => 0,
            'wishlist_id' => $wishlist_id

        );

        if ($wishlist_id === '#') {
            $response_data['notice'] = StringHelper::get_login_message_text();

        } else {
            $products = matrix_wishlist_get_products($wishlist_id);
            $response_data['product_count'] = count($products);
        }

        do_action('matrix_wishlist_load', $wishlist_id);

        echo matrix_wishlist_content($response_data);

        exit;
    }

    function wishlist_load_count()
    {
        $return = array('status' => 0, 'count' => 0);

        $key = GeneralHelper::generate_wishlist_id_hash();

        if ($key === '#') {
            $return['notice'] = StringHelper::get_login_message_text();
        } else {
            $products = matrix_wishlist_get_products($key);


            $return['status'] = 1;
            $return['count'] = count($products);
        }

        do_action('wishlist_load_count', $key);

        echo json_encode($return);
        die();
    }


}