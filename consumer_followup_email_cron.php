<?php
include '../../../wp-load.php';
ob_start();
?>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Matching Certified Aquascape Contractors</title>
    </head>
    <body onload='' style='-webkit-font-smoothing:antialiased;' bgcolor='#FFFFFF'>
        <style type='text/css'>
            body {-webkit-font-smoothing:antialiased; background-color: #FFFFFF}
            /* override changes made by email clients */
            .ExternalClass * {line-height: 123%}  
            .outlook {line-height: 124%}
            body { -webkit-font-smoothing: antialiased; background-color: #FFFFFF }
            .ReadMsgBody {WIDTH: 100%}
            .ExternalClass {WIDTH: 100%}
            .outlook {line-height: 95%}
            span.yshortcuts { color:#000; background-color:none; border:none;}
            span.yshortcuts:hover,
            span.yshortcuts:active,
            span.yshortcuts:focus {color:#000; background-color:none; border:none;}
            /* end override */
            @media only screen
            and (min-device-width : 320px)
            and (max-device-width : 480px), (max-width : 480px) {
                *[class].content {
                    width: 100%;
                }
                *[class].preheader {
                    text-align:center !important;
                    margin-bottom:0 !important;
                }
                *[class].banner {
                    width: 100%;
                    height: auto;
                }
                *[class].mobileStack {
                    display:block;
                    width: 100% !important;
                    margin-bottom:15px;
                }
                *[class].mobileHide {
                    display:none !important;
                }
            </style>
            <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='-webkit-font-smoothing:antialiased;'>
                <tr>
                    <td align='center'><!--Wrapper-->
                        <table class='content' width='640' align='center' cellpadding='0' cellspacing='0' border='0' bgcolor='#ffffff'><tr><td>

                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                        <tr>
                                            <td class='mobileStack'><img border='0' style='display:block;' src='http://www.aquascapeinc.com/images/email-stripe.gif' width='640' height='11' /></td>
                                        </tr>
                                        <tr>
                                            <td class='mobileStack' align='left'><a href='http://www.aquascapeinc.com' target='_new'><img border='0' style='display:block;' src='http://www.aquascapeinc.com/images/promo-logo.gif' alt='Aquascape, Inc.' width='156' height='62' /></a></td>
                                        </tr>
                                    </table>

                                    <table width='100%' border='0' cellpadding='10' cellspacing='0'>     
                                        <tr>
                                            <td align='left' style='-webkit-text-size-adjust:none; color:#3d4549;font-size:12px;font-family:Arial, Helvetica, sans-serif; height:30px'><h2  style='-webkit-text-size-adjust:none; color:#3d4549; font-size:14px; font-family:Arial, Helvetica, sans-serif; height:30px'>Thank you for using our Certified Aquascape Contractor locator.</h2><br />
                                                Thank you %fname% for recently using our Certified Aquascape Contractor locator. It's been two weeks since your search was completed and we are interested in hearing about your experience. Your feedback is very important to us and will be used to help insure we meet your expectations and provide outstanding customer service.<br /><br />

                                                Please take a few minutes to answer our short, online survey.<br /><br />

                                                To access the survey, <a href='https://www.surveymonkey.com/s/YWRWXPN?c=%id%' target='_new'>click here</a>. <br /><br />

                                                <a href='https://www.surveymonkey.com/s/YWRWXPN?c=%id%' target='_new'><img border='0' style='display:block;' src='http://www.aquascapeinc.com/images/go2survey.jpg' alt='Go to Survey' width='231' height='62' /></a><br />

                                                This link is uniquely tied to this survey and your email address. Please do not forward this message.
                                            </td>
                                            <td align='right' valign='top' style='-webkit-text-size-adjust:none; color:#3d4549;font-size:12px;font-family:Arial, Helvetica, sans-serif; height:30px'><a href='http://www.aquascapeinc.com' target='_new'><img border='0' style='display:block;' src='http://www.aquascapeinc.com/images/CAC-logo.gif' alt='Certified Aquascape Contractors' width='114' height='100' align='right' /></a>
                                            </td>
                                        </tr>     
                                    </table> 
                                    <table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
                                        <tr>
                                            <td class='mobileStack' valign='bottom'><img border='0' style='display:block;' src='http://www.aquascapeinc.com/images/email-stripe.gif' width='640' height='11' /></td>
                                        </tr>
                                    </table>
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' >
                                        <tr>
                                            <td class='mobileStack' align='left'valign='bottom' style='-webkit-text-size-adjust:none;mso-line-height-rule:exactly;line-height:11px;color:#FFFFFF;font-size:10px;font-family:Arial, Helvetica, sans-serif;'><br /><font color='#FFFFFF'>
                                                &copy; 2013 Aquascape, Inc. All Rights Reserved.<br /></font></td>
                                        </tr>
                                    </table>
                                    <!--End of Wrapper -->
                                </td></tr></table>
                    </td></tr></table>
        </body>
    </html>   
    <?php
    $body = ob_get_clean();
   // $to = 'narolainfotech2016@gmail.com';
   // $to = 'rpt@narola.email';
    $to = 'jjames@aquascapeinc.com';
    $subject = 'Aquascape Contractor Survey';
    //$admin_email = get_the_author_meta('user_email');
    $admin_email = "emails@aquascapeinc.com";
    $title = get_bloginfo('name');
    global $wpdb;
    $visitor_tablename = $wpdb->prefix . 'cac_locator_leads';
    $visitor_details = $wpdb->get_results("SELECT id, firstname, lastname, email, DATE_FORMAT(created, '%Y-%m-%d') FROM $visitor_tablename WHERE DATE(created) = DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");

    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From:';
    $headers[] = $title;
    $headers[] = $admin_email;
    if (count($visitor_details) > 0) {
        foreach ($visitor_details as $data):
            //  $headers[] = 'Cc:' . $email->email;
            $body1 = str_replace('%fname%', $data->firstname, $body);
            $bodynew = str_replace('%id%', $data->id, $body1);
          wp_mail($data->email, $subject, $bodynew, $headers);
            //wp_mail($to, $subject, $bodynew, $headers);
            $update_status = array(
                            'followup'    =>  '1'
                        );
         $updated = $wpdb->update($visitor_tablename, $update_status, array('id' => $data->id));
        endforeach;
    }
    else{
    echo "<br>Recored Not Found";
}
