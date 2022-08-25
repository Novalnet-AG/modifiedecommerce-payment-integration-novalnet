/*
 * Novalnet Credit Card script
 * @author      Novalnet AG <technic@novalnet.de>
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
if (typeof(jQuery) == 'undefined') {
    var s = document.createElement("script");
    s.type = "text/javascript";
    var nn_cc_root = document.getElementById('nn_root_cc_catalog').value;
    s.src = nn_cc_root + "includes/external/novalnet/js/jquery.js";
    document.getElementsByTagName("head")[0].appendChild(s);
}

if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener("load", novalnet_cc_load);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent("onload", novalnet_cc_load);
}

function novalnet_cc_load()
{
    var formid = '';
    jQuery(document).ready(function () {
        jQuery('form').each(function () {
            if (jQuery(this).attr('id') == 'checkout_payment') {
                formid = 'checkout_payment';
            }
        });
    });
    
    if (formid == '') {
        formid = jQuery('#nn_root_cc_catalog').closest('form').attr('id');
    }
    if (formid == undefined) {
        jQuery('#nn_root_cc_catalog').closest('form').attr('id', 'checkout_payment');
        formid = 'checkout_payment';
    }
    
    jQuery('input[name=novalnet_cc_savecard]').click(function () {
        jQuery('#novalnet_cc_newcard').prop('checked', false);
        jQuery('#nn_cc_acc').css('display', 'none');
        jQuery('#nn_cc_savecard').css('display', 'none');
        jQuery('#novalnet_cc_newcard').val('');
    });

    jQuery(document).ready(function () {
		load_creditcard_iframe(formid);
        if(jQuery('[name = nn_cc_savecard]').val() == 1){
            jQuery('#nn_cc_savecard').show();
        }
        if(jQuery('#nn_payment_ref_tid').val()){
            jQuery('#nn_cc_savecard').hide();
        }
        jQuery('#saved_novalnet_cc_newcard').click(function () {
            jQuery('#saved_nn_cc_new').hide();
            jQuery('#nn_cc_acc, #novalnet_cc_givencard, #nn_cc_savecard').show();
            jQuery('#novalnet_cc_savecard').val(0);       
            NovalnetUtility.setCreditCardFormHeight();            
        });
        jQuery('#novalnet_cc_givencard').click(function () {
            jQuery('#saved_nn_cc_new').show();
            jQuery('#nn_cc_acc, #novalnet_cc_givencard, #nn_cc_savecard').hide();
            jQuery('#novalnet_cc_savecard').val(1);
        });
        
        var paymentDiv = jQuery( "input[name='payment']" ).closest( "div" );
        jQuery(paymentDiv).click(function() {            
            if(!jQuery("nnIframe").is(":visible")){
                NovalnetUtility.setCreditCardFormHeight();
            }
        });
    });
    
    jQuery('#'+formid).submit(
        function (event) {
            var selected_payment = (jQuery("input[name='payment']").attr('type') == 'hidden') ? jQuery("input[name='payment']").val() : jQuery("input[name='payment']:checked").val();            
             if ( selected_payment == 'novalnet_cc') {
                var pan_hash = jQuery("#nn_cc_pan_hash").val();
                var change_account = jQuery('#novalnet_cc_newcard').val();
                var saved_account = jQuery('#novalnet_cc_savedcard').val();
                var novalnet_cc_savecard = jQuery('#novalnet_cc_savecard').val();
                if ((pan_hash == '' && change_account == 'new') || (pan_hash == '' && saved_account == 'saved' && novalnet_cc_savecard == 0)) {
                   NovalnetUtility.getPanHash();
                   event.preventDefault();
                   event.stopImmediatePropagation();
                }
            }
        });
        
}

function load_creditcard_iframe(formid)
{            
    var nn_css_label = jQuery('#nn_css_label').val();
    var nn_css_input = jQuery('#nn_css_input').val();
    var nn_css_text = jQuery('#nn_css_text').val();
    var nn_cc_formdetails  = jQuery('#nn_cc_iframe_data').val();
    var nn_cc_details = JSON.parse(nn_cc_formdetails);

    // Set your Client key
    NovalnetUtility.setClientKey((nn_cc_details.client_key !== undefined) ? nn_cc_details.client_key : '');

    var requestData = {
        'callback': {
          on_success: function (result) {
            var novalnetCCForm = $('#nnIframe').closest('form').attr('id');
            $('#nn_cc_pan_hash').val(result['hash']);
            $('#nn_cc_uniqueid').val(result['unique_id']);
            $('#'+formid).submit();
            return true;
          },
          on_error: function (result) {
           if ( undefined !== result['error_message'] ) {
              alert(result['error_message']);
              return false;
            }
          },
          on_show_overlay:  function (result) {
            document.getElementById('nnIframe').classList.add("nn_cc_overlay");
          },
          on_hide_overlay:  function (result) {
            document.getElementById('nnIframe').classList.remove("nn_cc_overlay");
          }
        },
        'iframe': {
          id: "nnIframe",
          inline: (nn_cc_details.inline_form !== undefined) ? nn_cc_details.inline_form : '0',
          style: {
            container: (nn_css_text !== undefined) ? nn_css_text : '',
            input: (nn_css_input !== undefined) ? nn_css_input : '' ,
            label: (nn_css_label !== undefined) ? nn_css_label : '' ,
          },
        },
        customer: {
          first_name: (nn_cc_details.first_name !== undefined) ? nn_cc_details.first_name : '',
          last_name: (nn_cc_details.last_name !== undefined) ? nn_cc_details.last_name : nn_cc_details.first_name,
          email: (nn_cc_details.email !== undefined) ? nn_cc_details.email : '',
          billing: {
            street: (nn_cc_details.street !== undefined) ? nn_cc_details.street : '',
            city: (nn_cc_details.city !== undefined) ? nn_cc_details.city : '',
            zip: (nn_cc_details.zip !== undefined) ? nn_cc_details.zip : '',
            country_code: (nn_cc_details.country_code !== undefined) ? nn_cc_details.country_code : ''
          },
        },
        transaction: {
          amount: (nn_cc_details.amount !== undefined) ? nn_cc_details.amount : '',
          currency: (nn_cc_details.currency !== undefined) ? nn_cc_details.currency : '',
          test_mode: (nn_cc_details.test_mode !== undefined) ? nn_cc_details.test_mode : '0',
        },
        custom: {
            lang: (nn_cc_details.lang !== undefined) ? nn_cc_details.lang : ''
        }
      };
      
      NovalnetUtility.createCreditCardForm(requestData);
}
