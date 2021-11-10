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
define('MODULE_PAYMENT_NOVALNET_PP_TEXT_DESCRIPTION', '<br>You will be redirected to PayPal. Please don&#39;t close or refresh the browser until the payment is completed<br />');
define('MODULE_PAYMENT_NOVALNET_PP_PUBLIC_TITLE', xtc_image(DIR_WS_ICONS.'novalnet/novalnet_paypal.png', "PayPal"));
define('MODULE_PAYMENT_NOVALNET_PP_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC);
define('MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE_TITLE', 'Shopping type');
define('MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE_DESC', 'Select shopping type <br><span id=\'paypal_message\' style=color:red></span>');
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
define('MODULE_PAYMENT_NOVALNET_PP_BLOCK_TITLE', '<b>PayPal API Configuration</b>');
define('MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK', 'One-click shopping');
define('MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT', 'Zero amount booking');
define('MODULE_PAYMENT_NOVALNET_PP_TRANSACTION_ID', 'PayPal transaction ID ');
define('MODULE_PAYMENT_NOVALNET_PP_REF_TRANSACTION_ID', 'Novalnet transaction ID ');
define('MODULE_PAYMENT_NOVALNET_PP_NEW_ACCOUNT', 'Proceed with new PayPal account details');
define('MODULE_PAYMENT_NOVALNET_PP_GIVEN_ACCOUNT', 'Given PayPal account details');
define('MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK_DESC', '<br>Once the order is submitted, the payment will be processed as a reference transaction at Novalnet<br>');
define('MODULE_PAYMENT_NOVALNET_PP_SHOW_MESSAGE', 'In order to use this option you must have billing agreement option enabled in your PayPal account. Please contact your account manager at PayPal.');
define('MODULE_PAYMENT_NOVALNET_PP_SAVECARD_DETAILS', 'Save my PayPal account details for later purchases');
define('MODULE_PAYMENT_NOVALNET_PP_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING', '<br>This order will be processed as zero amount booking which store your payment data for further online purchases');
define('MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT_COMMENTS', '<span style="color: red;"><br/>This order will be processed as zero amount booking which store your payment data for further online purchases</span>');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_TITLE', 'Payment action');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_DESC', 'Choose whether or not the payment should be charged immediately. Capture completes the transaction by transferring the funds from buyer account to merchant account. Authorize verifies payment details and reserves funds to capture it later, giving time for the merchant to decide on the order');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_CAPTURE', 'Capture');
define('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_AUTH', 'Authorize');
