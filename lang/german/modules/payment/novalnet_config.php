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
 * Script : novalnet_config.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');

define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Novalnet Haupteinstellungen');
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION', '<span style="font-weight: bold; color:#878787;">Bevor Sie beginnen, lesen Sie bitte die Installationsanleitung und melden Sie sich mit Ihrem H&auml;ndlerkonto im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> an. Um ein H&aumlndlerkonto zu erhalten, senden Sie bitte eine E-Mail an <a style="font-weight: bold; color:#0080c9" href="mailto:sales@novalnet.de">sales@novalnet.de</a> oder rufen Sie uns unter +49 89 923068320 an</span><br/><br/><span style="font-weight: bold; color:#878787;">Um PayPal-Transaktionen zu akzeptieren, konfigurieren Sie Ihre PayPal-API-Informationen im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a>> PROJEKT > Wählen Sie Ihr Projekt > Zahlungsmethoden > Paypal > Konfigurieren </span>');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', ' Produktaktivierungsschl&uuml;ssel');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC', ' Ihren Produktaktivierungsschlüssel finden Sie im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a>: PROJEKT > W&auml;hlen Sie Ihr Projekt > Shop-Parameter > API-Signatur (Produktaktivierungsschl&uuml;ssel)');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE', 'H&auml;ndler-ID');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_TITLE', 'Authentifizierungscode');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE', 'Projekt-ID');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Auswahl der Tarif-ID');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC', 'W&aumlhlen Sie eine Tarif-ID, die dem bevorzugten Tarifplan entspricht, den Sie im Novalnet Admin-Portal f&uuml;r dieses Projekt erstellt haben ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Zahlungs-Zugriffsschl&uuml;ssel');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC', '');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY_TITLE', 'Schl&uuml;sselkunde');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY_DESC', '');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_TITLE', '<h2>Verwaltung des Bestellstatus f&uuml;r ausgesetzte Zahlungen</h2>On-hold-Bestellstatus');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_DESC', 'W&aumlhlen Sie, welcher Status f&uuml;r On-hold-Bestellungen verwendet wird, solange diese nicht best&auml;tigt oder storniert worden sind');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_TITLE', 'Status f&uuml;r stornierte Bestellungen');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_DESC', 'W&auml;hlen Sie, welcher Status f&uuml;r stornierte oder voll erstattete Bestellungen verwendet wird ');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h2>Benachrichtigungs- / Webhook-URL festlegen </h2>Manuelles Testen der Benachrichtigungs- / Webhook-URL erlauben');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', 'Aktivieren Sie diese Option, um die Novalnet-Benachrichtigungs-/Webhook-URL manuell zu testen. Deaktivieren Sie die Option, bevor Sie Ihren Shop liveschalten, um unautorisierte Zugriffe von Dritten zu blockieren');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE', 'E-Mail-Benachrichtigungen einschalten');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC', 'Aktivieren Sie diese Option, um die angegebene E-Mail-Adresse zu benachrichtigen, wenn die Benachrichtigungs- / Webhook-URL erfolgreich ausgeführt wurde');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', 'E-Mails senden an');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'E-Mail-Benachrichtigungen werden an diese E-Mail-Adresse gesendet ');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_TITLE', 'Benachrichtigungs- / Webhook-URL ');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_DESC', 'Eine Benachrichtigungs- / Webhook-URL ist erforderlich, um die Datenbank / das System des H&auml;ndlers mit dem Novalnet-Account synchronisiert zu halten (z.B. Lieferstatus). Weitere Informationen finden Sie in der Installationsanleitung');
define('MODULE_PAYMENT_NOVALNET_TARIFF_PERIOD_ERROR', 'Geben Sie bitte eine g&uuml;ltige Abonnementsperiode ein');
