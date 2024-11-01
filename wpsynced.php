<?php

/*
Plugin Name: Synced Daily Deals
Plugin URI:  https://synced.io
Description: Automatize daily deals from synced.io
Version:     1.1
Author:      Catalin Ionescu
Author URI:  https://synced.io
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.8
Tested up to: 4.4-alpha
*/


if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}


GLOBAL $wpdb;

define('SYNCED_POST_TYPE', 'product');
define('SYNCED_TAXONOMY', 'product_cat');
define('SYNCED_DIR', plugin_dir_path(__FILE__));
define('SYNCED_URL', plugin_dir_url(__FILE__));
define('SYNCED_TABLE_SETTINGS', $wpdb->prefix . "synced_settings");
define('SYNCED_TABLE_MERCHANTS', $wpdb->prefix . "synced_merchants");
define('SYNCED_TABLE_CATEGORIES', $wpdb->prefix . "synced_categories");
define('SYNCED_TABLE_OFFERS', $wpdb->prefix . "synced_offers");
define('SYNCED_TABLE_PROGRAMS', $wpdb->prefix . "synced_programs");
define('SYNCED_TABLE_COUNTRIES', $wpdb->prefix . "synced_countries");
define('SYNCED_OPTION_ADMIN_NOTICE', '_synced_admin_notice');

register_activation_hook( __FILE__, 'synced_activation' );
register_deactivation_hook( __FILE__, 'synced_deactivation' );


function synced_loader()
{
    
    if (is_admin())
    {
        require_once SYNCED_DIR . 'controllers/admin/main.php';
        $wpsynced_main_admin = new wpsynced_main_admin();
        $wpsynced_main_admin->synced_index();
        unset($wpsynced_main_admin);        
    }
    else
    {
        require_once SYNCED_DIR . 'controllers/front/main.php';
        $wpsynced_main_front = new wpsynced_main_front();
        $wpsynced_main_front->synced_index();
        unset($wpsynced_main_front);
    }
    
}

synced_loader();




function synced_activation()
{
   require_once SYNCED_DIR . 'controllers/activate.php';    
   $wpsynced_activate = new wpsynced_activate();
   $wpsynced_activate->synced_create_options();  
   $wpsynced_activate->synced_create_tables();
   unset($wpsynced_activate);
   
    
}


function synced_deactivation() 
{  
    require_once SYNCED_DIR . 'controllers/deactivate.php';
    $wpsynced_deactivate = new wpsynced_deactivate();
    $wpsynced_deactivate->synced_deactivate();
    unset($wpsynced_deactivate);
}