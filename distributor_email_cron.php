<?php

// Include detabase config file
include '../../../wp-load.php';
global $wpdb;

// Define table name
$distributor_tablename = $wpdb->prefix . 'ppd';
$cac_tablename = $wpdb->prefix . 'cac_application_entries';
$lead_tablename = $wpdb->prefix . 'cac_locator_leads';

// set up containing array
$return = array();

// start by getting all the distributors to build the report for

$dist_query = $wpdb->get_results("SELECT * FROM $distributor_tablename");

if (empty($dist_query)) {
    echo 'Recored Not Found!!!!';
    die;
}
foreach ($dist_query as $rows):
    $return[$rows->id] = array(
        'lead_contact_email' => strtolower($rows->lead_contact_email),
        'company_name' => $rows->company_name,
    );
endforeach;

// get all the cac's associated with the distributor
foreach ($return as $distId => $dist) {
    $cac_res = $wpdb->get_results("SELECT 
	`id`,
	`company_name`,
	`purchase_from_company1`,
	`purchase_from_company2`,
	`purchase_from_company3`						
	FROM $cac_tablename 
	WHERE (purchase_from_company1 = {$distId} OR purchase_from_company2 = {$distId} OR purchase_from_company3 = {$distId})");


    foreach ($cac_res as $cac_row) {
        $lead_res = $wpdb->get_results("SELECT * FROM $lead_tablename
		   WHERE FIND_IN_SET('" . $cac_row->id . "',cacID) 
		   AND DATE(created) >=  (CURDATE() -  INTERVAL 1 WEEK)");
        if (empty($lead_res)) {
            //  echo 'Recored Not Found!!!!';
            // die;
        }

        foreach ($lead_res as $lead_row):
            $return[$distId]['leads'][] = array(
                'lead_id' => $lead_row->id,
                'lastname' => $lead_row->lastname,
                'zip' => $lead_row->zip,
                'created' => date('m/d/Y - g:i a', strtotime($lead_row->created)),
                'cac_company' => $cac_row->company_name,
                    // 'company_email' => $cac_row->company_name,
            );
        endforeach;
    }
}

// loop through results and email them out
foreach ($return as $dist) {

    $to_array = explode(",", $dist['lead_contact_email']);
    
    // make sure the dist has leads to send
    if (isset($dist['leads']) && count($dist['leads']) > 0) {
        $email = "<strong>The following CACs listed " . $dist['company_name'] . " as their distributor and have received leads this week.</strong><br /><br />";

        foreach ($dist['leads'] as $lead) {
            $email .= "CAC: " . $lead['cac_company'] . "<br />";
            $email .= "Received: " . $lead['created'] . "<br />";
            $email .= "Lead Last Name: " . $lead['lastname'] . "<br />";
            $email .= "Zip / Postal Code: " . $lead['zip'] . "<br />";
            $email .= "<br /><br />";
        }
    } else {
        // $email = "<strong>No leads were received for your CACs this week.</strong>";			
        $email = "NA";
    }
    
   
    if ($email != 'NA') { // skip emails with no leads
        // loop through all dist addresses and email them out
        foreach ($to_array as $to) {
            
            // Create the Transport
            $subject = 'Aquascape Contractor Leads';
        //  echo   $admin_email = get_the_author_meta('user_email');
            $admin_email = "emails@aquascapeinc.com";
            
            $title = get_bloginfo('name');

             $headers = array('Content-Type: text/html; charset=UTF-8', 'From:', $title, $admin_email);
          
            wp_mail($to, $subject, $email, $headers);
            echo "Email Address : " .$to ."</br>"; 
            echo $email;
            
            // sleep for 2 second so not to hammer mail systems and get flagged as abusive/spammer
           sleep(2);
        }// end foreach
    }
}