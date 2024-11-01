<?php

class wpsynced_main_front
{
    var $product_id;

    function synced_index()
    {
        add_filter('woocommerce_product_add_to_cart_text', array($this, 'wpsynced_b'));
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'wpsynced_b'));
        add_filter('woocommerce_product_class', array($this, 'wpsynced_class'), 'wpsynced_class', 10,  4);
        add_action('plugins_loaded', 'wpsynced_override_woocommerce');
    }

    function wpsynced_b()
    {

        return __('Buy', 'woocommerce');
    }

    function wpsynced_class($classname, $product_type,  $post_type,  $product_id )
    {
        return 'wpsynced_product_external_class';
    }


    

}


function wpsynced_override_woocommerce()
{    
    if ( class_exists( 'WC_Product_External' ))
    {
        class wpsynced_product_external_class extends WC_Product_External
        {

            public function get_product_url()
            {
                $product_url = get_post_meta ( $this->id, '_product_url', true);
                return $product_url;
            }

        }
    }
}