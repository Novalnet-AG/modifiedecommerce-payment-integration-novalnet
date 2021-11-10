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
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', 'Kredit- / Debitkarte ');
define('MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE', xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_visa.png', "Kredit- / Debitkarte").' '.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_mastercard.png', "Kredit- / Debitkarte") .' '.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_amex.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_maestro.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_cartasi.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_unionpay.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_discover.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_diners.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_jcb.png', "Kredit- / Debitkarte").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_carte-bleue.png', "Kredit- / Debitkarte"));

define('MODULE_PAYMENT_NOVALNET_CC_TEXT_DESCRIPTION', '<br>Der Betrag wird Ihrer Kredit-/Debitkarte belastet <br />');
define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_INLINE_FORM_TITLE', 'Inline-Formular erm&ouml;glichen');
define('MODULE_PAYMENT_NOVALNET_CC_INLINE_FORM_DESC', '');
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
define('MODULE_PAYMENT_NOVALNET_CC_BLOCK_TITLE', '<b>Kreditkarte Konfiguration</b>');
define('MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS', 'Ihre Kreditkartendaten sind ungültig.');
define('MODULE_PAYMENT_NOVALNET_CC_NEW_ACCOUNT', 'Neue Kartendaten f&uuml;r sp&auml;tere K&auml;ufe hinzuf&uuml;gen');
define('MODULE_PAYMENT_NOVALNET_CC_GIVEN_ACCOUNT', 'Eingegebene Kartendaten');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_TITLE', '<h3>CSS-Einstellungen f&uuml;r den iFrame mit Kreditkartendaten</h3><span style="font-weight:normal;">Beschriftung</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_TITLE', '<span style="font-weight:normal;">Eingabe</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_TITLE', '<span style="font-weight:normal;">
Text f&uuml;r das CSS</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS', 'Ich m&ouml;chte meine Kartendaten f&uuml;r sp&auml;tere Eink&auml;ufe speichern ');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING', '<span style="color:red"><br/>Diese Bestellung wird als Nullbuchung verarbeitet. Ihre Zahlungsdaten werden f&uuml;r zuk&uuml;nftige Online-Eink&auml;ufe gespeichert</span>'); 
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_TITLE', 'Aktion für vom Besteller autorisierte Zahlungen');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_DESC', 'W&aumlhlen Sie, ob die Zahlung sofort belastet werden soll oder nicht. Zahlung einziehen: Betrag sofort belasten. Zahlung autorisieren: Die Zahlung wird &uumlberpr&uumlft und autorisiert, aber erst zu einem sp&aumlteren Zeitpunkt belastet. So haben Sie Zeit, &uumlber die Bestellung zu entscheiden');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_CAPTURE', 'Zahlung einziehen');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_AUTH', 'Zahlung autorisieren');
