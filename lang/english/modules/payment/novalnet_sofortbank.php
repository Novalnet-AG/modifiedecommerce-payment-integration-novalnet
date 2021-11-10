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
 * Script : novalnet_sofortbank.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_TEXT_TITLE', 'Instant Bank Transfer ');
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_TEXT_DESCRIPTION', '<br>After the successful verification, you will be redirected to Novalnet secure order page to proceed with the payment<br/>');
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_PUBLIC_TITLE', (((!defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY')) || MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? '<a href="https://www.novalnet.com/online-instant-transfer" title="Instant Bank Transfer " target="_blank"/>'.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_instantbank.png', "Instant Bank Transfer").'</a>':''));
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_VISIBILITY_BYAMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_VISIBILITY_BYAMOUNT_DESC', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ENDCUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ENDCUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_PAYMENT_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_PAYMENT_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_SOFORTBANK_BLOCK_TITLE', '<b>Instant Bank Transfer Configuration</b>');