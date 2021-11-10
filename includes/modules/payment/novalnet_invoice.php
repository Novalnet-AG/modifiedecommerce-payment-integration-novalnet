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
 * Script : novalnet_invoice.php
 *
 */
include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_invoice extends NovalnetPayment
{
    var $code = 'novalnet_invoice',
        $title,
        $description,
        $enabled,
        $key = 27,
        $sort_order = 0,
        $payment_type = 'INVOICE';

    /**
     * Constructor
     *
     */
    function __construct()
    {
        global $order;
        // Payment title
        $this->setPaymentDetails();

        if (strpos(MODULE_PAYMENT_INSTALLED, $this->code) !== false) {
            $this->fraud_module    = ((MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE == 'False') ? false : MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE);
            $this->fraud_module_status = (boolean)($this->fraud_module);

            if (is_object($order)) {
                $this->update_status();
            }
        }
    }

    /**
     * Core Function : update_status()
     *
     */
    public function update_status()
    {
        global $order;

        $this->updateStatusProcess();
    }

    /**
     * Core Function : javascript_validation()
     *
     */
    public function javascript_validation()
    {
        return false;
    }

    /**
     * Core Function : selection()
     *
     */
    public function selection()
    {
        global $order;

        // Validate the based on the configuration
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT) || !NovalnetUtil::validateCallbackStatus($this->code, $this->fraud_module)) {
            return false;
        }

        // Validate status of fraud modules
        $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);

        // Unset the TID session if other payment selected previously
        if (!empty($_SESSION['payment']) && isset($_SESSION['novalnet'][$this->code]['tid']) && $_SESSION['payment'] != $this->code && $_SESSION['novalnet'][$this->code]['gateway_response']['tid_status'] == '100') {
            unset($_SESSION['novalnet'][$this->code]['tid']);
        }

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        // Information to the end user
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_INVOICE_ENDCUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

        list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);

        if ($payment_implementation_type == 'error') {
             $notification .= '<br>'.$error;
        }
        $selection = $this->checkoutSelectionDetails();

        $selection['description'] .= $this->description . $notification;

        $selection['description'] .= '

        <input type="hidden" id="nn_root_invoice_catalog" value="'.DIR_WS_CATALOG.'"/><script src="https://cdn.novalnet.de/js/v2/NovalnetUtility.js" type="text/javascript"></script><script src="'.DIR_WS_CATALOG.'includes/external/novalnet/js/novalnet_invoice.js'.'" type="text/javascript"></script><input type="hidden" name="nn_invoice_birthdate_error" id="nn_invoice_birthdate_error" value="'.MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_ERROR_MSG.'">';

        if (isset($_SESSION['novalnet'][$this->code]['tid']) && $this->fraud_module) {
            $selection['fields'] = NovalnetUtil::buildCallbackFieldsAfterResponse($this->fraud_module, $this->code);
        }

        // Get customer details
        $customer_details = NovalnetUtil::collectCustomerDobGenderFax($order->customer['email_address']);

        // Display guarantee payment date of birth field
        if (!isset($_SESSION['novalnet'][$this->code]['tid']) && $payment_implementation_type == 'guarantee' && empty($order->customer['company']) ) {
            $selection['fields'][] = array(
                'title' => MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE."<span style='color:red'> * </span>",
                'field' => NovalnetUtil::getGuaranteeField($this->code.'birthdate', $customer_details)
            );
        } elseif (!isset($_SESSION['novalnet'][$this->code]['tid']) && $payment_implementation_type != 'error' && $payment_implementation_type != 'guarantee' && in_array($this->fraud_module, array('CALLBACK', 'SMS')) && $this->fraud_module_status) {
            $_SESSION['novalnet'][$this->code]['fraud_module_active'] = true;

            $fraud_module_value = $this->checkoutFraudModuleSelection();

            $selection['fields'][] = array(
                'title' => constant('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_'. $this->fraud_module .'_INPUT_TITLE')."<span style='color:red'> * </span>",
                'field' => xtc_draw_input_field($this->code . $fraud_module_value[$this->fraud_module]['name'], (isset($order->customer[$fraud_module_value[$this->fraud_module]['value']]) ? $order->customer[$fraud_module_value[$this->fraud_module]['value']] : ''), 'id="' . $this->code . '-'. strtolower($this->fraud_module) .'"')
            );
        }
        return $selection;
    }

    /**
     * Core Function : pre_confirmation_check()
     *
     */
    public function pre_confirmation_check()
    {
        global $order;
        $post = $_REQUEST;

        list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);

        if ($payment_implementation_type == 'error') {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }

        $_SESSION['novalnet'][$this->code]['process_guarantee'] = false;

        if ($payment_implementation_type == 'guarantee' && $order->billing['company'] == '') {
            $_SESSION['novalnet'][$this->code]['process_guarantee'] = true;
            $current_age = $post['novalnet_invoicebirthdate'];
            if ($post['novalnet_invoicebirthdate'] == '') {
                $error_message = MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_EMPTY_ERROR;
            } elseif ($post['novalnet_invoicebirthdate'] != '') {
                if (NovalnetUtil::validateAge($current_age) && MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE == 'False') {
                    $error_message = utf8_decode(MODULE_PAYMENT_NOVALNET_AGE_ERROR);
                }
            }
        }
        if ($error_message != '' && MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE == 'False'){
            unset($_SESSION['novalnet'][$this->code]['process_guarantee']);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error_message=' . $error_message, 'SSL', true, false));
        }

        return false;
    }

    /**
     * Core Function : confirmation()
     *
     */
    public function confirmation()
    {
        global $order;

        // Check amount validation for Fraud module after generating the pin number
        if (isset($_SESSION['novalnet'][$this->code]['secondcall'])) {
            if ($_SESSION['novalnet'][$this->code]['order_amount'] != NovalnetUtil::getPaymentAmount((array)$order)) {
                if (isset($_SESSION['novalnet'])) {
                    unset($_SESSION['novalnet']);
                }
                    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_FRAUDMODULE_AMOUNT_CHANGE_ERROR), 'SSL', true, false));
            }
        }

        // Payment order amount
        $_SESSION['novalnet'][$this->code]['order_amount'] = NovalnetUtil::getPaymentAmount((array)$order);

        return false;
    }

    /**
     * Core Function : process_button()
     *
     */
    public function process_button()
    {
        $post = $_REQUEST;

        // Sending new pin number to Novalnet server
        if (isset($post['novalnet_invoice_new_pin']) && $post['novalnet_invoice_new_pin'] == 1) {
            $new_pin_response = NovalnetUtil::doXMLCallbackRequest('TRANSMIT_PIN_AGAIN', $this->code);

            // Converting Xml response from Novalnet server
            $response = NovalnetUtil::getStatusFromXmlResponse($new_pin_response);

            // If the transation is failure
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . trim(NovalnetUtil::setUtf8Mode($response['status_message'])), 'SSL', true, false));
        } elseif (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $order_details = isset($_SESSION['novalnet'][$this->code]) ? $_SESSION['novalnet'][$this->code]:array();

            $_SESSION['novalnet'][$this->code] = array_merge($post, $order_details, $post, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));
        } else {
            // Display error message
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' .MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE, 'SSL', true, false));
        }

        // Hiding Buy button in confirmation page
        return NovalnetUtil::confirmButtonDisableActivate();
    }

    /**
     * Core Function : before_process()
     *
     */
    public function before_process()
    {
        global $order;

        // Sending pin number to Novalnet server
        if (isset($_SESSION['novalnet'][$this->code]['secondcall']) && $_SESSION['novalnet'][$this->code]['secondcall']) {
            $callback_response = ($this->fraud_module && in_array($this->fraud_module, array('SMS', 'CALLBACK'))) ? NovalnetUtil::doXMLCallbackRequest('PIN_STATUS', $this->code) : '';

            // Converting Xml response from Novalnet server
            $response = NovalnetUtil::getStatusFromXmlResponse($callback_response);

            // Novalnet transaction status got failure for displaying error message
            if ($response['status'] != 100) {
                if ($response['status'] == '0529006') {
                    $_SESSION[$this->code.'payment_lock_nn'] = true;
                }
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . (!empty($response['status_message']) ? NovalnetUtil::setUtf8Mode($response['status_message']) : $response['pin_status']['status_message']), 'SSL', true, false));
            } else {
                // Novalnet transaction status got success
                $_SESSION['novalnet'][$this->code]['gateway_status'] = $response['tid_status'];
            }
        } else {
            $urlparam =  array_merge((array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

            // Get common request parameters
            $input_params = array_merge(NovalnetUtil::getCommonRequestParams($urlparam), $this->paymentKeyModeType($this->code));

            // Appending affiliate parameters
            NovalnetUtil::getAffDetails($input_params);
            $input_params['invoice_type'] = $this->payment_type;
            $invoice_due_date = (MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE != '') ? (date('Y-m-d', strtotime('+'.MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE.' days'))) : '';

            // Get invoice due date
            if ($invoice_due_date != '') {
                $input_params['due_date'] = $invoice_due_date;
            }

            // Assigning on hold parameter
            if (MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE == 'authorize' &&  $_SESSION['novalnet'][$this->code]['order_amount'] >= MODULE_PAYMENT_NOVALNET_INVOICE_MANUAL_CHECK_LIMIT) {
                $input_params['on_hold']  = '1';
            }
            list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);

            $invalid_valid_dob = NovalnetUtil::validateAge($_SESSION['novalnet'][$this->code]['novalnet_invoicebirthdate']);
            if($payment_implementation_type == 'guarantee' && (!$invalid_valid_dob || $order->billing['company'] != '')) {
                $input_params['key']          = 41;
                $input_params['payment_type'] = 'GUARANTEED_INVOICE';
                if(!empty($_SESSION['novalnet'][$this->code]['novalnet_invoicebirthdate'])) {
                    $input_params['birth_date']   = date('Y-m-d',$_SESSION['novalnet'][$this->code]['novalnet_invoicebirthdate']);
                } else {
                    $input_params['company'] = $order->billing['company'];
                }
            } else {
                $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);

                $fraud_module_active = false;

                // Appending parameters for Fraud module
                if (!empty($_SESSION['novalnet'][$this->code]['fraud_module_active']) && isset($this->fraud_module) && $this->fraud_module_status) {
                    $fraud_module_active = true;
                    if ($this->fraud_module == 'CALLBACK') {
                        $input_params['tel'] = trim($_SESSION['novalnet'][$this->code]['novalnet_invoice_fraud_tel']);
                        $input_params['pin_by_callback'] = '1';
                    } else {
                        $input_params['mobile'] = trim($_SESSION['novalnet'][$this->code]['novalnet_invoice_fraud_mobile']);
                        $input_params['pin_by_sms'] = '1';
                    }
                }
            }
            unset($input_params['tariff_type']);
            $_SESSION['novalnet'][$this->code]['order_amount'] = $input_params['amount'];
            // Send all parameters to Novalnet Server

            $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
            parse_str($response, $payment_response);

            // Novalnet transaction status got success
            if ($payment_response['status'] == '100') {
                // Update Novalnet transaction comments
                $this->updateSessionDetails($payment_response, $input_params);

                $_SESSION['novalnet_invoice_callback_max_time_nn']   = time() + (30 * 60);

                if ($fraud_module_active) {
                    // Redirect to checkout page for displaying fraud module message
                    NovalnetUtil::gotoPaymentOnCallback($this->code, $this->fraud_module, $this->fraud_module_status);
                }
            } else {
                // Novalnet transaction status got failure for displaying error message
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . NovalnetUtil::getTransactionMessage($payment_response), 'SSL', true, false));
            }
        }
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
        global $insert_id;
        // Get Invoice payment reference comments
        $reference_comments = NovalnetUtil::novalnetReferenceComments($insert_id, $_SESSION['novalnet'][$this->code]);
        $comments = $_SESSION['novalnet'][$this->code]['novalnet_comments'];
        if (!in_array($_SESSION['novalnet'][$this->code]['gateway_status'], array('75'))) {
            $comments = $_SESSION['novalnet'][$this->code]['novalnet_comments'].$reference_comments;
        }

        if ($_SESSION['novalnet'][$this->code]['payment_id'] == '41') {
            $order_status = ($_SESSION['novalnet'][$this->code]['gateway_status'] == '75') ? MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_PENDING_ORDER_STATUS : ($_SESSION['novalnet'][$this->code]['gateway_status'] == '91' ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE : MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS);
        } else {
           $order_status = ($_SESSION['novalnet'][$this->code]['gateway_status'] == '91' ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE : MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS);
        }

        // Update order comments in TABLE_ORDERS
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET
                                    orders_status = '".$order_status."',
                                    comments ='".$comments."'
                                    WHERE orders_id='".$insert_id."'");

        // Update order comments in TABLE_ORDERS_STATUS_HISTORY
        xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET
                                    orders_status_id = '".$order_status."',
                                    comments ='".$comments."'
                                    WHERE orders_id='".$insert_id."'");

        NovalnetUtil::doPostProcess(array(
            'payment'  => $this->code,
            'order_no' => $insert_id
        ));

        // Sending post back call to Novalnet server
        NovalnetUtil::postBackCall(array(
            'payment'  => $this->code,
            'order_no' => $insert_id
        ));

    }

    /**
     * Core Function : check()
     *
     */
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /**
     * Core Function : install()
     *
     */
    public function install()
    {
        if (NovalnetUtil::globalConfigInstallError($this->code)) {
            return false;
        } else {
            xtc_db_query("INSERT INTO ". TABLE_CONFIGURATION ."
            (configuration_key, configuration_value, configuration_group_id, sort_order,set_function, use_function, date_added)
          VALUES
            ('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS\',".MODULE_PAYMENT_NOVALNET_INVOICE_STATUS.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE','capture', '6', '3', 'xtc_mod_select_option(array(\'capture\' => MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_CAPTURE,\'authorize\' => MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE_AUTH),\'MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE\',".MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_MANUAL_CHECK_LIMIT', '', '6', '4', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE','False', '6', '5', 'xtc_mod_select_option(array(\'False\' => MODULE_PAYMENT_NOVALNET_OPTION_NONE,\'CALLBACK\' => MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONCALLBACK,\'SMS\' => MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONSMS,),\'MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE\',".MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE.",' , '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_LIMIT', '', '6', '6','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE', '', '6', '7','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT', '', '6', '8','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_ENDCUSTOMER_INFO', '', '6', '9','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER', '0', '6', '10', '',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS', '2',  '6', '11', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS', '3',  '6', '12', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_ZONE', '0', '6', '13', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE','False', '6', '14', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE\',".MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_PENDING_ORDER_STATUS', '1',  '6', '15', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_MIN_AMOUNT', '', '6', '16','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE','True', '6', '17', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE\',".MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE.",' , '',now())            
            ");
        }
    }

    /**
     * Core Function : remove()
     *
     */
    public function remove()
    {
        xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
    }

    /**
     * Core Function : keys()
     *
     */
    public function keys()
    {
        $this->nnJsInclude();

        echo '<input type="hidden" id="invoice_due_date_error" value="'.MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_ERROR.'"><script src="' . DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';

        // Validate admin configuration
        $this->validateAdminConfiguration(true);
        return array(
            'MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS',
            'MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_CAPTURE_AUTHORIZE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_MANUAL_CHECK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_FRAUDMODULE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_INVOICE_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_INVOICE_PAYMENT_ZONE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_PENDING_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_INVOICE_GUARANTEE_MIN_AMOUNT',
            'MODULE_PAYMENT_NOVALNET_INVOICE_FORCE_NON_GUARANTEE',

        );
    }

    /**
     * Validate admin configuration
     * @param $admin
     *
     * @return bool
     */
    public function validateAdminConfiguration($admin = false)
    {
        if (MODULE_PAYMENT_NOVALNET_INVOICE_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_INVOICE_BLOCK_TITLE')) {
            // Validate payment visibility amount
            if (MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_INVOICE_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_INVOICE_BLOCK_TITLE);
                }
                return false;
            } elseif (MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE != '' && (!is_numeric(trim(MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE))
            || MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE < 7 )) {
                // Validate Invoice due date
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_INVOICE_BLOCK_TITLE, MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_ERROR);
                }
                return false;
            } elseif (MODULE_PAYMENT_NOVALNET_INVOICE_ENABLE_GUARANTEE == 'True') {
                $this->guaranMinValid($this->code, $admin);
            }
            return true;
        }
    }

    /**
     * Update session details
     * @param $payment_response
     * @param $input_params
     *
     * @return void
     */
    public function updateSessionDetails($payment_response, $input_params)
    {
        global $order;
        $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE == 'True');

        // Form transaction comments
        $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode, $input_params['payment_type'], $payment_response);

        // Get Invoice / Prepayment comments
        list($invoice_comments,$bank_details) = NovalnetUtil::formInvoicePrepaymentComments($payment_response);
        // format th bank details to store in table
        $_SESSION['novalnet'][$this->code]['bank_details'] = serialize($bank_details);

        $_SESSION['novalnet'][$this->code] = array_merge(
            $_SESSION['novalnet'][$this->code], $this->paymentInitialParams($input_params),
            array(
            'tid'              => $payment_response['tid'],
            'amount'           => $_SESSION['novalnet'][$this->code]['order_amount'],
            'total_amount'     => $_SESSION['novalnet'][$this->code]['order_amount'],
            'gateway_response' => $payment_response,
            'test_mode'        => $test_mode,
            'customer_id'      => $payment_response['customer_no'],
            'bank_details'     => $invoice_comments,
            'novalnet_comments' => PHP_EOL.$order->info['comments'].$transaction_comments,
            'payment_details'  => $_SESSION['novalnet'][$this->code]['bank_details'],
            'gateway_status'   => $payment_response['tid_status'],
            'currency'         => $payment_response['currency'],
            'customer_no'      => $payment_response['customer_no']
            )
        );

        // full payment comments for invoice
        if (in_array($payment_response['tid_status'] , array('91', '100')) || $payment_response['tid_status'] == '50') {
                $_SESSION['novalnet'][$this->code]['novalnet_comments'] = $_SESSION['novalnet'][$this->code]['novalnet_comments'].$invoice_comments;
        }

        // Update Novalnet order comments in TABLE_ORDERS
        $order->info['comments'] = $_SESSION['novalnet'][$this->code]['novalnet_comments'];
    }
}
