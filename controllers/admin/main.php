<?php

class wpsynced_main_admin
{

    function synced_index()
    {
        /* Create products custom post */
        add_action('init', array($this, 'synced_create_custom_post_type_product'));
        
        /* Register menu and css */
        add_action('admin_menu', array($this, 'synced_add_custom_menu'));
        add_action('admin_enqueue_scripts', array($this, 'synced_register_plugin_dependecies'));

        /* Start the session to be used later */
        add_action('init', array($this, 'synced_start_session'), 1);
        add_action('wp_logout', array($this, 'synced_end_session'));
        add_action('wp_login', array($this, 'synced_end_session'));
    
        /* Check if woocommerce is activated */
        add_action( 'admin_notices', array($this,'synced_admin_notice') );                
    }

    
    /* Check if woocommerce is installed */
    function synced_check_woocommerce()
    {
        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
        {

		echo '<div class="update-nag" style="border-color: red;">' . __( 'The <strong>Synced.io</strong> plugin requires that the <strong>WooCommerce</strong> (v2.1+) plugin be installed and activated.');
		echo ' <a href="http://wordpress.org/plugins/woocommerce/">';
		echo __( 'Download the WooCommerce Plugin' );
		echo '</a></div>';
            
        }        
    }
        
    /* Add custom menu function */
    function synced_add_custom_menu()
    {
        add_menu_page('Synced', 'Synced', 'manage_options', 'wp-synced-menu', array($this, 'wp_synced_menu'), SYNCED_URL . 'icons/144.png', 100);
        add_submenu_page('wp-synced-menu', 'Merchants', 'Merchants', 'manage_options', 'wp_synced_merchants', array($this, 'wp_synced_merchants_menu'));
        add_submenu_page('wp-synced-menu', 'Online Offers', 'Online Offers', 'manage_options', 'wp_synced_offers', array($this, 'wp_synced_online_offers_menu'));
        add_submenu_page('wp-synced-menu', 'Categories', 'Categories', 'manage_options', 'wp_synced_categories', array($this, 'wp_synced_categories_menu'));
    }

    function synced_admin_notice()
    {
        $this->synced_check_woocommerce();
    }

    function synced_register_plugin_dependecies($hook)
    {
        wp_register_style('synced-styles', SYNCED_URL . 'css/style.css');
        wp_enqueue_style('synced-styles');
    }
    
    /* Create product type*/
    function synced_create_custom_post_type_product()
    {

        $labels = array(
            'name'               => _x('Products', 'post type general name'),
            'singular_name'      => _x('Product', 'post type singular name'),
            'add_new'            => _x('Add New', 'book'),
            'add_new_item'       => __('Add New Product'),
            'edit_item'          => __('Edit Product'),
            'new_item'           => __('New Product'),
            'all_items'          => __('All Products'),
            'view_item'          => __('View Product'),
            'search_items'       => __('Search Products'),
            'not_found'          => __('No products found'),
            'not_found_in_trash' => __('No products found in the Trash'),
            'parent_item_colon'  => '',
            'menu_name'          => 'Products'
        );
        
        $args   = array(
            'labels'        => $labels,
            'description'   => 'Holds our products and product specific data',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'has_archive'   => true,
        );
        
        register_post_type('product', $args);
    }

    
    function synced_create_custom_post_type_coupon()
    {

        $labels = array(
            'name'               => _x('Coupons', 'post type general name'),
            'singular_name'      => _x('Coupon', 'post type singular name'),
            'add_new'            => _x('Add New', 'book'),
            'add_new_item'       => __('Add New Coupon'),
            'edit_item'          => __('Edit Coupon'),
            'new_item'           => __('New Coupon'),
            'all_items'          => __('All Coupons'),
            'view_item'          => __('View Coupons'),
            'search_items'       => __('Search Coupons'),
            'not_found'          => __('No coupons found'),
            'not_found_in_trash' => __('No coupons found in the Trash'),
            'parent_item_colon'  => '',
            'menu_name'          => 'Coupons'
        );
        
        $args   = array(
            'labels'        => $labels,
            'description'   => 'Holds our coupons and coupons specific data',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'has_archive'   => true,
        );
        
        register_post_type('shop_coupon', $args);
    }    
    
    
    /* Manage Settings */
    function wp_synced_menu()
    {
        GLOBAL $wpdb;

        $z = new stdClass();

        $countries    = $wpdb->get_results("SELECT * FROM " . SYNCED_TABLE_COUNTRIES, ARRAY_A);
        $row          = $wpdb->get_row("SELECT * FROM " . SYNCED_TABLE_SETTINGS . " WHERE local_settings_id = 1", ARRAY_A);
        $z->settings  = $row;
        $z->countries = $countries;


        //print_r($z); exit;
        if (isset($_POST['save']))
        {
            $data = array(
                'api_wsdl'              => mysql_real_escape_string($_POST['api_wsdl']),
                'api_username'          => mysql_real_escape_string($_POST['api_username']),
                'api_password'          => mysql_real_escape_string($_POST['api_password']),
                'api_countries'         => mysql_real_escape_string($_POST['api_countries']),
                'offer_type'            => mysql_real_escape_string(stripslashes($_POST['api_offer_type'])),
                'merchant_unjoined'     => mysql_real_escape_string(stripslashes($_POST['merchant_unjoined'])),    
                'offer_unjoined'        => mysql_real_escape_string(stripslashes($_POST['offer_unjoined'])),                
            );

            $affected_rows = false;

            if ($row !== NULL)
            {
                $affected_rows = $wpdb->update(SYNCED_TABLE_SETTINGS, $data, array('local_settings_id' => 1));
            }
            else
            {
                $affected_rows = $wpdb->insert(SYNCED_TABLE_SETTINGS, $data);
            }

            $synced_msg = ($affected_rows !== FALSE) ? 'The settings was updated succesfully !' : 'There was an error when trying to update the settings !';
            echo '<div class="updated"><p>' . $synced_msg . '</p></div>';

            $z->settings = $data;
        }


        require_once SYNCED_DIR . 'views/admin/wp_synced_menu.php';
    }

    /* Manage Merchants */

    function wp_synced_merchants_menu()
    {
        GLOBAL $wpdb;
        $z = new stdClass();
        
        
        $where = '1 = 1';
        
        if (isset($_POST['import_merchants']))
        {
            require_once SYNCED_DIR . 'controllers/crons/crons.php';
            $wpsynced_crons = new wpsynced_crons();
            $wpsynced_crons->synced_get_categories();
            $wpsynced_crons->synced_get_offers();            
            $wpsynced_crons->synced_get_merchants();
            unset($wpsynced_crons);
        }


        /* Search Filter */
        if (isset($_POST['action_search']))
        {
            $_SESSION['synced_search_merchants'] = (isset($_POST['input_search'])) ? $_POST['input_search'] : '';
        }

        $z->input_search = (isset($_SESSION['synced_search_merchants']) && $_SESSION['synced_search_merchants'] <> '')  ? $_SESSION['synced_search_merchants'] : '';
        $where .= ($z->input_search <> '') ? " AND a.merchant_name LIKE '%" . $z->input_search . "%'" : '';
        
        
        /* Get Total Records & Paginate */
        $total = $wpdb->get_results("SELECT a.merchant_id FROM " . SYNCED_TABLE_MERCHANTS . " a INNER JOIN " . SYNCED_TABLE_OFFERS . " b ON a.merchant_id = b.merchant_id INNER JOIN " . SYNCED_TABLE_CATEGORIES . " c ON b.category_id = c.category_id WHERE " . $where . " GROUP BY a.merchant_id", ARRAY_A);


        $current_page = (isset($_GET['pag']) && $_GET['pag'] > 1) ? $_GET['pag'] : 1;
        $num_pages    = (count($total) % 20 > 1) ? count($total) / 20 + 1 : count($total);
        $from_row     = ($current_page - 1) * 20;

        $z->args = array(
            'base'               => menu_page_url('wp_synced_merchants', 0) . '%_%',
            'format'             => '&pag=%#%',
            'total'              => $num_pages,
            'current'            => $current_page,
            'show_all'           => True,
            'end_size'           => 1,
            'mid_size'           => 2,
            'prev_next'          => True,
            'prev_text'          => __('« Previous'),
            'next_text'          => __('Next »'),
            'type'               => 'plain',
            'add_args'           => False,
            'add_fragment'       => '',
            'before_page_number' => '',
            'after_page_number'  => '');


        /* Get the data */
        $rows = $wpdb->get_results("SELECT a.*, c.category_name, COUNT(b.offer_id) AS count_offers FROM " . SYNCED_TABLE_MERCHANTS . " a INNER JOIN " . SYNCED_TABLE_OFFERS . " b ON a.merchant_id = b.merchant_id INNER JOIN " . SYNCED_TABLE_CATEGORIES . " c ON b.category_id = c.category_id WHERE " . $where . " GROUP BY a.merchant_id LIMIT " . $from_row . ",20", ARRAY_A);
        $z->data  = $rows;
        $z->total = count($total);

        require_once SYNCED_DIR . 'views/admin/wp_synced_merchants_menu.php';
    }

    /* Manage Categories */

    function wp_synced_categories_menu()
    {
        GLOBAL $wpdb;
        $z = new stdClass();

        if (isset($_GET['edit']) && isset($_POST['assign']))
        {
            $category_id = $_GET['category_id'];
            $wp_term_id  = $_POST['wp_term_id'];

            require_once SYNCED_DIR . 'models/functions.php';
            $wpsynced_functions = new wpsynced_functions();

            $wpsynced_functions->assign_synced_category($category_id, $wp_term_id);

            $z->data  = $wpsynced_functions->get_synced_category($category_id);
            $z->terms = $wpsynced_functions->synced_get_custom_terms();

            unset($wpsynced_functions);

            echo '<div class="updated"><p>The category was assigned !</p></div>';            
            
            require_once SYNCED_DIR . 'views/admin/wp_synced_categories_edit.php';
            return;
        }

        if (isset($_GET['edit']) && !isset($_POST['assign']))
        {
            $category_id = $_GET['category_id'];

            require_once SYNCED_DIR . 'models/functions.php';
            $wpsynced_functions = new wpsynced_functions();

            $z->data  = $wpsynced_functions->get_synced_category($category_id);
            $z->terms = $wpsynced_functions->synced_get_custom_terms();

            unset($wpsynced_functions);

            require_once SYNCED_DIR . 'views/admin/wp_synced_categories_edit.php';
            return;
        }


        $rows = $wpdb->get_results("SELECT a.*, b.name AS 'term_name' FROM " . SYNCED_TABLE_CATEGORIES . " a LEFT JOIN wp_terms b ON a.wp_id = b.term_id  WHERE a.parent_id = 0 ORDER BY a.category_name", ARRAY_A);
        foreach ($rows as $key => $row)
        {
            $subcategories               = $wpdb->get_results("SELECT a.*, b.name AS 'term_name' FROM " . SYNCED_TABLE_CATEGORIES . " a LEFT JOIN wp_terms b ON a.wp_id = b.term_id WHERE a.parent_id = " . $row['category_id'] . " ORDER BY a.category_name", ARRAY_A);
            $rows[$key]['subcategories'] = $subcategories;
        }

        $z->data = $rows;
        //print_r($z->data);exit;

        require_once SYNCED_DIR . 'views/admin/wp_synced_categories_menu.php';
    }

    /* Manage Offers */

    function wp_synced_online_offers_menu()
    {
        GLOBAL $wpdb;
        $z = new stdClass();
        $where = " 1 = 1 ";
        
        /* Filter by search keyword */
        $z->input_search['merchant']   = ''; 
        $z->input_search['published']  = 'all'; 
        $z->input_search['expired']    = 'all';         
        $z->input_search['category']   = ''; 
        
        if (isset($_POST['action_search']))
        {
            $_SESSION['synced_search_merchants'] = (isset($_POST['input_search_merchant'])) ? $_POST['input_search_merchant'] : '';
            $_SESSION['synced_search_published'] = (isset($_POST['input_search_published'])) ? $_POST['input_search_published'] : 'all';            
        }

        $z->input_search['merchant'] = (isset($_SESSION['synced_search_merchants']) && $_SESSION['synced_search_merchants'] <> '')  ? $_SESSION['synced_search_merchants'] : '';
        $where .= ($z->input_search['merchant'] <> '') ? " AND b.merchant_name LIKE '%" . $z->input_search['merchant'] . "%'" : '';
        
        $z->input_search['published'] = (isset($_SESSION['synced_search_published']) && $_SESSION['synced_search_published'] <> 'all') ? $_SESSION['synced_search_published']:'all';
        switch ($z->input_search['published'])
        {
            case "published":
                $where .= " AND a.wp_id > 0 "; 
                break;
            case "not_published":
                $where .= " AND a.wp_id = 0 "; 
                break;            
        }

        
        
        if (isset($_POST['import_offers']))
        {
            require_once SYNCED_DIR . 'controllers/crons/crons.php';
            $wpsynced_crons = new wpsynced_crons();
            $wpsynced_crons->synced_get_categories();
            $wpsynced_crons->synced_get_offers();
            $wpsynced_crons->synced_get_merchants();
            unset($wpsynced_crons);
        }

        if (isset($_POST['build_posts']))
        {

            if (!isset($_POST['sel']))
            {
                echo '<div class="updated"><p>You must select at least one offer !</p></div>';
            }
            else
            {
                require_once SYNCED_DIR . 'models/posts.php';
                $wpsynced_posts = new wpsynced_posts();

                foreach ($_POST['sel'] as $reference_id)
                {
                    $wpsynced_posts->synced_build_post('offers', $reference_id);
                }
                
                unset($wpsynced_posts);
            }
        }

        
        if (isset($_POST['delete_posts']))
        {

            if (!isset($_POST['sel']))
            {
                echo '<div class="updated"><p>You must select at least one offer !</p></div>';
            }
            else
            {
                require_once SYNCED_DIR . 'models/posts.php';
                $wpsynced_posts = new wpsynced_posts();

                foreach ($_POST['sel'] as $reference_id)
                {
                    $wpsynced_posts->synced_unpublish_post($reference_id);
                }
                
                unset($wpsynced_posts);
            }
        }        
        
        $total = $wpdb->get_results("SELECT a.offer_id FROM " . SYNCED_TABLE_OFFERS . " a INNER JOIN " . SYNCED_TABLE_MERCHANTS . " b ON a.merchant_id = b.`merchant_id` INNER JOIN " . SYNCED_TABLE_CATEGORIES . " c ON a.category_id = c.category_id WHERE " . $where . " GROUP BY a.offer_id ", ARRAY_A);


        $current_page = (isset($_GET['pag']) && $_GET['pag'] > 1) ? $_GET['pag'] : 1;
        $num_pages    = (count($total) % 20 > 1) ? count($total) / 20 + 1 : count($total) / 20;
        $from_row     = ($current_page - 1) * 20;

        $z->args = array(
            'base'               => menu_page_url('wp_synced_offers', 0) . '%_%',
            'format'             => '&pag=%#%',
            'total'              => $num_pages,
            'current'            => $current_page,
            'show_all'           => True,
            'end_size'           => 1,
            'mid_size'           => 2,
            'prev_next'          => True,
            'prev_text'          => __('« Previous'),
            'next_text'          => __('Next »'),
            'type'               => 'plain',
            'add_args'           => False,
            'add_fragment'       => '',
            'before_page_number' => '',
            'after_page_number'  => '');

        $rows     = $wpdb->get_results("SELECT a.*, b.merchant_name, c.category_name FROM " . SYNCED_TABLE_OFFERS . " a INNER JOIN " . SYNCED_TABLE_MERCHANTS . " b ON a.merchant_id = b.`merchant_id` INNER JOIN " . SYNCED_TABLE_CATEGORIES . " c ON a.category_id = c.category_id WHERE " . $where . " GROUP BY a.offer_id LIMIT " . $from_row . ",20", ARRAY_A);
        $z->data  = $rows;
        $z->total = count($total);
        //print_r($z->data);exit;

        require_once SYNCED_DIR . 'views/admin/wp_synced_online_offers_menu.php';
    }

    function synced_start_session()
    {
        if (!session_id())
        {
            session_start();
        }
    }

    function synced_end_session()
    {
        session_destroy();
    }

}
