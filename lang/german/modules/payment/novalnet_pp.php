<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to Novalnet End User License Agreement
 * 
 * DISCLAIMER
 *
 * If you wish to customize Novalnet payment extension for your needs, please contact technic@novalnet.de for more information.
 *
 * @author 		Novalnet AG 
 * @copyright 	Novalnet
 * @license 	https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 *
 * Script : novalnet_pp.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_PP_TEXT_TITLE', 'PayPal ');
define('MODULE_PAYMENT_NOVALNET_PP_TEXT_DESCRIPTION', '<br>Sie werden zu PayPal weitergeleitet. Um eine erfolgreiche Zahlung zu gew&auml;hrleisten, darf die Seite nicht geschlossen oder neu geladen werden, bis die Bezahlung abgeschlossen ist <br />');
define('MODULE_PAYMENT_NOVALNET_PP_PUBLIC_TITLE', xtc_image(DIR_WS_ICONS.'novalnet/novalnet_paypal.png', "PayPal"));
define('MODULE_PAYMENT_NOVALNET_PP_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE_TITLE', 'Einkaufstyp');
define('MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE_DESC', 'Einkaufstyp ausw&auml;hlen <br><span id=\'paypal_message\' style=color:red></span>');
define('MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT_DESC', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_PAYMENT_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_PAYMENT_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_BLOCK_TITLE', '<b>PayPal API Konfiguration</b>');
define('MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK', 'Kauf mit einem Klick');
define('MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT', 'Transaktionen mit Betrag 0');
define('MODULE_PAYMENT_NOVALNET_PP_REF_TRANSACTION_ID', 'Novalnet Transaktions-ID ');
define('MODULE_PAYMENT_NOVALNET_PP_TRANSACTION_ID', 'PayPal Transaction TD');
define('MODULE_PAYMENT_NOVALNET_PP_SHOW_MESSAGE', 'Um diese Option zu verwenden, m&uuml;ssen Sie die Option Billing Agreement (Zahlungsvereinbarung) in Ihrem PayPal-Konto aktiviert haben. Kontaktieren Sie dazu bitte Ihren Kundenbetreuer bei PayPal.');
define('MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK_DESC', '<br>Sobald die Bestellung abgeschickt wurde, wird die Zahlung bei Novalnet als Referenztransaktion verarbeitet.<br>');
define('MODULE_PAYMENT_NOVALNET_PP_NEW_ACCOUNT', 'Mit neuen PayPal-Kontodetails fortfahren');
define('MODULE_PAYMENT_NOVALNET_PP_GIVEN_ACCOUNT', 'Angegebene PayPal-Kontodetails');
define('MODULE_PAYMENT_NOVALNET_PP_SAVECARD_DETAILS', 'Ich m&oumlchte meine PayPal-Kontodaten f&uumlr sp&aumltere Eink&aumlufe speichern');
define('MODULE_PAYMENT_NOVALNET_PP_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING', '<br>Diese Bestellung wird als Nullbuchung verarbeitet. Ihre Zahlungsdaten werden f&uuml;r zuk&uuml;nftige Online-Eink&auml;ufe gespeichert');
define('MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT_COMMENTS', '<span style="color: red;"><br/>Diese Bestellung wird als Null-Betrag-Buchung bearbeitet, in der Ihre Zahlungsdaten f&uuml;r weitere Online-Eink&auml;ufe gespeichert werden
</span>');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_TITLE', 'Aktion für vom Besteller autorisierte Zahlungen ');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_DESC', ' W&auml;hlen Sie, ob die Zahlung sofort belastet werden soll oder nicht. Zahlung einziehen: Betrag sofort belasten. Zahlung autorisieren: Die Zahlung wird &uuml;berpr&uuml;ft und autorisiert, aber erst zu einem sp&auml;teren Zeitpunkt belastet. So haben Sie Zeit, &uuml;ber die Bestellung zu entscheiden');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_CAPTURE', ' Zahlung einziehen');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_AUTH', ' Zahlung autorisieren');
