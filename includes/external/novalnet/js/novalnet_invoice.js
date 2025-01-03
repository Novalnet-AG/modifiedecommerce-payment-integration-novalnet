/*
 * Novalnet Invoice
 * @author 		Novalnet AG <technic@novalnet.de>
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
function validateDateFormat(e) {
     if ( !NovalnetUtility.validateDateFormat( e.value ) ) {
        alert(jQuery('#nn_invoice_birthdate_error').val());
    }
}


