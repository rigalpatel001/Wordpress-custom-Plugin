<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aquascape
 * @subpackage Aquascape/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aquascape
 * @subpackage Aquascape/public
 * @author     Scott Rhodes <test@t.com>
 */
class Aquascape_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('wp_ajax_ebook_form_data', array($this, 'ebook_form_data'));
        add_action('wp_ajax_nopriv_ebook_form_data', array($this, 'ebook_form_data'));
        // MAP ajax  functions
        add_action('wp_ajax_distributor_result_data', array($this, 'distributor_result_data'));
        add_action('wp_ajax_nopriv_distributor_result_data', array($this, 'distributor_result_data'));
        // Get distributor MAP lat&lng ajax  functions
        add_action('wp_ajax_distributor_get_latlng', array($this, 'distributor_get_latlng'));
        add_action('wp_ajax_nopriv_distributor_get_latlng', array($this, 'distributor_get_latlng'));
        
        // CAC  Locatorajax email function
       // add_action('wp_ajax_find_cac_user_email', array($this, 'find_cac_user_email'));
       // add_action('wp_ajax_nopriv_find_cac_user_email', array($this, 'find_cac_user_email'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aquascape-public.css', array(), $this->version, 'all');
        wp_enqueue_style('jqueryuitab', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), '', 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        
        $mapapikey = get_option('aqua_googlemap_apikey');
        wp_enqueue_script("jquery-ui-tabs");
        wp_enqueue_script('form-validatorjs', plugin_dir_url(__FILE__) . 'js/jquery.form-validator.min.js', array('jquery'), false);
        wp_enqueue_script('cac-form-validatorjs', plugin_dir_url(__FILE__) . 'js/parsley.min.js', array('jquery'), false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aquascape-public.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'ajax_params', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_script('mapapi', 'http://maps.google.com/maps/api/js?key='.$mapapikey, array('jquery'), false);
        wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js', array('jquery'), false);
        wp_enqueue_script('custommap', plugin_dir_url(__FILE__) . 'js/custommap.js', array('jquery'), false);
    }

    /**
     * Append  Google Anylitics code in page body 
     *
     * @since  1.0.0
     */
    public function aquascape_google_analytics() {
        $gscript = get_option('aqua_cac_google_analytics_script');
        if (!empty($gscript)) :
            echo $gscript;
        endif;
    }

    /**
     *  Set session this function is used to start session
     *
     * @since    1.0.0
     */
    public function aqua_register_session() {
        if (!session_id())
            session_start();
    }

    /**
     * Format Phone number based on country 
     *
     * @since    1.0.0
     */
    public static function format_phone_US($phone) {
        // note: making sure we have something
        if (!isset($phone{3})) {
            return '';
        }
        // note: strip out everything but numbers 
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch ($length) {
            case 7:
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
                break;
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
                break;
            case 11:
                return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3-$4", $phone);
                break;
            default:
                return $phone;
                break;
        }
    }

    /**
     * Get Latitude and longitude using google map API 
     *
     * @since    1.0.0
     */
    public static function getlatitude($zip, $country = NULL) {
        global $wpdb;
        $latlng_table_name = $wpdb->prefix . "cac_latlong";
        $zipresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM $latlng_table_name where zipcode= %s", $zip));
        if (count($zipresult) > 0) {
            $result = array(
                'lat' => $zipresult->lat,
                'lng' => $zipresult->lng
            );
            return $result;
        } else {
            if($country=="US"){
                $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip . ", United States ") . "&sensor=false";
            }elseif($country=="CA"){
                $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip . ", Canada") . "&sensor=false";
            }else{
                $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip ."," .$country ) . "&sensor=false";
            }
          //  echo $url;
            $result_string = file_get_contents($url);
            $result = json_decode($result_string, true);
            if ($result['status'] == "OK") {
                // Get lat & lng
                $result1[] = $result['results'][0];
                $result2[] = $result1[0]['geometry'];
                $result3[] = $result2[0]['location'];
                $lat = $result3[0]['lat'];
                $lng = $result3[0]['lng'];

                // Get city state
                $citystate[] = $result['results'][0];
                $citystate2[] = $citystate[0]['formatted_address'];
                $address[] = $citystate2[0];
                $address = explode(",", $address[0]);
                $city = $address[0];
                $stateinfo = $address[1];
                $state = strtok($stateinfo, " ");
                //Store info in database
                $data = array(
                    'zipcode' => $zip,
                    'lat' => $lat,
                    'lng' => $lng,
                    'city' => $city,
                    'state' => $state,
                );
                //Insert in database
                $inserted = $wpdb->insert($latlng_table_name, $data);
                return $result3[0];
            } else {
                return false;
            }
        }
    }

    /**
     * Get City and State using google map API 
     *
     * @since    1.0.0
     */
    public static function getcitystate($zip) {
        global $wpdb;
        $latlng_table_name = $wpdb->prefix . "cac_latlong";
        $citystateresult = $wpdb->get_row($wpdb->prepare("SELECT city,state FROM $latlng_table_name where (zipcode=%s AND  trim(coalesce(city, '')) <>'' )", $zip));
        if (count($citystateresult) > 0) {
            $result = array(
                'city' => $citystateresult->city,
                'state' => $citystateresult->state
            );
            return $result;
        } else {
            $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip) . "&sensor=false";
            $result_string = file_get_contents($url);
            $result = json_decode($result_string, true);
            if ($result['status'] == "OK") {

                // Get lat & lng
                $latlng[] = $result['results'][0];
                $latlng2[] = $latlng[0]['geometry'];
                $latlng3[] = $latlng2[0]['location'];
                $lat = $latlng3[0]['lat'];
                $lng = $latlng3[0]['lng'];


                $result1[] = $result['results'][0];
                $result2[] = $result1[0]['formatted_address'];
                $address[] = $result2[0];
                $address = explode(",", $result2[0]);
                $stateinfo = $address[1];

                //Store info in database
                $data = array(
                    'zipcode' => $zip,
                    'lat' => $lat,
                    'lng' => $lng,
                    'city' => $address[0],
                    'state' => strtok($stateinfo, " ")
                );
                //Insert in database
                $inserted = $wpdb->insert($latlng_table_name, $data);
                $result = array(
                    'city' => $address[0],
                    'state' => strtok($stateinfo, " ")
                );
                return $result;
            } else {
                return false;
            }
        }
    }

    /**
     * Get Distributor List Dropdown
     *
     * @since    1.0.0
     */
    public static function getdistributor() {
        global $wpdb;
        $dirstibutor_table_name = $wpdb->prefix . "ppd";
        // this will get the data from your table
        $retrieve_dist = $wpdb->get_results("SELECT id ,company_name FROM $dirstibutor_table_name group by company_name");
        if (count($retrieve_dist) > 0) {
            foreach ($retrieve_dist as $retrieved_data) {
                echo "<option value=$retrieved_data->id > $retrieved_data->company_name </option>";
            }
        }else{
            echo "Not found";
        }
    }
    
        /**
	 * Create CAC Manage form Shortcode 
	 *
	 * @since    1.0.0
	 */
        
        public function cac_manage_form_register_shortcodes() {
            
            function cac_manage_form(){
                  ob_start();
                  
             if(isset($_REQUEST['cacmanage']) && isset($_REQUEST['cacmanagefrm_nonce_field']) && wp_verify_nonce($_REQUEST['cacmanagefrm_nonce_field'], 'cacmanagefrm_nonce')) {
                   
                global $wpdb;
                $tablename = $wpdb->prefix . 'cac_application_entries'; 
                $secret = get_option('aqua_google_recapcha_secretkey');
                //$secret="6LcFERAUAAAAAD9Au9UFcI5FMB0ZfW95j3TY_lTv";
                $response=$_POST["g-recaptcha-response"];
                $verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
                $captcha_success=json_decode($verify);

                if ($captcha_success->success==false) {
                     echo   $errorMSG = "<div class='rechpcha_error'><p>Recaptcha Verification Error</p></div>";
                }
                else if ($captcha_success->success==true) {
               // sanitize form values
                $company_name       = sanitize_text_field( $_POST["company_name"] );
                $first_name         = sanitize_text_field( $_POST["first_name"] );
                $last_name          = sanitize_text_field( $_POST["last_name"] );
                $address            = sanitize_text_field( $_POST["address"] );
                $city               = sanitize_text_field( $_POST["city"] );
                $state              = sanitize_text_field( $_POST["state"] );
                $zip                = sanitize_text_field( $_POST["zip"] );
                $country            = sanitize_text_field( $_POST["country"] ); 
                $phone_primary      = sanitize_text_field( $_POST["phone_primary"] );
                $phone_fax          = sanitize_text_field( $_POST["phone_fax"] );
                $email              = sanitize_email( $_POST["email"] );     
                $url                = esc_url( $_POST["url"] );
                $business_age       = sanitize_text_field( $_POST["business_age"] );
                $annual_sales       = sanitize_text_field( $_POST["annual_sales"] ); 
                $total_employees    = sanitize_text_field( $_POST["total_employees"] );
                $fein               = sanitize_text_field( $_POST["fein"] );     
                $first_name_contact = sanitize_text_field( $_POST["first_name_contact"] );
                $last_name_contact  = sanitize_text_field( $_POST["last_name_contact"] );     
                $phone_primary_contact = sanitize_text_field( $_POST["phone_primary_contact"] );
                $phone_mobile       = sanitize_text_field( $_POST["phone_mobile"] );     
                $email_contact      = sanitize_email( $_POST["email_contact"] ); 
                $purchase_from1     = sanitize_text_field( $_POST["purchase_from1"] ); 
                $purchase_from_company1 = sanitize_text_field( $_POST["purchase_from_company1"] );     
                $purchase_from_city1    = sanitize_text_field( $_POST["purchase_from_city1"] ); 
                $purchase_from_state1   = sanitize_text_field( $_POST["purchase_from_state1"] );
                $purchase_from2         = sanitize_text_field( $_POST["purchase_from2"] );
                $purchase_from_company2 = sanitize_text_field( $_POST["purchase_from_company2"] );
                $purchase_from_city2    = sanitize_text_field( $_POST["purchase_from_city2"] );    
                $purchase_from_state2   = sanitize_text_field( $_POST["purchase_from_state2"] );
                $purchase_from3         = sanitize_text_field( $_POST["purchase_from3"] );     
                $purchase_from_company3 = sanitize_text_field( $_POST["purchase_from_company3"] );     
                $purchase_from_city3    = sanitize_text_field( $_POST["purchase_from_city3"] );
                $purchase_from_state3   = sanitize_text_field( $_POST["purchase_from_state3"] );    
                $business_description   = sanitize_text_field( $_POST["business_description"] ); 
                $purchase_from_note     = sanitize_text_field( $_POST["purchase_from_note"] );
                $percent_wf             = sanitize_text_field( $_POST["percent_wf"] ); 
                $offer_water_features   = sanitize_text_field( $_POST["offer_water_features"] ); 
                $have_retail_location   = sanitize_text_field( $_POST["have_retail_location"] );
                $total_aquascape_installs   = sanitize_text_field( $_POST["total_aquascape_installs"] );
                $installs_per_season    = sanitize_text_field( $_POST["installs_per_season"] );
                $aquascape_exclusive_use = sanitize_text_field( $_POST["aquascape_exclusive_use"] );
                $agree_aquascape_construction_methodology = sanitize_text_field( $_POST["agree_aquascape_construction_methodology"] );
                $leads_agree = sanitize_text_field( $_POST["leads_agree"] );
                $terms_agree = sanitize_text_field( $_POST["terms_agree"] );
               // $water_features_types =  sanitize_text_field($_POST['water_features_type']);
                $latlang =   Aquascape_Public::getlatitude($zip,$country);
                $lat = $latlang['lat'];
                $lang = $latlang['lng'];
                if(isset($_POST['water_features_type'])){
                    $water_features_type  = serialize($_POST['water_features_type']);
                    if (strpos($water_features_type, 'N;') !== false) { 
                        $water_features_type ='NULL';
                }
                }else{
                    $water_features_type ='NULL';
                }
               $data = array(
                                'company_name'          => $company_name,
                                'first_name'            => $first_name,
                                'last_name'             => $last_name,  
                                'address'               => $address,
                                'city'                  => $city,
                                'state'                 => $state,
                                'zip'                   => $zip,
                                'country'               => $country,
                                'phone_primary'         => $phone_primary,
                                'phone_fax'             => $phone_fax,
                                'email'                 => $email,
                                'url'                   => $url,
                                'business_age'          => $business_age,
                                'annual_sales'          => $annual_sales, 
                                'total_employees'       => $total_employees,
                                'fein'                  => $fein,
                                'first_name_contact'    => $first_name_contact,
                                'last_name_contact'     => $last_name_contact,
                                'phone_primary_contact' => $phone_primary_contact,
                                'phone_mobile'          =>  $phone_mobile,
                                'email_contact'         =>  $email_contact,
                                'purchase_from1'        =>  $purchase_from1,
                                'purchase_from_company1' => $purchase_from_company1,
                                'purchase_from_city1'    => $purchase_from_city1,
                                'purchase_from_state1'   => $purchase_from_state1,
                                'purchase_from2'         => $purchase_from2,
                                'purchase_from_company2' => $purchase_from_company2,
                                'purchase_from_city2'    => $purchase_from_city2,
                                'purchase_from_state2'   => $purchase_from_state2,
                                'purchase_from3'         => $purchase_from3,
                                'purchase_from_company3' => $purchase_from_company3,
                                'purchase_from_city3'    => $purchase_from_city3,
                                'purchase_from_state3'   => $purchase_from_state3,
                                'business_description'   => $business_description,
                                'purchase_from_note'     => $purchase_from_note,
                                'percent_wf'             => $percent_wf,
                                'offer_water_features'   => $offer_water_features,
                                'have_retail_location'   => $have_retail_location,
                                'total_aquascape_installs' => $total_aquascape_installs,
                                'installs_per_season'     => $installs_per_season,
                                'water_features_type'     => $water_features_type,
                                'aquascape_exclusive_use' => $aquascape_exclusive_use,
                                'agree_aquascape_construction_methodology' => $agree_aquascape_construction_methodology,
                                'lat'            => $lat,
                                'lng'            => $lang
                            );   
                          
                   $inserted = $wpdb->insert($tablename, $data);
                    if (false === $inserted) {
                        // There was an error

                        echo "error in Insert";
                    } else {
                     echo '<div  class="success_frm"><p>Form Successfully Submited...</p></div>';
                    }
                }   
               }
                  ?>
              <?php 
           
              $countries_list = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan",
               "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", 
               "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands",
               "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
               "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", 
               "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", 
               "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia",
               "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", 
               "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn",
               "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa",
               "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", 
               "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "Uruguay", "Uzbekistan",
               "Vanuatu", "Venezuela", "Vietnam",  "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
              
              ?>
             <div class="cac_frm_div">  
               <form role="form" id="cac_application_form" name="cac_application_form" action="" method="POST">
                <div class="well">
                    <h3>Company Information</h3>
                    <p>This information will be Aquascape's number one source of information that will be sent to the Consumer.</p>
                    <hr>
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input class="form-control" id="company_name" name="company_name" maxlength="255" placeholder="Company Name" required="" type="text">
                    </div>
                     <div class="frm-row">
                         <div class="half">
                              <div class="form-group">
                                <label for="first_name">Contact First Name</label>
                                <input id="first_name" name="first_name" class="form-control" maxlength="20" placeholder="First Name" required="" type="text">
                               </div>
                        </div>
                         <div class="half">
                             <div class="form-group">
                                 <label for="last_name">Contact Last Name</label>
                                <input id="last_name" name="last_name" class="form-control" maxlength="30" placeholder="Last Name" required="" type="text">
                             </div>
                         </div>
                    </div>  
                   
                    <div class="form-group">
                        <label for="address">Company Address</label>
                        <input class="form-control" id="address" name="address" maxlength="255" placeholder="Address" required="" type="text">
                    </div>
                   <div class="frm-row">
                         <div class="thirdhalf">
                              <div class="form-group">
                                <label for="city">City</label>
                                <input id="city" name="city" class="form-control" maxlength="30" placeholder="City" value="" required="" type="text">
                               </div>
                        </div>
                         <div class="thirdhalf">
                             <div class="form-group">
                                <label for="state">State</label>
                                <input id="state" name="state" class="form-control" maxlength="30" placeholder="State" value="" required="" type="text">
                             </div>
                         </div>
                         <div class="thirdhalf">
                             <div class="form-group">
                                <label for="zip">Zip/Postal Code</label>
                                <input id="zip" name="zip" class="form-control" maxlength="10" placeholder="Zipcode" value="" required="" type="tel">
                             </div>
                         </div>
                    </div>  
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country" class="form-control" required="">
                            <option value="">--Select One--</option>
                            <option value="US">US</option>
                            <option value="CA">Canada</option>
                            <?php 
                                    foreach($countries_list as $countries):
                                        echo ' <option value='.$countries.'>'.$countries .'</option>';
                                    endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                <label for="phone_primary">Company Phone</label>
                                <input id="phone_primary" name="phone_primary" class="form-control" maxlength="20" placeholder="Phone #" value="" required="" type="tel">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                <label for="phone_fax">Company Fax</label>
                                <input id="phone_fax" name="phone_fax" class="form-control" maxlength="20" placeholder="Fax #" value="" type="tel">
                            </div>
                        </div>
                    </div>
                    <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                  <label for="email">Company Email</label>
                                <input class="form-control" id="email" name="email" maxlength="255" placeholder="Email Address" required="" type="email">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                  <label for="url">Website Address</label>
                                <input class="form-control" id="url" name="url" maxlength="255" placeholder="Website Address" type="url">
                            </div>
                        </div>
                    </div>
                    <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                 <label for="business_age">Years in Business</label>
                                <input class="form-control" id="business_age" name="business_age" maxlength="3" placeholder="Years in Business" required="" type="number">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                <label for="annual_sales">Annual Sales</label>
                                <input class="form-control" id="annual_sales" name="annual_sales" maxlength="13" placeholder="Annual Sales" required="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                 <label for="total_employees">Number of Employees</label>
                                <input class="form-control" id="total_employees" name="total_employees" maxlength="5" placeholder="Number of Employees" required="" type="text">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                 <label for="fein">Federal Employer ID Number</label>
                                <input class="form-control" id="fein" name="fein" maxlength="10" placeholder="FEIN" required="" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <h3>Company Contact Information</h3>
                    <p>This information is for Aquascape Use Only.</p>
                    <hr>
                     <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                 <label for="first_name_contact">Contact First Name</label>
                                <input id="first_name_contact" name="first_name_contact" class="form-control" maxlength="20" placeholder="First Name" required="" type="text">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                 <label for="last_name_contact">Contact Last Name</label>
                                <input id="last_name_contact" name="last_name_contact" class="form-control" maxlength="30" placeholder="Last Name" required="" type="text">
                            </div>
                        </div>
                    </div>
                   
                    <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                                <label for="phone_primary_contact">Contact Phone</label>
                                <input id="phone_primary_contact" name="phone_primary_contact" class="form-control" maxlength="20" placeholder="Phone #" value="" required="" type="tel">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                  <label for="phone_mobile">Mobile Phone</label>
                                <input id="phone_mobile" name="phone_mobile" class="form-control" maxlength="20" placeholder="Mobile #" value="" required="" type="tel">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email_contact">Contact Email</label>
                        <input id="email_contact" name="email_contact" class="form-control" maxlength="255" placeholder="Email Address" required="" type="email">
                    </div>
                </div>
                <div class="well">
                    <p>Who do you purchase your AquascapePRO product or Aquascape retail products from? Please provide the name(s) of your Participating Authorized AquascapePRO Distributors (AAPD)</p>
                     <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                               <label for="purchase_from_1">Type</label>
                                <select id="purchase_from_1" name="purchase_from1" class="form-control purchase_from_select" required="">
                                    <option value="">--Select One--</option>
                                    <option value="AAPD">Distributor</option>
                                    <option value="Direct">Aquascape Direct</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                  <label for="purchase_from_company1">Pro Distributor Name</label>
                                <select id="purchase_from_company1" name="purchase_from_company1" class="form-control">
                                     <option value="">--Select One--</option>
                                    <?php 
                                            Aquascape_Public:: getdistributor();
                                      ?>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div class="frm-row hidden" id="purchase_from_extra_1">
                        <div class="half">
                            <div class="form-group">
                                 <label for="purchase_from_city1">City</label>
                                 <input id="purchase_from_city1" name="purchase_from_city1" class="form-control" maxlength="30" placeholder="City" type="text">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                <label for="purchase_from_state1">State</label>
                                <input id="purchase_from_state1" name="purchase_from_state1" class="form-control" maxlength="30" placeholder="State" type="text">
                            </div>
                        </div>
                    </div>
                   
                    <hr>
                     <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                               <label for="purchase_from_2">Type</label>
                                <select id="purchase_from_2" name="purchase_from2" class="form-control purchase_from_select">
                                    <option value="">--Select One--</option>
                                    <option value="AAPD">Distributor</option>
                                    <option value="Direct">Aquascape Direct</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                 <label for="purchase_from_company2">Pro Distributor Name</label>
                                <select id="purchase_from_company2" name="purchase_from_company2" class="form-control">
                                    <option value="">--Select One--</option>
                                    <?php 
                                            Aquascape_Public:: getdistributor();
                                      ?>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div class="frm-row hidden" id="purchase_from_extra_2">
                        <div class="half">
                            <div class="form-group">
                                <label for="purchase_from_city2">City</label>
                                <input id="purchase_from_city2" name="purchase_from_city2" class="form-control" maxlength="30" placeholder="City" type="text">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                <label for="purchase_from_state2">State</label>
                                <input id="purchase_from_state2" name="purchase_from_state2" class="form-control" maxlength="30" placeholder="State" type="text">
                            </div>
                        </div>
                    </div>
                    <hr>
                     <div class="frm-row">
                        <div class="half">
                            <div class="form-group">
                              <label for="purchase_from_3">Type</label>
                                <select id="purchase_from_3" name="purchase_from3" class="form-control purchase_from_select">
                                    <option value="">--Select One--</option>
                                    <option value="AAPD">Distributor</option>
                                    <option value="Direct">Aquascape Direct</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                 <label for="purchase_from_company3">Pro Distributor Name</label>
                                <select id="purchase_from_company3" name="purchase_from_company3" class="form-control">
                                    <option value="">--Select One--</option>
                                    <?php 
                                            Aquascape_Public:: getdistributor();
                                      ?>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div class="frm-row hidden" id="purchase_from_extra_3">
                        <div class="half">
                            <div class="form-group">
                                <label for="purchase_from_city3">City</label>
                                <input id="purchase_from_city3" name="purchase_from_city3" class="form-control" maxlength="30" placeholder="City" type="text">
                            </div>
                        </div>
                        <div class="half">
                            <div class="form-group">
                                <label for="purchase_from_state3">State</label>
                                <input id="purchase_from_state3" name="purchase_from_state3" class="form-control" maxlength="30" placeholder="State" type="text">
                            </div>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label for="business_description">Comments</label>
                        <textarea id="business_description" name="business_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">

                    </div>
                </div> <!-- end well -->
                <div class="well">
                    <div class="form-group">
                        <label for="purchase_from_note">What is the primary description of your business?</label>
                        <textarea id="purchase_from_note" name="purchase_from_note" class="form-control" rows="3" required=""></textarea>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="percent_wf">What percentage of your business is water feature installation?</label>
                            </div>
                            <div class="col-md-3">
                                <input id="percent_wf" name="percent_wf" class="form-control" maxlength="3" required="" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="offer_water_features">Do you offer maintenance for water features?</label>
                        <div class="radio">
                            <label>
                                <input id="offer_water_features1" name="offer_water_features" value="1" data-parsley-multiple="offer_water_features" type="radio">
                                Yes
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input id="offer_water_features2" name="offer_water_features" value="0" required="" data-parsley-multiple="offer_water_features" type="radio">
                                No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="have_retail_location">Do you have a retail location?</label>
                        <div class="radio">
                            <label>
                                <input id="have_retail_location1" name="have_retail_location" value="1" required="" data-parsley-multiple="have_retail_location" type="radio">
                                Yes
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input id="have_retail_location2" name="have_retail_location" value="0" data-parsley-multiple="have_retail_location" type="radio">
                                No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="total_aquascape_installs">How many water features have you installed using the complete Aquascape system?</label>
                            </div>
                            <div class="col-md-3">
                                <input id="total_aquascape_installs" name="total_aquascape_installs" class="form-control" maxlength="6" required="" type="number">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="installs_per_season">How many water features do you install per season?</label>
                            </div>
                            <div class="col-md-3">
                                <input id="installs_per_season" name="installs_per_season" class="form-control" maxlength="5" required="" type="number">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="water_features_type">What types of water features do you currently install? (check all that apply)</label>
                        <div class="checkbox">
                            <label>
                                <input id="water_features_1" name="water_features_type[]" value="Fountainscapes" type="checkbox">
                                Fountainscapes
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input id="water_features_2" name="water_features_type[]" value="Ecosystem Ponds" type="checkbox">
                                Ecosystem Ponds
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input id="water_features_3" name="water_features_type[]" value="Pondless Waterfalls" type="checkbox">
                                Pondless Waterfalls
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input id="water_features_4" name="water_features_type[]" value="Commercial Water Features" type="checkbox">
                                Commercial Water Features
                            </label>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <h3>Exclusive Use of Aquascape Products</h3>
                    <p>Aquascape provides everything needed for a professional contractor to install or maintain a complete water feature. Therefore, all Certified, Professional and Master Certified Aquascape Contractors must exclusively install or use only Aquascape products on their water feature projects. </p>

                    <hr>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input id="aquascape_exclusive_use1" name="aquascape_exclusive_use" value="1" required="" data-parsley-multiple="aquascape_exclusive_use" type="radio">
                                By choosing you agree to exclusively use Aquascape products
                            </label>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="well">
                    <h3>Construction Methodology Compliance</h3>
                    <p>All Certified, Professional and Master Certified Aquascape Contractors must adhere to the Aquascape construction methodology at all times. </p>
                    <p>Our construction products and practices are the cornerstone of our success. Certified Aquascape Contractors are an extension of Aquascape and the only way we can guarantee a successful water feature installation is by using the appropriate products and procedures on every project</p>
                    <p>Examples of the Aquascape methodology include rocks and gravel installed in all ponds, no bottom drains, no bead filters, no drain pipes as pondless vaults or wetland filters, the use of skimmers and Biofalls or wetland filters on all water features where applicable, and many more.</p>
                    <hr>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input id="agree_aquascape_construction_methodology1" name="agree_aquascape_construction_methodology" value="1" required="" data-parsley-multiple="agree_aquascape_construction_methodology" type="radio">
                                By choosing you agree to comply with Aquascape construction methodologies
                            </label>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="well">
                    <h3>Leads from Aquascape</h3>
                    <p>Aquascape provides the contractor with many resources to obtain a construction lead. Those resources may come from but are not limited to Aquascape's website, "Find A Contractor" page, technical support, marketing, distributors, promotional events, Aquascape, Inc. employees or other Certified Aquascape Contractors. All leads supplied to my company by Aquascape, Inc. or one of Aquascape, Inc.'s resources must install or use only Aquascape products as well as comply with the Aquascape construction methodology. All leads supplied to my company by Aquascape, Inc. or one of its resources will be followed up within two business days.</p>
                    <hr>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input id="leads" name="leads_agree" value="1" required="" type="checkbox">
                                I agree to exclusively use Aquascape products on any lead provided to me by Aquascape and follow up on any leads within two business days
                            </label>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="well">
                    <h3>Signature</h3>
                    <p>I hereby enter into this agreement to represent myself and my company, as a Certified Aquascape Contractor. By entering this agreement, I will follow the guidelines, policies, and recertification requirements provided by Aquascape, Inc. at all times while representing myself as a Certified Aquascape Contractor. The Certified Aquascape Contractor Program is designed to offer the consumer a qualified installer that understands how to properly install and maintain Aquascape, Inc. systems. Aquascape, Inc. holds each individual Certified Aquascape Contractor to the highest standards of the Water Garden industry. By entering the contractual agreement, I agree and understand all contents of this agreement. Aquascape, Inc. will periodically review my account activity to track purchases, and to maintain the credibility of the Certified Aquascape Contractor Program. I understand that a 100% commitment is a requirement to maintain active status within this program.</p>
                    <hr>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input id="signature" name="terms_agree" value="1" required="" type="checkbox">
                                I Agree
                            </label>
                        </div>
                    </div>
                    <hr>

                </div>
                <?php 
                     $site_key = get_option('aqua_google_recapcha_sitekey');
                ?>
                 <div class="g-recaptcha" data-sitekey="<?php echo $site_key;?>"></div>
                  <?php wp_nonce_field('cacmanagefrm_nonce', 'cacmanagefrm_nonce_field'); ?>
                <button type="submit" name="cacmanage" class="btn btn-default">Submit Application</button>
            </form>    
        </div>        
                  <?php
                  $content = ob_get_clean();
                  return $content;
            }
             add_shortcode("aquascape-cac-manage-form", "cac_manage_form");
        }

        /**
	 * Create Shortcode Form
	 *
	 * @since    1.0.0
	 */
        
        public function form_register_shortcodes() {
        
         /**
	 * Mmange  Form data and display form
	 *
	 * @since    1.0.0
	 */
          
        function frm_shortcode() {
           // ob_start();
            // if (!session_id()) {
                // session_start();
            // }
            $_SESSION['cac_frm_key'] = rand(1, 1000);
            global $wpdb;
            $cac_table_name = $wpdb->prefix . "cac_application_entries";
            $country_result =  $wpdb->get_results("SELECT distinct(country) FROM $cac_table_name where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1' AND (country!='US' AND country!='CA')");
            ?>
            <div class="cac-find-div">
			     <span class="search-icn"></span>
                <div>
                    <h1 class="single_t">CAC Locator WalkRought</h1>
                </div>
                <div>
                    <p class="frm_heading">
                       <h1>Find &amp; Connect with a Nearby Certified Aquascape Contractor</h1>
                    </p>
                    <form action="<?php echo esc_attr(get_option('aqua_cac_result_page_url')); ?>" class="cac_find_frm" name="cac_find_frm" id="cac_find_frm" class="has-validation-callback"  method="POST">
                        <div class="frm-row">
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">First Name</label><br/>
                                     <input class="frm_input"  placeholder="First Name" type="text" id="firstname" name="firstname" value="" required="required" data-validation="required" >
                                </div>
                            </div>
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">Last Name</label><br/>
                                     <input class="frm_input"  placeholder="Last Name" type="text" id="lastname" name="lastname" value=""  required="required" data-validation="required">
                                </div>
                            </div>
                        </div>
                        <div class="frm-row">
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">Email Address</label><br/>
                                     <input class="frm_input"  placeholder="Email" type="email" id="email" name="email" value=""  required="required" data-validation="required email">
                                </div>
                            </div>
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">Phone (OPtional)</label><br/>
                                     <input class="frm_input"  placeholder="Phone" type="text" id="phone" name="phone" value="" pattern="(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}" title="10 digit phone number" data-validation-help="e.g. 123-456-7890" data-validation="custom" data-validation-regexp="(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}" data-validation-optional="true" >
                                </div>
                            </div>
                        </div>
                        <div class="frm-row">
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">Zip code</label><br/>
                                     <input class="frm_input"  placeholder="Zipcode" required="required" data-validation="required"  type="text" id="zip" name="zip" value="" >
                                </div>
                            </div>
                            <div class="half">
                                <div class="form-control">	
                                    <label class="">Country</label><br/>
                                    <select name='country' required="" data-validation="required" class="frm_input">
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <?php 
                                             foreach($country_result as $country): ?>
                                                  <option value="<?php echo $country->country; ?>"><?php echo $country->country; ?></option>
                                        <?php  endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                             <p>By submitting the form, you agree it's okay for us to share your info with a nearby Certified Aquascape Contractor who will be in touch with you to schedule your consultation.</p>
                            
                        </div>
                        <div class="form-control">
                                  <?php wp_nonce_field('cacfrm_nonce', 'cac_nonce_field'); ?>
    		               <input type="hidden" name="cacfrm_submitted"  />
                               <input type="hidden" name="cac_frm_key" value="<?php echo $_SESSION['cac_frm_key']; ?>" />
    		               <input type="submit" name= "cacsubmit" class="sbtn" id="submit" value="Get Started Today!" />
                        </div>
                    </form>
                </div>
            </div>
           <?php 
            //$content = ob_get_clean();
           // return $content;
        }
       
      function cf_shortcode() {
            ob_start();
            frm_shortcode();
            return ob_get_clean();
        }  

        add_shortcode("aquascape-form", "cf_shortcode");
        
     }   

         /**
	 * Create Shortcode to disply closest  CAC
	 *
	 * @since    1.0.0
	 */
        
        public function closest_cac_shortcodes() {
            
            function closestcac() {
              
            if(isset($_REQUEST['cacsubmit']) && isset($_REQUEST['cac_nonce_field']) && wp_verify_nonce($_REQUEST['cac_nonce_field'], 'cacfrm_nonce')  && $_REQUEST['cac_frm_key'] == $_SESSION['cac_frm_key']) {
             

			  $_SESSION['cac_frm_key'] ='';
                
                global $wpdb;
                $tablename = $wpdb->prefix . 'cac_locator_leads';  
                $tablelatlong = $wpdb->prefix . 'cac_latlong';  
                // show welcome message ?>
               <div class="welcome_msg_container">
                    <h1 class="text-center text-green" style="text-transform:none;">Thank You!</h1>
                    <h4 class="text-center text-lt-blue">We can't wait to learn more about what you have in mind. Thanks for trusting us to help get your  project going. We know you're gonna love it.</h4>
                </div>
             <?php    // sanitize form values
		$fname    = sanitize_text_field( $_POST["firstname"] );
		$lname    = sanitize_text_field( $_POST["lastname"] );
		$email    = sanitize_email( $_POST["email"] );
		$phone    = sanitize_text_field( $_POST["phone"] );
		$zipcode  = sanitize_text_field( $_POST["zip"] );
		$country  = sanitize_text_field( $_POST["country"] );
                
                $phone = Aquascape_Public::format_phone_US($phone);
                $latlang =   Aquascape_Public::getlatitude($zipcode,$country);
                $address  =  Aquascape_Public::getcitystate($zipcode);
                $city  = $address['city'];
                $state = $address['state'];
                $lat = $latlang['lat'];
                $lang = $latlang['lng'];
                if($country!='US' && $country!='CA'){
                 
                    $query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1' AND country='$country' order by cac_type ASC LIMIT 4 ", $lat, $lang, $lat);
                }else{
                    //check chikacoland area cac
//                    if($zipcode=="60174"){
//                        $query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1'  HAVING distance < 75  LIMIT 3 ", $lat, $lang, $lat);
//                    }else{
//                         $query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1'  AND zip!='60174'  HAVING distance < 75  LIMIT 3 ", $lat, $lang, $lat);
//                    }
                    $query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1' AND zip!='60174'   HAVING distance < 75  ORDER BY cac_type, distance LIMIT 3", $lat, $lang, $lat);
                    }
                   //check chicagoland area cac
                    $chicagoresult ='';
                    if($zipcode=="60174"){
                        $chicago_query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1'  HAVING distance < 45  LIMIT 1 ", $lat, $lang, $lat);
                        $chicagoresult = $wpdb->get_row($chicago_query);
                    }
                   
                //$query = sprintf("SELECT id,cac_type,url,company_name,phone_primary,email,city,state,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM `wp_cac_application_entries`  where (cac_type='Master Certified Aquascape Contractor' OR cac_type='Professional Certified Aquascape Contractor' ) AND active='1'  HAVING distance < 75 ORDER BY distance LIMIT 3 ", $lat, $lang, $lat);
                $result = $wpdb->get_results($query);
                $count = 1;
                if (count($result) > 0) {
                    $data = array(
                        'firstname' => $fname,
                        'lastname' => $lname,
                        'email' => $email,
                        'phone' => $phone,
                        'zip' => $zipcode,
                        'country' => $country,
                        'city' => $city,
                        'state' => $state,
                    );
                    //Insert in database
                    $inserted = $wpdb->insert($tablename, $data);
                    $user_id =  $wpdb->insert_id;
                    // Add Google Analitics Event
                    ?>
                  <script> 
                    ga('send', 'event', {
                    eventCategory: 'CAC Locator Form',
                    eventAction: 'Submit',
                    eventLabel: 'CAC Form Success'
                    });
                    </script>
                     <?php if($country!='US' && $country!='CA'){ ?>
                        <div class="container">
                            <div class="top-bottom-border text-center">
                                <h4>Outside of North America?</h4>
                                <p class="text-center">Our network of trustworthy Certified Aquascape Contractors (CAC) continues to grow world-wide and can currently be found in several areas outside of North America. Please feel free to reach out to any of the Certified Aquascape Contractors listed below, or contact our Customer Care department for assistance by <a href="javascript: void(0)" class="zenbox_contact">clicking here</a>.</p>
                            </div>
                        </div>
                    <?php  }else{?>
                           <div class="text_container">
                                           <div class="top-bottom-border text-center">
                                            <h4>Here's What Happens Next</h4>
                                            <p class="text-center">
                                                Right now, your info is on its way to the trustworthy hands of a couple of local Certified Aquascape Contractors (CAC). They will be in touch with you soon to schedule your in-person consultation.
                                            </p>
                                          </div>
                                 </div>
                    <?php }?>
                              
                        <div  class="location-table">
                         <table>
                             <tr>
                                 <th></th>
                                 <th>Company Name</th>
                                 <th>Address</th>
                                 <th>Phone</th>
                                <?php if($country =='US' || $country =='CA' ){?> <th>Distance</th><?php } ?>
                                 <th></th>
                             </tr>
                        <?php foreach ($result as $data) : ?>
                               <tr>
                                 <td><span class="step-green"> <?php  echo $count;?> </span></td>
                                  <td>
                                      <?php 
                                            if (strpos($data->url, 'http') !== false){ $url = $data->url;} else { $url = "http://".$data->url;}
                                       ?>
                                      <a href="<?php echo $url ;?>" target="_blank"> <?php  echo $data->company_name;?></a>
                                   <br>
                                  <strong><?php echo $data->cac_type ;?></strong>
                                  </td>
                                  <td> <?php  echo $data->city."," .$data->state;?></td>
                                  <td> 
                                      <a href="tel://<?php echo $data->phone_primary;?>"><?php  echo $data->phone_primary;?></a>
                                      </td>
                                   <?php  if($country=='US' || $country=='CA'){ ?>  <td> <?php  echo round($data->distance,2) .' Miles';?></td><?php } ?>
                                   <td>
                                      <a class="actionButton2" href="mailto:<?php echo $data->email;?>?subject=Aquascape%20Project%20Question"><span class="glyphicon glyphicon-envelope"></span> Email</a>

                                  </td>
                               </tr>
                        <?php $cacid[] = $data->id;  $count++; endforeach; 
                               if(!empty($chicagoresult)) {?>
                               <tr class="container simple-table alert alert-info">
                                  <td colspan="6"> <p>If you are searching for the local <strong>Aquascape Chicagoland Construction</strong> team, they can be reached at...</p></td>
                                </tr>
                                <tr>
                                     <?php 
                                            if (strpos($chicagoresult->url, 'http') !== false){ $curl = $chicagoresult->url;} else { $curl = "http://".$chicagoresult->url;}
                                      ?>
                                    <td colspan="2"><a href="<?php echo $curl ;?>" target="_blank">
                                       <?php  echo $chicagoresult->company_name;?></a><br>
                                       <strong><?php echo $chicagoresult->cac_type ;?> </strong> 
                                    </td>                       
                                  <td>
                                     <?php  echo $chicagoresult->city."," .$chicagoresult->state;?>
                                  </td>
                                <td><a href="tel://<?php echo $chicagoresult->phone_primary;?>"><?php echo $chicagoresult->phone_primary;?></a></td>
                                <td><?php  echo round($chicagoresult->distance,2) .' Miles';?></td>
                                  <td>
                                      <a class="actionButton2" href="mailto:<?php echo $chicagoresult->email;?>?subject=Aquascape%20CAC%20Locator%20Referral">Email</a>
                                  </td>
                              </tr>
                               <?php 
                                    $chicago_id = $chicagoresult->id;       
                                    Aquascape_Public::send_user_email($user_id,$result,$chicagoresult);
                                    Aquascape_Public::send_cac_locator_email($user_id,$result,$chicagoresult);
                                    //Sore Id in hidden Input and make ajax call
                                    $cacid[] =  $chicago_id;                      
                                    $cacid = implode(",",$cacid);
                                        $data = array(
                                       'cacID' =>  $cacid
                                         );
                                    $updated = $wpdb->update($tablename, $data, array('id' => $user_id));
                               }else{
                                    Aquascape_Public::send_user_email($user_id,$result);                  
                                    Aquascape_Public::send_cac_locator_email($user_id,$result);                  
                                    $cacid = implode(",",$cacid);
                                        $data = array(
                                       'cacID' =>  $cacid
                                         );
                                    $updated = $wpdb->update($tablename, $data, array('id' => $user_id));
                             }
                        ?>
                        </table>
                     </div>
               <?php } else {?>
                    <script> 
                    ga('send', 'event', {
                    eventCategory: 'CAC Locator Form No Result',
                    eventAction: 'Submit',
                    eventLabel: 'CAC No Result Shown'
                    });
                    </script>
                    <div class="top-bottom-border text-center">
                        <div class="alert alert-warning">
                            Sorry, but we were unable to locate any contractors in your area. Please <a href="http://www.aquascapeinc.com/find-aquascape-certified-contractors">click here</a> to try another location or feel free to contact our Customer Care department for assistance by <a href="javascript:%20void(0)" class="zenbox_contact">clicking here</a>.
                        </div>
                    </div>
               <?php  }
                 }else{?>
                    <div class="welcome_msg_container">
                       <h1 class="text-center text-green" style="text-transform:none;">Thank You!</h1>
                       <h4 class="text-center text-lt-blue">We can't wait to learn more about what you have in mind. Thanks for trusting us to help get your  project going. We know you're gonna love it.</h4>
                   </div>
                     <div class="top-bottom-border text-center">
                        <div class="alert alert-warning">
                            Sorry, but we were unable to locate any contractors in your area. Please <a href="http://www.aquascapeinc.com/find-aquascape-certified-contractors">click here</a> to try another location or feel free to contact our Customer Care department for assistance by <a href="javascript:%20void(0)" class="zenbox_contact">clicking here</a>.
                        </div>
                    </div>
                 <?php }
                 if ( !empty($result) &&  count($result) > 0) {
                     ?>
                      <div style="margin:50px auto;">
                               <p class="text-center"><strong>If for any reason you aren't able to connect with a CAC or have any questions and feedback along the way, please <a href="javascript:%20void(0)" class="zenbox_contact">connect</a> with us directly. We'd love to hear from you.</strong></p>
                          </div>
                       <!-- Start Water Feature Dream Book -->  
                         <div class="blue_box_dream">                      
                            <div class="dream_book_section">
                               <h3><?php  !empty($fname) ? $fname.', ' : ''; ?> Get a FREE Copy of Our Water Feature Dream Book</h3>
                               <p style="color:#004990;">Let us know how you learned about Aquascape, and we’ll mail you a copy of our beautiful Water Feature Dream Book, free of charge. </p>
                               <form class="has-validation-callback" id="cacLocator_offer" role="form" method="POST" name="cacLocator_offer">
                                  <div class="form-group has-success">
                                     <label for="referral" class=" control-label"><span class="text-primary">*</span> How did you hear about us?</label>
                                     <select class="form-control valid" name="referral" id="referral" required="" data-validation="required" style="">
                                        <option value="">Select</option>
                                        <option value="Customer Care">Contacted Customer Care department</option>
                                        <option value="Search Engine">Search Engine (Google, Yahoo, etc.)</option>
                                        <option value="Pond Stars">Nat Geo Wild's Pond Stars</option>
                                        <option value="Pond Tour">Pond Tour</option>
                                        <option value="Retail Store">Retail Store</option>
                                        <option value="Garden Show">Garden Show</option>
                                        <option value="Social Media">Social Media (Facebook, Twitter, etc.)</option>
                                        <option value="Local Paper">Local Paper</option>
                                        <option value="Other">Other</option>
                                     </select>
                                  </div>
                                  <div id="completeForm" style="display: none;">
                                     <p class="white">Thanks for that information! Let us know your mailing address, and we’ll send you a free copy of our Water Feature Dream Book.</p>
                                     <div class="row">
                                        <div class="form-group">
                                           <label for="address" class=" control-label"><span class="text-primary">*</span> Address</label>
                                           <input class="form-control" id="address" name="address" required="required" data-validation="required" type="text">
                                        </div>
                                        <div class="form-group">
                                           <label for="city" class=" control-label"><span class="text-primary">*</span> City</label>
                                           <input class="form-control" id="city" name="city" required="required" value="" data-validation="required" type="text">
                                        </div>
                                        <div class="form-group">
                                           <label for="state" class=" control-label"><span class="text-primary">*</span> State / Province</label>
                                        <?php  if($country=='US' || $country=='CA'){ ?> 
                                           <select class="form-control" id="state" name="state" required="" data-validation="required">
                                           <optgroup label="United States">
                                                <option value="">Select State/ Province</option>
                                                 <option value="AL">Alabama</option>
                                                 <option value="AK">Alaska</option>
                                                 <option value="AZ">Arizona</option>
                                                 <option value="AR">Arkansas</option>
                                                 <option value="CA">California</option>
                                                 <option value="CO">Colorado</option>
                                                 <option value="CT">Connecticut</option>
                                                 <option value="DE">Delaware</option>
                                                 <option value="DC">Washington D.C.</option>
                                                 <option value="FL">Florida</option>
                                                 <option value="GA">Georgia</option>
                                                 <option value="HI">Hawaii</option>
                                                 <option value="ID">Idaho</option>
                                                 <option value="IL">Illinois</option>
                                                 <option value="IN">Indiana</option>
                                                 <option value="IA">Iowa</option>
                                                 <option value="KS">Kansas</option>
                                                 <option value="KY">Kentucky</option>
                                                 <option value="LA">Louisiana</option>
                                                 <option value="ME">Maine</option>
                                                 <option value="MD">Maryland</option>
                                                 <option value="MA">Massachusetts</option>
                                                 <option value="MI">Michigan</option>
                                                 <option value="MN">Minnesota</option>
                                                 <option value="MS">Mississippi</option>
                                                 <option value="MO">Missouri</option>
                                                 <option value="MT">Montana</option>
                                                 <option value="NE">Nebraska</option>
                                                 <option value="NV">Nevada</option>
                                                 <option value="NH">New Hampshire</option>
                                                 <option value="NJ">New Jersey</option>
                                                 <option value="NM">New Mexico</option>
                                                 <option value="NY">New York</option>
                                                 <option value="NC">North Carolina</option>
                                                 <option value="ND">North Dakota</option>
                                                 <option value="OH">Ohio</option>
                                                 <option value="OK">Oklahoma</option>
                                                 <option value="OR">Oregon</option>
                                                 <option value="PA">Pennsylvania</option>
                                                 <option value="RI">Rhode Island</option>
                                                 <option value="SC">South Carolina</option>
                                                 <option value="SD">South Dakota</option>
                                                 <option value="TN">Tennessee</option>
                                                 <option value="TX">Texas</option>
                                                 <option value="UT">Utah</option>
                                                 <option value="VT">Vermont</option>
                                                 <option value="VA">Virginia</option>
                                                 <option value="WA">Washington</option>
                                                 <option value="WV">West Virginia</option>
                                                 <option value="WI">Wisconsin</option>
                                                 <option value="WY">Wyoming</option>
                                              </optgroup>
                                              <optgroup label="Canada">
                                                 <option value="AB">Alberta</option>
                                                 <option value="BC">British Columbia</option>
                                                 <option value="MB">Manitoba</option>
                                                 <option value="NB">New Brunswick</option>
                                                 <option value="NF">Newfoundland</option>
                                                 <option value="NT">Northwest Territories</option>
                                                 <option value="NS">Nova Scotia</option>
                                                 <option value="NU">Nunavut</option>
                                                 <option value="ON">Ontario</option>
                                                 <option value="PE">Prince Edward Island</option>
                                                 <option value="QC">Qu�bec</option>
                                                 <option value="SK">Saskatchewan</option>
                                                 <option value="YT">Yukon Territory</option>
                                              </optgroup>
                                           </select>
                                            <?php }else{ ?>
                                                 <input class="form-control" id="state" name="state" required="required" value="" data-validation="required" type="text">
                                        <?php   }?>
                                        </div>
                                        <div class="form-group">
                                           <label for="zipcode" class=" control-label"><span class="text-primary">*</span> Zip / Postal Code</label>
                                           <input class="form-control" id="zipcode" name="zipcode" required="required" value="<?php if(!empty($zipcode)){ echo $zipcode;}?>" data-validation="required" type="text">
                                        </div>
                                        <div class="form-group">
                                           <label for="phone" class=" control-label"><span class="text-primary">*</span> Phone</label>
                                           <input class="form-control has-help-txt" id="phone" name="phone" pattern="(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}" title="10 digit phone number" data-validation-help="e.g. 123-456-7890" required="required" value="<?php if(!empty($phone)){ echo $phone;}?>" data-validation="required custom" data-validation-regexp="(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}" type="tel">
                                        </div>
                                        <div class="form-group">
                                            <input value="<?php if(!empty($email)){ echo $email;}?>" name="orderEmail" type="hidden">
                                            <input value="<?php if(!empty($user_id)){ echo $user_id;}?>" name="usr_id" type="hidden">
                                          <input id="cacLocator_offer_button"  name="cacoffer_submit" value="Submit" class="btn btn-default" type="button">
                                          <?php wp_nonce_field('cacoffer_nonce', 'cacoffer_nonce_field'); ?>
                                        </div>
                                     </div>
                                  </div>
                               </form>
                               <div id="cacLocator_offer_message" style="margin-top:10px;"></div>
                            </div>
                         </div>  
                       <!-- End Water Feature Dream Book-->   
                 <?php }
            }
             add_shortcode("aquascape-closest-cac", "closestcac");
        }
         /**
	 * Process Ebook form data
	 *
	 * @since    1.0.0
	 */
        
        public function ebook_form_data() {
            if (isset($_REQUEST)) {
                  global $wpdb;
                 $tablename = $wpdb->prefix . 'cac_locator_leads';  
                  parse_str($_REQUEST['data'], $output);
                  $email_id = $output['orderEmail'];
                  // check already  subscribe or not
                   $chk_exist = $wpdb->get_results( "SELECT * FROM $tablename  where (offer='1' AND email='$email_id')" );
                   if (count($chk_exist)> 0){
                       echo "exits";
                       exit;
                   }else{
                        $data = array(
                            'address1'    =>  $output['address'],
                            'city'        =>  $output['city'],
                            'state'        =>  $output['state'],
                            'zip'         =>  $output['zipcode'],
                            'phone'       =>  $output['phone'],
                            'offer'  =>  1,
                        );
                        $updated = $wpdb->update($tablename, $data, array('id' => $output['usr_id']));
                        echo "Sucess";
                       exit;
                }
            }  
        }
        
         /**
	 * Send User Email to list of cac found
	 *
	 * @since    1.0.0
	 */
        public static function send_user_email($id, $cac_id,$cicago_id = NULL){
           ob_start(); 
           global $wpdb;
           $visitor_tablename = $wpdb->prefix . 'cac_locator_leads';
           $visitor_details = $wpdb->get_row("SELECT firstname,lastname,email FROM $visitor_tablename where id= $id");
           $name = $visitor_details->firstname . ' ' . $visitor_details->lastname;
           $visitor_email = $visitor_details->email;
          ?>
                <html>
                   <head>
                      <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                      <title>Matching Certified Aquascape Contractors</title>
                   </head>
                   <body  style="-webkit-font-smoothing:antialiased; bgcolor:'#FFFFFF';">
                      <style type='text/css'>
                         body {-webkit-font-smoothing:antialiased; background-color: #FFFFFF}
                         /* override changes made by email clients */
                        .ExternalClass * {line-height: 123%} 
                        .outlook {line-height: 124%}
                         body { -webkit-font-smoothing: antialiased; background-color: #FFFFFF }
                         .ReadMsgBody {WIDTH: 100%}
                         .ExternalClass {WIDTH: 100%}
                         .outlook {line-height: 95%}
                         span.yshortcuts { color:#000; background-color:none;border:none;}
                         span.yshortcuts:hover,span.yshortcuts:active,span.yshortcuts:focus {color:#000; background-color:none; border:none;}
                         /* end override */
                         @media only screen
                         and (min-device-width : 320px)
                         and (max-device-width : 480px), (max-width : 480px){
                         *[class].content { width: 100%;}
                         *[class].preheader {text-align:center !important;margin-bottom:0 !important;}
                         *[class].banner { width: 100%;height: auto;}
                         *[class].mobileStack {display:block;width: 100% !important;margin-bottom:15px;}
                         *[class].mobileHide{display:none !important;}
                      </style>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='-webkit-font-smoothing:antialiased;'>
                         <tr>
                            <td align='center'>
                               <!--Wrapper-->
                               <table class='content' width=640' align='center' cellpadding='0' cellspacing='0' border='0' bgcolor='#ffffff'>
                                  <tr>
                                     <td>
                                        <?php  $header_content =  get_option('ac_visitor_email_header'); 
                                               echo  str_replace('%Visitorname%', $name, $header_content);
                                        ?>
                                        <table width='100%' border='0' cellpadding='10' cellspacing='0'>
                                           <tr>
                                              <td style='padding-bottom:20px;'>
                                                 <table cellspacing='0' cellpadding='0' border='0' width='100%'>
                                                    <tbody>
                                                       <tr>
                                                          <td style='background-color: #f6f6f6; padding: 25px;'>
                                                             <table width='100%' border='0' cellpadding='5' cellspacing='5'>
                                                               <?php $count =1;
                                                                   foreach ($cac_id as $retrieved_data):?>
                                                                         <tr>
                                                                                <td valign="top" width="40"><img src="<?php echo home_url();?>/wp-content/plugins/aquascape/public/css/list-icon-<?php echo $count; ?>.gif"></td>
                                                                                <td valign="top" style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549; text-align:left;">
                                                                                   <span  style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color:#78bc31; text-align:left; line-height: 28px;">
                                                                                   <strong><?php echo $retrieved_data->company_name; ?></strong></span><br /><strong><?php echo $retrieved_data->cac_type; ?></strong><br /><?php echo $retrieved_data->city."," .$retrieved_data->state;?><br />Phone: <?php echo $retrieved_data->phone_primary; ?><br />
                                                                                   Email: <a href="mailto:<?php echo $retrieved_data->email; ?>?subject=Aquascape%20Projec=t%20Question" target="_blank"><?php echo $retrieved_data->email; ?></a><br />
                                                                                   Website: <a href="<?php echo $retrieved_data->url; ?>" target="_blank"><?php echo $retrieved_data->url; ?></a><br />
                                                                                </td>
                                                                        </tr>
                                                                        <?php 
                                                                           if($count < 3) :  ?>
                                                                            <tr>
                                                                                <td valign="top" width="40">&nbsp;</td>
                                                                                     <td>
                                                                                      <hr/>
                                                                                </td>
                                                                            </tr>
                                                                          <?php  endif;
                                                                        ?>
                                                                        
                                                                <?php  $count++; endforeach; ?>
                                                             </table>
                                                             <br/>
                                                             <hr />
                                                             <br />
                                                          <?php if(isset($cicago_id)) { ?>   
                                                             <table width='100%' border='0' cellpadding='5' cellspacing='5'>
                                                                <tr>
                                                                   <td colspan='2' align='left' style='font-family:Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549; text-align:left;'>
                                                                      If you are searching for the local <strong>Aquascape Chicagola=
                                                                      nd Construction</strong> team, they can be reached at...
                                                                   </td>
                                                                </tr>
                                                                <tr>
                                                                   <td valign="top" width="40">&nbsp;</td>
                                                                   <td valign="top" style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549;text-align:left;">
                                                                      <span  style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color:#78bc31; text-align:left; line-height: 28px;"><strong><?php echo $cicago_id->company_name; ?></strong></span><br /><strong>
                                                                      <?php echo $cicago_id->cac_type; ?></strong><br /><?php echo $cicago_id->city."," .$cicago_id->state;?><br />
                                                                      Phone: <?php echo $cicago_id->phone_primary; ?><br />Email: <a href="mailto:<?php echo $cicago_id->email; ?>?subject=3DAquascape%20Project%20Question" target="_blank">
                                                                      <?php echo $cicago_id->email; ?></a><br />Website: <a href="<?php echo $cicago_id->url; ?>" target="_blank"><?php echo $cicago_id->url; ?></a><br />
                                                                   </td>
                                                                </tr>
                                                             </table>
                                                          <?php } ?>   
                                                          </td>
                                                       </tr>
                                                    </tbody>
                                                 </table>
                                              </td>
                                           </tr>
                                        </table>
                                        <?php  echo  get_option('ac_visitor_email_footer'); ?>
                                        <!--End of Wrapper -->
                                     </td>
                                  </tr>
                               </table>
                            </td>
                         </tr>
                      </table>
                   </body>
                </html>
         <?php  $body = ob_get_clean();
        $to = 'narolainfotech2016@gmail.com';
      //$to = 'jjames@aquascapeinc.com';
        $subject = get_option('ac_visitor_email_subject');
        $admin_email = get_the_author_meta( 'user_email' );
        $title = get_bloginfo( 'name' );
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From:',$title,$admin_email);
        if (esc_attr(get_option('aqua_cac_testing_mod')) == "1"){
            wp_mail($to, $subject, $body, $headers);
        }else{
              wp_mail($visitor_email, $subject, $body, $headers);
        }
    }
    
        /**
	 * Send Email to Found CAC Locator 
	 *
	 * @since    1.0.0
	 */
        public static function send_cac_locator_email($id, $cac_id,$cicago_id = NULL){
           ob_start(); 
           global $wpdb;
           $visitor_tablename = $wpdb->prefix . 'cac_locator_leads';
           $visitor_details = $wpdb->get_row("SELECT firstname,lastname,email,zip,phone FROM $visitor_tablename where id= $id");
           $name = $visitor_details->firstname . ' ' . $visitor_details->lastname;
           $visitor_email = $visitor_details->email;
          ?>
                <html>
                   <head>
                      <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                      <title>Matching Certified Aquascape Contractors</title>
                   </head>
                   <body  style="-webkit-font-smoothing:antialiased; bgcolor:'#FFFFFF';">
                      <style type='text/css'>
                         body {-webkit-font-smoothing:antialiased; background-color: #FFFFFF}
                         /* override changes made by email clients */
                        .ExternalClass * {line-height: 123%} 
                        .outlook {line-height: 124%}
                         body { -webkit-font-smoothing: antialiased; background-color: #FFFFFF }
                         .ReadMsgBody {WIDTH: 100%}
                         .ExternalClass {WIDTH: 100%}
                         .outlook {line-height: 95%}
                         span.yshortcuts { color:#000; background-color:none;border:none;}
                         span.yshortcuts:hover,span.yshortcuts:active,span.yshortcuts:focus {color:#000; background-color:none; border:none;}
                         /* end override */
                         @media only screen
                         and (min-device-width : 320px)
                         and (max-device-width : 480px), (max-width : 480px){
                         *[class].content { width: 100%;}
                         *[class].preheader {text-align:center !important;margin-bottom:0 !important;}
                         *[class].banner { width: 100%;height: auto;}
                         *[class].mobileStack {display:block;width: 100% !important;margin-bottom:15px;}
                         *[class].mobileHide{display:none !important;}
                      </style>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='-webkit-font-smoothing:antialiased;'>
                         <tr>
                            <td align='center'>
                               <!--Wrapper-->
                               <table class='content' width=640' align='center' cellpadding='0' cellspacing='0' border='0' bgcolor='#ffffff'>
                                  <tr>
                                     <td>
                                        <?php  $header_content =  get_option('ac_contractor_email_header'); 
                                               echo  str_replace('%Visitorname%', $name, $header_content);
                                        ?>
                                          <table width='100%' border='0' cellpadding='10' cellspacing='0' bgcolor='#FFFDE2'>
                                              <tr>
                                                    <td valign="top" width="40"></td>
                                                      <td valign="top" style="font-family: Helvetica, arial, sans-serif; font-size:14px; color: #3d4549; text-align:left;">
                                                       <strong><?php echo $name;?></strong><br />Zip/Postal code : <?php echo $visitor_details->zip;?><br />
                                                       <?php if(!empty($visitor_details->email)) {?>  Email: <a href="mailto:<?php echo $visitor_details->email;?>?subject=Aquascape%20Project%20Question" target="_blank">
                                                       <?php echo $visitor_details->email;?></a><br /><?php } ?>
                                                    <?php if(!empty($visitor_details->phone)) {?>    Phone: <?php echo $visitor_details->phone;?><br />
                                                    <br /> <?php } ?>
                                                      </td>
                                                </tr>
                                         </table>
                                        <table width='100%' border='0' cellpadding='10' cellspacing='0'>
                                           <tr>
                                              <td style='padding-bottom:20px;'>
                                                 <table cellspacing='0' cellpadding='0' border='0' width='100%'>
                                                    <tbody>
                                                       <tr>
                                                          <td style='background-color: #f6f6f6; padding: 25px;'>
                                                             <table width='100%' border='0' cellpadding='5' cellspacing='5'>
                                                               <?php $count =1;
                                                                   foreach ($cac_id as $retrieved_data):?>
                                                                         <tr>
                                                                                <td valign="top" width="40"><img src="<?php echo home_url();?>/wp-content/plugins/aquascape/public/css/list-icon-<?php echo $count; ?>.gif"></td>
                                                                                <td valign="top" style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549; text-align:left;">
                                                                                   <span  style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color:#78bc31; text-align:left; line-height: 28px;">
                                                                                   <strong><?php echo $retrieved_data->company_name; ?></strong></span><br /><strong><?php echo $retrieved_data->cac_type; ?></strong><br /><?php echo $retrieved_data->city."," .$retrieved_data->state;?><br />Phone: <?php echo $retrieved_data->phone_primary; ?><br />
                                                                                   Email: <a href="mailto:<?php echo $retrieved_data->email; ?>?subject=Aquascape%20Projec=t%20Question" target="_blank"><?php echo $retrieved_data->email; ?></a><br />
                                                                                   Website: <a href="<?php echo $retrieved_data->url; ?>" target="_blank"><?php echo $retrieved_data->url; ?></a><br />
                                                                                </td>
                                                                        </tr>
                                                                        <?php 
                                                                           if($count < 3) :  ?>
                                                                            <tr>
                                                                                <td valign="top" width="40">&nbsp;</td>
                                                                                     <td>
                                                                                      <hr/>
                                                                                </td>
                                                                            </tr>
                                                                          <?php  endif; ?>
                                                                <?php  $cac_emaiiids[] = $retrieved_data->email; $count++; endforeach; ?>
                                                             </table>
                                                             <br/>
                                                             <hr />
                                                             <br />
                                                         <?php if(isset($cicago_id)) { ?>   
                                                             <table width='100%' border='0' cellpadding='5' cellspacing='5'>
                                                                <tr>
                                                                   <td colspan='2' align='left' style='font-family:Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549; text-align:left;'>
                                                                      If you are searching for the local <strong>Aquascape Chicagola=
                                                                      nd Construction</strong> team, they can be reached at...
                                                                   </td>
                                                                </tr>
                                                                <tr>
                                                                   <td valign="top" width="40">&nbsp;</td>
                                                                   <td valign="top" style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #3d4549;text-align:left;">
                                                                      <span  style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color:#78bc31; text-align:left; line-height: 28px;"><strong><?php echo $cicago_id->company_name; ?></strong></span><br /><strong>
                                                                      <?php echo $cicago_id->cac_type; ?></strong><br /><?php echo $cicago_id->city."," .$cicago_id->state;?><br />
                                                                      Phone: <?php echo $cicago_id->phone_primary; ?><br />Email: <a href="mailto:<?php echo $cicago_id->email; ?>?subject=3DAquascape%20Project%20Question" target="_blank">
                                                                      <?php echo $cicago_id->email; ?></a><br />Website: <a href="<?php echo $cicago_id->url; ?>" target="_blank"><?php echo $cicago_id->url; ?></a><br />
                                                                   </td>
                                                                </tr>
                                                             </table>
                                                          <?php } ?>   
                                                          </td>
                                                       </tr>
                                                    </tbody>
                                                 </table>
                                              </td>
                                           </tr>
                                        </table>
                                        <?php  echo  get_option('ac_contractor_email_footer'); ?>
                                        <!--End of Wrapper -->
                                     </td>
                                  </tr>
                               </table>
                            </td>
                         </tr>
                      </table>
                   </body>
                </html>
         <?php  $body = ob_get_clean();
         
        $to = 'narolainfotech2016@gmail.com';
       // $to = 'jjames@aquascapeinc.com';
        $subject = get_option('ac_contractor_email_subject');
        $admin_email = get_the_author_meta( 'user_email' );
        $title = get_bloginfo( 'name' );
        
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From:';
        $headers[] = $title;
        $headers[] = $admin_email;
         foreach($cac_emaiiids as $email):
            $headers[] = 'Cc:' .$email;
        endforeach;
        if(isset($cicago_id)) {
             $headers[] = 'Cc:' .$cicago_id->email;
        }
        if (esc_attr(get_option('aqua_cac_testing_mod')) == "1"){
             $headersnew = array('Content-Type: text/html; charset=UTF-8', 'From:',$title,$admin_email);
              wp_mail($to, $subject, $body, $headersnew);
        }else{
            wp_mail($to, $subject, $body, $headers);
        }
       }
	
	 /**
	 * Distributor Find 
	 *
	 * @since  1.0.0
	 */
	public function aquascape_distributor_find() {
		include_once 'partials/aquascape-public-distibutor-find.php';
               
                add_shortcode("distributor-find", "distributor_find_shortcodes");
	}
        
        /**
	 * Get Distributor lat & lng AJAX callback functions
	 *
	 * @since  1.0.0
	 */
         public function distributor_get_latlng(){
            if($_REQUEST){ 
                parse_str($_REQUEST['data'], $output);
                $city  = $output['city'];
                $state = $output['state'];       
                $zip = $output['zipcode'];       
                if($zip){
                    global $wpdb;
                        $latlng_table_name = $wpdb->prefix . "cac_latlong";
                        $zipresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM $latlng_table_name where zipcode= %s", $zip));
                        if (count($zipresult) > 0) {
                            echo json_encode( array(
                                    "Result" => "Success",
                                    'lat' => $zipresult->lat,
                                    'lng' => $zipresult->lng
                                ));
                        }else{ 
                              $coordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($zip) . '&sensor=true');
                              $coordinates = json_decode($coordinates);
                                if ($coordinates->status == "OK") {
                                    echo json_encode(array(
                                        "Result" => "Success",
                                        'lat' => $coordinates->results[0]->geometry->location->lat,
                                        'lng' => $coordinates->results[0]->geometry->location->lng
                                    ));
                                    $infodata =   $coordinates->results[0]->formatted_address;
                                    $address = explode(",", $infodata);
                                    $city = $address[0];
                                    $stateinfo = $address[1];
                                    $state = strtok($stateinfo, " ");
                                    
                                    $data = array(
                                            'zipcode' =>  $zip,
                                            'lat'     =>  $coordinates->results[0]->geometry->location->lat,
                                            'lng'     =>  $coordinates->results[0]->geometry->location->lng,
                                            'city'    =>  $city,
                                            'state'   =>  $state
                                           );
                                 // Insert in database
                                  $inserted = $wpdb->insert($latlng_table_name, $data);
                                } else {
                                    echo json_encode(array("Result" => "Error", "Message" => "Sorry, but we could not locate your position in our database."));
                                    die();
                                }
                           } 
              }
             else if ($city && $state) {
                global $wpdb;
                   $latlng_table_name = $wpdb->prefix . "cac_latlong";
                   $zipcitystateresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM $latlng_table_name where city= %s AND state=%s",$city,$state));
                   if (count($zipcitystateresult) > 0) {
                       echo json_encode( array(
                                        "Result" => "Success",
                                        'lat' => $zipcitystateresult->lat,
                                        'lng' => $zipcitystateresult->lng
                             ));
                   }else{ 
                        $coordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($zip .  ", " . $city) . '&sensor=true');
                        $coordinates = json_decode($coordinates);
                        if($coordinates->status == "OK" )
                        {
                           echo json_encode( array(
                           "Result" => "Success",
                           'lat' => $coordinates->results[0]->geometry->location->lat,
                           'lng' => $coordinates->results[0]->geometry->location->lng
                         ));
                                         
                        }else{
                            echo json_encode(array("Result" => "Error", "Message" => "Sorry, but we could not locate your position in our database."));
                            die();
                        }
                    }   
             }
             else {
            // if($zip && $city && $state){
                global $wpdb;
                   $latlng_table_name = $wpdb->prefix . "cac_latlong";
                   $zipcitystateresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM $latlng_table_name where zipcode= %s AND city= %s AND state=%s",$zip,$city,$state));
                   if (count($zipcitystateresult) > 0) {
                       echo json_encode( array(
                                        "Result" => "Success",
                                        'lat' => $zipcitystateresult->lat,
                                        'lng' => $zipcitystateresult->lng
                             ));
                   }else{ 
                        $coordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($zip .  ", " . $city) . '&sensor=true');
                        $coordinates = json_decode($coordinates);
                        if($coordinates->status == "OK" )
                        {
                           echo json_encode( array(
                           "Result" => "Success",
                           'lat' => $coordinates->results[0]->geometry->location->lat,
                           'lng' => $coordinates->results[0]->geometry->location->lng
                         ));
                                         
                        }else{
                            echo json_encode(array("Result" => "Error", "Message" => "Sorry, but we could not locate your position in our database."));
                            die();
                        }
                    }   
             }
            die();
         }
       }
        
        /**
	 * Distributor Find Result AJAX callback functions
	 *
	 * @since  1.0.0
	 */
        public function distributor_result_data(){
          if($_REQUEST){ 
                parse_str($_REQUEST['data'], $output);
                global $wpdb;
                $radius    =   $output['radius'];
                $lat       =   $output['lat'];
                $lang      =   $output['lng'];    
                $tablename =  $wpdb->prefix . 'ppd';  ;
                $query = sprintf("SELECT id,company_name,phone,address,email,website,lat,lng,city,state,zip,( 3959 * acos( cos(radians(%s)) * cos(radians(lat)) * cos(radians(lng) - radians(%s)) + sin(radians(%s)) * sin(radians(lat))  ) ) AS distance FROM $tablename  where display='y'   HAVING distance < $radius  ORDER BY  distance", $lat, $lang, $lat);
                $result = $wpdb->get_results($query);
                 $mapLetter = "A";
                foreach($result as $r) :
                    if ($r->website != "N/A" && strlen($r->website)) {
                        $url = parse_url($r->website);
                        $url = "http://" . $url['host'] . $url['path'] . $url['query'];
                    } else {
                        $url = "";
                    }
                    
                    $return[]['pin'] = array(
                    'lat' =>      $r->lat,
                    'lng'      => $r->lng,
                    'mapLabel' => $mapLetter,
                    'name'     => $r->company_name,
                    'phone'    => "(" . substr($r->phone, 0, 3) . ") " . substr($r->phone, 3, 3) . "-" . substr($r->phone, 6, 4),
                    'address'  => ucwords(strtolower($r->address)),
                    'city'     => ucwords(strtolower($r->city)),
                    'state'    => strtoupper($r->state),
                    'zip'      => $r->zip,
                    'distance' => round($r->distance,2),
                    'website'  => $url
                   );$mapLetter++;
                endforeach; 
                  echo json_encode( $return );
            }
           die();
        }
        
        
        /**
	 * Ajax Request to send CAC user email
	 *
	 * @since  1.0.0
	 */
//         public function find_cac_user_email(){
//            
//         }
}