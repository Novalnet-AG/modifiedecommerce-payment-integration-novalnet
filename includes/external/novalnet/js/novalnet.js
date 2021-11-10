/*
 * Novalnet Authorize Capture script
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
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



});
