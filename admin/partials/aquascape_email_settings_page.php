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
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Email template settings', 'aquascape'); ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="cac_tabs" class="rml_page_post_setting">
                
                <ul class="nav-tab-wrapper">
                    <li><a href="#cac-tabs-1" class="nav-tab">EMAIL TO VISITOR</a></li>
                    <li><a href="#cac-tabs-2" class="nav-tab">EMAIL TO CONTRACTOR</a></li>   
                </ul>
                <div id="cac-tabs-1">
                  <form method="post" name="ac_visitor_email_form" class="visitor_email_form" action="options.php">
                        <?php //settings_errors(); ?>
                        <?php settings_fields('ac_visitor_email_setting'); ?>
                        <?php do_settings_sections('ac_visitor_email_setting'); ?>
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field">
                                <th scope="row"><label for="ac_visitor_email_subject"><?php _e('Visitor Email Subject', 'aquascape'); ?> </label></th>
                                <td> <input type="text" name="ac_visitor_email_subject" id="rml_email_subject" value="<?php echo esc_attr(get_option('ac_visitor_email_subject')); ?>" size="30" /></td>
                            </tr>
                            
                            <tr class="form-field">
                                <th scope="row"><label for="ac_visitor_email_subject"><?php _e('Visitor Email Header Content', 'aquascape'); ?></label></th>
                                <td><div class="email_box">
                                    <?php
                                    $content = get_option('ac_visitor_email_header');
                                    $editor_id = 'ac_visitor_email_header';

                                    wp_editor($content, $editor_id);
                                    ?>
                                </div></td>
                            </tr>
                            
                             <tr class="form-field">
                                <th scope="row"><label for="ac_visitor_email_subject"><?php _e('Visitor Email Footer Content', 'aquascape'); ?></label></th>
                                <td> <div class="email_box">
                                    <?php
                                    $content = get_option('ac_visitor_email_footer');
                                    $editor_id = 'ac_visitor_email_footer';

                                    wp_editor($content, $editor_id);
                                    ?>
                                </div></td>
                            </tr>
                        </tbody>
                    </table>
                        <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
                    </form> 
                </div>
                <div id="cac-tabs-2">
                  <form method="post" name="ac_visitor_email_form" class="visitor_email_form" action="options.php">
                        <?php //settings_errors(); ?>
                        <?php settings_fields('ac_contractor_email_setting'); ?>
                        <?php do_settings_sections('ac_contractor_email_setting'); ?>
                      <table class="form-table">
                          <tbody>
                              <tr class="form-field">
                                  <th scope="row"><label for="ac_contractor_email_subject"><?php _e('Contractor Email Subject', 'aquascape'); ?> </label></th>
                                  <td> <input type="text" name="ac_contractor_email_subject" id="rml_email_subject" value="<?php echo esc_attr(get_option('ac_contractor_email_subject')); ?>" size="30" /></td>
                              </tr>

                              <tr class="form-field">
                                  <th scope="row"><label for="ac_contractor_email_header"><?php _e('Contractor Email Header Content', 'aquascape'); ?></label></th>
                                  <td><div class="email_box">
                                          <?php
                                          $content = get_option('ac_contractor_email_header');
                                          $editor_id = 'ac_contractor_email_header';

                                          wp_editor($content, $editor_id);
                                          ?>
                                      </div></td>
                              </tr>

                              <tr class="form-field">
                                  <th scope="row"><label for="ac_contractor_email_footer"><?php _e('Contractor Email Footer Content', 'aquascape'); ?></label></th>
                                  <td> <div class="email_box">
                                          <?php
                                          $content = get_option('ac_contractor_email_footer');
                                          $editor_id = 'ac_contractor_email_footer';

                                          wp_editor($content, $editor_id);
                                          ?>
                                      </div></td>
                              </tr>
                          </tbody>
                      </table>
                        <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
                    </form>  
                </div>
            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                </div>
            </div>
        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <br class="clear">
    </div>
    <!-- #poststuff -->   
    </div>