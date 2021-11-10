/*
 * Novalnet PayPal script
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
if (typeof(jQuery) == 'undefined') {
    var s = document.createElement("script");
    s.type = "text/javascript";
    var nn_root = document.getElementById('nn_root_catalog').value;
    s.src = nn_root + "includes/external/novalnet/js/jquery.js";
    document.getElementsByTagName("head")[0].appendChild(s);
}

if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener("load", novalnet_pp_load);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent("onload", novalnet_pp_load);
}

function novalnet_pp_load()
{
    novalnet_pp_message();
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE]"]').click(function () {
        novalnet_pp_message();
    });
	if(jQuery('#novalnet_pp_old_acc').val() != '') {
		jQuery('#novalnet_pp_change_account').val(1);
	}
    jQuery('#novalnet_pp_description').html(jQuery('#nn_lang_pp_one_click_desc').val());
    jQuery('#nn_pp_savecard').hide();
    jQuery('#novalnet_pp_oneclick_proceed').click(function () {
        jQuery('#nn_lang_pp_new_account, #nn_pp_savecard').show();
        jQuery('#novalnet_pp_old_acc, #novalnet_paypal_old_details, #novalnet_pp_oneclick_proceed').hide();
        jQuery('#novalnet_pp_description').html(jQuery('#nn_old').html());
        jQuery('#novalnet_pp_change_account').val(0);
        jQuery('#novalnet_pp_old_value').val(0);
    });

    jQuery('#nn_lang_pp_new_account').click(function () {
        jQuery('#nn_pp_savecard, #nn_lang_pp_new_account').hide();
        jQuery('#novalnet_paypal_old_details, #novalnet_pp_old_acc, #novalnet_pp_oneclick_proceed').show();
        jQuery('#novalnet_pp_old_value').val(1);
        jQuery('#novalnet_pp_description').html(jQuery('#nn_lang_pp_one_click_desc').val());
    });

}

function novalnet_pp_message()
{
    var novalnet_pp_shop_type = jQuery("input[name='configuration[MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE]']:checked").val();
    if (novalnet_pp_shop_type =='ZEROAMOUNT' || novalnet_pp_shop_type =='ONECLICK') {
        var message = jQuery('#nn_pp_message').val();
        jQuery('#paypal_message') .text(message);
    } else {
        jQuery('#paypal_message') .text('');
    }
}
