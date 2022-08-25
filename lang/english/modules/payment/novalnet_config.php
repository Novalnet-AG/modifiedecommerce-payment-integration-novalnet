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
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Novalnet Global Configuration');
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION', '<span style="font-weight: bold; color:#878787;">Please read the Installation Guide before you start and login to the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a> using your merchant account. To get a merchant account, mail to <a style="font-weight: bold; color:#0080c9;" href="mailto:sales@novalnet.de">sales@novalnet.de</a> or call +49 (089) 923068320</span><br/><br/><span style="font-weight: bold; color:#878787;">To accept PayPal transactions, configure your PayPal API info in the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a>> PROJECT > "Project" Information > Payment Methods > Paypal > Configure</span> ');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', 'Product activation key');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC', 'Get your Product activation key from the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a>: PROJECT > Choose your project > Shop Parameters > API Signature (Product activation key)');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE', 'Merchant ID');
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_TITLE', 'Authentication code');
define('MODULE_PAYMENT_NOVALNET_AUTHCODE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE', 'Project ID');
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC', '');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Select Tariff ID');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC', 'Select a Tariff ID to match the preferred tariff plan you created at the Novalnet Admin Portal for this project');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Payment access key');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC', '');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY_TITLE', 'Client key');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY_DESC', '');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_TITLE', '<h2>Order status management for on-hold transactions</h2>On-hold order status');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_DESC', 'Status to be used for on-hold orders until the transaction is confirmed or canceled');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_TITLE', 'Canceled order status');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_DESC', 'Status to be used when order is canceled or fully refunded');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h2>Notification / Webhook URL Setup</h2>Allow manual testing of the Notification / Webhook URL');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', 'Enable this to test the Novalnet Notification / Webhook URL manually. Disable this before setting your shop live to block unauthorized calls from external parties');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE', 'Enable e-mail notification');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC', 'Enable this option to notify the given e-mail address when the Notification / Webhook URL is executed successfully');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', 'Send e-mail to');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'Notification / Webhook URL execution messages will be sent to this e-mail');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_TITLE', 'Notification / Webhook URL');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_DESC', 'Notification / Webhook URL is required to keep the merchant’s database/system synchronized with the Novalnet account (e.g. delivery status). Refer the Installation Guide for more information');
