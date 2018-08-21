<?php 
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the International distributor of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('International Distributor', 'aquascape'); ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div class="internation_distributor">
                <div>
                  <form method="post" name="ac_visitor_email_form" class="visitor_email_form" action="options.php">
                        <?php //settings_errors(); ?>
                        <?php settings_fields('aqua_international_distributor_options_setting'); ?>
                        <?php do_settings_sections('aqua_international_distributor_options_setting'); ?>
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field">
                                <th scope="row"><label for="ac_visitor_email_subject"><?php _e('International Distributor', 'aquascape'); ?></label></th>
                                <td>
                                    <div class="email_box">
                                    <?php
                                    $content = get_option('aqua_international_distributor');
                                    $editor_id = 'aqua_international_distributor';

                                    wp_editor($content, $editor_id);
                                    ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                        <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
                    </form> 
               </div>
             </div>
         </div>
     </div>
</div>