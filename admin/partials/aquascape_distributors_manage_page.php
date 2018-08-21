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
    $table_name = $wpdb->prefix . 'ppd'; // do not forget about tables prefix
    $message = '';
    $notice = '';
    // this is default $item which will be used for new records
    $default = array(
    'id' => 0,
    'company_name' => NULL,
    'lead_contact_email' => NULL,
    'address' => NULL,
    'city' => NULL,
    'state' => NULL,
    'zip' => NULL,
    'country' => NULL,
    'phone' => NULL,
    'email' => NULL,
    'website' => NULL,
    'display' => NULL,
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
    // here we are verifying does this request is post back and have correct nonce
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
    // combine our default item with request params
    $item = shortcode_atts($default, $_REQUEST);
    // validate data, and if all ok save item to database
    // if id is zero insert otherwise update
    
    
    $item_valid = aquascape_validate_distributor($item);
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
        if (!$item) {
            $item = $default;
            $notice = __('Item not found', 'aquascape');
        }
    }
}

// here we adding our custom meta box
    add_meta_box('distibutor_form_meta_box', 'Distributors', 'aquascape_distributors_form_meta_box_handler', 'aquascape-distributormanage', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Distributors', 'aquascape') ?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=aquascape-disributors'); ?>"><?php _e('back to list', 'aquascape') ?></a>
    </h2>

    <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif; ?>
    <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif; ?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('aquascape-distributormanage', 'normal', $item); ?>
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
function aquascape_distributors_form_meta_box_handler($item)
{
    ?>
<div id="disributormanage" class="rml_page_post_setting">
  
  <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Display', 'aquascape')?></label>
        </th>
        <td>
            <select id="display" name="display" class="short-input">
                    <option value="y" <?php if(esc_attr($item['display']) == "y") echo 'selected="selected"'; ?>>Yes</option>
                    <option value="n" <?php if(esc_attr($item['display']) == "n") echo 'selected="selected"'; ?>>No</option>
            </select>
        </td>
    </tr>
   <tr class="form-field">
        <th valign="top" scope="row">
            <label for="company"><?php _e('Company', 'aquascape')?></label>
        </th>
        <td>
             <input id="company_name" name="company_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['company_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Company', 'aquascape')?>">
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="lead_contact_email"><?php _e('Contact Email*', 'aquascape')?></label></br>
            <p class="sub_label">Multiple addresses must be separated with a comma</p>
        </th>
        <td>
           <textarea id="lead_contact_email" name="lead_contact_email" class="form-control" rows="3"><?php echo esc_attr($item['lead_contact_email'])?></textarea>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Address"><?php _e('Address', 'aquascape')?></label>
        </th>
        <td>
             <input id="address" name="address" type="text" style="width: 95%" value="<?php echo esc_attr($item['address'])?>"
                   size="50" class="code" placeholder="<?php _e('Address', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="City"><?php _e('City', 'aquascape')?></label>
        </th>
        <td>
             <input id="city" name="city" type="text" style="width: 95%" value="<?php echo esc_attr($item['city'])?>"
                   size="50" class="code" placeholder="<?php _e('City', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="State"><?php _e('State', 'aquascape')?></label>
        </th>
        <td>
             <input id="state" name="state" type="text" style="width: 95%" value="<?php echo esc_attr($item['state'])?>"
                   size="50" class="code" placeholder="<?php _e('State', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Zip"><?php _e('Zip', 'aquascape')?></label>
        </th>
        <td>
             <input id="zip" name="zip" type="text" style="width: 95%" value="<?php echo esc_attr($item['zip'])?>"
                   size="50" class="code" placeholder="<?php _e('Zip', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="country"><?php _e('Country', 'aquascape')?></label>
        </th>
        <td>
            <select id="country" name="country" class="short-input">
                    <option value="USA" <?php if(esc_attr($item['country']) == "USA") echo 'selected="selected"'; ?>>USA</option>
                    <option value="Canada" <?php if(esc_attr($item['country']) == "Canada") echo 'selected="selected"'; ?>>Canada</option>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Phone"><?php _e('Phone', 'aquascape')?></label>
        </th>
        <td>
             <input id="phone" name="phone" type="text" style="width: 95%" value="<?php echo esc_attr($item['phone'])?>"
                   size="50" class="code" placeholder="<?php _e('Phone', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Email"><?php _e('Email', 'aquascape')?></label>
        </th>
        <td>
             <input id="email" name="email" type="text" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                   size="50" class="code" placeholder="<?php _e('Email', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="website"><?php _e('Website', 'aquascape')?></label>
        </th>
        <td>
             <input id="website" name="website" type="text" style="width: 95%" value="<?php echo esc_attr($item['website'])?>"
                   size="50" class="code" placeholder="<?php _e('Website', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Latitude"><?php _e('Latitude', 'aquascape')?></label>
        </th>
        <td>
             <input id="lat" name="lat" type="text" style="width: 95%" value="<?php echo esc_attr($item['lat'])?>"
                   size="50" class="code" placeholder="<?php _e('Latitude', 'aquascape')?>">
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Longitude"><?php _e('Longitude', 'aquascape')?></label>
        </th>
        <td>
             <input id="lng" name="lng" type="text" style="width: 95%" value="<?php echo esc_attr($item['lng'])?>"
                   size="50" class="code" placeholder="<?php _e('Longitude', 'aquascape')?>">
        </td>
    </tr>
    </tbody>
</table>
</div>  
<?php
}
/**
 * Validatiion Fuction
 * 
 */
function aquascape_validate_distributor($item)
{
    $messages = array();

    if (empty($item['company_name'])) $messages[] = __('Compnay Name is required', 'aquascape');
    if (empty($item['lead_contact_email'])) $messages[] = __('Contact email is required', 'aquascape');
    if (empty($item['address'])) $messages[] = __('Address is required', 'aquascape');
    if (empty($item['city'])) $messages[] = __('City is required', 'aquascape');
    if (empty($item['state'])) $messages[] = __('State is required', 'aquascape');
    if (empty($item['zip'])) $messages[] = __('Zip is required', 'aquascape');
    if (empty($item['country'])) $messages[] = __('Country is required', 'aquascape');
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}