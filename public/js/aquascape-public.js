(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
        
        $(window).load(function () {
        $.validate({
            form: '#cac_find_frm',
           // modules: 'html5'
        });
        
         $('#referral').on('change', function () {
		$("#completeForm").css('display', (this.value == '') ? 'none' : 'block');
	});	
       
        // if($('#cacuseremail').val() != '') {
          //   console.log("found");
            
            var aaa =  $('#cacuseremail').val();
//             if (typeof aaa === "undefined") {
//                   console.log("Not set");
//                }else
//                {
//                      console.log(aaa);
//                      alert(aaa);
//                }
             
         //}
        //cac form validation & show hide field
        $(".purchase_from_select").change(function(){
        var opt_id = $(this).attr('id');
        opt_id = opt_id.split("_");
        
        if( $(this).val() === "Other" )
        {
            $("#purchase_from_extra_" + opt_id[2]).show();
            $("#purchase_from_company" + opt_id[2]).hide();
            $("#purchase_from_company" + opt_id[2]).after('<input type="text" id="purchase_from_company' + opt_id[2] + '_2" name="CAC_App[purchase_from_company' + opt_id[2] + '_2]" class="form-control" maxlength="255" placeholder="Distributor Name" >');
            $("#purchase_from_extra_" + opt_id[2]).removeClass("hidden");
        } else if ($(this).val() === "Direct") {
            $("#purchase_from_company" + opt_id[2]).val('1');
            $("#purchase_from_extra_" + opt_id[2]).addClass("hidden");
            $("#purchase_from_company" + opt_id[2] + '_2').remove();
            $("#purchase_from_company" + opt_id[2]).show();
        } else {
            $("#purchase_from_extra_" + opt_id[2]).addClass("hidden");
            $("#purchase_from_company" + opt_id[2] + '_2').remove();
            $("#purchase_from_company" + opt_id[2]).show();
            $("#purchase_from_company" + opt_id[2]).val('');
        }
    });
    
    //Find cac send email 
    
    
   
    });   
     //$('#cac_application_form').parsley();
    window.ParsleyConfig = {
        errorsWrapper: '<div></div>',
        errorTemplate: '<span class="label label-warning"></span>'
    };
    
    
     jQuery(document).ready(function($) {
      // Distributor find tabs
       jQuery( "#distributortabs" ).tabs();
      
        $('#cacLocator_offer_button').click(function (e) {

            var  valid = true;
            if ($('#address').val() == '') {
              jQuery('#address').css({"border":" 1px solid #ff0000"});
              valid = false;
            }
            if ($('#city').val() == '') {
                jQuery('#city').css({"border":" 1px solid #ff0000"});
                valid = false;
            }
            if ($('#zipcode').val() == '') {
                 jQuery('#zipcode').css({"border":" 1px solid #ff0000"});
                 valid = false;
            }
            if ($('#phone').val() == '') {
                jQuery('#phone').css({"border":" 1px solid #ff0000"});  
                 valid = false;
            }
            if ($('#state').val() == '') {
                jQuery('#state').css({"border":" 1px solid #ff0000"});  
                 valid = false;
            }
            if (!valid) {
                return false;
            }else{
             $.ajax({
                    url: ajax_params.ajax_url,
                    type: 'post',
                    data: {
                       action: "ebook_form_data",
                        data: $("#cacLocator_offer").serialize()
                    },
                    success:function(data) {
                        if(data=="Sucess"){
                              $('#cacLocator_offer_message').addClass("success_frm");
                              $('#cacLocator_offer_message').html(" <p>Thank you we'll mail you copy right a way.</p>");
                              $('#cacLocator_offer').hide();
                              //Google analytics event
                               ga('send', 'event', {
                                eventCategory: 'Dreambook form',
                                eventAction: 'Submit',
                                eventLabel: 'Dreambook form Success'
                            });
                              
                        }else{

                            $('#cacLocator_offer_message').addClass("alert alert-warning");
                            $('#cacLocator_offer_message').html(" <p>This email address has already signed up.</p>");
                            //Google analytics event
                               ga('send', 'event', {
                                eventCategory: 'Dreambook form',
                                eventAction: 'Submit',
                                eventLabel: 'Dreambook form Fail'
                            });
                        }
                    },
                    error: function(errorThrown){
                        console.log(errorThrown);
                    }
                });
            }
               e.preventDefault(); //  
      });
     
 });
        

})( jQuery );
