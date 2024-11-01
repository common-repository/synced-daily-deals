<?php

class wpsynced_crons
{
    var $inserted_merchants = 0;
    var $updated_merchants  = 0;

    var $inserted_categories = 0;
    var $updated_categories  = 0;
    
    var $inserted_offers     = 0;
    var $updated_offers      = 0;

    /* Import Programs */    
    function synced_get_programs()
    {
        GLOBAL $wpdb;

        $row = $wpdb->get_row( "SELECT * FROM " . SYNCED_TABLE_SETTINGS . " WHERE local_settings_id = 1", ARRAY_A);
        
        if (is_null($row))
        {
            echo '<br>There is no configuration for the API . Please configure the API first !';
            return;
        }
        
        $api_wsdl         = $row['api_wsdl'];
        $api_username     = $row['api_username'];
        $api_subscription = $row['api_password'];
        $api_country      = $row['api_countries'];


        $this->synced_mark_add(SYNCED_TABLE_PROGRAMS);

        $merchants = $wpdb->get_results("SELECT merchant_id FROM " . SYNCED_TABLE_MERCHANTS, ARRAY_A);
        foreach($merchants as $merchant)
        {
        
            try
            {
                $client    = new SoapClient($api_wsdl);
                $response  = $client->get_merchant($api_username, $api_subscription, $merchant['merchant_id'], $api_country);
            } 
            catch (Exception $e)
            {
                echo $e->getMessage();
                return;
            }        
        
            
            if (isset($response->programs->program))
            {
            
                $row = (is_array($response->programs->program)) ? $response->programs->program[0] : $response->programs->program;
                $data = array('program_name'            => $row->program_name, 
                                  'merchant_id'         => $merchant['merchant_id'],                    
                                  'program_id'          => $row->program_id,
                                  'program_category_id' => $row->program_category_id,
                                  'marked'              => 0);


                $found = $wpdb->get_row( "SELECT local_program_id FROM " . SYNCED_TABLE_PROGRAMS . " WHERE program_id = " . $row->program_id . " LIMIT 1", ARRAY_A);  
                if (is_null($found))
                {            
                    $wpdb->insert(SYNCED_TABLE_PROGRAMS, $data);        
                    $this->inserted_programs++;
                }
                else
                {
                    $wpdb->update(SYNCED_TABLE_PROGRAMS, $data, array('program_id' => $row->program_id));        
                    $this->updated_programs++;                
                }
            
            }
        }            
               
        $this->synced_mark_del(SYNCED_TABLE_PROGRAMS);
    }      
    
    /* Import Categories */    
    function synced_get_categories()
    {
        GLOBAL $wpdb;

        echo '<br>Categories - Trying to acces the API ...';
        $row = $wpdb->get_row( "SELECT * FROM " . SYNCED_TABLE_SETTINGS . " WHERE local_settings_id = 1", ARRAY_A);
        
        if (is_null($row))
        {
            echo '<br>There is no configuration for the API . Please configure the API first !';
            return;
        }
        
        $api_wsdl         = $row['api_wsdl'];
        $api_username     = $row['api_username'];
        $api_subscription = $row['api_password'];

        try
        {
            $client    = new SoapClient($api_wsdl);
            $response  = $client->get_categories($api_username, $api_subscription);
        } 
        catch (Exception $e)
        {
            echo $e->getMessage();
            return;
        }

        echo '<br>Connection succed';
        
        $this->synced_mark_add(SYNCED_TABLE_CATEGORIES);
        
        foreach ($response->category as $row)
        {
            $data = array('category_name'        => $row->category_name, 
                          'category_id'          => $row->category_id,
                          'parent_id'            => $row->parent_id,
                          'marked'               => 0);
            
                     
            $found = $wpdb->get_row( "SELECT local_category_id FROM " . SYNCED_TABLE_CATEGORIES . " WHERE category_id = " . $row->category_id . " LIMIT 1", ARRAY_A);  
            if (is_null($found))
            {            
                $wpdb->insert(SYNCED_TABLE_CATEGORIES, $data);        
                $this->inserted_categories++;
            }
            else
            {
                $wpdb->update(SYNCED_TABLE_CATEGORIES, $data, array('category_id' => $row->category_id));        
                $this->updated_categories++;                
            }
             
        }
        
        $removed = $this->synced_mark_del(SYNCED_TABLE_CATEGORIES);

        echo '<br>New categories : ' . $this->inserted_categories . ' | Updated categories : ' . $this->updated_categories . ' | Removed categories : ' . $removed;          
        
    }    
    
    /* Import Merchants */
    function synced_get_merchants()
    {
        GLOBAL $wpdb;

        echo '<br>Merchants - Trying to acces the API ...';
        $row = $wpdb->get_row( "SELECT * FROM " . SYNCED_TABLE_SETTINGS . " WHERE local_settings_id = 1", ARRAY_A);
        
        if (is_null($row))
        {
            echo '<br>There is no configuration for the API . Please configure the API first !';
            return;
        }
        
        $this->synced_mark_add(SYNCED_TABLE_MERCHANTS);
        
        $api_wsdl         = $row['api_wsdl'];
        $api_username     = $row['api_username'];
        $api_subscription = $row['api_password'];
        $api_country_id   = $row['api_countries'];        

        $offers = $wpdb->get_results("SELECT merchant_id FROM " . SYNCED_TABLE_OFFERS . " GROUP BY merchant_id", ARRAY_A);

        $client = new SoapClient($api_wsdl);        
        
        foreach ($offers as $offer)
        {
            $api_merchant_id  = $offer['merchant_id'];	

            try
            {
                $merchant  = $client->get_merchant($api_username, $api_subscription, $api_merchant_id, $api_country_id);	
            } 
            catch (Exception $e)
            {
                echo $e->getMessage();
                return;
            }

            echo '<br>Connection succed';

            if (!isset($merchant->merchant_id))
            {
                continue;
            }        

            $row = $merchant;
            
            $data = array(
            'merchant_name'          => $row->merchant_name, 
            'merchant_id'            => $row->merchant_id,
            'merchant_description'   => $row->merchant_description,
            'merchant_url'           => $row->merchant_url,
            'merchant_logo'          => $row->merchant_logo,
            'marked'                 => 0);

            $found = $wpdb->get_row( "SELECT local_merchant_id FROM " . SYNCED_TABLE_MERCHANTS . " WHERE merchant_id = " . $row->merchant_id . " LIMIT 1", ARRAY_A);  
            if (is_null($found))
            {            
                $wpdb->insert(SYNCED_TABLE_MERCHANTS, $data);        
                $this->inserted_merchants++;
            }
            else
            {
                $wpdb->update(SYNCED_TABLE_MERCHANTS, $data, array("merchant_id" => $row->merchant_id));        
                $this->updated_merchants++;                
            }

        }
        
        $removed = $this->synced_mark_del(SYNCED_TABLE_MERCHANTS);        
        
        echo '<br>New merchants : ' . $this->inserted_merchants . ' | Updated merchants : ' . $this->updated_merchants . ' | Removed merchants : ' . $removed;
        return;
    }
    
    /* Import Offers */
    function synced_get_offers()
    {
        GLOBAL $wpdb;

        echo '<br>Offers - Trying to acces the API ...';
        $row = $wpdb->get_row( "SELECT * FROM " . SYNCED_TABLE_SETTINGS . " WHERE local_settings_id = 1", ARRAY_A);
        
        if (is_null($row))
        {
            echo '<br>There is no configuration for the API . Please configure the API first !';
            return;
        }
        
        $api_wsdl          = $row['api_wsdl'];
        $api_username      = $row['api_username'];
        $api_subscription  = $row['api_password'];
        $api_country_id    = $row['api_countries']; // replace with your country id        

        $api_network_id    = 0;   // replace with your network id
        $api_merchant_id   = 0;   // replace with your country id
        $api_category_id   = 0;   // replace with your category id


        $api_start_date    = '';  // replace with start date
        $api_end_date      = '';  // replace with end date
        $api_offer_type    = ($row['offer_type'] <> '-') ? $row['offer_type'] : '';  // replace with offer type
        $api_has_postback  = 0;   // replace with has postback

        try
        {
            $client    = new SoapClient($api_wsdl);
            $offers    = $client->get_offers($api_username, $api_subscription, $api_network_id, $api_merchant_id, $api_category_id, $api_country_id, $api_start_date, $api_end_date, $api_offer_type, $api_has_postback);	
        } 
        catch (Exception $e)
        {
            echo $e->getMessage();
            return;
        }

        echo '<br>Connection succed';
        
        if (!isset($offers->offer) || count($offers->offer) == 0)
        {
            echo '<br>There are no offer in the feed !';
            return;
        }
        
        /* Mark data for deletions */
        $this->synced_mark_add(SYNCED_TABLE_OFFERS);
        
        foreach ($offers->offer as $row)
        {
            $data = array(
                        'offer_id'               => $row->offer_id, 
                        'category_id'            => $row->category_id,
                        'merchant_id'            => $row->merchant_id,
                        'program_id'             => $row->program_id,
                        'offer_start_date'       => $row->offer_start_date,
                        'offer_end_date'         => $row->offer_end_date,
                        'offer_affiliate_url'    => $row->offer_affiliate_url,
                        'offer_title'            => $row->offer_title,
                        'offer_description'      => $row->offer_description,                
                        'offer_img_url'          => (isset($row->offer_img_url))       ? $row->offer_img_url : '',
                        'offer_large_img_url'    => (isset($row->offer_large_img_url)) ? $row->offer_large_img_url : '',
                        'offer_type'             => '',
                        'offer_voucher_code'     => $row->offer_voucher_code, 

                        'offer_lat'              => $row->offer_latitude,
                        'offer_lon'              => $row->offer_longitude,                
                        'offer_price'            => $row->offer_price,
                        'offer_discount'         => $row->offer_discount,
                        'offer_currency'         => $row->offer_currency,
                        'is_holiday_offer'       => $row->is_holiday_offer, 
                        'marked'                 => 0
                );
            
            $found = $wpdb->get_row( "SELECT local_offer_id FROM " . SYNCED_TABLE_OFFERS . " WHERE offer_id = " . $row->offer_id . " LIMIT 1", ARRAY_A);  
            if (is_null($found))
            {
                $wpdb->insert(SYNCED_TABLE_OFFERS, $data);
                $this->inserted_offers++;
            }
            else
            {
                $wpdb->update(SYNCED_TABLE_OFFERS, $data, array('offer_id' => $row->offer_id)); 
                $this->updated_offers++;
            }
            
            
        }
        
        /* Delete marked data for deletions */
        $removed = $this->synced_mark_del(SYNCED_TABLE_OFFERS);        
        
        echo '<br>New offers : ' . $this->inserted_offers . ' | Updated offers : ' . $this->updated_offers . ' | Removed offers : ' . $removed;        
        
    }    

    /* Mark for deletions */
    private function synced_mark_add($table)
    {
        GLOBAL $wpdb;        
        $updated = $wpdb->update($table, array('marked' => 1), array('marked' => 0));
                
    }
    
    /* Mark for deletions */
    private function synced_mark_del($table)
    {
        GLOBAL $wpdb;   
        
        $settings = $wpdb->get_row( "SELECT * FROM " . SYNCED_TABLE_SETTINGS, ARRAY_A);
        
        if (is_null($settings))
        {
            echo '<br>There is no configuration for the API . Please configure the API first !';
            return;
        }
        
        if ($table == SYNCED_TABLE_MERCHANTS && $settings['merchant_unjoined'] == "delete")
        {
            require_once SYNCED_DIR . 'models/posts.php';        
            $marked  = $wpdb->get_results("SELECT a.post_id FROM wp_postmeta a INNER JOIN " . $table . " b ON a.meta_value = b.merchant_id AND a.meta_key = '_synced_merchant_id' WHERE b.marked = 1", ARRAY_A);
            
            $wpsynced_posts = new wpsynced_posts();
            $wpsynced_posts->synced_delete_post($marked);
            unset($wpsynced_posts);            
            
        }

        
        if ($table == SYNCED_TABLE_OFFERS && $settings['offer_unjoined'] == "delete")
        {
            require_once SYNCED_DIR . 'models/posts.php';        
            $marked  = $wpdb->get_results("SELECT a.post_id FROM wp_postmeta a INNER JOIN " . $table . " b ON a.meta_value = b.offer_id AND a.meta_key = '_synced_offer_id' WHERE b.marked = 1", ARRAY_A);
            
            $wpsynced_posts = new wpsynced_posts();
            $wpsynced_posts->synced_delete_post($marked);
            unset($wpsynced_posts);            
            
        }        
        
        $deleted = $wpdb->delete($table, array('marked' => 1));
        
        return $deleted;
    }
    
}

