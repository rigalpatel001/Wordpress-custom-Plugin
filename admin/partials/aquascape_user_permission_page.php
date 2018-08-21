<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the User permission aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/admin/partials
 */
?>

<div class="wrap">
    <h2>User Access Permission</h2>
   <?php   
      $user = wp_get_current_user();
   if(in_array( 'administrator', (array) $user->roles )) { ?>
    <div class="aquascape-wrapper">
        <div class="aquascape-left">
             <form method="post" name="aqua_user_permission" class="aqua_user_permission" action="options.php">
                    <?php settings_fields('aqua_userpermission_options_setting'); ?>
                    <?php do_settings_sections('aqua_userpermission_options_setting'); ?>
                 <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="userpermission"><?php _e('Select Users', 'aquascape'); ?> </label></th>
                               
                                <td>
                                    <select name="aqua_userpermission[]"  multiple="multiple" class="widefat" size="10">
                                        <option value="">Select Users </option>
                                          <?php 
                                            $blogusers = get_users();
                                            $user_per =  get_option('aqua_userpermission');
                                            foreach ( $blogusers as $user ){
                                               $id = "'".$user->ID."'";
                                                ?>
                                                <option value="<?php echo $user->ID;?>" <?php if(!empty($user_per)){ if (in_array($user->ID, get_option('aqua_userpermission'))) {echo 'selected="selected"'; }}?> ><?php echo $user->user_nicename; ?></option>
                                            <?php } ?>
                                  </select>
                                </td>  
                            </tr>
                        </tbody>
                  </table>
                <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
             </form> 
        </div>
        <div class="aquascape-right">
        </div>
    </div>
   <?php } else{ ?>
    <div class="notice notice-error"><p><?php printf( esc_attr__( 'You Do not have sufficient permission to access this page..', 'aquascape' ), '<code>.notice-error</code>' ); ?></p></div>
   <?php } ?>
</div>