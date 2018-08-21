<?php

/**
 * Create custom Distributor list class
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/includes
 */

class Custom_Distributor_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'distributormanage',
            'plural' => 'distributormanage',
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
    }
   
   
    /**
     * Render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_name($item)
   {    
        $user = wp_get_current_user();
        $user_per =  get_option('aqua_userpermission');
        $actions = array(
            'edit' => sprintf('<a href="?page=aquascape-disributorsmanage&id=%s">%s</a>', $item['id'], __('Edit', 'aqua-scape')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'aqua-scape')),
        );
        $actions_new = array(
             'edit' => sprintf('<a href="?page=aquascape-disributorsmanage&id=%s">%s</a>', $item['id'], __('Edit', 'aqua-scape')),
        );
        if (in_array('administrator', (array) $user->roles)) {
            return sprintf('%s %s', $item['company_name'], $this->row_actions($actions)
            );
        } else {
            if (!empty($user_per)) {
                if (in_array($user->ID, get_option('aqua_userpermission'))) {
                    return sprintf('%s %s', $item['company_name'], $this->row_actions($actions)
                    );
                } else {
                    return sprintf('%s %s', $item['company_name'], $this->row_actions($actions_new)
                    );
                }
            } else {
                return sprintf('%s %s', $item['company_name'], $this->row_actions($actions_new)
                );
            }
        }
    }
   
   
    /**
     * Add checkbox column renders
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
     * Show Active/Inactive status
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_display($item)
    {
        if( $item['display'] == "n"){
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
     * Format Phonenumber
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_phone($item)
    {
        return sprintf( "(".substr($item['phone'], 0, 3).") ".substr($item['phone'], 3, 3)."-".substr($item['phone'], 6, 4));
    }
    
    
    /**
     * This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() 
    {
        $user = wp_get_current_user();
        $user_per =  get_option('aqua_userpermission');
         if (in_array('administrator', (array) $user->roles)) {
            $columns = array(
                'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                'name' => __('Company', 'aqua-scape'),
                'city' => __('City', 'aqua-scape'),
                'state' => __('State/Province', 'aqua-scape'),
                'phone' => __('Phone', 'aqua-scape'),
                'display' => __('Display', 'aqua-scape'),
            );
        } else {
            if (!empty($user_per)) {
                if (in_array($user->ID, get_option('aqua_userpermission'))) {
                    $columns = array(
                        'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                        'name' => __('Company', 'aqua-scape'),
                        'city' => __('City', 'aqua-scape'),
                        'state' => __('State/Province', 'aqua-scape'),
                        'phone' => __('Phone', 'aqua-scape'),
                        'display' => __('Display', 'aqua-scape'),
                    );
                }else{
                    $columns = array(
                    'name' => __('Company', 'aqua-scape'),
                    'city' => __('City', 'aqua-scape'),
                    'state' => __('State/Province', 'aqua-scape'),
                    'phone' => __('Phone', 'aqua-scape'),
                    'display' => __('Display', 'aqua-scape'),
                );
                }
            } else {
                $columns = array(
                    'name' => __('Company', 'aqua-scape'),
                    'city' => __('City', 'aqua-scape'),
                    'state' => __('State/Province', 'aqua-scape'),
                    'phone' => __('Phone', 'aqua-scape'),
                    'display' => __('Display', 'aqua-scape'),
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
            'name' => array('Company', true),
            'city' => array('City', true),
            'state' => array('State/Province', true)
        );
        return $sortable_columns;
    }
    /**
     * Get state name form code
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_state($item)
    {
       $state = array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'DC' => 'Washington D.C.',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
            'AB' => 'Alberta',
            'BC' => 'British Columbia',
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NF' => 'Newfoundland',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Qu&eacute;bec',
            'SK' => 'Saskatchewan',
            'YT' => 'Yukon Territory'
         );
        if (array_key_exists($item['state'], $state)) {
            return $state[$item['state']];
        }else{
            return $item['state'];
        }
        
     }
    
    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
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
                }else{
                   $actions = array(
                    '' => ''
                ); 
                }
            } else {
                $actions = array(
                    '' => ''
                );
            }
        }
        
        return $actions;
    }
    /**
     * This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ppd'; // do not forget about tables prefix
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
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ppd'; // do not forget about tables prefix
        $per_page = 10; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
		$paged = isset($_REQUEST['paged']) ? ($_REQUEST['paged'] - 1 ) * $per_page : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'company_name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
         if(isset($_REQUEST['s'])){
              $str = explode(' ', $_REQUEST['s']);
              $srh = "where company_name LIKE '{$str[0]}%' ";
              $total_items = $wpdb->get_var("SELECT COUNT(*)  FROM $table_name $srh");   
              $this->items = $wpdb->get_results("SELECT * FROM $table_name $srh ORDER BY $orderby $order LIMIT $paged ,$per_page", ARRAY_A);     
        } else {
              $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
            }
        
       // $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}