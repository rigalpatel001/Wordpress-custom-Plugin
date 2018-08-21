(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
        
        jQuery(document).ready(function () {
        jQuery("#cac_tabs").tabs();
        jQuery("#leads_tabs").tabs();
         
         
         //cac form validation & show hide field
          if($.trim($('#purchase_from_company1_2').val()) != ''){ $("#purchase_from_company1").hide(); }
          if($.trim($('#purchase_from_company2_2').val()) != ''){ $("#purchase_from_company2").hide(); }
          if($.trim($('#purchase_from_company3_2').val()) != ''){ $("#purchase_from_company3").hide(); }
         
         //cac form validation & show hide field
        $("#purchase_from_1").change(function(){
           
         if( $(this).val() === "Other" ){
             $("#purchase_from_company1_2").show();
             $("#purchase_from_company1").val('');
             $("#purchase_from_company1").hide();
         } else if ($(this).val() === "Direct") {
             $("#purchase_from_company1_2").val('');
             $("#purchase_from_company1").show();
             $("#purchase_from_company1").val('1');
             $("#purchase_from_company1_2").hide();
             $("#purchase_from_company1_2").val('');
         }
         else{
             $("#purchase_from_company1").val('');
             $("#purchase_from_company1").show();
             $("#purchase_from_company1_2").hide();
         }
      });
      
      // purchase  form2 change event
     $("#purchase_from_2").change(function(){
       if( $(this).val() === "Other" ){
             $("#purchase_from_company2_2").show();
             $("#purchase_from_company2").val('');
             $("#purchase_from_company2").hide();
            
         } else if ($(this).val() === "Direct") {
             $("#purchase_from_company2_2").val('');
             $("#purchase_from_company2").show();
             $("#purchase_from_company2").val('1');
             $("#purchase_from_company2_2").hide();
             $("#purchase_from_company2_2").val('');
         }
         else{
             $("#purchase_from_company2").val('');
             $("#purchase_from_company2").show();
             $("#purchase_from_company2_2").hide();
         }
      });
      // purchase  form3 change event
     $("#purchase_from_3").change(function(){
       if( $(this).val() === "Other" ){
             $("#purchase_from_company3_2").show();
             $("#purchase_from_company3").val('');
             $("#purchase_from_company3").hide();
         } else if ($(this).val() === "Direct") {
             $("#purchase_from_company3_2").val('');
             $("#purchase_from_company3").show();
             $("#purchase_from_company3").val('1');
             $("#purchase_from_company3_2").hide();
             $("#purchase_from_company3_2").val('');
         }
         else{
             $("#purchase_from_company3").val('');
             $("#purchase_from_company3").show();
             $("#purchase_from_company3_2").hide();
         }
      });
       
    });
    
    
    //search report ajax
    jQuery(document).ready(function($) {
      
       jQuery('input[type=radio][name=yearmonth]').change(function() {
            // alert("change");
            if (this.value == 'month') {
              $('#report_month').show();
            }
            else{
                $('#report_month').hide();
            }
         });
      
        $('#search_report').click(function (e) {
             $.ajax({
                    url: ajax_params.ajax_url,
                    type: 'post',
                    data: {
                       action: "search_report_results",
                        data: $("#report_frm").serialize()
                    },
                    success:function(data) {
                        $('#result_data').html(data);
                       // result_data
                    },
                    error: function(errorThrown){
                        console.log(errorThrown);
                    }
                });
               e.preventDefault(); //  
      });
 });
})( jQuery );