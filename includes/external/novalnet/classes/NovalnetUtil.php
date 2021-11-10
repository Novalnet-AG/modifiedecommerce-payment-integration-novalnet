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
 * @author      Novalnet AG
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 *
 * Script : NovalnetUtil.php
 *
 */
ob_start();

include_once(DIR_FS_INC.'xtc_format_price_order.inc.php');
include_once(DIR_FS_INC.'xtc_php_mail.inc.php');
include_once(DIR_FS_INC.'xtc_validate_email.inc.php');

class NovalnetUtil
{

    /**
     * Validate the global configuration and display the error/warning message
     * @param $admin
     *
     * @return boolean
     */
    public static function checkMerchantConfiguration($admin = false)
    {
        // Validate basic configurations.
        $error = self::merchantValidate($admin);

        if ($error && $_REQUEST['action']) {
            if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config') !== false) {
                if ($admin && defined('MODULE_PAYMENT_NOVALNET_CONFIG_BLOCK_TITLE')) {
                    if ($error !== true) {
                        echo self::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_CONFIG_BLOCK_TITLE, $error);
                    } else {
                        echo self::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_CONFIG_BLOCK_TITLE);
                    }
                }
            }
            return false;
        }
        if ($error == true) {
            return false;
        }

        return true;
    }

    /**
     * Validate the payment configuration and display the error/warning message
     * @param $admin
     *
     * @return mixed
     */
    public static function merchantValidate($admin)
    {
        $merchant_api_error = false;

        if (empty(MODULE_PAYMENT_NOVALNET_PUBLIC_KEY)) {
            $merchant_api_error = true;
        } elseif ((MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO != '' && !self::validateEmail(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO))) {
            $merchant_api_error = true;
        }

        return $merchant_api_error;
    }

    /**
     * Validate E-mail address
     * @param $emails
     *
     * @return boolean
     */
    public static function validateEmail($emails)
    {
        $email = explode(',', $emails);
        foreach ($email as $value) {
            // Validate E-mail.
            if (!xtc_validate_email($value)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Get SEPA due date
     * @param none
     *
     * @return string
     */
    public static function sepaDuedate()
    {
        $sepa_due_date_limit = trim(MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE);
        if ($sepa_due_date_limit != '' && $sepa_due_date_limit <= 14 && $sepa_due_date_limit >= 2)
        {
            return $sepa_due_date_limit;
        }
    }

    /**
     * Show validation error in back end
     * @param $error_payment
     * @param $other_error
     *
     * @return string
     */
    public static function novalnetBackEndShowError($error_payment, $other_error = '')
    {
        if ( $_REQUEST['action'] && !$_REQUEST['box']) {
            return '<div style="border: 1px solid #0080c9; background-color: #FCA6A6; padding: 10px; font-family: Arial, Verdana; font-size: 11px; margin:0px 5px 5px 0;"><b>'.$error_payment.'</b><br/><br/>'.($other_error != '' ? $other_error : MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR).'</div>';
        }
    }

    /**
     * Process payment visibility
     * @param $order_amount
     * @param $payment_visible_amount
     *
     * @return boolean
     */
    public static function hidePaymentVisibility($order_amount, $payment_visible_amount)
    {
        return ($payment_visible_amount == '' || ((int)$payment_visible_amount <= (int)$order_amount));
    }

    /**
     * Validate callback status
     * @param $payment
     * @param $callback
     *
     * @return boolean
     */
    public static function validateCallbackStatus($payment, $callback = false)
    {
        return ($callback && isset($_SESSION[$payment.'payment_lock_nn']) && $_SESSION[$payment.'payment_lock_nn'] && time() < $_SESSION[$payment.'_callback_max_time_nn']) ? false : true;
    }

    /**
     * Function to communicate transaction parameters with Novalnet Paygate
     * @param $paygate_url
     * @param $data
     * @param $build_query
     *
     * @return array
     */
    public static function doPaymentCurlCall($paygate_url, $data, $build_query = true)
    {
        $paygate_query = ($build_query) ? http_build_query($data) : $data;

        // Initiate cURL.
        $curl_process = curl_init($paygate_url);
        // Set cURL options.
        curl_setopt($curl_process, CURLOPT_POST, 1);
        curl_setopt($curl_process, CURLOPT_POSTFIELDS, $paygate_query);
        curl_setopt($curl_process, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl_process, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_process, CURLOPT_RETURNTRANSFER, 1);

        // Custom CURL time-out.
        curl_setopt($curl_process, CURLOPT_TIMEOUT, 240);

        // Execute cURL.
        $response = curl_exec($curl_process);
        // Handle cURL error.
        if (curl_errno($curl_process)) {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, utf8_decode('error_message=' .curl_error($curl_process)), 'SSL', true, false));
        }

        // Close cURL.
        curl_close($curl_process);
        parse_str($response, $payment_response);
        return $response;
    }

    /**
     * Build input fields to get PIN
     * @param $fraud_module
     * @param $code
     *
     * @return array
     */
    public static function buildCallbackFieldsAfterResponse($fraud_module, $code)
    {
        $pin_field = array();
        $pin_field[] = array(
                         'title' => MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_REQUEST_DESC."<span style='color:red'> * </span>",
                         'field' => xtc_draw_input_field($code . '_fraud_pin', '', 'autocomplete="off" id="' . $code . '-'. strtolower($fraud_module) .'pin"')
                       );
        $pin_field[] = array(
                        'title' => '',
                        'field' => xtc_draw_checkbox_field($code.'_new_pin', '1', false, 'id="' . $code . '-' . strtolower($fraud_module) . 'new_pin"') . MODULE_PAYMENT_NOVALNET_FRAUDMODULE_NEW_PIN
                       );
        return $pin_field;
    }

    /**
     * Validate status of fraud module
     * @param $payment
     * @param $fraud_module
     *
     * @return boolean
     */
    public static function setFraudModuleStatus($payment, $fraud_module)
    {
        global $order;

        $customer_iso_code = strtoupper($order->customer['country']['iso_code_2']);

        // Check for fraud module.
        if (!$fraud_module || !in_array($customer_iso_code, array('DE', 'AT', 'CH')) || constant('MODULE_PAYMENT_'.strtoupper($payment).'_CALLBACK_LIMIT') > self::getPaymentAmount((array)$order)) {
            return false;
        }
        return true;
    }

    /**
     * Validate input form callback parameters
     * @param $data
     * @param $fraud_module
     * @param $fraud_module_status
     * @param $code
     *
     * @return void
     */
    public static function validateCallbackFields($data, $fraud_module, $fraud_module_status, $code)
    {
        if (empty($_SESSION['novalnet'][$code]['tid']) && $fraud_module_status) {
            if ($fraud_module == 'CALLBACK') {
                // Check telephone number
                $tel_number = trim($data[$code . '_fraud_tel']);
                if (empty($tel_number) || (!preg_match("/^(\+{0,1}[0-9][0-9\s]*|[0-9][0-9\s]*)$/", $tel_number))) {
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='. utf8_decode(MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TELEPHONE_ERROR), 'SSL', true, false));
                }
            } else {
                // Check mobile number
                $mobile_number = trim($data[$code . '_fraud_mobile']);
                if (empty($mobile_number) || (!preg_match("/^(\+{0,1}[0-9][0-9\s]*|[0-9][0-9\s]*)$/", $mobile_number))) {
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='. utf8_decode(MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_ERROR), 'SSL', true, false));
                }
            }
        }
    }

    /**
     * Redirect to checkout on success using fraud module
     * @param $payment
     * @param $fraud_module
     * @param $fraud_module_status
     *
     * @return void
     */
    public static function gotoPaymentOnCallback($payment, $fraud_module = null, $fraud_module_status = null)
    {
        if ($fraud_module && $fraud_module_status) {
            $_SESSION['novalnet'][$payment]['secondcall'] = true;
            $error_message = ($fraud_module == 'SMS') ? utf8_decode(MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_PIN_INFO) : utf8_decode(MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_INFO);

            // Display fraud module message for payment page after geting response
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . trim($error_message), 'SSL', true, false));
        }
    }

    /**
     * Validate pin field
     * @param $payment_module
     * @param $data
     *
     * @return void
     */
    public static function validateUserInputsOnCallback($payment_module, $data = array())
    {
        $data = array_map('trim', $data);

        // PIN validation
        if (!isset($data[$payment_module . '_new_pin']) && isset($data[$payment_module . '_fraud_pin']) && (empty($data[$payment_module . '_fraud_pin']) || !preg_match('/^[a-zA-Z0-9]+$/', $data[$payment_module . '_fraud_pin']))) {
            $error_message = $data[$payment_module . '_fraud_pin'] == '' ? MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_EMPTY : MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_NOTVALID;
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . trim($error_message), 'SSL', true, false));
        } else {
            // Check for new PIN request
            $_SESSION['novalnet'][$payment_module .'_new_pin'] = !isset($data[$payment_module . '_new_pin']) ? 0 : '';
        }
    }

    /**
     * Return payment amount of given order
     * @param $data
     *
     * @return integer
     */
    public static function getPaymentAmount($data)
    {
        $total = ((isset($_SESSION['customers_status']) && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)) ? ($data['info']['total'] + $data['info']['tax']) : $data['info']['total'];
        // Convert into Cents
        $total = ((sprintf('%0.2f', $total)) * 100);

        if (preg_match('/[^\d\.]/', $total)) {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, self::setUtf8Mode('error_message=' .MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE), 'SSL', true, false));
        }

        return $total;
    }

    /**
     * Collect Customer DOB, FAX, Gender information from the database
     * @param $customer_email
     *
     * @return array
     */
    public static function collectCustomerDobGenderFax($customer_email = '')
    {
        // Get customer details
        if ($customer_email != '') {
            $querySearch = (isset($_SESSION['customer_id']) && $_SESSION['customers_status']['customers_status_id'] != '1') ? 'customers_id= "'. xtc_db_input($_SESSION['customer_id']).'"' : 'customers_email_address= "'. xtc_db_input($customer_email).'"';
            $select_query = xtc_db_query("SELECT customers_id, customers_cid, customers_gender, customers_dob, customers_fax, customers_vat_id FROM ". TABLE_CUSTOMERS . " WHERE ".$querySearch." ORDER BY customers_id");
            $customer_dbvalue = xtc_db_fetch_array($select_query);
            if (!empty($customer_dbvalue)) {
                $customer_dbvalue['customers_dob'] = ($customer_dbvalue['customers_dob'] != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($customer_dbvalue['customers_dob'])) : '';
                return $customer_dbvalue;
            }
        }
        return array('u', '', '', '', '');
    }

    /**
     * Generate Novalnet gateway parameters based on payment selection
     * @param $data
     *
     * @return array
     */
    public static function getCommonRequestParams($data)
    {
        global $order;
        if ($data['order_amount'] == '') {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' .self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE), 'SSL', true, false));
        }

        $tariff_details = explode('-', MODULE_PAYMENT_NOVALNET_TARIFF_ID);
        $tariff_type    = $tariff_details[0];
        $tariff         = $tariff_details[1];

        $customer_details = self::collectCustomerDobGenderFax($data['customer']['email_address']);

        $database_version = xtc_db_query('SELECT version from database_version');
        $shop_version = xtc_db_fetch_array($database_version);

        // Get IP-address
        $client_ip = self::getIpAddress('REMOTE_ADDR');
        $system_ip = self::getIpAddress('SERVER_ADDR');

        // Form basic payment parameters
        $urlparam = array(
          'vendor'           => MODULE_PAYMENT_NOVALNET_VENDOR_ID,
          'product'          => MODULE_PAYMENT_NOVALNET_PRODUCT_ID,
          'tariff'           => $tariff,
          'auth_code'        => MODULE_PAYMENT_NOVALNET_AUTHCODE,
          'amount'           => $data['order_amount'],
          'currency'         => $data['info']['currency'],
          'first_name'       => $data['billing']['firstname'],
          'last_name'        => $data['billing']['lastname'],
          'gender'           => $customer_details['customers_gender'],
          'email'            => $data['customer']['email_address'],
          'street'           => $data['billing']['street_address'],          
          'search_in_street' => 1,
          'city'             => $data['billing']['city'],
          'zip'              => $data['customer']['postcode'],
          'tel'              => $data['customer']['telephone'],
          'fax'              => $customer_details['customers_fax'],
          'customer_no'      => (($customer_details['customers_id'] != '') ? $customer_details['customers_id'] : (($customer_details['customers_id'] != '') ? $customer_details['customers_id'] : 'Guest')),
          'system_name'      => 'modified',
          'remote_ip'        => $client_ip,
          'system_ip'        => $system_ip,
          'system_version'   => (! empty ($shop_version['version'] ) ? $shop_version['version']:'').'-NN11.2.1',
          'system_url'       => ((ENABLE_SSL == true) ? HTTPS_SERVER : HTTP_SERVER),
        );

        $urlparam['country_code'] = $data['billing']['country']['iso_code_2'];
        $urlparam['lang'] = ((isset($_SESSION['language_code'])) ? strtoupper($_SESSION['language_code']) : 'DE');
        $urlparam['tariff_type'] = $tariff_type;

        $additional_parameters = array();

        // Append Vendor script URL
        $additional_parameters['notify_url'] = trim(MODULE_PAYMENT_NOVALNET_CALLBACK_URL);

        // Append company parameter
        $additional_parameters['company'] = trim($data['billing']['company']);

        // Append vat parameter
        $additional_parameters['vat_id'] = $customer_details['customers_vat_id'];

        return array_merge(array_filter($urlparam), array_filter($additional_parameters));
    }

    /**
     * Form redirection parameters
     * @param $redirect_data
     *
     * @return void
     */
    public static function formRedirectionParams(&$redirect_data)
    {
        global $insert_id;

        $redirect_data['return_method']    = $redirect_data['error_return_method'] = 'POST';
        $redirect_data['user_variable_0']  = ((ENABLE_SSL == true) ? HTTPS_SERVER:HTTP_SERVER);
        $redirect_data['order_no']         = $insert_id;
        $redirect_data['implementation']   = 'ENC';
        $redirect_data['uniqid']           = self::getUniqueid();
        $checkout_process_url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');

        if (strpos($checkout_process_url, xtc_session_name()) == false) {
           $redirect_data['return_url']       = $redirect_data['error_return_url']  = xtc_href_link(FILENAME_CHECKOUT_PROCESS, xtc_session_name().'='.xtc_session_id(), 'SSL');
        }
        else {
            $redirect_data['return_url']       = $redirect_data['error_return_url']  = $checkout_process_url;
        }
    }

    /**
     * Perform encode process for redirection payment methods
     * @param &data
     *
     * @return void
     */
    public static function generateEncodeValue(&$data)
    {
        foreach (array('auth_code', 'product', 'tariff', 'amount', 'test_mode') as $key) {
            if (isset($data[$key])) {
                // Encoding process
                $data[$key] = htmlentities(base64_encode(openssl_encrypt($data[$key], "aes-256-cbc", $_SESSION['novalnet']['nn_access_key'], true, $data['uniqid'])));
            }
        }

         // Generate hash value
         $data['hash'] = self::generateHashValue($data);
    }

    /**
     * Perform Decode Generation process for redirection payment methods
     * @param &data
     *
     * @return void
     */
    public static function generateDecodedata(&$data)
    {
        foreach (array('auth_code','product','tariff','amount','test_mode') as $key) {
          // Decoding process
            $data[$key] = openssl_decrypt(base64_decode($data[$key]), "aes-256-cbc", $_SESSION['novalnet']['nn_access_key'], true, $data['uniqid']);
        }

        return $data;
    }

    /**
     * Perform HASH Generation process for redirection payment methods
     * @param &data
     *
     * @return void
     */
    public static function generateHashValue($data)
    {
        // Hash generation using sha256 and encoded merchant details
        return hash('sha256', ($data['auth_code'].$data['product'].$data['tariff'].$data['amount'].$data['test_mode'].$data['uniqid'].strrev($_SESSION['novalnet']['nn_access_key'])));
    }

    /**
     * Perform HASH Validation with paygate response
     * @param $data
     *
     * @return boolean
     */
    public static function validateHashResponse($data)
    {
        // Check for hash error
        return ($data['hash2'] != self::generateHashValue($data));
    }


    /**
     * Check transaction status message
     * @param $response
     *
     * @return string
     */
    public static function getTransactionMessage($response)
    {
        return (!empty($response['status_message']) ? utf8_decode($response['status_message']) : !empty($response['status_desc']) ? utf8_decode($response['status_desc']) : (!empty($response['status_text']) ? utf8_decode($response['status_text']) : MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR));
    }

    /**
     * Insert datas in Novalnet tables
     * @param $data
     *
     * @return void
     */
    public static function doPostProcess($data)
    {
        $payment = $data['payment'];

        // Insert datas in Novalnet transaction detail table
        self::logInitialTransaction($data);

        // Insert datas in Novalnet affiliate user detail table
        if (isset($_SESSION['nn_aff_id'])) {
            xtc_db_perform('novalnet_aff_user_detail', array(
                'aff_id'        => $_SESSION['nn_aff_id'],
                'customer_id'   => $_SESSION['customer_id'],
                'aff_order_no'  => $data['order_no']
            ));

            // Unset affiliate session
            unset($_SESSION['nn_aff_id']);
        }
    }

    /**
     * Updated the Novalnet transaction comments
     * @param $data
     *
     * @return void
     */
    public static function updateComments($data)
    {
        // Update Novalnet transaction comments and status.
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$data['order_status']."', comments='".$data['comments']."' WHERE orders_id='".$data['order_no']."'");

        $insert_values = array (
            'orders_id'         => $data['order_no'],
            'orders_status_id'  => $data['order_status'],
            'date_added'        => 'now()',
            'customer_notified' => (int) (SEND_EMAILS == 'true'),
            'comments'          => $data['comments'],
        );
        if (self::commentSentColumnExist()) {
            $insert_values['comments_sent'] = 1;
        }

        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $insert_values);
    }

    /**
     * Build the postback call for updating order_no
     * @param $data
     *
     * @return void
     */
    public static function postBackCall($data)
    {
        $payment = $data['payment'];
        // Second call for updating order_no
        $urlparam = array(
            'vendor'    => $_SESSION['novalnet'][$payment]['vendor'],
            'product'   => $_SESSION['novalnet'][$payment]['product'],
            'tariff'    => $_SESSION['novalnet'][$payment]['tariff'],
            'auth_code' => $_SESSION['novalnet'][$payment]['auth_code'],
            'key'       => $_SESSION['novalnet'][$payment]['payment_id'],
            'status'    => 100,
            'tid'       => $_SESSION['novalnet'][$payment]['tid'],
            'order_no'  => $data['order_no'],
        );

        // Add invoice_ref parameter for Invoice and Prepayment
        if (in_array($_SESSION['novalnet'][$payment]['payment_id'], array(27, 41))) {
            $urlparam['invoice_ref'] .= 'BNR-'.$_SESSION['novalnet'][$payment]['product'].'-'.$data['order_no'];
        }

         //For displaying Novalnet comments in front end order history
        if (self::commentSentColumnExist()) {
            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, array(
                'comments_sent' => 1
            ), "update", "orders_id='". $data['order_no'] ."'");
        }

        // Send parameters to Novalnet paygate
        self::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $urlparam);

        // Unset all Novalnet session value
        if (isset($_SESSION['novalnet'])) {
            unset($_SESSION['novalnet']);
        }
    }

    /**
     * Transaction failure process.
     * @param $response
     * @param $payment
     * @param $error_message
     *
     * @return void
     */
    public static function transactionFailure($response, $payment, $error_message = '')
    {
        global $order;

        // Get Error message.
        $message = ($error_message == '') ? self::getTransactionMessage($response) : $error_message;

        $order_status = MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED;

         // Get test mode value.
        $test_mode = (int)(!empty($response['test_mode']) || constant('MODULE_PAYMENT_' . $payment . '_TEST_MODE' == 'True'));
        $transaction_comments = self::formPaymentComments($response['tid'], $test_mode);

        // Update Novalnet transaction comments and status.
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".NovalnetUtil::checkDefaultOrderStatus($order_status)."', comments='".$message.$transaction_comments."' WHERE orders_id='".xtc_db_input($response['order_no'])."'");

        $insert_values = array (
            'orders_id'         => $response['order_no'],
            'orders_status_id'  => $order_status,
            'date_added'        => 'now()', 
            'customer_notified' => (int) (SEND_EMAILS == 'true'),
            'comments'          => PHP_EOL. PHP_EOL.$message.$transaction_comments,
        );
        if (self::commentSentColumnExist()) {
            $insert_values['comments_sent'] = 1;
        }

        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $insert_values);


        if (isset($_SESSION['novalnet'][$payment]['input_params'])) {
            unset($_SESSION['novalnet'][$payment]['input_params']);
        }

        // Novalnet transaction status got failure for displaying error message
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . $message, 'SSL', true, false));
    }

    /**
     * Validate for users over 18 only
     * @param $birthdate
     *
     * @return boolean
     */
    public static function validateAge($birthdate)
    {
        return (empty($birthdate) || time() < strtotime('+18 years', strtotime($birthdate)));
    }


    /**
     * Check condition for displaying birthdate field
     * @param $order
     * @param $payment_name
     *
     * @return integer
     */
    public static function paymentImplementationType($order, $payment_name)
    {
        // Get payment name in caps
        $payment_name_caps = strtoupper($payment_name);

        $guarantee_payment = in_array($payment_name, array('novalnet_invoice', 'novalnet_sepa')) ? constant('MODULE_PAYMENT_'.$payment_name_caps.'_ENABLE_GUARANTEE') : constant('MODULE_PAYMENT_'.$payment_name_caps.'_STATUS');

        // Get guarantee minimum and maximum amount value

        $minimum_amount = trim(constant('MODULE_PAYMENT_'.$payment_name_caps.'_GUARANTEE_MIN_AMOUNT')) ? trim(constant('MODULE_PAYMENT_'.$payment_name_caps.'_GUARANTEE_MIN_AMOUNT')) : '999';
        // Get order details
        $customer_iso_code = strtoupper($order->customer['country']['iso_code_2']);
        $amount = self::getPaymentAmount((array)$order);

        // Delivery address
        $delivery_address = array(
            'street_address' => $order->delivery['street_address'],
            'city'           => $order->delivery['city'],
            'postcode'       => $order->delivery['postcode'],
            'country'        => $order->delivery['country']['iso_code_2'],
        );

        // Billing address
        $billing_address = array(
            'street_address' => $order->billing['street_address'],
            'city'           => $order->billing['city'],
            'postcode'       => $order->billing['postcode'],
            'country'        => $order->billing['country']['iso_code_2'],
        );
      // Check guarantee payment
        if ($guarantee_payment == 'True') {
             if ((((int) $amount >= (int) $minimum_amount) && in_array($customer_iso_code, array('DE', 'AT', 'CH')) && $order->info['currency'] == 'EUR' && $delivery_address === $billing_address)) {
                return array('guarantee', '');
            } elseif (constant('MODULE_PAYMENT_'.$payment_name_caps.'_FORCE_NON_GUARANTEE') == 'True') {
                return array('normal', '');
            } else {
                $guarantee_error = '';
                if (!in_array($customer_iso_code, array('DE', 'AT', 'CH'))) {
                    $guarantee_error .= self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY);
                }
                if ($order->info['currency'] !== 'EUR' ) {
                    $guarantee_error .= '<br/>'.self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY);
                }

                if (array_diff($billing_address, $delivery_address)) {
                    $guarantee_error .= '<br/>'.self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS);
                }
                if ( (int) $amount < (int) $minimum_amount ) {
                    $guarantee_error .= '<br/>'.sprintf(self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT), str_replace('.', ',', $minimum_amount/100) .' '. 'EUR');
                }
                $error_message = PHP_EOL.$guarantee_error;

                return array('error', $error_message);
            }
        }
    }

    /**
     * To get the masked account details
     * @param $customers_id
     * @param $payment
     *
     * @return mixed
     */
    public static function getPaymentRefDetails($customers_id, $payment)
    {
        // Get payment details from novalnet transaction details table

        $query = xtc_db_query("SELECT payment_details FROM novalnet_transaction_detail WHERE customer_id='". xtc_db_input($customers_id) ."' and payment_type = '" . $payment . "' AND one_click_shopping = '1' AND (payment_details != '' OR payment_details IS NULL) order by id desc limit 1");

        while ($data = xtc_db_fetch_array($query)) {
              $result = unserialize($data['payment_details']);
        }
        return $result;
    }

    /**
     * Return JS script to disable confirm button
     * @param none
     *
     * @return string
     */
    public static function confirmButtonDisableActivate()
    {
        return '<script type="text/javascript">

                document.getElementById("checkout_confirmation").onclick = function(){
                document.getElementById("checkout_confirmation").onclick = function() { return false; }
                setTimeout( function() {
                    document.getElementById("checkout_confirmation").disabled = true;
                    document.getElementById("checkout_confirmation").style.opacity = "0.1";
                    }, 500);
                }

            </script>';
    }

    /**
     * Perform server XML request
     * @param $request_type
     * @param $payment_type
     *
     * @return array
     */
    public static function doXMLCallbackRequest($request_type, $payment_type)
    {
         $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <nnxml>
            <info_request>
              <vendor_id>' . $_SESSION['novalnet'][$payment_type]['vendor']. '</vendor_id>
              <vendor_authcode>' .$_SESSION['novalnet'][$payment_type]['auth_code'] . '</vendor_authcode>
              <request_type>' . $request_type . '</request_type>
              <lang>' . ((isset($_SESSION['language_code'])) ? strtoupper($_SESSION['language_code']) : 'DE') . '</lang>
              <tid>' . $_SESSION['novalnet'][$payment_type]['tid'] . '</tid>
              <remote_ip>' . self::getIpAddress('REMOTE_ADDR') . '</remote_ip>';

        // Update PIN value for PIN_STATUS request_type
        if ($request_type == 'PIN_STATUS') {
            $xml .= '<pin>' .trim($_SESSION['novalnet'][$payment_type][$payment_type.'_fraud_pin']) . '</pin>';
        }
        $xml .= '</info_request></nnxml>';

        return self::doPaymentCurlCall('https://payport.novalnet.de/nn_infoport.xml', $xml, false);
    }

    /**
     * Get status and message from server response
     * @param $response
     *
     * @return array
     */
    public static function getStatusFromXmlResponse($response)
    {
        $xml = simplexml_load_string($response);
        return json_decode(json_encode((array)$xml), true);
    }

    /**
     * To control the UTF-8 characters
     * @param $string
     *
     * @return integer
     */
    public static function setUtf8Mode($string)
    {
        if (in_array($_SESSION['language_charset'], array('iso-8859-1', 'iso-8859-15'))) {
            return utf8_decode($string);
        }        
        return $string;
    }

    /**
     * Return Invoice / Prepayment comments
     * @param $data
     *
     * @return array
     */
    public static function formInvoicePrepaymentComments($data)
    {
        $trans_comments = PHP_EOL.self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH).PHP_EOL;
        $invoice_due_date = $data['due_date'];
        if ($data['tid_status'] == '100') {
            $trans_comments .= ($invoice_due_date != '') ? self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_DUE_DATE).': '.date(DATE_FORMAT, strtotime($invoice_due_date)).PHP_EOL : '';
        }
        $trans_comments .= MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER.': '.$data['invoice_account_holder']. PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_IBAN.': '.$data['invoice_iban']. PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_SWIFT_BIC.': '.$data['invoice_bic'].PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_BANK.': '.self::setUtf8Mode($data['invoice_bankname']).' '.self::setUtf8Mode($data['invoice_bankplace']).PHP_EOL;
        $trans_comments .= self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_AMOUNT).': '.xtc_format_price_order($data['amount'], 1, $data['currency']).PHP_EOL;
      
        return array($trans_comments, array('order_no'  => '','tid' => $data['tid'],'test_mode' => $data['test_mode'],
            'account_holder' => $data['invoice_account_holder'], 'bank_name' => $data['invoice_bankname'],'bank_city' => $data['invoice_bankplace'],
            'amount' => $data['amount']*100,'currency' => $data['currency'],'bank_iban' => $data['invoice_iban'],
            'bank_bic' => $data['invoice_bic'],'due_date' => $invoice_due_date ));
    }

    /**
     * Return Invoice / Prepayment payment reference comments
     * @param $order_id
     * @param $data
     *
     * @return array
     */
    public static function novalnetReferenceComments($order_id, $data)
    {
        $comments = self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT) . PHP_EOL;
        $comments .=  MODULE_PAYMENT_NOVALNET_INVPRE_REF1. ':  BNR-' . (!empty($data['product']) ? $data['product'] : MODULE_PAYMENT_NOVALNET_PRODUCT_ID) . '-' .  $order_id. PHP_EOL;
        $comments .=  MODULE_PAYMENT_NOVALNET_INVPRE_REF2 .': TID'.' '. $data['tid'] . PHP_EOL;

        return $comments;
    }

    /**
     * Return Affiliate details
     * @param &$urlparam
     *
     * @return void
     */
    public static function getAffDetails(&$urlparam)
    {
        // Get Payment access key
        $_SESSION['novalnet']['nn_access_key'] = MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY;

        // Check for previous affilliate
        if ($_SESSION['customer_id'] != '' && $_SESSION['customers_status']['customers_status_id'] != DEFAULT_CUSTOMERS_STATUS_ID_GUEST && (!isset($_SESSION['nn_aff_id']) || $_SESSION['nn_aff_id'] == '' )) {

            $value = xtc_db_query('SELECT aff_authcode, aff_accesskey FROM novalnet_aff_account_detail WHERE aff_id = "'.xtc_db_input($_SESSION['nn_aff_id']).'"');
            // Re-assign vendor details
            $db_value = xtc_db_fetch_array($value);
            if (trim($db_value['aff_accesskey']) != '' && trim($db_value['aff_authcode']) != '' && $_SESSION['nn_aff_id'] != '') {
                $urlparam['vendor']         = $_SESSION['nn_aff_id'];
                $urlparam['auth_code']      = $db_value['aff_authcode'];
                $_SESSION['novalnet']['nn_access_key']  = $db_value['aff_accesskey'];
            }

        }
    }

    /**
     * Function to log all Novalnet transaction in novalnet_transaction_detail table
     * @param $data
     *
     * @return void
     */
    public static function logInitialTransaction($data)
    {
        // Insert values in Novalnet transaction details table
        $payment = $data['payment'];
        $session_value = $_SESSION['novalnet'][$payment];
        $nn_unserialize = unserialize($session_value['payment_details']);
        unset($nn_unserialize['payment_id']);

        $table_values = array(
            'tid'                   => $session_value['tid'],
            'payment_id'            => $session_value['payment_id'],
            'payment_type'          => $data['payment'],
            'amount'                => $session_value['amount'],
            'gateway_status'        => $session_value['gateway_status'],
            'order_no'              => $data['order_no'],
            'date'                  => date('Y-m-d H:i:s'),
            'test_mode'             => (int)(isset($session_value['gateway_response']['test_mode']) && $session_value['gateway_response']['test_mode'] == 1),
            'one_click_shopping'    => (int)!empty($session_value['one_click_shopping']),
            'payment_details'       => empty($nn_unserialize) ? serialize(NovalnetPayment::paymentInitialParams($session_value)) : serialize(array_merge($nn_unserialize, NovalnetPayment::paymentInitialParams($session_value))),
            'customer_id'           => $session_value['gateway_response']['customer_no'],
            'reference_transaction' => $session_value['reference_transaction'],
            'zerotrxnreference'     => $session_value['zerotrxnreference'],
            'zerotrxndetails'       => $session_value['zerotrxndetails'],
            'zero_transaction'      => $session_value['zero_transaction'],
            'callback_amount'       => (in_array($data['payment'], array('novalnet_invoice','novalnet_prepayment', 'novalnet_barzahlen')) || ($session_value['gateway_status'] !=100)) ? '0' : $session_value['total_amount'],
            'total_amount'          => $_SESSION['novalnet'][$data['payment']]['total_amount'],
          );

         xtc_db_perform('novalnet_transaction_detail', $table_values);
    }

    /**
     * Check for comments sent column in database
     * @param none
     *
     * @return boolean
     */
    public static function commentSentColumnExist()
    {
        $sql_query = xtc_db_query('SHOW COLUMNS from ' . TABLE_ORDERS_STATUS_HISTORY . ' LIKE "comments_sent"');
        return xtc_db_fetch_array($sql_query);
    }

    /**
     * Form guarantee field
     * @param $name
     * @param $customer_details
     *
     * @return string
     */
    public static function getGuaranteeField($name, $customer_details)
    {
       return xtc_draw_input_field($name, (isset($customer_details['customers_dob']) ? $customer_details['customers_dob'] : ''), 'id="'.$name.'" placeholder="'.MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT.'" autocomplete="OFF" maxlength="10" onkeydown="return NovalnetUtility.isNumericBirthdate(this,event)" onblur="return validateDateFormat(this)"');
    }

    /**
     * Get / Validate IP address
     * @param $ip_type
     *
     * @return string
     */
    public static function getIpAddress($ip_type)
    {
        if ($ip_type == 'REMOTE_ADDR') {
            $ipAddress = xtc_get_ip_address();
        } else {
            if (empty($_SERVER[$ip_type]) && !empty($_SERVER['SERVER_NAME'])) {
                // Handled for IIS server
                $ipAddress = gethostbyname($_SERVER['SERVER_NAME']);
            } else {
                $ipAddress = $_SERVER[$ip_type];
            }
        }
        return $ipAddress;
    }

    /**
     * Check order status
     * @param $order_status
     *
     * @return string
     */
    public static function checkDefaultOrderStatus($order_status)
    {
        return !empty($order_status) ? $order_status : DEFAULT_ORDERS_STATUS_ID;
    }

    /**
     * Form transaction comments
     * @param $tid
     * @param $test_mode
     * @param $payment_type
     * @param $response
     *
     * @return string
     */
    public static function formPaymentComments($tid, $test_mode, $payment_type = false, $response = false)
    {
        $transaction_comments = '';
        if ($tid) {
            $transaction_comments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS.PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $tid;
        }

        // Add test_mode text
        if ($test_mode) {
            $transaction_comments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE.PHP_EOL;
        }

        if (!empty($payment_type) && in_array($payment_type, array('GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA'))) {
            $transaction_comments .= self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS_GUARANTEE_PAYMENT).PHP_EOL;
        }

        if ($response['tid_status'] == 75) {
            $transaction_comments .= ($payment_type == 'GUARANTEED_INVOICE') ? PHP_EOL.self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE).PHP_EOL : PHP_EOL.self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_GUARANTEED_SEPA_MESSAGE).PHP_EOL;
        }

        return $transaction_comments;
    }

    /**
     * Form masked detail fields
     * @param $data
     *
     * @return string
     */
    public static function showMaskedDetails($data)
    {
        $masked_details = '';
        foreach ($data as $attribute) {
            $required = !empty($attribute['required']) ? '<span style="color:red"> * </span>' : '';
            $masked_details .= '<tr><td class="main">'.$attribute['label'].$required.'</td>
                    <td class="main">'.$attribute['value'].'</td>
                </tr>';
        }

        return $masked_details;
    }

    /**
     * Checking zero amount process
     * @param &$data
     * @param $payment
     *
     * @return void
     */
    public static function zeroAmount(&$data, $payment)
    {

        if ($data['tariff_type'] == '2' && constant('MODULE_PAYMENT_'.strtoupper($payment).'_SHOP_TYPE') == 'ZEROAMOUNT') {
            unset($data['tariff_type'], $data['on_hold']);

            // Assigning parameter for zero amount booking
            $_SESSION['novalnet'][$payment]['zero_transaction'] = 1;
            $data['amount'] = 0;
            $data['create_payment_ref'] = 1;
            $_SESSION['novalnet'][$payment]['zerotrxndetails'] = serialize($data);
        }
    }

    /**
     * Get Novalnet transaction information from novalnet_transaction_detail table
     * @param $order_no
     *
     * @return array
     */
    public static function getNovalnetTransDetails($order_no)
    {

        $sqlQuery = xtc_db_query("SELECT tid, payment_id, payment_type, amount, gateway_status, test_mode, customer_id, zero_transaction, zerotrxndetails, payment_details,payment_ref,callback_amount,total_amount,refund_amount,date FROM novalnet_transaction_detail WHERE order_no='". xtc_db_input($order_no) ."'");
        $transInfo = xtc_db_fetch_array($sqlQuery);

        if (!$transInfo) {
            $sqlQuery = xtc_db_query("SELECT tid, payment_id, payment_type, amount, gateway_status, test_mode, customer_id, zero_transaction, zerotrxndetails, payment_details,callback_amount,total_amount,refund_amount,date FROM novalnet_transaction_detail WHERE order_no='". xtc_db_input($order_no) ."'");
            $transInfo = xtc_db_fetch_array($sqlQuery);
        }

        $transInfo['payment_details'] = unserialize($transInfo['payment_details']);

        return $transInfo;
    }

    /**
     * Function to update order status as per Merchant selection
     * @param $order_id
     * @param $orders_status_id
     * @param $message
     *
     * @return boolean
     */
    public static function updateOrderStatus($order_id, $orders_status_id = DEFAULT_ORDERS_STATUS_ID, $message)
    {
        $column_exist = self::commentSentColumnExist() ? ',comments_sent=1' : '';

        xtc_db_query("INSERT INTO ". TABLE_ORDERS_STATUS_HISTORY ." SET orders_id =  '$order_id', date_added = NOW(), customer_notified = '1', comments = '$message', orders_status_id = '$orders_status_id'".$column_exist);
    }

    /**
     * Function to Check the waring error to install global configuration
     * @param $code
     *
     * @return void
     */
    public static function globalConfigInstallError($code)
    {
        if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config') === false) {
            include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet.php');
            xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=payment&error='.MODULE_PAYMENT_NOVALNET_CONFIG_INSTALL_ERROR.'&module=' . $code));
        }
    }

   /**
     * Built Barzahlen comments
     * @param $response
     * @param $due_date
     *
     * @return array
     */
    public static function formBarzahlenComments($response, $due_date = false)
    {
        $barzahlen_comments = '';

        $slip_due_date = !empty($due_date) ? $due_date : $response['cp_due_date'];

        $barzahlen_comments .= MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE_TEXT . ': '.date(DATE_FORMAT, strtotime($slip_due_date)).PHP_EOL;

        $nearest_store =  self::getNearestStore($response);
        $nearest_store['nearest_store'] = $nearest_store;
        if (!empty($nearest_store)) {
            $barzahlen_comments .= PHP_EOL . self::setUtf8Mode(MODULE_PAYMENT_NOVALNET_BARZAHLEN_NEAREST_STORE_DETAILS_TEXT).PHP_EOL;
        }

        $nearest_store['cp_due_date'] = $slip_due_date;
        $i =0;
        foreach ($nearest_store as $key => $values) {
            $i++;
            if (!empty($nearest_store['nearest_store_title_'.$i])) {
                $barzahlen_comments .= PHP_EOL . self::setUtf8Mode($nearest_store['nearest_store_title_'.$i]);
            }
            if (!empty($nearest_store['nearest_store_street_'.$i])) {
                $barzahlen_comments .= PHP_EOL . self::setUtf8Mode($nearest_store['nearest_store_street_'.$i]);
            }
            if (!empty($nearest_store['nearest_store_city_'.$i])) {
                $barzahlen_comments .= PHP_EOL . self::setUtf8Mode($nearest_store['nearest_store_city_'.$i]);
            }
            if (!empty($nearest_store['nearest_store_zipcode_'.$i])) {
                $barzahlen_comments .= PHP_EOL . $nearest_store['nearest_store_zipcode_'.$i];
            }

            if (!empty($nearest_store['nearest_store_country_'.$i])) {
                $query = xtc_db_query("select countries_name from countries where countries_iso_code_2='". $nearest_store['nearest_store_country_'.$i] ."'");
                $get_country = xtc_db_fetch_array($query);
                $barzahlen_comments .= PHP_EOL . $get_country['countries_name'].PHP_EOL;
            }
        }
        $nearest_store['cp_checkout_token'] = $response['cp_checkout_token'];
        return array($barzahlen_comments, $nearest_store);
    }

    /**
     * Get Order Id
     * @param $payment_response
     * @param $payment
     *
     * @return int
     */
    public static function getOrderId($payment_response, $payment)
    {
        return !empty($payment_response['order_no']) ? $payment_response['order_no'] : (!empty($_SESSION['novalnet'][$payment]['input_params']['order_no']) ? $_SESSION['novalnet'][$payment]['input_params']['order_no'] : '');
    }

    /**
     * Get nearest store details
     * @param $response
     *
     * @return array
     */
    public static function getNearestStore($response)
    {
        $stores = array();
        foreach ($response as $sKey => $values) {
            if (stripos($sKey, 'nearest_store')!==false) {
                $stores[$sKey] = $values;
            }
        }
        return $stores;
    }

    /**
     * Gets the Unique Id
     *
     * @return string
     */
    public function getUniqueid()
    {
        $random_array = array('8','7','6','5','4','3','2','1','9','0','9','7','6','1','2','3','4','5','6','7','8','9','0');
        shuffle($random_array);
        return substr(implode($random_array, ''), 0, 16);
    }

    /**
     * Send payment notification mail
     * @param string $order_no
     * @param array $message
     *
     * @return string
     */
    public function sendPaymentNotificationMail($order_no, $message)
    {
        $query = xtc_db_query("select customers_id from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($order_no)."' ");
        $row = xtc_db_fetch_array($query);
        $customers_query = xtc_db_query("SELECT customers_firstname, customers_lastname, customers_email_address FROM ".TABLE_CUSTOMERS." WHERE customers_id='".(int)$row['customers_id']."'");
        $customers_data = xtc_db_fetch_array($customers_query);
        $order_message = xtc_db_query("select gateway_status from novalnet_transaction_detail where order_no = '".xtc_db_input($order_no)."' ");
        $order_status = xtc_db_fetch_array($order_message);
        if (!empty($customers_data['customers_email_address'])) {
            $subject = MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION . $order_no. MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION1  .STORE_NAME. ($order_status['gateway_status'] == '100' ? MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION2 : MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION4) ;
            $email_content    = '<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; margin:0; padding:0;">
                                    <div style="width:55%;height:auto;margin: 0 auto;background:rgb(247, 247, 247);border: 2px solid rgb(223, 216, 216);border-radius: 5px;box-shadow: 1px 7px 10px -2px #ccc;">
                                        <div style="min-height: 300px;padding:20px;">
                                            <table cellspacing="0" cellpadding="0" border="0" width="100%">

                                                <tr><b>'.MODULE_PAYMENT_NOVALNET_MAIL_MESSAGE.'</b> '.$customers_data['customers_firstname'].' '.$customers_data['customers_lastname'].' </tr>
                                                <br><br>
                                                <tr>'.MODULE_PAYMENT_NOVALNET_PAYMENT_INFORMATION.'<br>
                                                '.nl2br($message).'
                                                </tr><br><br>

                                            </table>
                                        </div>
                                        <div style="width:100%;height:20px;background:#00669D;"></div>
                                    </div>
                                </body>';

            xtc_php_mail(EMAIL_FROM, STORE_NAME, $customers_data['customers_email_address'], STORE_OWNER, '', '', '', '', '', $subject, $email_content, '');
        } else {
            return 'Mail not sent<br>';
        }
    }

    /**
     * Form fields to select alternate shopping type
     * @param $class_name
     * @param $text
     *
     * @return string
     */
    public static function showAlternateShoppingType($class_name, $text)
    {
        return '<span id ='. $class_name .' style="color:blue;cursor: pointer;">
                    <u>
                        <b>' . $text . '<br></b>
                    </u>
                </span>';
    }
    /**
     * Function : checkDefined()
     * @param $constant
     *
     *
     * @return string
     */
    public function checkDefined($constant) {
        $constant = strtoupper($constant);
        return defined($constant) ? constant($constant) : '';
    }
}

