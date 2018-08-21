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
    $table = new Custom_Cac_List_Table();
    $table->prepare_items();
    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2 delete_msg" id="message"><p>' . sprintf(__('Items deleted: %d', 'cltd_example'), count($_REQUEST['id'])) . '</p></div>';
    }
?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Certified Aquascape Contractors', 'aqua_scape') ?> 
         <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=aquascape-cacmanage'); ?>"><?php _e('Add new', 'aqua_scape') ?></a>
         <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'tools.php?cac_download'); ?>"><?php _e('Export', 'aqua_scape') ?></a></h2>
          <ul class="subsubsub">
	  <li class="all"><a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=aquascape-cacentries-view-all'); ?>" class="current">View All</a></li>
	 </ul>      
       <?php echo $message; ?>
        <form id="persons-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php
               $table->search_box( 'search', 'search_id' );   
               $table->display();
            ?>
        </form>
    </div>