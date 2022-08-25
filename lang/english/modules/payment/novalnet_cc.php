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
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', 'Credit/Debit Cards');
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_DESCRIPTION', '<br>The amount will be debited from your credit/debit card<br />');
define('MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE', xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_visa.png', "Credit/Debit Cards").' '.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_mastercard.png', "Credit/Debit Cards") .' '.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_amex.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_maestro.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_cartasi.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_unionpay.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_discover.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_diners.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_jcb.png', "Credit/Debit Cards").' '. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_carte-bleue.png', "Credit/Debit Cards"));

define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_CC_INLINE_FORM_TITLE', 'Enable inline form');
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
define('MODULE_PAYMENT_NOVALNET_CC_ONE_CLICK', 'One-click shopping');
define('MODULE_PAYMENT_NOVALNET_CC_ZERO_AMOUNT', 'Zero amount booking');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_TYPE', 'Type of card');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_HOLDER', 'Card holder name');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_NO', 'Card number');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_VALID_DATE', 'Expiry date');
define('MODULE_PAYMENT_NOVALNET_CC_BLOCK_TITLE', '<b>Credit Card Configuration</b>');
define('MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS', 'Your credit card details are invalid');
define('MODULE_PAYMENT_NOVALNET_CC_NEW_ACCOUNT', 'Add new card details for later purchases');
define('MODULE_PAYMENT_NOVALNET_CC_GIVEN_ACCOUNT', 'Given card details');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_TITLE', '<h3 id="css_settings">CSS settings for Credit Card iframe</h3><span id="css_settings_label" style="font-weight:normal;">Label</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_TITLE', '<span id="css_settings_label_input" style="font-weight:normal;">Input</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_TITLE', '<span id="css_settings_label_text" style="font-weight:normal;">CSS Text</span>');
define('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT_DESC', '');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS', 'I want to save my card details for later purchases');
define('MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING', '<br><span style="color:red">This order will be processed as zero amount booking which store your payment data for further online purchases</span>');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_TITLE', 'Payment action');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_DESC', 'Choose whether or not the payment should be charged immediately. Capture completes the transaction by transferring the funds from buyer account to merchant account. Authorize verifies payment details and reserves funds to capture it later, giving time for the merchant to decide on the order');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_CAPTURE', 'Capture');
define('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_AUTH', 'Authorize');
