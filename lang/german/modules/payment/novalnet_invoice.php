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
 * Script : novalnet_invoice.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE', 'Rechnung ');
define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION', '<br>Sie erhalten eine E-Mail mit den Bankdaten von Novalnet, um die Zahlung abzuschlie&szlig;en<br />');
define('MODULE_PAYMENT_NOVALNET_INVOICE_PUBLIC_TITLE', xtc_image(DIR_WS_ICONS.'novalnet/novalnet_invoice.png', "Rechnung "));
define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE_TITLE', MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE_DESC', MODULE_PAYMENT_NOVALNET_FRAUDMODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_TITLE', 'F&auml;lligkeitsdatum (in Tagen)');
define('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_DESC', ' Anzahl der Tage, die der K&auml;ufer Zeit hat, um den Betrag an Novalnet zu &uuml;berweisen (muss mehr als 7 Tage betragen). Wenn Sie dieses Feld leer lassen, werden standardm&auml;&szlig;ig 14 Tage als F&auml;lligkeitsdatum festgelegt ');
define('MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT_DESC', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENDCUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENDCUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS_TITLE', 'Callback-Bestellstatus');
define('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS_DESC', 'Status, der zu verwendet wird, wenn das Callback-Skript f&uuml;r eine bei Novalnet eingegangene Zahlung ausgef&uuml;hrt wird');
define('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_MIN_AMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_MIN_AMOUNT_DESC', MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE_TITLE', MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE_DESC', MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_DESC);
define('MODULE_PAYMENT_NOVALNET_INVOICE_BLOCK_TITLE', '<b>Kauf auf Rechnung Konfiguration</b>');
define('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_ERROR', 'Geben Sie bitte ein g&uuml;ltiges F&auml;lligkeitsdatum ein');
define('MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_TITLE', 'Aktion für vom Besteller autorisierte Zahlungen');
define('MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_DESC', 'W&auml;hlen Sie, ob die Zahlung sofort belastet werden soll oder nicht. Zahlung einziehen: Betrag sofort belasten. Zahlung autorisieren: Die Zahlung wird &uuml;berpr&uuml;ft und autorisiert, aber erst zu einem sp&auml;teren Zeitpunkt belastet. So haben Sie Zeit, &uuml;ber die Bestellung zu entscheiden ');
define('MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_CAPTURE', 'Zahlung einziehen');
define('MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_AUTH', 'Zahlung autorisieren');
define('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_PENDING_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_PENDING_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_DESC);
