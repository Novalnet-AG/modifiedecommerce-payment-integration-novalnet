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
 * Script : novalnet.php
 *
 */

define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE', '<b>Ablauf der Buchung steuern</b>');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT', 'Wählen Sie bitte einen Status aus');
define('MODULE_PAYMENT_NOVALNET_SELECT_CONFIRM_TEXT', 'Sind Sie sicher, dass Sie die Zahlung einziehen möchten?');
define('MODULE_PAYMENT_NOVALNET_SELECT_CANCEL_TEXT', 'Sind Sie sicher, dass Sie die Zahlung stornieren wollen?');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMOUNT_TEXT', 'Sind Sie sicher, dass Sie den Betrag zurückerstatten möchten?');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE', 'Die Buchung wurde am %s um %s Uhr bestätigt.');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_WITH_DUEDATE', 'Die Transaktion mit der TID: %s wurde erfolgreich bestätigt und das Fälligkeitsdatum auf %s gesetzt');
define('MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE', 'Die Transaktion wurde am %s um %s Uhr storniert.');
define('MODULE_PAYMENT_NOVALNET_TRANS_UPDATED_MESSAGE', 'Die Transaktion wurde mit dem Betrag ( %s ) und dem Fälligkeitsdatum %s aktualisiert');
define('MODULE_PAYMENT_NOVALNET_CASH_PAYMENT_TRANS_UPDATED_MESSAGE', 'Die Transaktion wurde mit dem Betrag ( %s ) aktualisiert und das Ablaufdatum des Belegs mit %s');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE', 'Geben Sie bitte den erstatteten Betrag ein');
define('MODULE_PAYMENT_NOVALNET_REFUND_TITLE', '<b>Ablauf der R&uuml;ckerstattung</b>');
define('MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG', 'Die Rückerstattung für die TID: %s mit dem Betrag %s wurde veranlasst.');
define('MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG', ' Die neue TID %s für den erstatteten Betrag: %s latuet');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_EX', ' (in der kleinsten W&auml;hrungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)');
define('MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT', 'Best&auml;tigen');
define('MODULE_PAYMENT_NOVALNET_UPDATE_TEXT', '&Auml;ndern');
define('MODULE_PAYMENT_NOVALNET_CANCEL_TEXT', 'Stornieren');
define('MODULE_PAYMENT_NOVALNET_ORDER_UPDATE', 'erfolgreichen');
define('MODULE_PAYMENT_NOVALNET_BOOK_TITLE', '<b>Transaktion durchf&uuml;hren</b>');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMOUNT_TEXT', 'Sind Sie sich sicher, dass Sie den Bestellbetrag buchen wollen?');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE', 'Buchungsbetrag der Transaktion');
define('MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE', 'Ihre Bestellung wurde mit einem Betrag von %s gebucht. Ihre neue TID für den gebuchten Betrag:%s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_TITLE', '<b>Betrag &auml;ndern</b>');
define('MODULE_PAYMENT_NOVALNET_TRANS_AMOUNT_TITLE', 'Betrag der Transaktion &auml;ndern');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_DUE_DATE_TITLE', '<b>Betrag / F&auml;lligkeitsdatum &auml;ndern</b>');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_SLIP_EXPIRY_DATE_TITLE', '<b> Betrag / Verfallsdatum des Zahlscheins &auml;ndern</b>');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_SLIP_EXPIRY_DATE_BUTTON', ' Betrag / Verfallsdatum des Zahlscheins &auml;ndern');
define('MODULE_PAYMENT_NOVALNET_TRANS_DUE_DATE_TITLE', 'F&auml;lligkeitsdatum der Transaktion');
define('MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE_TITLE', '<b>Verfallsdatum des Zahlscheins</b>');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_UPDATE_TEXT', 'Sind Sie sich sicher, dass Sie den Bestellbetrag ändern wollen?');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_DATE_UPDATE_TEXT', 'Sind Sie sicher, dass Sie den Bestellbetrag / das Fälligkeitsdatum ändern möchten?');
define('MODULE_PAYMENT_NOVALNET_VALID_DUEDATE_MESSAGE', 'Das Datum sollte in der Zukunft liegen.');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR', 'Leider konnte diese Bestellung nicht verarbeitet werden. Bitte geben Sie eine neue Bestellung auf.');
define('MODULE_PAYMENT_NOVALNET_REFUND_REFERENCE_TEXT', 'Referenz f&uuml;r die R&uuml;ckerstattung');
define('MODULE_PAYMENT_NOVALNET_INVALID_DATE', 'Ungültiges Fälligkeitsdatum');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_SLIP_EXPIRY_DATE_UPDATE_TEXT', 'Sind Sie sicher, dass sie den Bestellbetrag / das Ablaufdatum des Zahlscheins ändern wollen?');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE_TEXT', 'Verfallsdatum des Zahlscheins');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_NEAREST_STORE_DETAILS_TEXT', 'Barzahlen-Partnerfiliale in Ihrer Nähe ');
define('MODULE_PAYMENT_NOVALNET_SEPA_TRANS_UPDATED_MESSAGE', 'Der Betrag der Transaktion ( %s ) wurde am %s um %s Uhr erfolgreich geändert.');
define('MODULE_PAYMENT_NOVALNET_MAIL_MESSAGE', 'Sehr geehrter Herr / Frau / Frau');
define('MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE', 'Sie m&uuml;ssen die IP-Adresse Ihres ausgehenden Servers (%s) bei Novalnet konfigurieren. Bitte konfigurieren Sie es im Novalnet-Händleradministrationsportal oder wenden Sie sich an technic@novalnet.de');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_REF', 'Neue Kontodaten f&uuml;r sp&auml;tere K&auml;ufe hinzuf&uuml;gen');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_ACC', 'Kontoinhaber');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_REF_PROCEED', 'Mit neuen PayPal-Kontodetails fortfahren');
define('MODULE_PAYMENT_NOVALNET_TRUE', 'Wahr');
define('MODULE_PAYMENT_NOVALNET_FALSE', 'Falsch');
define('MODULE_PAYMENT_NOVALNET_CONFIG_BLOCK_TITLE', '<b>H&auml;ndler API Konfiguration</b>');
define('MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE', 'Zugelassene Gebiete');
define('MODULE_PAYMENT_NOVALNET_ALLOWED_DESC', 'Diese Zahlungsart wird nur f&uuml;r die aufgef&uuml;hrten Gebiete zugelassen. Geben Sie die Gebiete in folgendem Format ein, z.B. DE,AT,CH etc. Falls das Feld leer ist, sind alle Gebiete zugelassen');
define('MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE', 'Zahlungsart anzeigen');
define('MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE', 'Testmodus aktivieren');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE', '<div id="set_limit_title">Mindesttransaktionsbetrag f&uuml;r die Autorisierung </div>');
define('MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC', '<div id="set_limit_desc">&Uuml;bersteigt der Bestellbetrag das genannte Limit, wird die Transaktion, bis zu ihrer Best&auml;tigung durch Sie, auf on hold gesetzt. Sie können das Feld leer lassen, wenn Sie m&ouml;chten, dass alle Transaktionen als on hold behandelt werden.</div>');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION', '--Ausw&auml;hlen--');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TITLE', 'Betrugspr&uuml;fung aktivieren');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_DESC', ' Automatische PIN-Generierung zur Authentifizierung von K&auml;ufern in DE, AT und CH. Weitere Informationen finden Sie in der Installationsanleitung');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_TITLE', 'Mindestwarenwert f&uuml;r Betrugspr&uuml;fungsmodul (in der kleinsten W&auml;hrungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_DESC', 'Geben Sie den Mindestwarenwert ein, von dem ab das Betrugspr&uuml;fungsmodul aktiviert sein soll');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE', 'Benachrichtigung des K&auml;ufers');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC', 'Der eingegebene Text wird auf der Checkout-Seite angezeigt');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE', 'Geben Sie eine Sortierreihenfolge an');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC', 'Diese Zahlungsart wird unter anderen Zahlungsarten (in aufsteigender Richtung) anhand der angegebenen Nummer f&uuml;r die Sortierung eingeordnet');
define('MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_TITLE', ' Status f&uuml;r Bestellungen mit ausstehender Zahlung');
define('MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_DESC', 'W&auml;hlen Sie, welcher Status f&uuml;r Bestellungen mit ausstehender Zahlung verwendet wird');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE', 'Status f&uuml;r erfolgreichen Auftragsabschluss ');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC', 'W&auml;hlen Sie, welcher Status f&uuml;r erfolgreich abgeschlossene Bestellungen verwendet wird');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE', 'Zahlungsgebiet');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC', 'Diese Zahlungsart wird f&uuml;r die angegebenen Gebiete angezeigt');
define('MODULE_PAYMENT_NOVALNET_SHOP_TYPE_TITLE', 'Einkaufstyp');
define('MODULE_PAYMENT_NOVALNET_SHOP_TYPE_DESC', 'Einkaufstyp ausw&auml;hlen ');
define('MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE', 'Mindestbestellsumme');
define('MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC', 'Mindestbestellsumme zur Anzeige der ausgewählten Zahlungsart(en) im Checkout ');
define('MODULE_PAYMENT_NOVALNET_SELECT', '-- Ausw&auml;hlen -- ');
define('MODULE_PAYMENT_NOVALNET_OPTION_NONE', 'Keiner');
define('MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONCALLBACK', 'PIN-by-Callback');
define('MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONSMS', 'PIN-by-SMS');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS_GUARANTEE_PAYMENT', 'Diese Transaktion wird mit Zahlungsgarantie verarbeitet');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS', 'Novalnet-Transaktionsdetails');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet Transaktions-ID: ');
define('MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE', 'Testbestellung');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG', '<span style="color:red;">Die Zahlung wird im Testmodus durchgef&uuml;hrt, daher wird der Betrag f&uuml;r diese Transaktion nicht eingezogen.<br/></span>');
define('MODULE_PAYMENT_NOVALNET_JS_DEACTIVATE_ERROR', 'Aktivieren Sie bitte JavaScript in Ihrem Browser, um die Zahlung fortzusetzen.');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR', 'Während der Umleitung wurden einige Daten geändert. Die Überprüfung des Hashes schlug fehl.');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE', 'Ungültiger Betrag');
define('MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH', 'Überweisen Sie bitte den Betrag an die unten aufgeführte Bankverbindung unseres Zahlungsdienstleisters Novalnet.');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Kontoinhaber');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_DUE_DATE', 'Fälligkeitsdatum');
define('MODULE_PAYMENT_NOVALNET_BANK', 'Bank');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', 'Betrag');
define('MODULE_PAYMENT_NOVALNET_SWIFT_BIC', 'BIC');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF1', 'Verwendungszweck 1');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF2', 'Verwendungszweck 2');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT', 'Bitte verwenden Sie einen der unten angegebenen Verwendungszwecke für die Überweisung, da nur so Ihr Geldeingang zugeordnet werden kann:');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_INFO', 'In K&uuml;rze erhalten Sie einen Telefonanruf mit der PIN zu Ihrer Transaktion, um die Zahlung abzuschlie&szlig;en');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_PIN_INFO', 'In K&uuml;rze erhalten Sie eine SMS mit der PIN zu Ihrer Transaktion, um die Zahlung abzuschlie&szlig;en.');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_REQUEST_DESC', 'PIN zu Ihrer Transaktion');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_NEW_PIN', '&nbsp; PIN vergessen?');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_EMPTY', 'PIN eingeben');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_NOTVALID', 'Die von Ihnen eingegebene PIN ist falsch');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_AMOUNT_CHANGE_ERROR', 'Der Bestellbetrag hat sich ge&auml;ndert, setzen Sie bitte die neue Bestellung fort');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TELEPHONE_ERROR', 'Geben Sie bitte Ihre Telefonnummer ein');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_ERROR', 'Geben Sie bitte Ihre Mobiltelefonnummer ein.');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE', 'Ihr Geburtsdatum');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_ERROR_MSG', 'Geben Sie ein gültiges Geburtsdatum ein');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_INVOICE_CREDIT_COMMENTS', 'Novalnet-Callback-Skript erfolgreich ausgeführt für die TID: %s mit dem Betrag %s am %s um %s Uhr. Bitte suchen Sie nach der bezahlten Transaktion in unserer Novalnet-Händleradministration mit der TID: %s');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_UPDATE_COMMENTS', 'Novalnet-Callback-Skript erfolgreich ausgeführt für die TID: %s mit dem Betrag %s am %s um %s Uhr.');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_CANCELLED_COMMENT', 'Die Transaktion wurde storniert. Grund:');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK_COMMENTS', 'Novalnet-Callback-Nachricht erhalten: Chargeback erfolgreich importiert für die TID: %s Betrag: %s am %s um %s Uhr. TID der Folgebuchung: %s');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK_COMMENTS', 'Novalnet-Callback-Meldung erhalten: Rückerstattung / Bookback erfolgreich ausgeführt für die TID: %s Betrag: %s am %s %s. TID der Folgebuchung: %s');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'F&uuml;llen Sie bitte alle Pflichtfelder aus.');
define('MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR', 'Ihre Kontodaten sind ungültig.');
define('MODULE_PAYMENT_NOVALNET_AGE_ERROR', 'Sie müssen mindestens 18 Jahre alt sein');
define('MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_TITLE', '<h2>Einstellungen f&uuml;r die Zahlungsgarantie</h2><h3>Grundanforderungen f&uuml;r die Zahlungsgarantie</h3><ul>
	<li>Zugelassene Staaten: DE, AT, CH</li>
	<li>Zugelassene W&auml;hrung: EUR</li>
	<li>Mindestbetrag der Bestellung >= 9,99 EUR</li>
	<li>Mindestalter des Endkunden >= 18 Jahre</li>
	<li>Rechnungsadresse und Lieferadresse m&uuml;ssen &uuml;bereinstimmen</li>	
</ul><br/>Zahlungsgarantie aktivieren');
define('MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_DESC', 'Falls die Zahlungsgarantie zwar aktiviert ist, jedoch die Voraussetzungen f&uuml;r Zahlungsgarantie nicht erf&uuml;llt sind, wird die Zahlung ohne Zahlungsgarantie verarbeitet. Die Voraussetzungen finden Sie in der Installationsanleitung unter "Zahlungsgarantie aktivieren" ');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_TITLE', 'Mindestbestellbetrag für Zahlungsgarantie');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_DESC', ' Geben Sie den Mindestbetrag (in Cent) f&uuml;r die zu bearbeitende Transaktion mit Zahlungsgarantie ein. Geben Sie z.B. 100 ein, was 1,00 entspricht. Der Standbetrag ist 9,99 EUR ');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_AMOUNT_ERROR', 'Ung&uuml;ltiger Betrag');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_ERROR', 'Der Mindestbetrag sollte bei mindestens 9,99 EUR');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_EMPTY_ERROR', 'Geben Sie bitte Ihr Geburtsdatum ein');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT_ERROR', 'Ung&uuml;ltiges Datumsformat');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_TITLE', 'Zahlung ohne Zahlungsgarantie erzwingen');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_DESC', 'Falls die Zahlungsgarantie zwar aktiviert ist, jedoch die Voraussetzungen für Zahlungsgarantie nicht erfüllt sind, wird die Zahlung ohne Zahlungsgarantie verarbeitet. Die Voraussetzungen finden Sie in der Installationsanleitung unter "Zahlungsgarantie aktivieren"');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY', '<span style="color:red;">Die Zahlung kann nicht verarbeitet werden, da die grundlegenden Anforderungen für die Zahlungsgarantie nicht erf&uuml;llt wurden (Als Land ist nur Deutschland, &Ouml;sterreich oder Schweiz erlaubt)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY', '<span style="color:red;">Die Zahlung kann nicht verarbeitet werden, da die grundlegenden Anforderungen f&uuml;r die Zahlungsgarantie nicht erf&uuml;llt wurden (Als W&auml;hrung ist nur EUR erlaubt)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS', '<span style="color:red;">Die Zahlung kann nicht verarbeitet werden, da die grundlegenden Anforderungen f&uuml;r die Zahlungsgarantie nicht erf&uuml;llt wurden (Die Lieferadresse muss mit der Rechnungsadresse &uuml;bereinstimmen)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT', '<span style="color:red;">Die Zahlung kann nicht verarbeitet werden, da die grundlegenden Anforderungen f&uuml;r die Zahlungsgarantie nicht erf&uuml;llt wurden (Der Mindestbestellwert betr&auml;gt %s)</span>');
define('MODULE_PAYMENT_NOVALNET_CONFIG_INSTALL_ERROR', 'Konfigurieren Sie bitte die zentralen Novalnet-Einstellungen, um die Novalnet-Zahlungsart zu aktivieren');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE', 'Ihre Bestellung ist unter Bearbeitung. Sobald diese bestätigt wurde, erhalten Sie alle notwendigen Informationen zum Ausgleich der Rechnung. Wir bitten Sie zu beachten, dass dieser Vorgang bis zu 24 Stunden andauern kann');
define('MODULE_PAYMENT_NOVALNET_GUARANTEED_SEPA_MESSAGE', 'Ihre Bestellung wird derzeit überprüft. Wir werden Sie in Kürze über den Bestellstatus informieren. Bitte beachten Sie, dass dies bis zu 24 Stunden dauern kann');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION', 'Bestellbest&auml;tigung - Ihre Bestellung ');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION1', ' bei ');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION2', ' wurde best&auml;tigt');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION4', ' wurde storniert');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT', 'TT.MM.JJJJ');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION3', '<br><br>Wir freuen uns Ihnen mitteilen zu k&ouml;nnen, dass Ihre Bestellung best&auml;tigt wurde');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_INFORMATION', 'Zahlung Informationen:');

define('MODULE_PAYMENT_NOVALNET_METHOD_COMMENT', 'Zahlungsweise:');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_CALLBACK_INPUT_TITLE', 'Telefonnummer');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_INPUT_TITLE', 'Mobiltelefonnummer');

define('MODULE_PAYMENT_NOVALNET_PENDING_TO_ONHOLD', 'Novalnet-Callback-Nachricht erhalten: Der Status der Transaktion mit der TID: %s wurde am %s um Uhr von ausstehend auf ausgesetzt geändert.');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CONFIRMED', 'Novalnet-Callback-Nachricht erhalten: Die Buchung wurde am %s um Uhr bestätigt.');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CANCELLED', 'Novalnet-Callback-Nachricht erhalten: Die Transaktion wurde am %s um Uhr storniert');

$novalnet_temp_status_text = 'Zahlung &uuml;ber NN steht noch aus';
