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
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
     <div class="notice notice-warning">
       <p><span class="dashicons dashicons-warning"></span> Cron job is necessary for this plugin, you have to set <code>php -q <?php echo  ABS_AQUASCAPE; ?>cac_weekly_offer_email_cron.php</code> this cron job on your server.</p>
       <p><span class="dashicons dashicons-warning"></span> Cron job is necessary for this plugin, you have to set <code>php -q <?php echo  ABS_AQUASCAPE; ?>consumer_followup_email_cron.php</code> this cron job on your server.</p>
       <p><span class="dashicons dashicons-warning"></span> Cron job is necessary for this plugin, you have to set <code>php -q <?php echo  ABS_AQUASCAPE; ?>distributor_email_cron.php</code> this cron job on your server.</p>
    </div>
  
    <div class="aqua-scape-wrapper">
        <div class="aqua-scape-left">
             <form method="post" name="aqua_general_setting" class="aqua_general_setting" action="options.php">
                        <?php //settings_errors(); ?>
                      <?php settings_fields('aqua_general_options_setting'); ?>
                    <?php do_settings_sections('aqua_general_options_setting'); ?>
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field">
                                <th scope="row"><label for="aqua_google_recapcha_sitekey"><?php _e('Google recaptch Site Key', 'aquascape'); ?> </label></th>
                                <td> <input type="text" name="aqua_google_recapcha_sitekey" id="aqua_google_recapcha_sitekey" value="<?php echo esc_attr(get_option('aqua_google_recapcha_sitekey')); ?>" size="30" /></td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row"><label for="aqua_google_recapcha_secretkey"><?php _e('Google recaptch Secrete Key', 'aquascape'); ?> </label></th>
                                <td> <input type="text" name="aqua_google_recapcha_secretkey" id="aqua_google_recapcha_secretkey" value="<?php echo esc_attr(get_option('aqua_google_recapcha_secretkey')); ?>" size="30" /></td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row"><label for="aqua_googlemap_apikey"><?php _e('Google Map API Key', 'aquascape'); ?> </label></th>
                                <td> <input type="text" name="aqua_googlemap_apikey" id="aqua_googlemap_apikey" value="<?php echo esc_attr(get_option('aqua_googlemap_apikey')); ?>" size="30" /></td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row"><label for="aqua_cac_result_page_url"><?php _e('CAC Locator result page', 'aquascape'); ?> </label></th>
                                <td> <input type="text" name="aqua_cac_result_page_url" id="aqua_cac_result_page_url" value="<?php echo esc_attr(get_option('aqua_cac_result_page_url')); ?>" size="30" /></td>
                            </tr>
                             <tr class="form-field">
                                <th scope="row"><label for="aqua_cac_testing_mod"><?php _e('Enable Testing Mode', 'aquascape'); ?> </label></th>
                                <td>
                                    <select id="aqua_cac_testing_mod" name="aqua_cac_testing_mod" class="short-input">
                                        <option value="0" <?php if (esc_attr(get_option('aqua_cac_testing_mod')) == "0") echo 'selected="selected"'; ?>>No</option>
                                        <option value="1" <?php if (esc_attr(get_option('aqua_cac_testing_mod')) == "1") echo 'selected="selected"'; ?>>Yes</option>
                                    </select>
                                </td>
                            </tr>
                             <tr class="form-field">
                                <th scope="row"><label for="aqua_cac_google_analytics_script"><?php _e('Google Alytics Script', 'aquascape'); ?> </label></th>
                                <td>
                                    <div class="aqua_cac_google_analytics_script_box">
                                        <?php
                                        $content = get_option('aqua_cac_google_analytics_script');
                                        $editor_id = 'aqua_cac_google_analytics_script';
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
        <div class="aqua-scape-right">

            <div class="aqua-scape-right-panel">
                <h3>
                    Instructions
                </h3>
                <p>
                    
                </p>
                <h3> Short codes  </h3>
                <p class="plugin-info">
                    <strong> [aquascape-form] </strong> </br> This short code is used for find aquascape certified contractors.
                    </p>
                    <p class="plugin-info">
                        <strong> [aquascape-closest-cac]</strong>  </br> This short code is used for display closesest CAC locators
                    </p>
                    <p class="plugin-info">
                        <strong> [aquascape-cac-manage-form] </strong>  </br> This short code is used for cac application form
                    </p>
                    <p class="plugin-info">
                        <strong> [distributor-find] </strong>  </br> This short code is used for Distributor find with map location,marker
                    </p>
                    
                     <h3> Notes  </h3>
                    <p class="plugin-info">
                        <strong> [aquascape-closest-cac]</strong>  </br> This shorcode must added on "CAC Locator result page".
                    </p>
              </div>
        </div>
    </div>
</div>