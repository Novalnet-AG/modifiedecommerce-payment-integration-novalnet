/*
 * Novalnet Credit Card script
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
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
var targetOrigin = 'https://secure.novalnet.de';
function getFormValue()
{	
	var styleObj = {
        labelStyle : jQuery('#nn_label').val(),
        inputStyle : jQuery('#nn_input').val(),
        styleText  : jQuery('#nn_css_text').val(),
		};
    var textObj   = {
		cvcHintText: jQuery('#nn_cvc_hint').val(),
		errorText  : jQuery('#nn_iframe_error').val(),
		card_holder : {
			labelText : jQuery('#nn_holder_label').val(),
			inputText : jQuery('#nn_holder_input').val(),
		},
		card_number : {
			labelText : jQuery('#nn_number_label').val(),
			inputText : jQuery('#nn_number_input').val(),
		},
		expiry_date : {
			labelText : jQuery('#nn_expiry_label').val(),
			inputText : jQuery('#nn_expiry_input').val(),
		},
		cvc  : {
			labelText : jQuery('#nn_cvc_label').val(),
			inputText : jQuery('#nn_cvc_input').val(),
		}
	};
    var requestObj = {
        callBack: 'createElements',
        customText: textObj,
        customStyle: styleObj
    };
    ccloadIframe(JSON.stringify(requestObj))
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
        jQuery("#novalnet_cc_remove_data").click(function (e) {
            display_text = jQuery('#nn_cc_remove_message').val();
            e.stopImmediatePropagation();
            if (!confirm(display_text)) {
                   return false;
            }
            return true;
        });
    });

    if (formid == '') {
        formid = jQuery('#nn_root_cc_catalog').closest('form').attr('id');
    }
    if (formid == undefined) {
        jQuery('#nn_root_cc_catalog').closest('form').attr('id', 'checkout_payment');
        formid = 'checkout_payment';
    }

    jQuery("h2").click(function () {
          var payment_name = jQuery('input:radio[name=payment]:checked').val();
        if (payment_name ='novalnet_cc') {
              ccloadIframe(JSON.stringify({callBack: 'getHeight'}))
        }
    });

    var random_value = jQuery('#nn_payment_value').val();
	
    jQuery('input[name=novalnet_cc_savecard]').click(function () {
        jQuery('#novalnet_cc_newcard').prop('checked', false);
        jQuery('#nn_cc_acc').css('display', 'none');
        jQuery('#nn_cc_savecard').css('display', 'none');
        jQuery('#novalnet_cc_newcard').val('');
    });

    jQuery(document).ready(function () {
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
        });
        jQuery('#novalnet_cc_givencard').click(function () {
            jQuery('#saved_nn_cc_new').show();
            jQuery('#nn_cc_acc, #novalnet_cc_givencard, #nn_cc_savecard').hide();
            jQuery('#novalnet_cc_savecard').val(1);
        });
    });

    jQuery('#'+formid).submit(
        function (event) {
			var selected_payment = (jQuery("input[name='payment']").attr('type') == 'hidden') ? jQuery("input[name='payment']").val() : jQuery("input[name='payment']:checked").val();
            var change_account = jQuery('#novalnet_cc_newcard').val();
            var saved_account = jQuery('#novalnet_cc_savedcard').val();
            var novalnet_cc_savecard = jQuery('#novalnet_cc_savecard').val();
            if (selected_payment != 'novalnet_cc') {
                return true; }
            if ( selected_payment == 'novalnet_cc') {
                var pan_hash = jQuery("#nn_cc_pan_hash").val();
                if ((pan_hash == '' && change_account == 'new') || (pan_hash == '' && saved_account == 'saved' && novalnet_cc_savecard == 0)) {
                     event.preventDefault();
                     getHashFromserver();
                }
            }
        }
    );
    if (window.addEventListener) {
		// addEventListener works for all major browsers
        window.addEventListener('message', function (e) {
            addEvent(e);
        }, false);
    } else {
        // attachEvent works for IE8
        window.attachEvent('onmessage', function (e) {
            addEvent(e);
        });
    }
    // Function to handle Event Listener
    function addEvent(e)
    {
        if (e.origin === targetOrigin) {
			if (typeof e.data === 'string') {
				// Convert message string to object
				var data = eval('(' + e.data.replace(/(<([^>]+)>)/gi, "") + ')');
			} else {
				var data = e.data;
			}
			
			if (data['callBack'] == 'getHash') {
                if (data['error_message'] != undefined) {
                    alert(jQuery('<textarea />').html(data['error_message']).text());
                    return false;
                } else {
                    jQuery('#nn_cc_pan_hash').val(data['hash']);
                    jQuery('#nn_cc_uniqueid').val(data['unique_id']);
                    jQuery('#'+formid).submit();
                }
            } else if (data['callBack'] == 'getHeight') {
				jQuery('#nnIframe').attr('height',data['contentHeight']);
            }
        }
    }

    function getHashFromserver()
    {
		ccloadIframe(JSON.stringify({callBack: 'getHash'})); // Call the postMessage event for getting the iframe content height dynamically
    }

}

function ccloadIframe(request)
{
    var iframe = jQuery('#nnIframe')[0];
    iframe = iframe.contentWindow ? iframe.contentWindow : iframe.contentDocument.defaultView;
    iframe.postMessage(request, targetOrigin);
}
