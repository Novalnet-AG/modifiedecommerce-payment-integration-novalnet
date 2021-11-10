/*
 * Novalnet Invoice
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
$('document').ready(function () {
  
	jQuery("#novalnet_invoicebirthdate").keypress(function(e){
		if(e.charCode > 31 && (e.charCode < 48 || e.charCode > 57)) return false;
	});
	
	jQuery("#novalnet_invoicebirthdate").keyup(function(e){
		if( e.keyCode !=8 ) {
			var inv_dob_val = jQuery("#novalnet_invoicebirthdate").val();
			var inv_dob_val_count = jQuery("#novalnet_invoicebirthdate").val().length;
			if(inv_dob_val_count == '4'){
				jQuery("#novalnet_invoicebirthdate").val(inv_dob_val + '-');
			}else
			var pattern	=	/\d{4}\-\d{2}/;
			if(inv_dob_val_count == '7' && pattern.test(inv_dob_val) ){
				jQuery("#novalnet_invoicebirthdate").val(inv_dob_val + '-');
			}
			
		}
	
	});

});
