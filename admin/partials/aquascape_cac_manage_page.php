<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php    
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'cac_application_entries'; 
    $message = '';
    $notice = '';
    // this is default $item which will be used for new records
    $default = array(
    'id' => 0,
    'active' => 0,
    'approved' => 0,
    'approved_date' => NULL,
    'cac_type' => NULL,
    'macs_id' => NULL,
    'photos_job1' => 0,
    'photos_job2' => 0,
    'photos_job3' => 0,
    'receipts_submitted' => NULL,
    'company_name' => NULL,
    'first_name' => NULL,
    'last_name' => NULL,
    'address' => NULL,
    'city' => NULL,
    'state' => NULL,
    'zip' => NULL,
    'country' => NULL,
    'phone_primary' => NULL,
    'phone_mobile' => NULL,
    'phone_fax' => NULL,
    'email' => NULL,
    'url' => NULL,
    'business_age' => NULL,
    'fein' => NULL,
    'first_name_contact' => NULL,
    'last_name_contact' => NULL,
    'phone_primary_contact' => NULL,
    'email_contact' => NULL,
    'purchase_from1' => NULL,
    'purchase_from_company1' => NULL,
    'purchase_from_company1_2' => NULL,
    'purchase_from_city1' => NULL,
    'purchase_from_state1' => NULL,
    'purchase_from2' => NULL,
    'purchase_from_company2' => NULL,
    'purchase_from_company2_2' => NULL,
    'purchase_from_city2' => NULL,
    'purchase_from_state2' => NULL,
    'purchase_from3' => NULL,
    'purchase_from_company3' => NULL,
    'purchase_from_company3_2' => NULL,
    'purchase_from_city3' => NULL,
    'purchase_from_state3' => NULL,
    'purchase_from_note' => NULL,
    'annual_sales' => NULL,
    'total_employees' => NULL,
    'business_description' => NULL,
    'percent_wf' => NULL,
    'offer_water_features' => 0,
    'have_retail_location' => 0,
    'total_aquascape_installs' => NULL,
    'installs_per_season' => NULL,
    'water_features_type' => NULL,
    'aquascape_exclusive_use' => NULL,
    'agree_aquascape_construction_methodology' => NULL,
    'lat' => NULL,
    'lng' => NULL
);

    /**
    * Get Latitude and longitude using google map API 
    *
    * @since    1.0.0
    */
   function getlatitude($zip,$country = NULL) {
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
     * Get Distributor List Dropdown
     *
    * @since    1.0.0
    */
    function getdistributor() {
        global $wpdb;
        $dirstibutor_table_name = $wpdb->prefix . "ppd";
        // this will get the data from your table
        $retrieve_dist = $wpdb->get_results("SELECT id ,company_name FROM $dirstibutor_table_name group by company_name");
        if (count($retrieve_dist) > 0) {
            foreach ($retrieve_dist as $retrieved_data) {
              //  echo "<option value=$retrieved_data->id > $retrieved_data->company_name </option>";
                echo '<option value="'.$retrieved_data->id.'"'.(strcmp($retrieved_data->id,esc_attr($item['macs_id']))==0?' selected="selected"':'').'>'. $retrieved_data->company_name.'</option>';
            }
        }
    }

// here we are verifying does this request is post back and have correct nonce
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
    // combine our default item with request params
    $item = shortcode_atts($default, $_REQUEST);    
//    if (!empty($item['water_features_type'])) {
//        $water_features_type = implode(",", $item['water_features_type']);
//    }
//    unset($item['water_features_type']);
//     if (!empty($item['water_features_type'])) {
//       $item['water_features_type'] = $water_features_type;
//     }

   $water_features_type  = serialize($item['water_features_type']);
   if (strpos($water_features_type, 'N;') !== false) { $water_features_type ='NULL';}
   $item['water_features_type'] = $water_features_type;
    // validate data, and if all ok save item to database
    // if id is zero insert otherwise update
      $item_valid = aqua_scape_validate_form($item);
  if ($item_valid === true) {
    if ($item['id'] == 0) {
         $latlang    =  getlatitude($item['zip']);
	    $item['lat'] = $latlang['lat'];
            $item['lng'] = $latlang['lng'];
        
        $result = $wpdb->insert($table_name, $item);
        $item['id'] = $wpdb->insert_id;
        if ($result) {
            $message = __('Item was successfully saved', 'aquascape');
        } else {
            $notice = __('There was an error while saving item', 'aquascape');
        }
    } else {
      
        $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
        if ($result) {
            $message = __('Item was successfully updated', 'aquascape');
        } else {
            $notice = __('There was an error while updating item', 'aquascape');
        }
    }
    } else {
    // if $item_valid not true it contains error message(s)
        $notice = $item_valid;
       }
    } else {
    // if this is not post back we load item to edit or give new one to create
    $item = $default;
    if (isset($_REQUEST['id'])) {
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
     
       // echo "SELECT count(*) as total FROM  $table_lead_name WHERE cacID IN ($id)";
        if (!$item) {
            $item = $default;
            $notice = __('Item not found', 'aquascape');
        }

    }
}

// here we adding our custom meta box
    add_meta_box('cac_form_meta_box', 'Certified Aquascape Contractors', 'aqua_scape_cac_form_meta_box_handler', 'aquascape-cacmanage', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Certified Aquascape Contractors', 'aquascape') ?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=aquascape-cacentries-view-all'); ?>"><?php _e('back to list', 'aquascape') ?></a>
    </h2>

    <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif; ?>
    <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif; ?>

    <form id="form" method="POST" class="validate" novalidate="novalidate">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('aquascape-cacmanage', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'aquascape') ?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php


/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function aqua_scape_cac_form_meta_box_handler($item)
{
    global $wpdb;
    $dirstibutor_table_name = $wpdb->prefix . "ppd";
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
<div id="cac_tabs" class="rml_page_post_setting">
  <ul class="nav-tab-wrapper">
    <li><a href="#cac-tabs-1" class="nav-tab">Internal</a></li>
    <li><a href="#cac-tabs-2" class="nav-tab">CAC Info</a></li>    
    <li><a href="#cac-tabs-3" class="nav-tab">Details</a></li>    
  </ul>
 
 <div id="cac-tabs-1">
  <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Leads Sent', 'aquascape')?></label>
        </th>
        <td><?php // if(!empty($lead_result)){ echo $lead_result->total;}?>
            <?php  if (isset($_REQUEST['id'])) {
                     // Find Total Leads
                    $id = $_REQUEST['id'];
                    $table_lead_name = $wpdb->prefix . 'cac_locator_leads'; // do not forget about tables prefix
                    //$lead_result =  $wpdb->get_row("SELECT count(*) as total FROM  $table_lead_name WHERE cacID IN ($id)");
                    $lead_result =  $wpdb->get_row($wpdb->prepare("SELECT count(*)  as total FROM $table_lead_name WHERE FIND_IN_SET(%s,cacID)", $id)); 
                    echo  $lead_result->total;
             } ?>
            
        </td>
    </tr>
   <tr class="form-field">
        <th valign="top" scope="row">
            <label for="macscustomer"><?php _e('MACS Customer #', 'aquascape')?></label>
        </th>
        <td>
             <input id="macs_id" name="macs_id" type="text" style="width: 95%" value="<?php echo esc_attr($item['macs_id'])?>"
                   size="50" class="code" placeholder="<?php _e('MACS Customer', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="photosjob1"><?php _e('Job 1 Photos Approved', 'aquascape')?></label>
        </th>
        <td>
                <select id="photos_job1" name="photos_job1" class="short-input">
                    <option value="0" <?php if(esc_attr($item['photos_job1']) == "0") echo 'selected="selected"'; ?>>No</option>
                    <option value="1" <?php if(esc_attr($item['photos_job1']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                </select>
         </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="photosjob2"><?php _e('Job 2 Photos Approved ', 'aquascape')?></label>
        </th>
        <td>
            <select id="photos_job2" name="photos_job2" class="short-input">
                        <option value="0" <?php if(esc_attr($item['photos_job2']) == "0") echo 'selected="selected"'; ?>>No</option>
                        <option value="1" <?php if(esc_attr($item['photos_job2']) == "1") echo 'selected="selected"'; ?>>Yes</option>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="photosjob3"><?php _e('Job 3 Photos Approved', 'aquascape')?></label>
        </th>
        <td>
            <select id="photos_job3" name="photos_job3" class="short-input">
                    <option value="0" <?php if(esc_attr($item['photos_job3']) == "0") echo 'selected="selected"'; ?>>No</option>
                    <option value="1" <?php if(esc_attr($item['photos_job3']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="receiptssubmitted"><?php _e('Receipts/Invoices Submitted', 'aquascape')?></label>
        </th>
        <td>
            <select id="receipts_submitted" name="receipts_submitted" class="short-input">
                        <option value="0" <?php if(esc_attr($item['receipts_submitted']) == "0") echo 'selected="selected"'; ?>>No</option>
                        <option value="1" <?php if(esc_attr($item['receipts_submitted']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                    </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="approved"><?php _e('CAC Approved', 'aquascape')?></label>
        </th>
        <td>
            <select id="approved" name="approved" class="short-input">
                        <option value="0" <?php if(esc_attr($item['approved']) == "0") echo 'selected="selected"'; ?> >No</option>
                        <option value="1" <?php if(esc_attr($item['approved']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                    </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="approveddate"><?php _e('CAC Approval Date', 'aquascape')?></label>
        </th>
        <td>
             <input id="approved_date" name="approved_date" type="date" style="width: 95%" value="<?php echo esc_attr($item['approved_date'])?>"
                   size="50" class="code" placeholder="<?php _e('CAC Approval Dat', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="cactype"><?php _e('CAC Level', 'aquascape')?></label>
        </th>
        <td>
            <select id="cac_level" name="cac_type" class="short-input">
                        <option value="">Select CAC Level</option>
                        <option value="Certified Aquascape Contractor" <?php if(esc_attr($item['cac_type']) == "Certified Aquascape Contractor") echo 'selected="selected"'; ?>>Certified Aquascape Contractor</option>
                        <option value="Professional Certified Aquascape Contractor" <?php if(esc_attr($item['cac_type']) == "Professional Certified Aquascape Contractor") echo 'selected="selected"'; ?>>Professional Certified Aquascape Contractor</option>
                        <option value="Master Certified Aquascape Contractor" <?php if(esc_attr($item['cac_type']) == "Master Certified Aquascape Contractor") echo 'selected="selected"'; ?>>Master Certified Aquascape Contractor</option>
                    </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="active"><?php _e('Active', 'aquascape')?></label>
        </th>
        <td>
            <select id="active" name="active" class="short-input">
                  <option value="0" <?php if(esc_attr($item['active']) == "0") echo 'selected="selected"'; ?> >No</option>
                  <option value="1" <?php if(esc_attr($item['active']) == "1") echo 'selected="selected"'; ?>>Yes</option>
            </select>
        </td>
    </tr>
      <tr class="form-field">
        <th valign="top" scope="row">
            <label for="first_name_contact"><?php _e('Contact First Name', 'aquascape')?></label>
        </th>
        <td>
            <input id="first_name_contact" name="first_name_contact" type="text" style="width: 95%" value="<?php echo esc_attr($item['first_name_contact'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact First Name', 'aquascape')?>">
        </td>
    </tr>
      <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lastnamecontact"><?php _e('Contact Last Name', 'aquascape')?></label>
        </th>
        <td>
            <input id="last_name_contact" name="last_name_contact" type="text" style="width: 95%" value="<?php echo esc_attr($item['last_name_contact'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Last Name', 'aquascape')?>">
        </td>
    </tr>
      <tr class="form-field">
        <th valign="top" scope="row">
            <label for="phone_primary_contact"><?php _e('Contact Phone', 'aquascape')?></label>
        </th>
        <td>
            <input id="phone_primary_contact" name="phone_primary_contact" type="text" style="width: 95%" value="<?php echo esc_attr($item['phone_primary_contact'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Phone', 'aquascape')?>">
        </td>
    </tr>
	 <tr class="form-field">
        <th valign="top" scope="row">
            <label for="phone_mobile"><?php _e('Contact Mobile', 'aquascape')?></label>
        </th>
        <td>
            <input id="phone_mobile" name="phone_mobile" type="text" style="width: 95%" value="<?php echo esc_attr($item['phone_mobile'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Mobile', 'aquascape')?>">
        </td>
    </tr>
      <tr class="form-field">
        <th valign="top" scope="row">
            <label for="email_contact"><?php _e('Contact Email', 'aquascape')?></label>
        </th>
        <td>
            <input id="email_contact" name="email_contact" type="text" style="width: 95%" value="<?php echo esc_attr($item['email_contact'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Email', 'aquascape')?>">
        </td>
    </tr>
    </tbody>
</table>
  </div>  
   <div id="cac-tabs-2">
      <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field form-required">
        <th valign="top" scope="row">
            <label for="company_name"><?php _e('Company Name', 'aquascape')?></label>
        </th>
        <td>
            <input id="company_name" name="company_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['company_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Company Name', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field form-required">
        <th valign="top" scope="row">
            <label for="first_name"><?php _e('Contact First Name', 'aquascape')?></label>
        </th>
        <td>
           <input id="first_name" name="first_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['first_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact First Name', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field form-required">
        <th valign="top" scope="row">
            <label for="last_name"><?php _e('Contact Last Name', 'aquascape')?></label>
        </th>
        <td>
            <input id="last_name" name="last_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['last_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Last Name', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="address"><?php _e('Company Address', 'aquascape')?></label>
        </th>
        <td>
            <input id="address" name="address" type="text" style="width: 95%" value="<?php echo esc_attr($item['address'])?>"
                   size="50" class="code" placeholder="<?php _e('Company Address', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="city"><?php _e('City', 'aquascape')?></label>
        </th>
        <td>
            <input id="city" name="city" type="text" style="width: 95%" value="<?php echo esc_attr($item['city'])?>"
                   size="50" class="code" placeholder="<?php _e('City', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="state"><?php _e('State', 'aquascape')?></label>
        </th>
        <td>
            <input id="state" name="state" type="text" style="width: 95%" value="<?php echo esc_attr($item['state'])?>"
                   size="50" class="code" placeholder="<?php _e('State', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="zip"><?php _e('Zip/Postal Code', 'aquascape')?></label>
        </th>
        <td>
            <input id="name" name="zip" type="tel" style="width: 95%" value="<?php echo esc_attr($item['zip'])?>"
                   size="50" class="code" placeholder="<?php _e('Zip/Postal Code', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="country"><?php _e('Country', 'aquascape')?></label>
        </th>
        <td>
            <select name="country" id="country">
		  <option value="">--Select One--</option>
                  <option value="US" <?php if(esc_attr($item['country']) == "US") echo 'selected="selected"'; ?>>US</option>
                  <option value="CA" <?php if(esc_attr($item['country']) == "CA") echo 'selected="selected"'; ?>>Canada</option>
<!--                  <option value="Other" <?php //if(esc_attr($item['country']) == "Other") echo 'selected="selected"'; ?>>Other</option>-->
	           <?php
                      foreach ($countries_list as $countries):?>
                          <option value="<?php echo $countries;?>" <?php if(esc_attr($item['country']) == $countries) echo 'selected="selected"'; ?>><?php echo $countries; ?></option>
                     <?php endforeach;
                      ?>
            </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="phone_primary"><?php _e('Company Phone', 'aquascape')?></label>
        </th>
        <td>
            <input id="phone_primary" name="phone_primary" type="tel" style="width: 95%" value="<?php echo esc_attr($item['phone_primary'])?>"
                   size="50" class="code" placeholder="<?php _e('Company Phone', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="phone_fax"><?php _e('Company Fax', 'aquascape')?></label>
        </th>
        <td>
            <input id="phone_fax" name="phone_fax" type="tel" style="width: 95%" value="<?php echo esc_attr($item['phone_fax'])?>"
                   size="50" class="code" placeholder="<?php _e('Company Fax', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="email"><?php _e('Company Email', 'aquascape')?></label>
        </th>
        <td>
            <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                   size="50" class="code" placeholder="<?php _e('Company Email', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="url"><?php _e('Website Address', 'aquascape')?></label>
        </th>
        <td>
            <input id="url" name="url" type="url" style="width: 95%" value="<?php echo esc_attr($item['url'])?>"
                   size="50" class="code" placeholder="<?php _e('Website Address', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="business_age"><?php _e('Years in Business', 'aquascape')?></label>
        </th>
        <td>
            <input id="business_age" name="business_age" type="number" maxlength="3" style="width: 95%" value="<?php echo esc_attr($item['business_age'])?>"
                   size="50" class="code" placeholder="<?php _e('Years in Business', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="annual_sales"><?php _e('Annual Sales', 'aquascape')?></label>
        </th>
        <td>
            <input id="annual_sales" name="annual_sales" type="text" style="width: 95%" value="<?php echo esc_attr($item['annual_sales'])?>"
                   size="50" class="code" placeholder="<?php _e('Annual Sales', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="total_employees"><?php _e('Number of Employees', 'aquascape')?></label>
        </th>
        <td>
            <input id="total_employees" name="total_employees" type="text" style="width: 95%" value="<?php echo esc_attr($item['total_employees'])?>"
                   size="50" class="code" placeholder="<?php _e('Number of Employees', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="fein"><?php _e('Federal Employer ID Number', 'aquascape')?></label>
        </th>
        <td>
            <input id="fein" name="fein" type="text" style="width: 95%" value="<?php echo esc_attr($item['fein'])?>"
                   size="50" class="code" placeholder="<?php _e('FEIN', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_1"><?php _e('Purchase From 1', 'aquascape')?></label>
        </th>
        <td>
            <select id="purchase_from_1" name="purchase_from1" class="form-control purchase_from_select">
                     <option value="">--Select One--</option>
                     <option value="AAPD" <?php if(esc_attr($item['purchase_from1']) == "AAPD") echo 'selected="selected"'; ?>>AAPD</option>
                     <option value="Direct" <?php if(esc_attr($item['purchase_from1']) == "Direct") echo 'selected="selected"'; ?>>Aquascape Direct</option>
                     <option value="Other" <?php if(esc_attr($item['purchase_from1']) == "Other") echo 'selected="selected"'; ?>>Other</option>
            </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_company1"><?php _e('Compnay 1', 'aquascape')?></label>
        </th>
        <td>
                <input id="purchase_from_company1_2" value="<?php echo esc_attr($item['purchase_from_company1_2'])?>" class="form-control" name="purchase_from_company1_2" maxlength="255" placeholder="Company Name1" type="text" <?php if(empty(esc_attr($item['purchase_from_company1_2']))){ echo "style='display:none'"; }?>>
                <select id="purchase_from_company1" name="purchase_from_company1" class="form-control">
                    <option value="">--Select One--</option>
                    <?php 
                        $retrieve_dist = $wpdb->get_results("SELECT id ,company_name FROM $dirstibutor_table_name group by company_name");
                        if (count($retrieve_dist) > 0) {
                            foreach ($retrieve_dist as $retrieved_data) {
                                echo '<option value="' . $retrieved_data->id . '"' . (strcmp($retrieved_data->id, esc_attr($item['purchase_from_company1'])) == 0 ? ' selected="selected"' : '') . '>' . $retrieved_data->company_name . '</option>';
                            }
                        }
                   ?>
                </select>
        </td>
    </tr>
     <tr class="form-field" >
        <th valign="top" scope="row">
            <label for="purchase_from_city1"><?php _e('City 1', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_city1" name="purchase_from_city1" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_city1'])?>"
                   size="50" class="code" placeholder="<?php _e('City', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_state1"><?php _e('State 1', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_state1" name="purchase_from_state1" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_state1'])?>"
                   size="50" class="code" placeholder="<?php _e('State', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_2"><?php _e('Purchase From 2', 'aquascape')?></label>
        </th>
        <td>
                <select id="purchase_from_2" name="purchase_from2" class="form-control purchase_from_select">
                    <option value="">--Select One--</option>
                    <option value="AAPD" <?php if(esc_attr($item['purchase_from2']) == "AAPD") echo 'selected="selected"'; ?>>AAPD</option>
                    <option value="Direct" <?php if(esc_attr($item['purchase_from2']) == "Direct") echo 'selected="selected"'; ?>>Aquascape Direct</option>
                    <option value="Other" <?php if(esc_attr($item['purchase_from2']) == "Other") echo 'selected="selected"'; ?>>Other</option>
                </select>
            </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_company2"><?php _e('Company 2', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_company2_2" value="<?php echo esc_attr($item['purchase_from_company2_2'])?>" class="form-control" name="purchase_from_company2_2" maxlength="255" placeholder="Company Name2" type="text" <?php if(empty(esc_attr($item['purchase_from_company2_2']))){ echo "style='display:none'"; }?>>
             <select id="purchase_from_company2" name="purchase_from_company2" class="form-control">
                    <option value="">--Select One--</option>
                     <?php 
                        $retrieve_dist = $wpdb->get_results("SELECT id ,company_name FROM $dirstibutor_table_name group by company_name");
                        if (count($retrieve_dist) > 0) {
                            foreach ($retrieve_dist as $retrieved_data) {
                                echo '<option value="' . $retrieved_data->id . '"' . (strcmp($retrieved_data->id, esc_attr($item['purchase_from_company2'])) == 0 ? ' selected="selected"' : '') . '>' . $retrieved_data->company_name . '</option>';
                            }
                        }
                   ?>
             </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_city2"><?php _e('City 2', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_city2" name="purchase_from_city2" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_city2'])?>"
                   size="50" class="code" placeholder="<?php _e('City', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_state2"><?php _e('State 2', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_state2" name="purchase_from_state2" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_state2'])?>"
                   size="50" class="code" placeholder="<?php _e('State', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_3"><?php _e('Purchase From 3', 'aquascape')?></label>
        </th>
        <td>   
            <select id="purchase_from_3" name="purchase_from3" class="form-control purchase_from_select">
                    <option value="">--Select One--</option>
                    <option value="AAPD" <?php if(esc_attr($item['purchase_from3']) == "AAPD") echo 'selected="selected"'; ?>>AAPD</option>
                    <option value="Direct" <?php if(esc_attr($item['purchase_from3']) == "Direct") echo 'selected="selected"'; ?>>Aquascape Direct</option>
                    <option value="Other" <?php if(esc_attr($item['purchase_from3']) == "Other") echo 'selected="selected"'; ?>>Other</option>
                </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_company3"><?php _e('Company 3', 'aquascape')?></label>
        </th>
        <td> <input id="purchase_from_company3_2" value="<?php echo esc_attr($item['purchase_from_company3_2'])?>" class="form-control" name="purchase_from_company3_2" maxlength="255" placeholder="Company Name3" type="text" <?php if(empty(esc_attr($item['purchase_from_company3_2']))){ echo "style='display:none'"; }?>>
                <select id="purchase_from_company3" name="purchase_from_company3" class="form-control">
                    <option value="">--Select One--</option>
                    <?php 
                        $retrieve_dist = $wpdb->get_results("SELECT id ,company_name FROM $dirstibutor_table_name group by company_name");
                        if (count($retrieve_dist) > 0) {
                            foreach ($retrieve_dist as $retrieved_data) {
                                echo '<option value="' . $retrieved_data->id . '"' . (strcmp($retrieved_data->id, esc_attr($item['purchase_from_company3'])) == 0 ? ' selected="selected"' : '') . '>' . $retrieved_data->company_name . '</option>';
                            }
                        }
                   ?>
                </select>
            </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_city3"><?php _e('City 3', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_city3" name="purchase_from_city3" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_city3'])?>"
                   size="50" class="code" placeholder="<?php _e('City', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_state3"><?php _e('State 3', 'aquascape')?></label>
        </th>
        <td>
            <input id="purchase_from_state3" name="purchase_from_state3" type="text" style="width: 95%" value="<?php echo esc_attr($item['purchase_from_state3'])?>"
                   size="50" class="code" placeholder="<?php _e('State', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lat"><?php _e('Latitude', 'aquascape')?></label>
        </th>
        <td>
            <input id="lat" name="lat" type="text" style="width: 95%" value="<?php echo esc_attr($item['lat'])?>"
                   size="50" class="code" placeholder="<?php _e('Latitude', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lng"><?php _e('Longitude', 'aquascape')?></label>
        </th>
        <td>
            <input id="lng" name="lng" type="text" style="width: 95%" value="<?php echo esc_attr($item['lng'])?>"
                   size="50" class="code" placeholder="<?php _e('Longitude', 'aquascape')?>">
        </td>
    </tr>
    </tbody>
</table>    
  </div>  
     <div id="cac-tabs-3">
  <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="purchase_from_note"><?php _e('Comments', 'aquascape')?></label>
        </th>
        <td>
            <textarea id="purchase_from_note" name="purchase_from_note" class="form-control" rows="3"><?php echo esc_attr($item['purchase_from_note'])?></textarea>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="business_description"><?php _e('What is the primary description of your business?', 'aquascape')?></label>
        </th>
        <td>
            <textarea id="business_description" name="business_description" class="form-control" rows="3" ><?php echo esc_attr($item['business_description'])?></textarea>
        </td>
    </tr>

     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="percent_wf"><?php _e('What percentage of your business is water feature installation?', 'aquascape')?></label>
        </th>
        <td>
            <input id="name" name="percent_wf" type="text" style="width: 95%" value="<?php echo esc_attr($item['percent_wf'])?>"
                   size="50" class="code" placeholder="<?php _e('percentage of your business', 'aquascape')?>">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="offer_water_features"><?php _e('Do you offer maintenance for water features?', 'aquascape')?></label>
        </th>
        <td>
                  <select id="offer_water_features" name="offer_water_features" class="short-input">
                            <option value="0" selected="selected" <?php if(esc_attr($item['offer_water_features']) == "0") echo 'selected="selected"'; ?>>No</option>
                            <option value="1" <?php if(esc_attr($item['offer_water_features']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                   </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="have_retail_location"><?php _e('Do you have a retail location?', 'aquascape')?></label>
        </th>
        <td>
            <select id="have_retail_location" name="have_retail_location" class="short-input">
                    <option value="0" selected="selected" <?php if (esc_attr($item['have_retail_location']) == "0") echo 'selected="selected"'; ?>>No</option>
                    <option value="1" <?php if (esc_attr($item['have_retail_location']) == "1") echo 'selected="selected"'; ?>>Yes</option>
            </select>                  
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="total_aquascape_installs"><?php _e('How many water features have you installed using the complete Aquascape system?', 'aquascape')?></label>
        </th>
        <td>
            <input id="total_aquascape_installs" name="total_aquascape_installs" type="number" style="width: 95%" value="<?php echo esc_attr($item['total_aquascape_installs'])?>"
                   size="50" class="code">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="installs_per_season"><?php _e('How many water features do you install per season?', 'aquascape')?></label>
        </th>
        <td>
            <input id="installs_per_season" name="installs_per_season" type="number" style="width: 95%" value="<?php echo esc_attr($item['installs_per_season'])?>"
                   size="50" class="code">
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="water_features_1"><?php _e('What types of water features do you currently install? (check all that apply)', 'aquascape')?></label>
        </th>
        <?php 
        //echo "<pre>";
        // print_r($item['water_features_type']);
        //if (strpos( esc_attr($item['water_features_type']), 'Fountainscapes') !== false) {echo "hiii";}
        ?>
        <td>
             <input id="water_features_1" name="water_features_type[]" value="Fountainscapes" type="checkbox" <?php if (strpos( esc_attr($item['water_features_type']), 'Fountainscapes') !== false) {echo "checked='checked'";} ?>>Fountainscapes
        </td>
        <td>
             <input id="water_features_2" name="water_features_type[]" value="Ecosystem Ponds" type="checkbox" <?php if (strpos( esc_attr($item['water_features_type']), 'Ecosystem Ponds') !== false) {echo "checked='checked'";} ?>>Ecosystem Ponds
        </td>
        <td>
             <input id="water_features_3" name="water_features_type[]" value="Pondless Waterfalls" type="checkbox" <?php if (strpos( esc_attr($item['water_features_type']), 'Pondless Waterfalls') !== false) {echo "checked='checked'";} ?>>Pondless Waterfalls
        </td>
        <td>
           <input id="water_features_4" name="water_features_type[]" value="Commercial Water Features" type="checkbox" <?php if (strpos( esc_attr($item['water_features_type']), 'Commercial Water Features') !== false) {echo "checked='checked'";} ?>>Commercial Water Features
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="aquascape_exclusive_use"><?php _e('Exclusive Use of Aquascape Products', 'aquascape')?></label>
        </th>
        <td>
           <select id="aquascape_exclusive_use" name="aquascape_exclusive_use" class="short-input">
                            <option value="0" <?php if(esc_attr($item['aquascape_exclusive_use']) == "0") echo 'selected="selected"'; ?>>No</option>
                            <option value="1" <?php if(esc_attr($item['aquascape_exclusive_use']) == "1") echo 'selected="selected"'; ?>>Yes</option>
                        </select>
        </td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row">
            <label for="agree_aquascape_construction_methodology"><?php _e('Construction Methodology Compliance', 'aquascape')?></label>
        </th>
        <td>
            <select id="agree_aquascape_construction_methodology" name="agree_aquascape_construction_methodology" class="short-input">
                  <option value="0" <?php if(esc_attr($item['agree_aquascape_construction_methodology']) == "0") echo 'selected="selected"'; ?>>No</option>
                  <option value="1" <?php if(esc_attr($item['agree_aquascape_construction_methodology']) == "1") echo 'selected="selected"'; ?>>Yes</option>
             </select>
        </td>
    </tr>
    </tbody>
</table>
  </div> 
</div>

<?php
}
/**
 * Validatiion Fuction
 * 
 */
function aqua_scape_validate_form($item)
{
    $messages = array();

    if (empty($item['company_name'])) $messages[] = __('Company Name is required', 'cltd_example');
    if (empty($item['first_name'])) $messages[] = __('First Name is required', 'cltd_example');
    if (empty($item['country'])) $messages[] = __('Country is required', 'cltd_example');
    if (empty($item['zip'])) $messages[] = __('Zipcode required', 'cltd_example');
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'cltd_example');
   

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}