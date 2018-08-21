<?php
/**
 * The public-facing distibutor  find functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Aqua_Scape
 * @subpackage Aqua_Scape/public
 */
?>
<?php 
 /**
 * Create CAC Manage form Shortcode 
 *
 * @since    1.0.0
 */
function distributor_find_shortcodes() {
 ?>
<div class="front_find_distributor_section">
        <h1 class="main_title">Find a Distributor</h1>
        <div id="distributortabs">
            <ul>
               <li><a href="#tabs-1">USA / Canada</a></li>
               <li><a href="#tabs-2">International</a></li>
          </ul>
        <div id="tabs-1" class="tab-content">
                <div class="distributor_find_section">
                    <p>To find an Aquascape distributor, search by your current location or enter a location manually with a zip code or city and state.</p>
                    <div id="locateUser-holder">
                        <button id="locateUser" class="btn btn-default btn-lg" onclick="locateUser()">
                            <span class="glyphicon glyphicon-map-marker"></span> Use My Current Location
                        </button>
                    </div>
                    <hr>
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="zipcode" class="col-sm-2 control-label"></label>
                            <div class="">
                                <input class="form-control" id="zipcode" name="zipcode" placeholder="Postal Code" type="tel">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city" class="col-sm-2 control-label">Or</label>
                            <div class="">
                                <input class="form-control" id="city" name="city" placeholder="City" type="text">
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <div class="">
                                <select name="state" id="state" class="form-control">
                                    <option value="">- State/Province -</option>
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
                                       <option value="QC">Qu�bec</option>
                                       <option value="SK">Saskatchewan</option>
                                       <option value="YT">Yukon Territory</option>
                                    </optgroup>
                                    <optgroup label="Others">
                                         <option value="others">Others</option>
                                    </optgroup>
                                    </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="radius" class="col-sm-2 control-label">Within</label>
                            <div class="">
                                <select id="radius" name="radius" class="form-control">
                                    <option value="25">25 Miles</option>
                                    <option value="50">50 Miles</option>
                                    <option value="75">75 Miles</option>
                                    <option value="100">100 Miles</option>
                                    <option value="200">200 Miles</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                                <button id="location_search_button" type="submit" class="btn btn-default btn-lg">Find a Distributor</button>
                                <img id="dajax_loader" src="<?php echo esc_url( plugins_url( '/css/ajax-loader.gif', dirname(__FILE__) ) ); ?>" style="display:none">
                            </div>
                        </div>
                        <input id="search_type" name="search_type" value="distributor" type="hidden">
                    </form>
                </div>
                <!-- Responsive iFrame -->
                <div id="map-canvas" class="Flexible-container">

                </div>
                <div id="user-location"></div>
                <div id="user" class="table-responsive">
                    <table id="locations-table" class="table table-condensed" style="visibility: hidden;">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Company Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
        </div>
            <div id="tabs-2">
                <div class="distributor_find_section">
                    <?php 
                        $content = get_option('aqua_international_distributor');
                        if(!empty($content)):
                            echo $content;
                        endif;
                    ?>
                   </div>
             </div>
    </div>
   <?php } ?>