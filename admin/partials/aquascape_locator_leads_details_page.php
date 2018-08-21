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
    $table_name = $wpdb->prefix . 'cac_locator_leads';  
     if (isset($_REQUEST['id'])) {
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
        if (!$item) {
            $notice = __('Item not found', 'aquascape');
        }
    }
    
   ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Certified Aquascape Contractors', 'aquascape') ?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=aquascape-locator-leads'); ?>"><?php _e('back to list', 'aquascape') ?></a>
    </h2>  

<div id="leads_tabs" class="leads_page_post_setting">
  <ul class="nav-tab-wrapper">
    <li><a href="#lead-tabs-1" class="nav-tab">Lead</a></li>
    <li><a href="#lead-tabs-2" class="nav-tab">Matched CACs</a></li>    
  </ul>
 
    <div id="lead-tabs-1">
   <table cellspacing="2" cellpadding="5" style="width: 100%;" class="widefat striped">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="leadinformation"><?php _e('Lead Information', 'aquascape')?></label>
        </th>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Created"><?php _e('Created', 'aquascape')?></label>
        </th>
        <td>
           <?php  echo date('F j,Y',strtotime($item['created'])); ?> 
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'aquascape')?></label>
        </th>
        <td>
           <?php  echo $item['firstname'] .' '. $item['lastname']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Address"><?php _e('Address', 'aquascape')?></label>
        </th>
        <td>
              <?php  echo $item['address1']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="City"><?php _e('City', 'aquascape')?></label>
        </th>
        <td>
             <?php  echo $item['city']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="State"><?php _e('State', 'aquascape')?></label>
        </th>
        <td>
             <?php  echo $item['state']; ?>
        </td>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Zip"><?php _e('Zip', 'aquascape')?></label>
        </th>
        <td>
            <?php  echo $item['zip']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Country"><?php _e('Country', 'aquascape')?></label>
        </th>
        <td>
            <?php  echo $item['country']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Phone"><?php _e('Phone', 'aquascape')?></label>
        </th>
        <td>
            <?php  echo $item['phone']; ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="newletter"><?php _e('Water Feature Dream Book', 'aquascape')?></label>
        </th>
        <td>
            <?php  if($item['offer'] == 0){ echo 'No'; }else{ echo 'Yes';}  ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="terms"><?php _e('Accepted Terms', 'aquascape')?></label>
        </th>
        <td>
            <?php  echo $item['agree']; ?>
        </td>
    </tr>
    </tbody>
   </table>
    </div>
    <div id="lead-tabs-2">
        <?php 
         $tbl_name = $wpdb->prefix . 'cac_application_entries';  
          if (isset($_REQUEST['id'])) {
            if (!empty($item['cacID'])) {?>
                <div id="mactch_cac_data">
                    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="widefat striped">
                        <tbody>
                                <?php
                                    $cacid = $item['cacID'];
                                    $results = $wpdb->get_results("SELECT * FROM $tbl_name WHERE id IN ( $cacid)");
                                    foreach ($results as $retrieved_data):
                                        echo ' <tr class="form-field">';
                                        echo '<td>'. $retrieved_data->company_name .'</td>';
                                        echo '<td>'. $retrieved_data->city .'</td>';
                                        echo '<td>'. $retrieved_data->state .'</td>';
                                        echo '<td>'. $retrieved_data->phone_primary .'</td>';
                                        echo '<td>'. $retrieved_data->email .'</td>';
                                        echo '</tr>';
                                    endforeach; ?>
                         </tbody> 
                      </table>
                              <?php  }
                             } ?>
                </div>
    </div>
    </div>
</div>