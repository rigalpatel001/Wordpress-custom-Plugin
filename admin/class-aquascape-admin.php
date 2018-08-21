<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aquascape
 * @subpackage Aquascape/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aquascape
 * @subpackage Aquascape/admin
 * @author     Scott Rhodes <test@t.com>
 */
class Aquascape_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aquascape_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aquascape_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aquascape-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aquascape_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aquascape_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                wp_enqueue_script ('jquery-ui-tabs');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aquascape-admin.js', array( 'jquery' ), $this->version, false );
                wp_localize_script( $this->plugin_name, 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) ); 
	}
        
        /**
	 * Generate CAC Users CSV file
	 *
	 * @since    1.0.0
	 */
        
       public function Cac_csv_generate() {
           
           function load_cac_csv(){
            global $wpdb;
            $ShowTable = $wpdb->prefix . 'cac_application_entries';
            $csv_output = '';
            $header_fields = array(
            'id' => "Application ID",
            'active' => "Active",
            'approved' => "Approved",
            'approved_date' => "Date Approved",
            'cac_type' => "CAC Level",
            'macs_id' => "MACS Customer Number",
            'photos_job1' => "Job 1 Photos Approved",
            'photos_job2' => "Job 2 Photos Approved",
            'photos_job3' => "Job 3 Photos Approved",
            'receipts_submitted' => "Receipts Submitted",
            'company_name' => "Business Name",
            'first_name' => "First Name",
            'last_name' => "Last Name",
            'address' => "Address",
            'city' => "City",
            'state' => "State",
            'zip' => "Zip Code",
            'country' => "Country",
            'phone_primary' => "Phone",
            'phone_mobile' => "Mobile Phone",
            'phone_fax' => "Fax",
            'email' => "E-Mail",
            'url' => "Website Address",
            'business_age' => "Business Age",
            'fein' => "Federal Tax ID",
            'first_name_contact' => "Contact First Name",
            'last_name_contact' => "Contact Last Name",
            'phone_primary_contact' => "Contact Phone",
            'email_contact' => "Contact Email",
            'purchase_from1' => "Purchase From 1",
            'purchase_from_company1' => "Purchase Company 1",
            'purchase_from_company1_2' => "Other Company 1",
            'purchase_from_city1' => "Purchase City 1",
            'purchase_from_state1' => "Purchase State 1",
            'purchase_from2' => "Purchase From 2",
            'purchase_from_company2' => "Purchase Company 2",
            'purchase_from_company2_2' => "Other Company 2",
            'purchase_from_city2' => "Purchase City 2",
            'purchase_from_state2' => "Purchase State 2",
            'purchase_from3' => "Purchase From 3",
            'purchase_from_company3' => "Purchase Company 3",
            'purchase_from_company3_2' => "Other Company 3",
            'purchase_from_city3' => "Purchase City 3",
            'purchase_from_state3' => "Purchase State 3",
            'purchase_from_note' => "Comments",
            'annual_sales' => "Annual Sales",
            'total_employees' => "No. Employees",
            'business_description' => "Business Description",
            'percent_wf' => "Percent Water Features",
            'offer_water_features' => "Maintenance Offered",
            'have_retail_location' => "Retail Location",
            'total_aquascape_installs' => "Total Aquascape Installs",
            'installs_per_season' => "Water Features per Season",
            'water_features_type' => "Types Installed",
            'aquascape_exclusive_use' => "Exclusively Use Aquascape",
            'agree_aquascape_construction_methodology' => "Construction Methodology Compliance",
            'created' => "Application Date"
         );
            foreach ($header_fields as $result) {
                    $csv_output .= $result . ",";      
                }   
            //get field keys
            foreach (array_keys($header_fields) as $key) {
                $filed[] = $key;
            }
             $fileds = implode(",",$filed);
            
//            $results = $wpdb->get_results("SHOW COLUMNS FROM $ShowTable");
//            if (count($results) > 0) {
//                foreach ($results as $result) {
//                    $csv_output .= $result->Field . " ";      
//                }
//            }
            $csv_output .= "\n";
            $results = $wpdb->get_results("SELECT $fileds FROM $ShowTable ", ARRAY_A);
            if (count($results) > 0) {
                foreach ($results as $kk => $result) {
                     if(!empty($result)) :
                       if(!empty($result['water_features_type']) && $result['water_features_type']!= 'NULL'){  $result['water_features_type'] =  implode("/",unserialize($result['water_features_type'])); }
                       
                       $result['company_name'] = preg_replace("/[^a-zA-Z]+/", " ", $result['company_name']);
                       $result['purchase_from_note'] = preg_replace("/[^a-zA-Z]+/", " ", $result['purchase_from_note']);
                       $result['business_description'] = preg_replace("/[^a-zA-Z]+/", " ", $result['business_description']);
                       $remove_item = array(",");
                       $result = implode(",", str_replace($remove_item, '', $result));
                       $csv_output .= $result . "\n";
                    endif;
                    //$result = implode(" ", str_replace(' ', '', $result));
                    //$csv_output .= $result . "\n";
                }
            }
            return $csv_output;
        }

        global $pagenow;
         if ($pagenow == 'tools.php' && isset($_GET['cac_download'])) {
             header("Content-type: application/x-msdownload");
             header("Content-Disposition: attachment; filename=\"cacexport_" . date("Y-m-d_H-i-s") . ".csv\";" );
             header("Pragma: no-cache");
             header("Expires: 0");
             echo load_cac_csv();
             exit();
        }
    }
    
        /**
	 * Generate Disributors  CSV file
	 *
	 * @since    1.0.0
	 */
        
       public function Disributors_csv_generate() {
           
           function load_disributors_csv(){
            global $wpdb;
            $ShowTable = $wpdb->prefix . 'ppd';
            $csv_output = '';
            
            $header_fields = array(
            'id' => "Distributor ID",
            'company_name' => "Company Name",
            'lead_contact_email' => "Lead Contact Email",
            'address' => "Address",
            'city' => "City",
            'state' => "State",
            'zip' => "Zip Code",
            'country' => "Country",
            'phone' => "Phone Number",
            'email' => "Email",
            'website' => "Website",
            'display' => "Display"
         );
             foreach ($header_fields as $result) {
                    $csv_output .= $result . ",";      
                }   
            //get field keys
            foreach (array_keys($header_fields) as $key) {
                $filed[] = $key;
            }
             $fileds = implode(",",$filed);
            
//            $results = $wpdb->get_results("SHOW COLUMNS FROM $ShowTable");
//            if (count($results) > 0) {
//                foreach ($results as $result) {
//                    $csv_output .= $result->Field . " ";      
//                }
//            }
             
             
            $csv_output .= "\n";
            $results = $wpdb->get_results("SELECT $fileds FROM $ShowTable ", ARRAY_A);
            if (count($results) > 0) {
                foreach ($results as $kk => $result) {
                       $result['lead_contact_email'] = str_replace(',', '/', $result['lead_contact_email']);
                       $remove_item = array(' ',",");
                       $result = implode(",", str_replace($remove_item, ' ', $result));
                       $csv_output .= $result . "\n";
                    
                   // $result = implode(" ", str_replace(' ', '', $result));
                    //$csv_output .= $result . "\n";
                }
            }
            return $csv_output;
        }

        global $pagenow;
         if ($pagenow == 'tools.php' && isset($_GET['disributor_download'])) {
             header("Content-type: application/x-msdownload");
             header("Content-Disposition: attachment; filename=\"dristibutor_export_" . date("Y-m-d_H-i-s") . ".csv\";" );
             header("Pragma: no-cache");
             header("Expires: 0");
             echo load_disributors_csv();
             exit();
        }
    }
    
        /**
	 * Generate Locator Leads  CSV file
	 *
	 * @since    1.0.0
	 */
        
       public function Locator_leads_csv_generate() {
           
         /**
	 * GET CAC company name 
	 *
	 * @since    1.0.0
	 */
            function get_cac_company_name($cacid){
               global $wpdb;
               $tbl =  $wpdb->prefix . 'cac_application_entries';
               $compnaylist = '';
               $cacid = $cacid;
               if(!empty($cacid)){
               $company_name = $wpdb->get_results("SELECT company_name FROM $tbl where id IN($cacid)");
               if (count($company_name) > 0) {
                  foreach ($company_name as $cname) {
                    $compnaylist .= $cname->company_name . "/";      
                 }
                 
               }
                 return rtrim($compnaylist,'/');
               }else{
                   return '';
               }
               
           }
           
           
           function load_locator_leads_csv(){
            global $wpdb;
            $ShowTable =  $wpdb->prefix . 'cac_locator_leads';
            $csv_output = '';
            $header_fields = array(
            'created' => "Created",
            'firstname' => "First Name",
            'lastname' => "Last Name",
            'address1' => "Address",
            'city' => "City",
            'state' => "State",
            'zip' => "Zip Code",
            'country' => "Country",
            'phone' => "Phone Number",
            'email' => "Email",
            'cacID' => "Cac Id",
            'newsletter' => "Newsletter",
            'followup' => "Followup"
         );
             foreach ($header_fields as $result) {
                    $csv_output .= $result . ",";      
                }   
            //get field keys
            foreach (array_keys($header_fields) as $key) {
                $filed[] = $key;
            }
             $fileds = implode(",",$filed);
            
//            $results = $wpdb->get_results("SHOW COLUMNS FROM $ShowTable");
//            if (count($results) > 0) {
//                foreach ($results as $result) {
//                    $csv_output .= $result->Field . " ";      
//                }
//            }
            $csv_output .= "\n";
            $results = $wpdb->get_results("SELECT $fileds FROM $ShowTable ", ARRAY_A);
            if (count($results) > 0) {
                foreach ($results as $kk => $result) {
                   // $result['cacID'] = str_replace(',', '/', $result['cacID']);
                    $result['cacID'] = get_cac_company_name($result['cacID']);
                    $remove_item = array(",");
                    $result = implode(",", str_replace($remove_item, '', $result));
                    $csv_output .= $result . "\n";
                    //$result = implode(" ", str_replace(' ', '', $result));
                    //$csv_output .= $result . "\n";
                }
            }
            return $csv_output;
        }

        global $pagenow;
         if ($pagenow == 'tools.php' && isset($_GET['locatorleads_download'])) {
             header("Content-type: application/x-msdownload");
             header("Content-Disposition: attachment; filename=\"locatorleads_export_" . date("Y-m-d_H-i-s") . ".csv\";" );
             header("Pragma: no-cache");
             header("Expires: 0");
             echo load_locator_leads_csv();
             exit();
        }
    }
    
        /**
	 *  Report search  result
	 *
	 * @since  1.0.0
	 */
       public function  search_report_results(){
           
           if (isset($_REQUEST)) {
              parse_str($_REQUEST['data'], $output);
              $yearmonth = $output['yearmonth'];
              $year      = $output['year'];
              $state     = $output['state'];
              $month     = $output['month'];
              $condition = '1=1';
              if($yearmonth =='year'){
                  if(!empty($year)){
                  $condition = 'YEAR(created) ='.$year; 
                  $year_val = $year;
                  }
              }else{
                 if(!empty($month)){
                   $condition = 'YEAR(created) ='.$year .' AND MONTH(created) =' .$month; 
                   $year_val = $month .'/'.$year;
                 }else{
                     $condition = 'YEAR(created) ='.$year; 
                     $year_val = $year;
                 }
              }
              if(!empty($state)){
                  $condition.= ' AND state=' ."'".$state."'";
              }
              global $wpdb;
              $tablename = $wpdb->prefix . 'cac_locator_leads';
              $total_offer_singups = $wpdb->get_row("SELECT *,count(*) as total FROM wp_cac_locator_leads WHERE $condition AND offer =1");
              $total_lead = $wpdb->get_row("SELECT *,count(*) as total FROM wp_cac_locator_leads WHERE $condition ");
              $results = ''; 
              $results.= '<td>'. $year_val.' </td>';        
              $results.= '<td>'. $total_lead->total.' </td>';        
              $results.= '<td>'. $total_offer_singups->total .' </td>';        
              echo $results;
        }
        exit();
    }
	
       /**
	 * Add an Aqua Scape page
	 *
	 * @since  1.0.0
	 */
	public function add_menu_page() {
	
                /**
                 * Create admin side Aqua Scape Page
                 * 
                 * @since  1.0.0
                 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Aquascape Settings', 'aquascape' ),
			__( 'Aquascape', 'aquascape' ),
			'manage_options',
			'aquascape',
			array( $this, 'display_setting_page' )                        
		);
                
                
                /** 
                 * Create admin side List all CAC Entries
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'CAC Entries', 'aquascape' ),
			__( 'View all CAC Entries', 'aquascape' ),
			'manage_options',
			'aquascape-cacentries-view-all',
			array( $this, 'aquascape_cac_entries_page' )
		);
                
                 /** 
                 * Create admin side List all CAC Manage Forms
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'CAC Manage', 'aquascape' ),
			__( 'CAC Manage', 'aquascape' ),
			'manage_options',
			'aquascape-cacmanage',
			array( $this, 'aquascape_cac_manage_page' )
		);
                
                 /** 
                 * Create Distributer admin side  
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'Distributors List', 'aquascape' ),
			__( 'Distributors List', 'aquascape' ),
			'manage_options',
			'aquascape-disributors',
			array( $this, 'aquascape_distributors_page' )
		);
                
                /** 
                 * Distributer Manage in  admin side  
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'Distributors Manage', 'aquascape' ),
			__( 'Distributors Manage', 'aquascape' ),
			'manage_options',
			'aquascape-disributorsmanage',
			array( $this, 'aquascape_distributors_manage_page' )
		);
                
                /** 
                 * International Distributer Manage in  admin side  
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'International Distributors', 'aquascape' ),
			__( 'International Distributors', 'aquascape' ),
			'manage_options',
			'aquascape-international-dstributor',
			array( $this, 'aquascape_international_dstributor_page' )
		);
                
                
                
                
                 /**
                 * Create admin side Aqua Scape  Locator Leads  page
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'Locator Leads ', 'aquascape' ),
			__( 'Locator Leads ', 'aquascape' ),
			'manage_options',
			'aquascape-locator-leads',
			array( $this, 'aquascape_locator_leads_page' )
		);
                
                /**
                 * Create admin side Aqua Scape  Locator Leads  Detail page
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'null',
			__( 'Locator Leads ', 'aquascape' ),
			__( 'Locator Leads ', 'aquascape' ),
			'manage_options',
			'aquascape-locator-leads-details',
			array( $this, 'aquascape_locator_leads_details_page' )
		);
                
                /**
                 * Create admin side Email Template page
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'Email Template Settings ', 'aquascape' ),
			__( 'Email Template Settings ', 'aquascape' ),
			'manage_options',
			'aquascape-email-settings',
			array( $this, 'aquascape_email_settings_page' )
		);
         
                /**
                * Create admin side Reports page
                * 
                * @since  1.0.0
                */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'Reports ', 'aquascape' ),
			__( 'Reports ', 'aquascape' ),
			'manage_options',
			'aquascape-reports',
			array( $this, 'aquascape_reports_page' )
		);
                
                 /**
                 * Create User Permission Page
                 * 
                 * @since  1.0.0
                 */                
		$this->plugin_screen_hook_suffix = add_submenu_page(
                        'aquascape',
			__( 'User Permission', 'aquascape' ),
			__( 'User Permission', 'aquascape' ),
			'manage_options',
			'aquascape-user-permission',
			array( $this, 'aquascape_user_permission_page' )
		);  
                // add_action( "admin_print_scripts-{$page_hook_suffix}", 'wpdocs_plugin_admin_scripts');
                
        }
        
         /**
         * Aqua Scape register setting
         */
        
        public function general_register_setting(){
            register_setting('aqua_general_options_setting', 'aqua_google_recapcha_sitekey');                                                     // save email content
            register_setting('aqua_general_options_setting', 'aqua_google_recapcha_secretkey');                                                     // save email content
            register_setting('aqua_general_options_setting', 'aqua_googlemap_apikey');                                                     // save email content
            register_setting('aqua_general_options_setting', 'aqua_cac_result_page_url');  
            register_setting('aqua_general_options_setting', 'aqua_cac_testing_mod');  
            register_setting('aqua_general_options_setting', 'aqua_cac_google_analytics_script');  
        }
         /**
         * Aqua Scape Register Email  Setting
         */
        
        public function ac_visitor_register_email_setting(){
            register_setting('ac_visitor_email_setting', 'ac_visitor_email_subject');                                                     // save email content
            register_setting('ac_visitor_email_setting', 'ac_visitor_email_header');                                             // save email subject
            register_setting('ac_visitor_email_setting', 'ac_visitor_email_footer'); 
            // Contactor settings
            register_setting('ac_contractor_email_setting', 'ac_contractor_email_subject');                                                     // save email content
            register_setting('ac_contractor_email_setting', 'ac_contractor_email_header');                                             // save email subject
            register_setting('ac_contractor_email_setting', 'ac_contractor_email_footer');    
        }
        /**
         * Aqua Scape register User permission setting
         */
        
        public function ac_userpermission_register_setting(){
            register_setting('aqua_userpermission_options_setting', 'aqua_userpermission');
        }
        
        /**
         * Aqua Scape International Distributor setting
         */
        
        public function ac_international_distributor_setting(){
            register_setting('aqua_international_distributor_options_setting', 'aqua_international_distributor');
        }
        
        /**
	 * Aqua Scape Setting page for plugin
	 *
	 * @since  1.0.0
	 */
        
	public function display_setting_page() {
		include_once 'partials/aquascape_admin_setting_page.php';
	}
        
        /**
	 * Locator Leads listing page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_locator_leads_page() {
		include_once 'partials/aquascape_locator_leads_page.php';
	}
       
        /**
	 * Locator Leads Details page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_locator_leads_details_page() {
		include_once 'partials/aquascape_locator_leads_details_page.php';
	}
        
        /**
	 * Cac Entries page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_cac_entries_page() {
		include_once 'partials/aquascape_cac_entries_page.php';
	}
        
        /**
	 * Cac Entries page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_cac_manage_page() {
		include_once 'partials/aquascape_cac_manage_page.php';
	}
         
         /**
	 * Distributors List page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_distributors_page() {
		include_once 'partials/aquascape_distributorslist_page.php';
	}
         
        /**
	 * Distributors Manage  page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_distributors_manage_page() {
		include_once 'partials/aquascape_distributors_manage_page.php';
	}
        /**
	 * International Distributors Page
	 *
	 * @since  1.0.0
	 */
        
	public function aquascape_international_dstributor_page() {
		include_once 'partials/aquascape_international_distributor_page.php';
	}
        
        /**
	 * Email Setting   page
	 *
	 * @since  1.0.0
	 */
	public function aquascape_email_settings_page() {
		include_once 'partials/aquascape_email_settings_page.php';
	}
         /**
	 * User Permission Page
	 *
	 * @since  1.0.0
	 */
	public function aquascape_user_permission_page() {
		include_once 'partials/aquascape_user_permission_page.php';
	}
        
	/**
	 * Reports  page
	 *
	 * @since  1.0.0
	 */
	public function aquascape_reports_page() {
		include_once 'partials/aquascape_reports_page.php';
	}
}
