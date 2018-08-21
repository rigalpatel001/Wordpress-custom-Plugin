<?php

/**
 * Fired during plugin activation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aquascape
 * @subpackage Aquascape/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aquascape
 * @subpackage Aquascape/includes
 * @author     Scott Rhodes <test@t.com>
 */
class Aquascape_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
          //Create New Roles
            add_role( 'webmaster_role', 'Web Master');
           $role = get_role('webmaster_role');
           $role->add_cap('switch_themes');
           $role->add_cap('edit_themes');
           $role->add_cap('edit_plugins');
           $role->add_cap('edit_users');
           $role->add_cap('edit_files');
           $role->add_cap('manage_options');
           $role->add_cap('moderate_comments');
           $role->add_cap('manage_categories');
           $role->add_cap('manage_links');
           $role->add_cap('upload_files');
           $role->add_cap('import');
           $role->add_cap('unfiltered_html');
           $role->add_cap('edit_posts');
           $role->add_cap('edit_others_posts');
           $role->add_cap('edit_published_posts');
           $role->add_cap('publish_posts');
           $role->add_cap('edit_pages');
           $role->add_cap('read');
           $role->add_cap('level_10');
           $role->add_cap('level_9');
           $role->add_cap('level_8');
           $role->add_cap('level_7');
           $role->add_cap('level_6');
           $role->add_cap('level_5');
           $role->add_cap('level_4');
           $role->add_cap('level_3');
           $role->add_cap('level_2');
           $role->add_cap('level_1');
           $role->add_cap('level_0');
             
            global $wpdb;
            // create the Locator Lead table
            $aqua_locator_leads_tbl = $wpdb->prefix . 'cac_locator_leads';  
			
            if($wpdb->get_var("show tables like '$aqua_locator_leads_tbl'") != $aqua_locator_leads_tbl) 
            {
                $sql = "CREATE TABLE " . $aqua_locator_leads_tbl . " ( 
           `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `firstname` varchar(30) NOT NULL DEFAULT '',
            `lastname` varchar(40) NOT NULL DEFAULT '',
            `address1` varchar(255) NOT NULL DEFAULT '',
            `city` varchar(255) NOT NULL DEFAULT '',
            `state` varchar(50) NOT NULL,
            `zip` varchar(10) NOT NULL DEFAULT '',
            `country` varchar(255) NOT NULL,
            `phone` varchar(20) NOT NULL DEFAULT '',
            `email` varchar(255) NOT NULL DEFAULT '',
            `lookingFor` text NOT NULL,
            `select_status` varchar(255) NOT NULL,
            `cacID` varchar(255) NOT NULL,
            `newsletter` enum('yes','no') NOT NULL DEFAULT 'no',
            `agree` enum('yes','no') NOT NULL DEFAULT 'yes',
            `contest` enum('yes','no') NOT NULL DEFAULT 'no',
            `age` varchar(50) NOT NULL,
            `referral` varchar(50) NOT NULL,
            `gender` varchar(10) NOT NULL,
            `followup` tinyint(1) NOT NULL DEFAULT '0',
            `offer` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          )";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);
                    
            }  

            // create the lat & long table
	     $aqua_user_tbl = $wpdb->prefix . 'cac_latlong';   
            if($wpdb->get_var("show tables like '$aqua_user_tbl'") != $aqua_user_tbl) 
            {
                    $sql = "CREATE TABLE " . $aqua_user_tbl . " (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `zipcode` varchar(100) NOT NULL,
                    `lat` varchar(100) NOT NULL,
                    `lng` varchar(100) NOT NULL,
                    `city` varchar(100) NOT NULL,
                    `state` varchar(100) NOT NULL,
                    PRIMARY KEY (id)
                    );";
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);
                    
            }
            $aqua_cacentry_tbl = $wpdb->prefix . 'cac_application_entries';            
           
            // create cac application entries
            if($wpdb->get_var("show tables like '$aqua_cacentry_tbl'") != $aqua_cacentry_tbl) 
            {
              $sql = "CREATE TABLE " . $aqua_cacentry_tbl . "(
             `id` int(11) NOT NULL AUTO_INCREMENT,
            `active` tinyint(1) NOT NULL DEFAULT '0',
            `approved` tinyint(1) NOT NULL DEFAULT '0',
            `approved_date` date DEFAULT NULL,
            `cac_type` varchar(150) DEFAULT NULL,
            `macs_id` varchar(10) DEFAULT NULL,
            `photos_job1` int(1) NOT NULL DEFAULT '0',
            `photos_job2` int(1) NOT NULL DEFAULT '0',
            `photos_job3` int(1) NOT NULL DEFAULT '0',
            `receipts_submitted` tinyint(1) NOT NULL DEFAULT '0',
            `company_name` varchar(255) DEFAULT NULL,
            `first_name` varchar(20) DEFAULT NULL,
            `last_name` varchar(30) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `city` varchar(60) DEFAULT NULL,
            `state` varchar(40) DEFAULT NULL,
            `zip` varchar(10) DEFAULT NULL,
            `country` varchar(30) DEFAULT NULL,
            `phone_primary` varchar(20) DEFAULT NULL,
            `phone_mobile` varchar(20) DEFAULT NULL,
            `phone_fax` varchar(20) DEFAULT NULL,
            `email` varchar(155) DEFAULT NULL,
            `url` text,
            `business_age` int(2) DEFAULT NULL,
            `fein` varchar(10) DEFAULT NULL,
            `first_name_contact` varchar(20) DEFAULT NULL,
            `last_name_contact` varchar(20) DEFAULT NULL,
            `phone_primary_contact` varchar(20) DEFAULT NULL,
            `email_contact` varchar(155) DEFAULT NULL,
            `purchase_from1` varchar(20) DEFAULT NULL,
            `purchase_from_company1` varchar(255) DEFAULT NULL,
            `purchase_from_company1_2` varchar(255) DEFAULT NULL,
            `purchase_from_city1` varchar(30) DEFAULT NULL,
            `purchase_from_state1` varchar(40) DEFAULT NULL,
            `purchase_from2` varchar(20) DEFAULT NULL,
            `purchase_from_company2` varchar(255) DEFAULT NULL,
            `purchase_from_company2_2` varchar(255) DEFAULT NULL,
            `purchase_from_city2` varchar(30) DEFAULT NULL,
            `purchase_from_state2` varchar(40) DEFAULT NULL,
            `purchase_from3` varchar(20) DEFAULT NULL,
            `purchase_from_company3` varchar(255) DEFAULT NULL,
            `purchase_from_company3_2` varchar(255) DEFAULT NULL,
            `purchase_from_city3` varchar(30) DEFAULT NULL,
            `purchase_from_state3` varchar(40) DEFAULT NULL,
            `purchase_from_note` text,
            `annual_sales` varchar(12) DEFAULT NULL,
            `total_employees` int(5) DEFAULT NULL,
            `business_description` text,
            `percent_wf` int(3) DEFAULT NULL,
            `offer_water_features` tinyint(1) NOT NULL DEFAULT '0',
            `have_retail_location` tinyint(1) NOT NULL DEFAULT '0',
            `total_aquascape_installs` int(6) DEFAULT NULL,
            `installs_per_season` int(5) DEFAULT NULL,
            `water_features_type` text,
            `aquascape_exclusive_use` tinyint(1) NOT NULL DEFAULT '0',
            `agree_aquascape_construction_methodology` tinyint(1) NOT NULL DEFAULT '0',
            `lat` float(10, 6) NOT NULL DEFAULT '0.000000',
            `lng` float(10, 6) NOT NULL DEFAULT '0.000000',
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `password` varchar(255) DEFAULT NULL,
            `pw_reset_token` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `active` (`active`),
            KEY `country` (`country`),
            KEY `cac_type` (`cac_type`)
            )";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);  
        }
        
          $aqua_ppd_tbl = $wpdb->prefix . 'ppd';            
           
            // create cac application entries
            if($wpdb->get_var("show tables like '$aqua_ppd_tbl'") != $aqua_ppd_tbl) 
            {
              $sql = "CREATE TABLE " . $aqua_ppd_tbl . "(
             `id` int(5) NOT NULL AUTO_INCREMENT,
            `company_name` varchar(255) DEFAULT NULL,
			`lead_contact_email` TEXT DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `city` varchar(255) DEFAULT NULL,
            `state` varchar(255) DEFAULT NULL,
            `zip` varchar(6) DEFAULT NULL,
            `country` varchar(25) DEFAULT NULL,
            `phone` varchar(16) DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `website` varchar(255) DEFAULT NULL,
            `sort` char(2) DEFAULT NULL,
            `display` char(1) DEFAULT NULL,
            `lat` float(10, 6) NOT NULL DEFAULT '0.000000',
            `lng` float(10, 6) NOT NULL DEFAULT '0.000000',
            PRIMARY KEY (`id`)
            )";
              
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);  
        }   
    }
}