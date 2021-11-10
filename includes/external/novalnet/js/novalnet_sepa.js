/*
 * Novalnet Direct Debit SEPA Script
 * @author      Novalnet AG <technic@novalnet.de>
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
if (typeof(jQuery) == 'undefined') {
    var s = document.createElement('script');
    s.type = 'text/javascript';
    var nn_sepa_root = document.getElementById('nn_root_sepa_catalog').value;
    s.src = nn_sepa_root + 'includes/external/novalnet/js/jquery.js';
    document.getElementsByTagName('head')[0].appendChild(s);
}

if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener('load', novalnet_sepa_load);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent('onload', novalnet_sepa_load);
}

jQuery('#novalnet_oneclick_sepa_ref').ready(function () {
    jQuery('#nn_sepa_mandate').css('display', 'block');
    if (jQuery('#nn_payment_ref_tid_sepa').val() != '' && jQuery('#nn_payment_ref_tid_sepa').length != '0') {
        jQuery('#novalnet_sepa_change_account').val('saved');
    }

    jQuery('#novalnet_oneclick_sepa_ref').click(function () {
        jQuery('#nn_sepa_acc, #nn_sepa_savecard').show();
        jQuery('#novalnet_oneclick_sepa_ref_div, #novalnet_oneclick_sepa_ref').hide();
        jQuery('#novalnet_oneclick_sepa_click_new').css('display','block');
        jQuery('#novalnet_sepa_savecheck').val(0);
        jQuery('#novalnet_sepa_change_account').val('newform');
    });
    jQuery('#novalnet_oneclick_sepa_click_new').click(function () {
        jQuery('#nn_sepa_acc, #nn_sepa_savecard').hide();
        jQuery('#novalnet_oneclick_sepa_ref, #novalnet_oneclick_sepa_ref_div').show();
        jQuery('#novalnet_oneclick_sepa_click_new').css('display','none');
        jQuery('#novalnet_sepa_savecard').val(0);
        jQuery('#novalnet_sepa_change_account').val('saved');
    });

});

function novalnet_sepa_load()
{
    jQuery("#novalnet_sepa_remove_data").click(function (e) {
        display_text = jQuery('#nn_sepa_remove_message').val();
        e.stopImmediatePropagation();
        if (!confirm(display_text)) {
            e.stopImmediatePropagation();
               return false;
        }
        return true;
    });

     var random_value = jQuery('#nn_payment_value').val();
     var sepa_ref_tid = jQuery('#nn_payment_ref_tid_sepa').val();
    if (sepa_ref_tid == null) {
        jQuery('#nn_sepa_acc').css('display', 'block');
        jQuery('#nn_sepa_savecard').css('display', 'block');
        jQuery('#nn_sepa_mandate').css('display', 'block');
    }
}

jQuery('document').ready(function () {
    jQuery('form').each(function () {
		if (jQuery(this).attr('id') == 'checkout_payment') {
			formid = 'checkout_payment';
		}
	});

    if (formid == '') {
        formid = jQuery('#nn_root_sepa_catalog').closest('form').attr('id');
    }
    if (formid == undefined) {
        jQuery('#nn_root_sepa_catalog').closest('form').attr('id', 'checkout_payment');
        formid = 'checkout_payment';
    }
    jQuery('#'+formid).submit(
        function (event) {
            var selected_payment = (jQuery("input[name='payment']").attr('type') == 'hidden') ? jQuery("input[name='payment']").val() : jQuery("input[name='payment']:checked").val();
            var change_account = jQuery('#novalnet_sepa_change_account').val();
            if ( selected_payment == 'novalnet_sepa') {
                if ( undefined !== jQuery( '#novalnet_sepa_iban') ) {
                    var iban = NovalnetUtility.formatAlphaNumeric( jQuery( '#novalnet_sepa_iban').val() );
                    if (iban === '' && change_account == 'newform') {
                        alert(jQuery('#nn_sepa_iban_error').val());
                        return false;
                    } 
                }
            }
        });

    jQuery('#sepa_mandate_toggle').click(function () {
        jQuery('#sepa_mandate_details').toggle();
    });

    jQuery("#novalnet_sepa_birthdate").keypress(function(e){
        if(e.charCode > 31 && (e.charCode < 48 || e.charCode > 57)) return false;
    });

    jQuery("#novalnet_sepa_birthdate_one_click").keypress(function(e){
        if(e.charCode > 31 && (e.charCode < 48 || e.charCode > 57)) return false;
    });
    jQuery('#novalnet_sepa_iban').keyup(function (event) {
           this.value = this.value.toUpperCase();
           var field = this.value;
           var value = "";
           for(var i = 0; i < field.length;i++){
                   if(i <= 1){
                           if(field.charAt(i).match(/^[A-Za-z]/)){
                                   value += field.charAt(i);
                           }
                   }
                   if(i > 1){
                           if(field.charAt(i).match(/^[0-9]/)){
                                   value += field.charAt(i);
                           }
                   }
           }
           field = this.value = value;
      });

});

function validateDateFormat(e) {
     if ( !NovalnetUtility.validateDateFormat( e.value ) ) {
        alert(jQuery('#nn_sepa_birthdate_error').val());
    }
}
