/*
 * Novalnet Authorize Capture script
 * @author      Novalnet AG <technic@novalnet.de>
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */

jQuery('document').ready(function (e) {
        //capture authorize
        jQuery('#set_limit_title, #set_limit_desc').hide();
        jQuery('[name*="_MANUAL_CHECK_LIMIT]"]').hide();
            jQuery('[name*="_CAPTURE_AUTHORIZE]"]').click(function () {
                if (jQuery('[name*="_CAPTURE_AUTHORIZE]"]').prop('checked') == true) {
                        jQuery('#set_limit_title, #set_limit_desc').hide();
                        jQuery('[name*="_MANUAL_CHECK_LIMIT]"]').hide().val('');
                }
                if (jQuery('[name*="_CAPTURE_AUTHORIZE]"]').prop('checked') == false) {
                        jQuery('#set_limit_title, #set_limit_desc').show();
                        jQuery('[name*="_MANUAL_CHECK_LIMIT]"]').show();
                }

            });
    if (jQuery('[name*="_CAPTURE_AUTHORIZE]"]').prop('checked') == false) {
            jQuery('#set_limit_title, #set_limit_desc').show();
            jQuery('[name*="_MANUAL_CHECK_LIMIT]"]').show();
    }

jQuery( '[name*="MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE"], [name*="MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE"],  [name*="MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE"], [name*="MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE"]'  ).change(function(event) {
         jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE]"]').attr('id', 'invoice_due_date');
         jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE]"]').attr('id', 'sepa_due_date');
         jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE]"]').attr('id', 'cashpayment_due_date');
         jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE]"]').attr('id', 'prepayment_due_date');
         performAdminValidations(event);
    }); 
});
 
function performAdminValidations(event) {

  if (jQuery('#invoice_due_date').val() != undefined && jQuery.trim(jQuery('#invoice_due_date').val()) != '') {
        if (isNaN(jQuery('#invoice_due_date').val()) || jQuery('#invoice_due_date').val() < 7) {
            jQuery('#invoice_due_date').val('');
            event.preventDefault();
            alert(jQuery('#invoice_due_date_error').val());
        }
   } else if(jQuery('#sepa_due_date').val() != undefined && jQuery.trim(jQuery('#sepa_due_date').val()) != '') {
       if ( isNaN(jQuery('#sepa_due_date').val()) || jQuery('#sepa_due_date').val() < 2 || jQuery('#sepa_due_date').val() > 14) {
            jQuery('#sepa_due_date').val('');
            event.preventDefault();
            alert(jQuery('#sepa_due_date_error').val());
       }
   } else if(jQuery('#cashpayment_due_date').val() != undefined && jQuery.trim(jQuery('#cashpayment_due_date').val()) != '') {
       if ( isNaN(jQuery('#cashpayment_due_date').val()) || jQuery('#cashpayment_due_date').val() < 1) {
           jQuery('#cashpayment_due_date').val('');
            event.preventDefault();
            alert(jQuery('#cashpayment_due_date_error').val());
        }
    } else if(jQuery('#prepayment_due_date').val() != undefined && jQuery.trim(jQuery('#prepayment_due_date').val()) != '') {
		if ( isNaN(jQuery('#prepayment_due_date').val()) || jQuery('#prepayment_due_date').val() < 7 || jQuery('#prepayment_due_date').val() > 28) {
            jQuery('#prepayment_due_date').val('');
            event.preventDefault();
            alert(jQuery('#prepayment_due_date_error').val());
       }
	}
}
