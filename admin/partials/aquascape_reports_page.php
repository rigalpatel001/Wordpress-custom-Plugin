<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $wpdb;
?>
<div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Certified Aquascape Contractors Reports', 'aqua_scape') ?> 
        </h2>
        
        <form id="report_frm" method="POST">
            <table cellspacing="2" cellpadding="5" style="width: 100%;">
             <tbody>
             <tr class="form-field">
                 <th valign="top" scope="row">
                     <label for="year"><?php _e('Select Year or Month', 'aquascape')?></label>
                 </th>
                 <td>
                     <input type="radio" name="yearmonth" value="year" checked="checked">Year
                     <input type="radio" name="yearmonth" value="month">Month
                 </td>
                 <th valign="top" scope="row">
                     <label for="year"><?php _e('State', 'aquascape')?></label>
                 </th>
                 <td>
                   <select class="form-control" id="state" name="state">
                         <option value="">Select State</option>
                                              <optgroup label="United States">
                                                 <option value="AL">Alabama</option>
                                                 <option value="AK">Alaska</option>
                                                 <option value="AZ">Arizona</option>
                                                 <option value="AR">Arkansas</option>
                                                 <option value="CA">California</option>
                                                 <option value="CO">Colorado</option>
                                                 <option value="CT">Connecticut</option>
                                                 <option value="DE">Delaware</option>
                                                 <option value="DC">Washington D.C.</option>
                                                 <option value="FL">Florida</option>
                                                 <option value="GA">Georgia</option>
                                                 <option value="HI">Hawaii</option>
                                                 <option value="ID">Idaho</option>
                                                 <option value="IL">Illinois</option>
                                                 <option value="IN">Indiana</option>
                                                 <option value="IA">Iowa</option>
                                                 <option value="KS">Kansas</option>
                                                 <option value="KY">Kentucky</option>
                                                 <option value="LA">Louisiana</option>
                                                 <option value="ME">Maine</option>
                                                 <option value="MD">Maryland</option>
                                                 <option value="MA">Massachusetts</option>
                                                 <option value="MI">Michigan</option>
                                                 <option value="MN">Minnesota</option>
                                                 <option value="MS">Mississippi</option>
                                                 <option value="MO">Missouri</option>
                                                 <option value="MT">Montana</option>
                                                 <option value="NE">Nebraska</option>
                                                 <option value="NV">Nevada</option>
                                                 <option value="NH">New Hampshire</option>
                                                 <option value="NJ">New Jersey</option>
                                                 <option value="NM">New Mexico</option>
                                                 <option value="NY">New York</option>
                                                 <option value="NC">North Carolina</option>
                                                 <option value="ND">North Dakota</option>
                                                 <option value="OH">Ohio</option>
                                                 <option value="OK">Oklahoma</option>
                                                 <option value="OR">Oregon</option>
                                                 <option value="PA">Pennsylvania</option>
                                                 <option value="RI">Rhode Island</option>
                                                 <option value="SC">South Carolina</option>
                                                 <option value="SD">South Dakota</option>
                                                 <option value="TN">Tennessee</option>
                                                 <option value="TX">Texas</option>
                                                 <option value="UT">Utah</option>
                                                 <option value="VT">Vermont</option>
                                                 <option value="VA">Virginia</option>
                                                 <option value="WA">Washington</option>
                                                 <option value="WV">West Virginia</option>
                                                 <option value="WI">Wisconsin</option>
                                                 <option value="WY">Wyoming</option>
                                              </optgroup>
                                              <optgroup label="Canada">
                                                 <option value="AB">Alberta</option>
                                                 <option value="BC">British Columbia</option>
                                                 <option value="MB">Manitoba</option>
                                                 <option value="NB">New Brunswick</option>
                                                 <option value="NF">Newfoundland</option>
                                                 <option value="NT">Northwest Territories</option>
                                                 <option value="NS">Nova Scotia</option>
                                                 <option value="NU">Nunavut</option>
                                                 <option value="ON">Ontario</option>
                                                 <option value="PE">Prince Edward Island</option>
                                                 <option value="QC">Québec</option>
                                                 <option value="SK">Saskatchewan</option>
                                                 <option value="YT">Yukon Territory</option>
                                              </optgroup>
                                           </select>
                 
                 </td>
             </tr>
             <tr>
                 <th valign="top" scope="row">
                     <label for="year"><?php _e('Year', 'aquascape')?></label>
                 </th>
                 <td>
                      <select id="display" name="year" class="short-input">
                          <option value="">Select Year</option>
                           <?php 

                          for ($i = 2010; $i <= 2030; $i++) {
                              echo "<option value=" . $i . ">" . $i . "</option>";
                          }
                          ?>
                   </select>
                 </td>
                
                 <td style="display:none;" id="report_month">
                      <select id="display" name="month" class="short-input">
                           <option value="">Select Month</option>
                         <?php 
                          for ($i = 1; $i <= 12; $i++) {
                              echo "<option value=" . $i . ">" . $i . "</option>";
                          }
                          ?>
                   </select>
                      
                 </td>
                 <td><input type="submit" value="<?php _e('Search', 'aquascape') ?>" id="search_report" class="button-primary" name="search"></td>
             </tr>
             <tr>
                
             </tr>
             </tbody>
            </table>  
        </form>
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="widefat striped">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="yearmonth"><?php _e('', 'aquascape')?></label>
        </th>
          <th valign="top" scope="row">
            <label for="totalleads"><?php _e('Total Leads', 'aquascape')?></label>
        </th>
        <th valign="top" scope="row">
            <label for="totlaoffer"><?php _e('Total Offer Signups', 'aquascape')?></label>
        </th>
    </tr>
    <tr id="result_data">
        <td align="center" colspan="2">Report Result.....<td>
    </tr>
    </tbody>
   </table>  
 </div>