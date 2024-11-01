<?php

class wpsynced_posts
{

    var $deleted_posts;

    
    /* Unpublish a post */
    public function synced_unpublish_post($offer_id)
    {
        GLOBAL $wpdb;
        
        $offer = $wpdb->get_row("SELECT wp_id FROM " . SYNCED_TABLE_OFFERS . " WHERE offer_id = " . $offer_id . " LIMIT 1", ARRAY_A);
        if (!is_null($offer))
        {
            wp_delete_post( $offer['wp_id'] );
            $wpdb->query("UPDATE " . SYNCED_TABLE_OFFERS . " SET wp_id = 0, wp_post_name = '' WHERE offer_id = " . $offer_id);            
        }
    }    
    
    
    /* Delete a post */
    public function synced_delete_post($posts)
    {
        
        $this->deleted_posts = 0;
        
        foreach($posts as $post)
        {
            wp_delete_post( $post['post_id'] );
            $this->deleted_posts++;
        }
        
        echo '<br>Deleted posts ' . $this->deleted_posts; 
    }
    
    private function synced_has_offers($content, $offers)
    {
        if (strpos($content, '{for_each_offer_start}'))
        {
            preg_match("#{for_each_offer_start}(.+){for_each_offer_end}#s",$content, $subcontent);            
            $subcontent = (isset($subcontent[1])) ? $subcontent[1] : '';
            
            $final = '';
            foreach ($offers as $offer)
            {
                $append = str_replace('{offer_title}', $offer['offer_title'], $subcontent);
                $final .= $append;
            }

            return preg_replace("#{for_each_offer_start}.+{for_each_offer_end}#s", $final, $content); //exit;                      
        }
        
        return $content;
    }
    
    
    private function synced_create_category($synced_category_id)
    {
        GLOBAL $wpdb;
        
        $category = $wpdb->get_row('SELECT category_name FROM ' . SYNCED_TABLE_CATEGORIES . ' WHERE category_id = ' . $synced_category_id . ' LIMIT 1', ARRAY_A);
        $category_name = (!is_null($category)) ? $category['category_name'] : '';
        
        if ($category_name <> '')
        {
            $parts = explode(' - ', $category_name);
            return wp_create_category( $parts[0], 0 );            
        }
        
        return false;
    }
      
    
    /* Build Post  - Even if is a merchant or an offer page */
    function synced_build_post($post_type, $reference_id)
    {
        GLOBAL $wpdb;
        
        
        switch ($post_type)
        {
            case "offers":
                
                $slug = "offer-" . $reference_id;
                echo '<br>Build Post ' . $reference_id;
               
                /* Get the offer */
                $row = $wpdb->get_row('SELECT a.*, b.* FROM ' . SYNCED_TABLE_MERCHANTS . ' a INNER JOIN ' . SYNCED_TABLE_OFFERS . ' b ON a.merchant_id = b.merchant_id WHERE b.offer_id = '.$reference_id.' LIMIT 1', ARRAY_A);
                
                /* Get the offer category */
                $category = $wpdb->get_row("SELECT * FROM " . SYNCED_TABLE_CATEGORIES . " WHERE category_id = " . $row['category_id'] . " LIMIT 1");
                
                /* Create the post array */
                $my_post = array(
                'post_content'  => $row['offer_description'], // full content of the post
                'post_title'    => $row['offer_title'], // The title of your post.
                'post_status'   => 'publish',
                'post_type'     => SYNCED_POST_TYPE,
                'post_excerpt'  => $row['offer_description'],
                );

                /* Check if there is already a post created for this offer */
                if ($row['wp_id'] > 0)
                {
                    $my_post['ID'] = $row['wp_id'];
                    $post_id = $my_post['ID'] ;
                    wp_update_post($my_post);
                }
                else
                {
                    $post_id = wp_insert_post( $my_post );

                    /* Check if post was added */
                    if ($post_id > 0)
                    {
                        $data = array('wp_id' => $post_id, 'wp_post_name' => $slug);                
                        $wpdb->update( SYNCED_TABLE_OFFERS, $data, array( 'offer_id' => $reference_id ) );
                    }                    
                } 
                
                /* Get the Offer Image */
                $image_url  = ($row['offer_large_img_url']) ? $row['offer_large_img_url'] : $row['merchant_logo'];
                $image_data = getimagesize($image_url);
                
                switch ($image_data[2])
                {
                    case IMAGETYPE_GIF:
                        $ext = 'gif';
                    break;

                    case IMAGETYPE_JPEG:
                        $ext = 'jpeg';
                    break;

                    case IMAGETYPE_PNG:
                        $ext = 'png';
                    break;

                    default:
                        $ext = 'na';
                    break;
                }                
                
                /* Add the attachement and associate the image to the current post */
                if ($ext !== 'na')
                {
                    $uploaddir  = wp_upload_dir();
                    $uploadfile = $uploaddir['path'] . '/' . $slug . '.' . $ext;
                    $contents   = file_get_contents($image_url);
                    $savefile   = fopen($uploadfile, 'w');
                    fwrite($savefile, $contents);
                    fclose($savefile); 


                    // The ID of the post this attachment is for.
                    $parent_post_id = $post_id;

                    // Check the type of file. We'll use this as the 'post_mime_type'.
                    $filetype = wp_check_filetype( basename( $uploadfile ), null );



                    // Prepare an array of post data for the attachment.
                    $attachment = array(
                            'guid'           => $uploadfile, 
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $uploadfile ) ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                    );

                    // Insert the attachment.
                    $attach_id = get_post_meta($parent_post_id, '_thumbnail_id', TRUE);
                    if ($attach_id > 0)
                    {
                        update_attached_file( $attach_id, $uploadfile );
                    }
                    else
                    {
                        $attach_id = wp_insert_attachment( $attachment, $uploadfile, $parent_post_id );
                    }
                    
                    
                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );

                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $uploadfile );
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    set_post_thumbnail( $parent_post_id, $attach_id ); 
                    
                }

                wp_set_object_terms( (int)$post_id, 'external', 'product_type' );
                
                /* Assign the category to the current post */
                if (!is_null($category))
                {
                    wp_set_object_terms( (int)$post_id, (int)$category->wp_id, 'product_cat' );
                }
                    
                /* Add meta */
                $meta = array();
                $meta['_visibility']               = 'visible';
                $meta['_stock']                    = '';
                $meta['_downloadable']             = 'no';
                $meta['_virtual']                  = 'no';
                $meta['_backorders']               = 'no';
                $meta['_stock_status']             = 'instock';
                $meta['_product_type']             = 'external';
                $meta['_product_url']              = urldecode($row['offer_affiliate_url']);
                $meta['_sku']                      = $row['offer_id'];
                $meta['_expiry_date']              = $row['offer_end_date'];
                $meta['_sale_price_dates_from']    = strtotime($row['offer_start_date']);
                $meta['_sale_price_dates_to']      = strtotime($row['offer_end_date']);                
                
                if ($row['offer_price'] > 0)
                {
                    $meta['_regular_price']            = ($row['offer_discount'] > 0) ? round($row['offer_price'] * 100 / $row['offer_discount'],2) : $row['offer_price'];
                    $meta['_price']                    = $row['offer_price'];
                    $meta['_sale_price']               = $row['offer_price'];
                }    
                
                $meta['_synced_is_product']        = true;
                $meta['_synced_offer_id']          = $row['offer_id'];
                $meta['_synced_merchant_id']       = $row['merchant_id'];                
                $meta['_synced_product']           = json_encode($row); // This stores all info about the product in 1 array.

                
                foreach($meta as $meta_key => $meta_value)
                {
                    update_post_meta( $post_id, $meta_key, $meta_value );
                }
                
                break;
        }
        
    }

}
