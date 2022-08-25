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
 * Script : NovalnetUtil.php
 *
 */
ob_start();
include_once(DIR_FS_INC.'xtc_format_price_order.inc.php');
include_once(DIR_FS_INC.'xtc_validate_email.inc.php');
include_once(DIR_FS_INC.'xtc_php_mail.inc.php');
include_once(DIR_FS_CATALOG.'includes/external/novalnet/classes/NovalnetUtil.php');

class NovalnetPayment extends NovalnetUtil
{

    /**
     * Function : setPaymentDetails()
     * @return void
     */
    function setPaymentDetails() {

        // Payment title
        $this->title = NovalnetUtil::checkDefined('MODULE_PAYMENT_'. $this->code .'_TEXT_TITLE');

        // Payment public title
        $this->public_title = NovalnetUtil::checkDefined('MODULE_PAYMENT_'. $this->code .'_PUBLIC_TITLE');

       // Payment description
        $this->description  = NovalnetUtil::checkDefined('MODULE_PAYMENT_'. $this->code .'_TEXT_DESCRIPTION');
        
        if (strpos(MODULE_PAYMENT_INSTALLED, $this->code) !== false) {
            $this->sort_order = NovalnetUtil::checkDefined('MODULE_PAYMENT_'. $this->code .'_SORT_ORDER');
            $this->enabled = NovalnetUtil::checkDefined('MODULE_PAYMENT_'. $this->code .'_STATUS') == 'True';
        }
    }
    
    /**
     * Function : updateStatusProcess()
     * @return void
     */
    function updateStatusProcess() {
        global $order;

        if (($this->enabled) && ((int) constant('MODULE_PAYMENT_'. strtoupper($this->code) . '_PAYMENT_ZONE') > 0)) {
            $check_flag = false;
            $check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_NOVALNET_CC_PAYMENT_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }
    
    /**
     * Function : checkoutSelectionDetails()
     * @return array
     */
    function checkoutSelectionDetails() {
        $database_version = xtc_db_query('SELECT version from database_version');
        $shop_version = xtc_db_fetch_array($database_version);
        $shop_version = explode('_', $shop_version['version']);
        if ($shop_version['1'] > '2.0.4.0') {
		    $js_scrpt = '<script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/jquery.js" type="text/javascript"></script>';
        }

        return array(
            'id'          => $this->code,
            'module'      => $this->title,
            'description' => $this->public_title.$js_scrpt,
        );
        
    }

    /**
     * Function : checkoutFraudModuleSelection()
     * @return array
     */ 
    function checkoutFraudModuleSelection() {
            // Display fraud module field
           return array(
                'CALLBACK' => array(
                    'name' =>'_fraud_tel',
                    'value' => 'telephone'
                ), 'SMS' => array(
                    'name' =>'_fraud_mobile',
                    'value' => 'mobile'
                )
            );
    }
    
    /**
     * Function : paymentKeyModeType()
     * @param $payment_type
     * @return array
     */
    function paymentKeyModeType($payment_type) {
        return array(
            'key'          => $this->key,
            'test_mode'    => (int)(constant('MODULE_PAYMENT_'.strtoupper($payment_type).'_TEST_MODE') == 'True'),
            'payment_type' => $this->payment_type
        );
    }
    
    /**
     * Function : guaranMinValid()
     * @param $payment_type
     * @param $admin
     * @return boolean
     */
    function guaranMinValid($payment_type, $admin) {
        $minimum_amount = trim(constant('MODULE_PAYMENT_'.strtoupper($payment_type).'_GUARANTEE_MIN_AMOUNT'));
                $minimum_amount = $minimum_amount != '' ? $minimum_amount : 999;
                // Validate GUARANTEE minimum amount
                if ($minimum_amount != '') {
                    if (!preg_match('/^\d+$/', $minimum_amount)) {
                        if ($admin) {
                            echo NovalnetUtil::novalnetBackEndShowError(constant('MODULE_PAYMENT_'.strtoupper($payment_type).'_BLOCK_TITLE'), MODULE_PAYMENT_NOVALNET_GUARANTEE_AMOUNT_ERROR);
                        }
                        return false;
                    } elseif ($minimum_amount < 999) {
                        if ($admin) {
                            echo NovalnetUtil::novalnetBackEndShowError(constant('MODULE_PAYMENT_'.strtoupper($payment_type).'_BLOCK_TITLE'), MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_ERROR);
                        }
                        return false;
                    }
                }
         return true;
    }

    /**
     * Function : paymentInitialParams()
     * @param $input_params
     * @return array
     */
    function paymentInitialParams($input_params) {
        return array(
            'vendor'           => $input_params['vendor'],
            'product'          => $input_params['product'],
            'tariff'           => $input_params['tariff'],
            'auth_code'        => $input_params['auth_code'],
            'payment_id'       => !empty($input_params['key']) ? $input_params['key'] : $input_params['payment_id'],
            );
    } 
    
    /**
     * Function : redirectInitialParams()
     * @param $payment_response
     * @return array
     */
    function redirectInitialParams($payment_response) {
     return array(
                  'tid'                   => $payment_response['tid'],
                  'amount'                => $payment_response['amount'],
                  'currency'              => $payment_response['currency'],
                  'gateway_response'      => $payment_response,
                  'customer_id'           => $payment_response['customer_no'],
                  'gateway_status'        => $payment_response['tid_status']
                  );
    }
    /**
     * Function : nnJsInclude()
     * @return void
     */
    function nnJsInclude () {
        $database_version = xtc_db_query('SELECT version from database_version');
        $shop_version = xtc_db_fetch_array($database_version);
        $shop_version = explode('_', $shop_version['version']);
        if (floor($shop_version['1']) == '1') {
            echo '<script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/jquery.js" type="text/javascript"></script>';
        }
        
    }
}
