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
 * Script : novalnet_cc.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', 'Kreditkarte ');
define('MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE', (((!defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY')) || MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True')?'<a href="https://www.novalnet.de/zahlungsart-kreditkarte" title="Kreditkarte" target="_blank"/>'.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_visa.png', "Kreditkarte").' '.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_master.png', "Kreditkarte").'</a>':''));
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_DESCRIPTION', '<br>Der Betrag wird von Ihrer Kreditkarte abgebucht, sobald die Bestellung abgeschickt wird.<br />');
define('MODULE_PAYMENT_NOVALNET_CC_REDIRECTION_TEXT_DESCRIPTION', '<br>Nach der erfolgreichen &Uuml;berpr&uuml;fung werden Sie auf die abgesicherte Novalnet-Bestellseite umgeleitet, um die Zahlung fortzusetzen<br />');
define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_TITLE', '3D-Secure aktivieren');
define('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_DESC', '3D-Secure wird f&uuml;r Kreditkarten aktiviert. Die kartenausgebende Bank fragt vom K&auml;ufer ein Passwort ab, welches helfen soll, betr&uuml;gerische Zahlungen zu verhindern. Dies kann von der kartenausgebenden Bank als Beweis verwendet werden, dass der K&auml;ufer tats&auml;chlich der Inhaber der Kreditkarte ist. Damit soll das Risiko von Chargebacks verringert werden');
define('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE_TITLE', '3D-Secure-Zahlungen unter vorgegebenen Bedingungen durchf&uuml;hren');
define('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE_DESC', 'Wenn 3D-Secure in dem dar&uuml;berliegenden Feld nicht aktiviert ist, sollen 3D-Secure-Zahlungen nach den Einstellungen zum Modul im Novalnet-Händleradministrationsportal unter "3D-Secure-Zahlungen durchf&uuml;hren (gem&auml;&szlig; vordefinierten Filtern und Einstellungen)" durchgef&uuml;hrt werden. <br>Wenn die vordefinierten Filter und Einstellungen des Moduls "3D-Secure durchf&uuml;hren" zutreffen, wird die Transaktion als 3D-Secure-Transaktion durchgef&uuml;hrt, ansonsten als Nicht-3D-Secure-Transaktion. <br>Beachten Sie bitte, dass das Modul "3D-Secure-Zahlungen durchf&uuml;hren (gem&auml;&szlig; vordefinierten Filtern und Einstellungen)" im Novalnet-Händleradministrationsportal konfiguriert sein muss, bevor es hier aktiviert wird. <br>F&uuml;r weitere Informationen sehen Sie sich bitte die Beschreibung dieses Betrugspr&uuml;fungsmoduls an (unter dem Reiter "Betrugspr&uuml;fungsmodule" unterhalb des Men&uuml;punkts "Projekte" f&uuml;r das ausgew&auml;hte Projekt im Novalnet-Händleradministrationsportal) oder kontaktieren Sie das Novalnet-Support-Team.');
define('MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE_TITLE', MODULE_PAYMENT_NOVALNET_SHOP_TYPE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE_DESC', MODULE_PAYMENT_NOVALNET_SHOP_TYPE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT_DESC', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_ENDCUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ENDCUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_ONE_CLICK', 'Kauf mit einem Klick');
define('MODULE_PAYMENT_NOVALNET_CC_ZERO_AMOUNT', 'Transaktionen mit Betrag 0');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_TYPE', 'Kartentyp');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_HOLDER', 'Name des Karteninhabers');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_NO', 'Kreditkartennummer');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_VALID_DATE', 'Ablaufdatum');
define('MODULE_PAYMENT_NOVALNET_YEAR_TEXT', 'Jahr');
define('MODULE_PAYMENT_NOVALNET_MONTH_TEXT', 'Monat');
define('MODULE_PAYMENT_NOVALNET_CC_BLOCK_TITLE', '<b>Kreditkarte Konfiguration</b>');
define('MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS', 'Ihre Kreditkartendaten sind ungültig.');
define('MODULE_PAYMENT_NOVALNET_CC_NEW_ACCOUNT', 'Neue Kartendaten eingeben');
define('MODULE_PAYMENT_NOVALNET_CC_GIVEN_ACCOUNT', 'Eingegebene Kartendaten');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_TITLE', '<h3>CSS-Einstellungen f&uuml;r den iFrame mit Kreditkartendaten</h3><span style="font-weight:normal;">Beschriftung</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_TITLE', '<span style="font-weight:normal;">Eingabe</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_TITLE', '<span style="font-weight:normal;">
Text f&uuml;r das CSS</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS', 'Meine Kartendaten für zukünftige Bestellungen speichern');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING', '<span style="color:red"><br/>Diese Bestellung wird als Null-Betrag-Buchung bearbeitet, in der Ihre Zahlungsdaten f&uuml;r weitere Online-Eink&auml;ufe gespeichert werden</span>');
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX_TITLE', 'Amex Logo');
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO_TITLE', 'Maestro Logo');
define('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_TITLE', 'Zahlungsvorgang');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_CAPTURE', 'Erfassung');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_AUTH', 'Genehmigen');
