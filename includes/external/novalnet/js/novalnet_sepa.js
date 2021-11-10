/*
 * Novalnet Direct Debit SEPA Script
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
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

function ibanvalueformat(field)
{
    field.value = field.value.toUpperCase();
    field.value = field.value.replace(/[^0-9a-zA-Z]+/g, '');

    var exp = String.fromCharCode(field.which);
    var r = new RegExp(/[!@#$%§^&*\(\)=\[\]\\\';,\/\{\}\|":<>?~`\.\-_\°]+/);
    if (exp.match(r)) {
        field.preventDefault();
    }

    return true;
}
function iban_validate(event, allowSpace)
{
    var keycode = ('which' in event) ? event.which : event.keyCode;
    var reg = /^(?:[A-Za-z0-9]+$)/;
    if (allowSpace == true) {
        var reg = /^(?:[A-Za-z0-9&\s]+$)/;
    }
    if (event.target.id == 'novalnet_sepa_account_holder') {
        var reg = /[^0-9\[\]\/\\#,+@!^()$~%'"=:;<>{}\_\|*°§?`]/g;
    }
    return (reg.test(String.fromCharCode(keycode)) || keycode == 0 || keycode == 8 || (event.ctrlKey == true && keycode == 114) || (allowSpace == true && keycode == 32)) ? true : false;
}
$('document').ready(function () {
    $('#sepa_mandate_toggle').click(function () {
        $('#sepa_mandate_details').toggle();
    });
	
	$("#novalnet_sepa_birthdate").keypress(function(e){
		if(e.charCode > 31 && (e.charCode < 48 || e.charCode > 57)) return false;
	});

	$("#novalnet_sepa_birthdate_one_click").keypress(function(e){
		if(e.charCode > 31 && (e.charCode < 48 || e.charCode > 57)) return false;
	});
		
	$("#novalnet_sepa_birthdate").keyup(function(e){
		if( e.keyCode !=8 ) {
			var sepa_dob_val = $("#novalnet_sepa_birthdate").val();
			var sepa_dob_val_count = $("#novalnet_sepa_birthdate").val().length;

			if(sepa_dob_val_count == '4'){
				$("#novalnet_sepa_birthdate").val(sepa_dob_val + '-');
			}else
			var pattern	=	/\d{4}\-\d{2}/;
			if(sepa_dob_val_count == '7' && pattern.test(sepa_dob_val) ){
				$("#novalnet_sepa_birthdate").val(sepa_dob_val + '-');
			}
	}
	});

	$("#novalnet_sepa_birthdate_one_click").keyup(function(e){
		if( e.keyCode !=8 ) {
			var sepa_dob_val_oneclick = $("#novalnet_sepa_birthdate_one_click").val();
			var sepa_dob_val_count_oneclick = $("#novalnet_sepa_birthdate_one_click").val().length;

			if(sepa_dob_val_count_oneclick == '4'){
				$("#novalnet_sepa_birthdate_one_click").val(sepa_dob_val_oneclick + '-');
			}else
			var pattern	=	/\d{4}\-\d{2}/;
			if(sepa_dob_val_count_oneclick == '7' && pattern.test(sepa_dob_val_oneclick) ){
				$("#novalnet_sepa_birthdate_one_click").val(sepa_dob_val_oneclick + '-');
			}
	}
	});
});
