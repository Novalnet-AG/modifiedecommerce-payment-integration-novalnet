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
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION', '<span style="font-weight: bold; color:#878787;"> Loggen Sie sich hier mit Ihren Novalnet H&auml;ndler-Zugangsdaten ein.Um neue Zahlungsarten zu aktivieren, kontaktieren Sie bitte <a style="font-weight: bold; color:#0080c9" href="mailto:support@novalnet.de">support@novalnet.de</a><br/><br/>Um zus&auml;tzliche Einstellungen vorzunehmen, loggen Sie sich in das <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet-Händleradministrationsportal</a> ein. <br/>Um sich in das Portal einzuloggen, ben&ouml;tigen Sie einen Account bei Novalnet. Falls Sie diesen noch nicht haben, kontaktieren Sie bitte <a style="font-weight: bold; color:#0080c9" href="mailto:sales@novalnet.de">sales@novalnet.de</a> (Tel: +49 (089) 923068320).</span><br/><br/><span style="font-weight: bold; color:#878787;">Um die Zahlungsart PayPal zu verwenden, geben Sie bitte Ihre PayPal-API-Daten in das <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet-H&auml;ndleradministrationsportal</a> ein.</span>');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', 'Aktivierungsschl&uuml;ssel des Produkts');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC', 'Novalnet-Aktivierungsschl&uuml;ssel f&uuml;r das Produkt eingeben. Um diesen Aktivierungschl&uuml;ssel f&uuml;r das Produkt zu erhalten, gehen Sie zum <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet-Händleradministrationsportal</a> - Projekte: Informationen zum jeweiligen Projekt - Parameter Ihres Shops: API Signature (Aktivierungsschl&uuml;ssel des Produkts)');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE', 'H&auml;ndler-ID');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_TITLE', 'Authentifizierungscode');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE', 'Projekt-ID');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Tarif-ID');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC', 'Novalnet-Tarif-ID ausw&auml;hlen');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Zahlungs-Zugriffsschl&uuml;ssel');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC', '');
define('MODULE_PAYMENT_NOVALNET_LAST_SUCCESSFUL_PAYMENT_SELECTION_TITLE', 'Default-Zahlungsart aktivieren');
define('MODULE_PAYMENT_NOVALNET_LAST_SUCCESSFUL_PAYMENT_SELECTION_DESC', 'F&uuml;r registrierte Benutzer wird die letzte ausgew&auml;hlte Zahlungsart als Standardeinstellung beim Checkout ausgew&auml;hlt');
define('MODULE_PAYMENT_NOVALNET_PROXY_TITLE', 'Proxy-Server');
define('MODULE_PAYMENT_NOVALNET_PROXY_DESC', 'Geben Sie die IP-Adresse Ihres Proxyservers zusammen mit der Nummer des Ports ein und zwar in folgendem Format: IP-Adresse : Nummer des Ports (falls notwendig)');
define('MODULE_PAYMENT_NOVALNET_GATEWAY_TIMEOUT_TITLE', 'Zeitlimit der Schnittstelle (in Sekunden)');
define('MODULE_PAYMENT_NOVALNET_GATEWAY_TIMEOUT_DESC', 'Falls die Verarbeitungszeit der Bestellung das Zeitlimit der Schnittstelle &uuml;berschreitet, wird die Bestellung nicht ausgef&uuml;hrt');
define('MODULE_PAYMENT_NOVALNET_REFERRER_ID_TITLE', 'Partner-ID');
define('MODULE_PAYMENT_NOVALNET_REFERRER_ID_DESC', 'Geben Sie die Partner-ID der Person / des Unternehmens ein, welche / welches Ihnen Novalnet empfohlen hat');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_TITLE', '<h2>Steuerung der angezeigten Logos </h2>Sie k&ouml;nnen die Anzeige der Logos auf der Checkout-Seite aktivieren oder deaktivieren<br><br>Logo der Zahlungsart anzeigen');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_DESC', 'Das Logo der Zahlungsart wird auf der Checkout-Seite angezeigt');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_TITLE', '<h2>Verwaltung des Bestellstatus f&uuml;r ausgesetzte Zahlungen</h2>On-hold-Bestellstatus');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_TITLE', 'Bestellstatus f&uuml;r Stornierung');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h2>Verwaltung des H&auml;ndlerskripts</h2>Deaktivieren Sie die IP-Adresskontrolle (nur zu Testzwecken)');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', ' Diese Option erm&ouml;glicht eine manuelle Ausf&uuml;hrung. Bitte deaktivieren Sie diese Option, bevor Sie Ihren Shop in den LIVE-Modus schalten, um nicht autorisierte Zugriffe von externen Parteien (au&szlig;er von Novalnet) zu vermeiden');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE', 'Email-Benachrichtigung f&uuml;r Callback aktivieren');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', 'Emailadresse (An)');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'Emailadresse des Empf&auml;ngers');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_BCC_TITLE', 'Emailadresse (Bcc)');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_BCC_DESC', 'Emailadresse des Empf&aumlngers f&uuml;r Bcc');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_TITLE', 'URL f&uuml;r Benachrichtigungen');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_DESC', 'Der URL f&uuml;r Benachrichtigungen dient dazu, Ihre Datenbank / Ihr System auf einem aktuellen Stand zu halten und den Novalnet-Transaktionsstatus abzugleichen');
define('MODULE_PAYMENT_NOVALNET_TARIFF_PERIOD_ERROR', 'Geben Sie bitte eine g&uuml;ltige Abonnementsperiode ein');
