<?php

class wpsynced_activate
{
    
    function synced_create_options()
    {
        add_option(SYNCED_OPTION_ADMIN_NOTICE);        
    }
    
    function synced_create_tables()
    {
        GLOBAL $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();
        
        /* Create the table for settings */
        $sql1 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_SETTINGS . " (
        local_settings_id int(11) NOT NULL AUTO_INCREMENT,
        api_wsdl varchar(255) NOT NULL,
        api_username varchar(255) DEFAULT '' NOT NULL,
        api_password varchar(255) DEFAULT '',
        api_schedule varchar(255) DEFAULT '',
        api_countries varchar(255) DEFAULT '',
        format_merchant_url varchar(255) DEFAULT '',
        format_offer_url varchar(255) DEFAULT '', 
        format_category_url varchar(255) DEFAULT '',
        template_merchant_page text,
        template_offer_page text,
        offer_type varchar(20) DEFAULT '',
        merchant_unjoined enum('nothing','delete') NOT NULL DEFAULT 'nothing', 
        offer_unjoined enum('nothing','delete') NOT NULL DEFAULT 'nothing',         
        last_update datetime,
        UNIQUE KEY local_settings_id (local_settings_id)
      ) $charset_collate;";

        dbDelta($sql1);


        /* Create the table for merchants */
        $sql2 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_MERCHANTS . " (
        local_merchant_id int(11) NOT NULL AUTO_INCREMENT,
        merchant_id int(11) NOT NULL,
        merchant_name varchar(255) DEFAULT '' NOT NULL,
        merchant_description text NOT NULL,
        merchant_url varchar(255) DEFAULT '',
        merchant_logo varchar(255) DEFAULT '', 
        wp_id int(11) NOT NULL,
        wp_post_name varchar(255) DEFAULT '',      
        marked int(11) NOT NULL,
        UNIQUE KEY local_merchant_id (local_merchant_id),
        UNIQUE KEY merchant_id (merchant_id)        
      ) $charset_collate;";

        dbDelta($sql2);


        /* Create the table for categories */
        $sql3 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_CATEGORIES . " (
        local_category_id int(11) NOT NULL AUTO_INCREMENT,
        category_id int(11) NOT NULL,
        parent_id int(11) NOT NULL,
        category_name varchar(255) DEFAULT '' NOT NULL,
        wp_id int(11) NOT NULL,
        wp_post_name varchar(255) DEFAULT '',      
        marked int(11) NOT NULL,
        UNIQUE KEY local_category_id (local_category_id),
        UNIQUE KEY category_id (category_id)        
      ) $charset_collate;";

        dbDelta($sql3);
        
        /* Create a table for online offers */
        $sql4 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_OFFERS . " (
         local_offer_id bigint(20) NOT NULL AUTO_INCREMENT,            
         offer_id bigint(20) NOT NULL,
         category_id bigint(20) NOT NULL,
         merchant_id bigint(20) NOT NULL,
         program_id varchar(255) CHARACTER SET utf8 NOT NULL,
         offer_start_date datetime NOT NULL,
         offer_end_date datetime NOT NULL,
         offer_affiliate_url text CHARACTER SET utf8 NOT NULL,
         offer_title text CHARACTER SET utf8 NOT NULL,
         offer_description text CHARACTER SET utf8 NOT NULL,
         offer_img_url text CHARACTER SET utf8 NOT NULL,
         offer_large_img_url text CHARACTER SET utf8 NOT NULL,
         offer_type varchar(100) CHARACTER SET utf8 NOT NULL,
         offer_voucher_code varchar(255) CHARACTER SET utf8 NOT NULL,
         offer_lat float(10,6) NOT NULL,
         offer_lon float(10,6) NOT NULL,
         offer_price decimal(10,2) NOT NULL DEFAULT '0.00',
         offer_discount decimal(4,2) NOT NULL DEFAULT '0.00',
         offer_currency char(3) COLLATE utf8_bin NOT NULL,
         is_holiday_offer enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
         wp_id int(11) NOT NULL,
         wp_post_name varchar(255) DEFAULT '',          
         marked smallint(6) NOT NULL,
         PRIMARY KEY (local_offer_id),
         UNIQUE KEY offer_id (offer_id),
         KEY offer_end_date (offer_end_date))";

        dbDelta($sql4);        

        
        /* Create the table for categories */
        $sql5 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_PROGRAMS . " (
        local_program_id int(11) NOT NULL AUTO_INCREMENT,
        merchant_id int(11) NOT NULL,        
        program_id int(11) NOT NULL,
        program_name varchar(255) DEFAULT '' NOT NULL,
        program_category_id int(11) NOT NULL,
        marked int(11) NOT NULL,
        PRIMARY KEY local_program_id (local_program_id),
        UNIQUE KEY program_id (program_id)        
      ) $charset_collate;";

        dbDelta($sql5); 
        
        
        $sql6_1 = "DROP TABLE IF EXISTS " . SYNCED_TABLE_COUNTRIES;
        
        dbDelta($sql6_1); 
        
        $sql6_2 = "CREATE TABLE IF NOT EXISTS " . SYNCED_TABLE_COUNTRIES . " (
        `country_id` int(20) NOT NULL AUTO_INCREMENT,
        `country_name` varchar(255) CHARACTER SET utf8 NOT NULL,
        `prefix` varchar(5) NOT NULL,
        `currency_id` int(11) NOT NULL,
        PRIMARY KEY (`country_id`))";
        
        dbDelta($sql6_2); 
        
        /* Insert the default data into countries table */
        $sql6_3 = "
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (2,'Albania','AL',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (5,'Andorra','AD',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (11,'Armenia','AM',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (14,'Austria','AT',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (15,'Azerbaijan','AZ',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (20,'Belarus','BY',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (21,'Belgium','BE',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (27,'Bosnia And Herzegovina','BA',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (33,'Bulgaria','BG',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (52,'Croatia (Hrvatska)','HR',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (54,'Cyprus','CY',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (55,'Czech Republic','CZ',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (57,'Denmark','DK',8);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (67,'Estonia','EE',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (72,'Finland','FI',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (73,'France','FR',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (80,'Georgia','GE',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (81,'Germany','DE',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (84,'Greece','GR',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (97,'Hungary','HU',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (98,'Iceland','IS',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (103,'Ireland','IE',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (105,'Italy','IT',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (109,'Kazakhstan','KZ',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (115,'Latvia','LV',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (120,'Liechtenstein','LI',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (121,'Lithuania','LT',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (122,'Luxembourg','LU',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (124,'Macedonia','MK',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (130,'Malta','MT',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (138,'Moldova','MD',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (139,'Monaco','MC',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (148,'Netherlands','NL',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (159,'Norway','NO',5);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (169,'Poland','PL',6);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (170,'Portugal','PT',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (174,'Romania','RO',7);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (182,'San Marino','SM',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (189,'Slovak Republic','SK',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (190,'Slovenia','SI',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (196,'Spain','ES',2);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (202,'Sweden','SE',4);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (203,'Switzerland','CH',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (214,'Turkey','TR',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (219,'Ukraine','UA',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (221,'United Kingdom','GB',1);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (227,'Vatican City (Holy See)','VA',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (239,'Serbia','RS',0);
        INSERT IGNORE INTO " . SYNCED_TABLE_COUNTRIES . " (`country_id`,`country_name`,`prefix`,`currency_id`) VALUES (240,'Montenegro','ME',0);";
        
        $inserts = explode(";", $sql6_3);
        foreach ($inserts as $insert)
        {
            if ($insert !== '')
            {
                $wpdb->query($insert);
            }
        }
        
        
        
    }

}
