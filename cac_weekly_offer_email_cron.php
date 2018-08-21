<?php
include '../../../wp-load.php';
ob_start();
global $wpdb;
$tablename = $wpdb->prefix . 'cac_locator_leads';
$cac_weekly_offer = $wpdb->get_results("SELECT * FROM $tablename WHERE `offer` = '1' AND created > DATE_SUB(NOW(), INTERVAL 7 DAY)");

$hStyle = "style=\"font-family: Helvetica, arial, sans-serif; text-align:left;\" ";
$tdAttributes = "valign=\"middle\" style=\"font-family: Helvetica, arial, sans-serif; font-size:12px; text-align:left;\" ";
if (count($cac_weekly_offer)> 0){
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 
    'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
    <head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Aquascape FREE Book Requests</title></head>
    <body onload='' style='-webkit-font-smoothing:antialiased;' bgcolor='#FFFFFF'>
        <h3 <?php echo $hStyle; ?>>The following consumers signed up for a FREE Water Feature Dream Book this week.</h3>
        <table width='100%' border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th <?php echo $tdAttributes; ?> scope=\"col\">firstname</th>
                <th <?php echo $tdAttributes; ?>scope=\"col\">lastname</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">address</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">city</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">state</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">zip</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">country</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">phone</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">email</th>
                <th <?php echo $tdAttributes; ?> scope=\"col\">received</th>
            </tr>
            <?php
            foreach ($cac_weekly_offer as $data):
            ?>
            <tr>
                <td <?php echo $tdAttributes; ?>> <?php echo $data->firstname; ?> </td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->lastname; ?> </td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->address1; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->city; ?> </td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->state; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->zip; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->country; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->phone; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->email; ?></td>
                <td <?php echo $tdAttributes; ?>><?php echo $data->created; ?></td>
            </tr>
            <?php endforeach;
            ?>                 

        </tbody></table>	
    <?php
    $body = ob_get_clean();
    }else{
    $body = '<h3'.$hStyle.'>No new requests, for a FREE Water Feature Dream Book, were received this week.</h3>';
    }
   // $to = 'narolainfotech2016@gmail.com';
     $to = 'jjames@aquascapeinc.com';
    $subject = 'Aquascape FREE Book Requests';
    //$admin_email = get_the_author_meta('user_email');
    $admin_email = "cac@aquascapeinc.com"; 
    $title = get_bloginfo('name');
//    $headers[] = 'Content-Type: text/html; charset=UTF-8';
//    $headers[] = 'From:';
//    $headers[] = $title;
//    $headers[] = "cac@aquascapeinc.com";
    
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From:', $title, $admin_email);
    wp_mail($to, $subject, $body, $headers);