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
 * Script : novalnet_przelewy24.php
 *
 */

include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_TEXT_TITLE', 'Przelewy24 ');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_TEXT_DESCRIPTION', '<br>Nach der erfolgreichen &Uuml;berpr&uuml;fung werden Sie auf die abgesicherte Novalnet-Bestellseite umgeleitet, um die Zahlung fortzusetzen<br />');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_PUBLIC_TITLE',(((!defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY')) || MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? '<a href="https://www.novalnet.de/przelewy24" title="Przelewy24" target="_blank"/>'.xtc_image(DIR_WS_ICONS.'novalnet/novalnet_przelewy24.png',"Przelewy24" ).'</a>':''));
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ALLOWED_TITLE', MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ALLOWED_DESC', MODULE_PAYMENT_NOVALNET_ALLOWED_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_VISIBILITY_BYAMOUNT_TITLE', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_VISIBILITY_BYAMOUNT_DESC', MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ENDCUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ENDCUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_PENDING_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_PENDING_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ORDER_STATUS_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_ORDER_STATUS_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_PAYMENT_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_PAYMENT_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_BLOCK_TITLE', '<b>Przelewy24 Konfiguration</b>');
