<?php

class wpsynced_functions
{

    /* Get synced category */
    public function get_synced_category($category_id)
    {
        GLOBAL $wpdb;
        return $wpdb->get_row("SELECT * FROM " . SYNCED_TABLE_CATEGORIES . " WHERE category_id = " . $category_id . " LIMIT 1");
    }

    /* Assign a synced category to a product category */
    public function assign_synced_category($category_id, $wp_term_id)
    {
        
        GLOBAL $wpdb;
        
        $data  = array('wp_id' => $wp_term_id);
        $where = array('category_id' => $category_id);
        return $wpdb->update(SYNCED_TABLE_CATEGORIES, $data, $where); 
        
    }
    
    /*  */
    public function synced_get_custom_terms()
    {

        $taxonomies = array('product_cat');
        
        $args = array(
            'orderby'           => 'name',
            'order'             => 'ASC',
            'hide_empty'        => false,
            'exclude'           => array(),
            'exclude_tree'      => array(),
            'include'           => array(),
            'number'            => '',
            'fields'            => 'all',
            'slug'              => '',
            'parent'            => '',
            'hierarchical'      => true,
            'child_of'          => 0,
            'childless'         => false,
            'get'               => '',
            'name__like'        => '',
            'description__like' => '',
            'pad_counts'        => false,
            'offset'            => '',
            'search'            => '',
            'cache_domain'      => 'core'
        );

        return get_terms($taxonomies, $args);

    }

}
