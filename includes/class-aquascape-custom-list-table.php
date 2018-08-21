<?php

/**
 * Create custom CAC list class
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/includes
 */

class Custom_Cac_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'cacmanage',
            'plural' => 'cacmanage',
        ));
    }
    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
        // return print_r( $item, true );
    }
   
        function column_age($item)
    {
        return '<em>' . $item['company_name'] . '</em>';
    }
    
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $user = wp_get_current_user();
        $user_per =  get_option('aqua_userpermission');
        $actions = array(
            'edit' => sprintf('<a href="?page=aquascape-cacmanage&id=%s">%s</a>', $item['id'], __('Edit', 'aqua-scape')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'aqua-scape')),
        );
        $actions_new = array(
            'edit' => sprintf('<a href="?page=aquascape-cacmanage&id=%s">%s</a>', $item['id'], __('Edit', 'aqua-scape')),
        );
		$name =   $item['first_name'].' '.$item['last_name'];
//        return sprintf('%s %s',
//            $name,
//            $this->row_actions($actions)
//        );
      if (in_array('administrator', (array) $user->roles)) {
            return sprintf('%s %s', $name, $this->row_actions($actions)
            );
        } else {
            if (!empty($user_per)) {
                if (in_array($user->ID, $user_per)) {
                    return sprintf('%s %s', $name, $this->row_actions($actions)
                    );
                } else {
                    return sprintf('%s %s', $name, $this->row_actions($actions_new)
                    );
                }
            }else{
                 return sprintf('%s %s', $name, $this->row_actions($actions_new)
                    );
            }
        }
  }
   
   
    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
   /**
     * Get Total laeds
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_lead($item)
    {
        global $wpdb;
        $id = $item['id'];
        $table_name = $wpdb->prefix . 'cac_locator_leads'; // do not forget about tables prefix
        //$result =  $wpdb->get_row(" SELECT count(*) as total FROM  $table_name WHERE cacID IN ($id)");
        $result =  $wpdb->get_row($wpdb->prepare("SELECT count(*)  as total FROM $table_name WHERE FIND_IN_SET(%s,cacID)", $id));
        return  $result->total;
		
    }
    /**
     * Show Active/Inactive status
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_active($item)
    {
        if( $item['approved'] == "0"){
           return sprintf(
            '<i class="dashicons dashicons-no" alt="f147" style="color:#FF0000"></i>'
        ); 
        }else{
            return sprintf( 
            ' <i class="dashicons dashicons-yes" alt="f147" style="color:#008000"></i>'
        );
        } 
    }
    
    /**
     * Change dataformat of Created field
     */
    function column_created($item)
    {
       return  date('F j,Y',strtotime($item['created'])); 
       
    }
    

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() 
    {
        $user = wp_get_current_user();
       // echo "<pre>";
       // print_r($user);
        $user_per =  get_option('aqua_userpermission');
        if (in_array('administrator', (array) $user->roles)) {
              $columns = array(
                'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                'name' => __('Name', 'aqua-scape'),
                'created' => __('Submitted', 'aqua-scape'),
                'company_name' => __('Company', 'aqua-scape'),
                'phone_primary' => __('Phone', 'aqua-scape'),
                'lead' => __('Lead', 'aqua-scape'),
                'active' => __('Approved', 'aqua-scape'),
            );
        } else {
            if (!empty($user_per)) {
                if (in_array($user->ID, $user_per)) {
                    $columns = array(
                        'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                        'name' => __('Name', 'aqua-scape'),
                        'created' => __('Submitted', 'aqua-scape'),
                        'company_name' => __('Company', 'aqua-scape'),
                        'phone_primary' => __('Phone', 'aqua-scape'),
                        'lead' => __('Lead', 'aqua-scape'),
                        'active' => __('Approved', 'aqua-scape'),
                    );
                } else {
                    $columns = array(
                        'name' => __('Name', 'aqua-scape'),
                        'created' => __('Submitted', 'aqua-scape'),
                        'company_name' => __('Company', 'aqua-scape'),
                        'phone_primary' => __('Phone', 'aqua-scape'),
                        'lead' => __('Lead', 'aqua-scape'),
                        'active' => __('Approved', 'aqua-scape'),
                    );
                }
            }else{
                $columns = array(
                        'name' => __('Name', 'aqua-scape'),
                        'created' => __('Submitted', 'aqua-scape'),
                        'company_name' => __('Company', 'aqua-scape'),
                        'phone_primary' => __('Phone', 'aqua-scape'),
                        'lead' => __('Lead', 'aqua-scape'),
                        'active' => __('Approved', 'aqua-scape'),
                    );
            }
        }
        return $columns;
    }
    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'first_name' => array('first_name', true),
            'company_name' => array('company_name', false),
            'phone_primary' => array('phone_primary', false),
        );
        return $sortable_columns;
    }
    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        $user = wp_get_current_user();
        $user_per =  get_option('aqua_userpermission');
       if (in_array('administrator', (array) $user->roles)) {
            $actions = array(
                'delete' => 'Delete'
            );
        } else {
            if (!empty($user_per)) {
                if (in_array($user->ID, get_option('aqua_userpermission'))) {
                    $actions = array(
                        'delete' => 'Delete'
                    );
                } else {
                    $actions = array(
                        '' => ''
                    );
                }
            }else{
                 $actions = array(
                        '' => ''
                    );
            }
        }
        return $actions;
    }
    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cac_application_entries'; // do not forget about tables prefix
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
     /**
     *  This method processes Search Box 
     * 
     */
    
    function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items())
            return;
        $input_id = $input_id . '-search-input';
      ?>
        <p class="search-box">
        <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
        <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
        <?php submit_button($text, 'button', false, false, array('id' => 'search-submit')); ?>
        </p>
        <?php
    }
    
    
    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cac_application_entries'; // do not forget about tables prefix
        $per_page = 10; // constant, how much records will be shown per page
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();
        // will be used in pagination settings
         $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
       //echo "hello";
        // prepare query params, as usual current page, order by and order direction
	$paged = isset($_REQUEST['paged']) ? ($_REQUEST['paged'] - 1 ) * $per_page : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        // Check search condition
        if(isset($_REQUEST['s'])){
             $str = explode(' ', $_REQUEST['s']);
              $srh = "where first_name LIKE '%{$str[0]}%' OR last_name LIKE '%{$str[0]}%' OR company_name LIKE '%{$str[0]}%' ";
              $total_items = $wpdb->get_var("SELECT COUNT(*)  FROM $table_name $srh ");   
              $this->items = $wpdb->get_results("SELECT * FROM $table_name $srh ORDER BY $orderby $order LIMIT $paged ,$per_page", ARRAY_A);     
        } else {
              $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
            }
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}