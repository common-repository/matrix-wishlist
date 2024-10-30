<?php

namespace MatrixAddons\MatrixWishlist\Admin\Settings;

class Wishlist extends \WC_Settings_Page
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = 'wishlist';
        $this->label = __('Wishlist', 'matrix-wishlist');

        parent::__construct();

        add_action('woocommerce_admin_field_wc_product_dropdown_categories', array($this, 'category'));
    }

    protected function get_own_sections()
    {
        return array(
            '' => __('General & Button', 'matrix-wishlist'),
            'wishlist_popup' => __('Wishlist Popup', 'matrix-wishlist'),
        );
    }

    public function category($value)
    {
        $id = sanitize_text_field($value['id']);
        ?>
        <tr>
            <th scope="row"><?php echo esc_htmL($value['title']); ?></th>
            <td>
                <?php
                $selected_cats = get_option($id);

                if (empty($selected_cats)) {
                    $selected_cats = array(0);
                }

                wc_product_dropdown_categories(
                    array(
                        'name' => $id,
                        'hide_empty' => 0,
                        'value_field' => 'id',
                        'multiple' => true,
                        'show_option_all' => esc_html__('All categories', 'matrix-wishlist'),
                        'show_option_none' => '',
                        'selected' => implode(',', $selected_cats)
                    ));
                ?>
                <div class="description"><?php echo esc_htmL($value['desc']); ?></div>
            </td>
        </tr>
        <?php
    }

    protected function get_settings_for_default_section()
    {

        $settings =
            array(

                array(
                    'title' => __('General Options', 'matrix-wishlist'),
                    'type' => 'title',
                    'id' => 'matrix_wishlist_general_options',
                ),

                array(
                    'title' => __('Disable for non login user', 'matrix-wishlist'),
                    'desc' => __('Disable for non login user', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_disable_for_non_login',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'desc_tip' => __('You can enable/disable wishlist for non login user', 'matrix-wishlist'),
                ),
                array(
                    'title' => __('Auto removed', 'matrix-wishlist'),
                    'desc' => __('Auto removed', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_auto_remove_after_cart',
                    'default' => 'no',
                    'type' => 'checkbox',
                    'desc_tip' => __('Auto remove product from the wishlist after adding to the cart', 'matrix-wishlist'),
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'matrix_wishlist_general_options',
                ),

                array(
                    'title' => __('Add to Wishlist Button', 'matrix-wishlist'),
                    'type' => 'title',
                    'id' => 'matrix_wishlist_add_to_wishlist_options',
                ),

                array(
                    'title' => __('Button Type', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_type',
                    'default' => 'button',
                    'type' => 'select',
                    'desc_tip' => __('Type of wishlist add to wishlist button', 'matrix-wishlist'),
                    'options' => array(
                        'button' => __('Button', 'matrix-wishlist'),
                        'link' => __('Link', 'matrix-wishlist'),
                    )
                ),
                array(
                    'title' => __('Button Action', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_action',
                    'default' => 'list',
                    'type' => 'select',
                    'desc_tip' => __('Action triggered by clicking on the wishlist button', 'matrix-wishlist'),
                    'options' => array(
                        'message' => __('Show Message', 'matrix-wishlist'),
                        'list' => __('Show Product List', 'matrix-wishlist'),
                    )
                ),
                array(
                    'title' => __('Action - After', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_action_after',
                    'default' => 'popup',
                    'type' => 'select',
                    'desc_tip' => __('Action triggered by clicking on the wishlist button after adding an item to the wishlist', 'matrix-wishlist'),
                    'options' => array(
                        'popup' => __('Open Wishlist Popup', 'matrix-wishlist'),
                        'page' => __('Open Wishlist Page', 'matrix-wishlist'),
                    )
                ),
                array(
                    'title' => __('Button Class', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_class',
                    'default' => '',
                    'type' => 'text',
                ),
                array(
                    'title' => __('Button Position - Archive Page', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_archive_page_position',
                    'default' => 'below_add_to_cart',
                    'type' => 'select',
                    'desc_tip' => __('Add to wishlist position on WooCommerce Archive page', 'matrix-wishlist'),
                    'options' => array(
                        'below_title' => esc_html__('Below title', 'matrix-wishlist'),
                        'below_rating' => esc_html__('Below rating', 'matrix-wishlist'),
                        'below_price' => esc_html__('Below price', 'matrix-wishlist'),
                        'above_add_to_cart' => esc_html__('Above add to cart button', 'matrix-wishlist'),
                        'below_add_to_cart' => esc_html__('Below add to cart button', 'matrix-wishlist'),
                        'none' => esc_html__('None', 'matrix-wishlist'),
                    )
                ),
                array(
                    'title' => __('Button Position - Single Page', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_single_page_position',
                    'default' => 'below_title',
                    'type' => 'select',
                    'desc_tip' => __('Add to wishlist position on WooCommerce Single page', 'matrix-wishlist'),
                    'options' => array(
                        'below_title' => esc_html__('Below title', 'matrix-wishlist'),
                        'below_rating' => esc_html__('Below rating', 'matrix-wishlist'),
                        'below_excerpt' => esc_html__('Below excerpt', 'matrix-wishlist'),
                        'above_add_to_cart' => esc_html__('Above add to cart button', 'matrix-wishlist'),
                        'below_add_to_cart' => esc_html__('Below add to cart button', 'matrix-wishlist'),
                        'below_meta' => esc_html__('Below meta', 'matrix-wishlist'),
                        'below_sharing' => esc_html__('Below sharing', 'matrix-wishlist'),
                        'none' => esc_html__('None', 'matrix-wishlist'),
                    )
                ),

                array(
                    'title' => __('Show only on selected category', 'matrix-wishlist'),
                    'desc' => __('Add to wishlist button on WooCommerce selected categories', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_show_only_on_categories',
                    'default' => '',
                    'type' => 'wc_product_dropdown_categories',
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'matrix_wishlist_add_to_wishlist_options',
                ),
            );

        return apply_filters('matrix_wishlist_general_settings', $settings);
    }

    protected function get_settings_for_wishlist_popup_section()
    {
        $settings =
            array(

                array(
                    'id' => 'matrix_wishlist_popup_options',
                    'title' => __('Wishlist Popup Options', 'matrix-wishlist'),
                    'type' => 'title',
                ),
                array(
                    'title' => __('Message Background Color', 'matrix-wishlist'),
                    /* translators: %s: default color */
                    'desc' => sprintf(__('Message Background color. Default %s.', 'matrix-wishlist'), '<code>#5fbd74</code>'),
                    'id' => 'matrix_wishlist_message_background_color',
                    'type' => 'color',
                    'css' => 'width:6em;',
                    'default' => '#5fbd74',
                    'autoload' => false,
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Header Background Color', 'matrix-wishlist'),
                    'desc' => __('Header Background Color', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_popup_header_background_color',
                    'type' => 'color',
                    'css' => 'width:6em;',
                    'default' => '#282727',
                    'autoload' => false,
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Footer Background Color', 'matrix-wishlist'),
                    'desc' => __('Footer Background Color', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_popup_footer_background_color',
                    'type' => 'color',
                    'css' => 'width:6em;',
                    'default' => '#282727',
                    'autoload' => false,
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Link to individual product', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_button_link_to_individual_product',
                    'default' => 'same_tab',
                    'type' => 'select',
                    'desc_tip' => __('Add to wishlist position on WooCommerce Single page', 'matrix-wishlist'),
                    'options' => array(
                        'new_tab' => esc_html__('Yes, Open in new tab', 'matrix-wishlist'),
                        'same_tab' => esc_html__('Yes, Open in same tab', 'matrix-wishlist'),
                        'none' => esc_html__('No', 'matrix-wishlist'),
                    )
                ),
                array(
                    'title' => __('Continue shopping link', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_continue_shopping_link',
                    'desc_tip' => __('Continue shopping link', 'matrix-wishlist'),
                    'type' => 'text',
                    'default' => get_permalink(wc_get_page_id('shop'))
                ,
                ),
                array(
                    'title' => __('Wishlist Page', 'matrix-wishlist'),
                    'id' => 'matrix_wishlist_page_id',
                    'desc_tip' => __('Wishlist Page', 'matrix-wishlist'),
                    'type' => 'single_select_page',
                    'args' => array(
                        'show_option_none' => __('Select a page', 'matrix-wishlist'),
                    )
                ),

                array(
                    'type' => 'sectionend',
                    'id' => 'matrix_wishlist_popup_options',
                )
            );

        return apply_filters('matrix_wishlist_popup_settings', $settings);
    }


}