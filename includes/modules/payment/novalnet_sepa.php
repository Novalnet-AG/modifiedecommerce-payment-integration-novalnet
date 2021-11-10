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
 * Script : novalnet_sepa.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_sepa extends NovalnetPayment
{
    var $code = 'novalnet_sepa',
        $title,
        $description,
        $enabled,
        $key = 37,
        $sort_order = 0,
        $payment_type = 'DIRECT_DEBIT_SEPA';

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
            $this->fraud_module     = ((MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE == 'False') ? false : MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE);
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
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_SEPA_VISIBILITY_BYAMOUNT) || !NovalnetUtil::validateCallbackStatus($this->code, $this->fraud_module)) {
            return false;
        }

        // Unset the TID session if other payment selected previously
        if (!empty($_SESSION['payment']) && isset($_SESSION['novalnet'][$this->code]['tid']) && $_SESSION['payment'] != $this->code) {
            unset($_SESSION['novalnet'][$this->code]['tid']);
        }

        // Validate status of fraud modules
        $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        // Information to the end user
        $notification = !empty($notification) ? $notification :'';

        list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);

        if ($payment_implementation_type == 'error') {
            $notification .= '<br>'.$error;
        }

        if (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ZEROAMOUNT' && $this->fraud_module_status != '1' && ( $payment_implementation_type == 'normal' || !$payment_implementation_type)) {
            $notification .=  NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SEPA_ZERO_AMOUNT_COMMENTS);
        }

        $selection = $this->checkoutSelectionDetails();
        $notification_info = trim(strip_tags(MODULE_PAYMENT_NOVALNET_SEPA_ENDCUSTOMER_INFO));
        $selection['description'] .= $this->description .$notification.'<br>' .$notification_info;

        // Display payment description and notification of the buyer message
        $selection['description'] .= '
        <input type="hidden" id="nn_root_sepa_catalog" value="'.DIR_WS_CATALOG.'"/>
        <script src="https://cdn.novalnet.de/js/v2/NovalnetUtility.js" type="text/javascript"></script><script src="'.DIR_WS_CATALOG.'includes/external/novalnet/js/novalnet_sepa.js'.'" type="text/javascript"></script>        
        <input type="hidden" name="nn_sepa_birthdate_error" id="nn_sepa_birthdate_error" value="'.MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_ERROR_MSG.'">
        <input type="hidden" name="nn_sepa_iban_error" id="nn_sepa_iban_error" value="'.MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR.'">';

        if (isset($_SESSION['novalnet'][$this->code]['tid']) && $this->fraud_module && $this->fraud_module_status) {
            // Display pin number field after getting response
            $selection['fields'] = NovalnetUtil::buildCallbackFieldsAfterResponse($this->fraud_module, $this->code);
        } else {
            $data = array(
                'vendor'    => MODULE_PAYMENT_NOVALNET_VENDOR_ID,
                'auth_code' => MODULE_PAYMENT_NOVALNET_AUTHCODE,
            );

            // Affiliate process
            NovalnetUtil::getAffDetails($data);

            $pin_by_callback = '';
            $_SESSION['novalnet'][$this->code]['fraud_module_active'] = false;
            // Loading Fraud module field
            if (!isset($_SESSION['novalnet'][$this->code]['tid']) && $payment_implementation_type != 'error' && $payment_implementation_type != 'guarantee' && in_array($this->fraud_module, array('CALLBACK', 'SMS')) && $this->fraud_module_status) {
                $_SESSION['novalnet'][$this->code]['fraud_module_active'] = true;
                $fraud_module_value = $this->checkoutFraudModuleSelection();
                $default_value = isset($order->customer[$fraud_module_value[$this->fraud_module]['value']]) ? $order->customer[$fraud_module_value[$this->fraud_module]['value']] : '';
                $pin_by_callback = NovalnetUtil::showMaskedDetails(array(
                    array(
                        'label'    => constant('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_'. $this->fraud_module.'_INPUT_TITLE'),
                        'value'    => xtc_draw_input_field($this->code.$fraud_module_value[$this->fraud_module]['name'], $default_value, 'id=' . $this->code . '-'. strtolower($this->fraud_module).' AUTOCOMPLETE=off'),
                        'required' => true
                )));
            }

            // Get customer details
            $customer_details = NovalnetUtil::collectCustomerDobGenderFax($order->customer['email_address']);

            if ($payment_implementation_type == 'guarantee' && empty($order->customer['company'])) {
                $guarantee_field = NovalnetUtil::showMaskedDetails(array(
                    array(
                        'label' => MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE,
                        'value' => NovalnetUtil::getGuaranteeField('novalnet_sepa_birthdate', $customer_details),
                        'required' => true,
                    )
                ));
            }

            $form_show = (in_array(MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE, array('False','ZEROAMOUNT'))) ? 'newform' : '';


            if (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK') {
                // Get masked account details
                $payment_details = NovalnetUtil::getPaymentRefDetails($_SESSION['customer_id'], $this->code);

                if ($payment_details) {
                    $form_show = !empty($_SESSION['novalnet'][$this->code]['novalnet_sepa_change_account']) ? 'newform' : 'saved';
                    if ($payment_implementation_type == 'guarantee' && empty($order->customer['company'])) {
                        $guarantee_field_one_click = NovalnetUtil::showMaskedDetails(array(
                            array(
                                'label' => MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE,
                                'value' => NovalnetUtil::getGuaranteeField('novalnet_sepa_birthdate_one_click', $customer_details),
                                'required' => true,
                            )
                        ));
                    }

                    $selection['fields'][] = array(
                      'title' => '',
                      'field' => '<a id="novalnet_oneclick_sepa_click_new" style="cursor:pointer;color:blue;display:none;font-weight:bold">'.MODULE_PAYMENT_NOVALNET_SEPA_GIVEN_ACCOUNT.'</a>'.

                 NovalnetUtil::showAlternateShoppingType('novalnet_oneclick_sepa_ref', MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_REF).'
                    <table class="paymentmoduledata" id="novalnet_oneclick_sepa_ref_div">'.
                        NovalnetUtil::showMaskedDetails(array(
                            array(
                                'label' => MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_IBAN,
                                'value' => $payment_details['iban'],
                            ),
                        )) .$guarantee_field_one_click .'
                    </table>

                      <input type="hidden" name="nn_payment_ref_tid_sepa" id="nn_payment_ref_tid_sepa" value="'.$payment_details['tid'].'">
                      <input type="hidden" name="novalnet_sepa_savecheck" id="novalnet_sepa_savecheck" value="1">
                      <input type="hidden" name="novalnet_sepa_savecard" id="novalnet_sepa_savecard" value="1"></div>
                      '
                    );
                }
            }
            if($form_show == '' && MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE == 'True' && MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK'){
                $form_show = 'newform';
            }
            $sepa_fields = '<input type="hidden" name="novalnet_sepa_change_account" id="novalnet_sepa_change_account" value="'.$form_show.'"/>
                                      <input type="hidden" id="nn_sepa_shopping_type" value="'.MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE.'"/>
                                      <input type="hidden" id="nn_lang_choose_payment_method" value="'.NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SELECT_PAYMENT_METHOD).'">';

            $selection['fields'][] = array(
                    'title' => '',
                    'field' => '<div id="nn_sepa_acc" style="display:none">
                             <table>'. NovalnetUtil::showMaskedDetails(array(
                                            array(
                                                'label' => MODULE_PAYMENT_NOVALNET_ACCOUNT_OR_IBAN,
                                                'value' => xtc_draw_input_field('novalnet_sepa_iban', '', 'id="novalnet_sepa_iban" AUTOCOMPLETE="off"'),
                                                'required' => true,
                                            ),
                                        )) . $guarantee_field . $sepa_fields . $pin_by_callback . '</table></div><style>
                                        #novalnet_sepa_iban{
                                          text-transform: uppercase;
                                        }
                                        </style>');


            if (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK') {
                  $selection['fields'][] = array(
                       'title' => '',
                       'field' => '<div id="nn_sepa_savecard" style="display:none;">'.xtc_draw_checkbox_field('nn_sepa_savecard', 1, false, 'id=nn_sepa_savecard').NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SEPA_SAVECARD_DETAILS)
                                   );
            }
                $selection['fields'][] =  array(
                    'title' => '',
                    'field' => '<div id="nn_sepa_mandate" style="display:none;"><table>'. NovalnetUtil::showMaskedDetails(array(
                                            array(
                                                'label' => '',
                                                'value' => NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SEPA_FORM_MANDATE_CONFIRM_TEXT),
                                            ))).'</table></div>');
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

        // Get guarantee payment values
        $guarantee_date_normal = $guarantee_date_one_click = '';

        if (isset($post['novalnet_sepa_birthdate'])) {
            $guarantee_date_normal = $post['novalnet_sepa_birthdate'];
            unset($post['novalnet_sepa_birthdate']);
        }
        if (isset($post['novalnet_sepa_birthdate_one_click'])) {
            $guarantee_date_one_click = $post['novalnet_sepa_birthdate_one_click'];
            unset($post['novalnet_sepa_birth_date_one_click']);
        }
        $_SESSION['novalnet'][$this->code]['novalnet_sepa_change_account'] = ! empty($post['novalnet_sepa_change_account']) ? $post['novalnet_sepa_change_account'] : 'saved';

        $_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final'] = ($_SESSION['novalnet'][$this->code]['novalnet_sepa_change_account'] == 'newform') ? $guarantee_date_normal : $guarantee_date_one_click;

        list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);
        if ($payment_implementation_type == 'error') {
            $payment_error_return = 'error_message=' . rawurlencode(NovalnetUtil::setUtf8Mode(filter_var($error, FILTER_SANITIZE_STRING)));
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }

        $_SESSION['novalnet'][$this->code]['process_guarantee'] = false;
         // Validate fraud module pin number field
        if (isset($_SESSION['novalnet'][$this->code]['secondcall'])) {
            NovalnetUtil::validateUserInputsOnCallback($this->code, $post, $this->fraud_module);
        } else {
            if (isset($post['nn_payment_ref_tid_sepa']) && $post['novalnet_sepa_change_account'] == 'saved') {
                $_SESSION['novalnet'][$this->code]['process_guarantee'] = true;
                $_SESSION['novalnet'][$this->code]['nn_payment_ref_enable']= true;
                $_SESSION['novalnet'][$this->code]['nn_payment_ref_tid_sepa'] = $post['nn_payment_ref_tid_sepa'];                
            }

            // Validate fraud module field
            if ($payment_implementation_type != 'guarantee' && MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE == 'False') {
                $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);
                NovalnetUtil::validateCallbackFields($post, $this->fraud_module, $this->fraud_module_status, $this->code);
            }
        }

        if ($payment_implementation_type == 'guarantee' && $order->billing['company'] == '') {
            $_SESSION['novalnet'][$this->code]['process_guarantee'] = true;
            $current_age = $_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final'];

            if ($_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final'] == '' && MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE == 'False') {
                $error_message = MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_EMPTY_ERROR;
            } elseif ($_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final'] != '') {
                if (NovalnetUtil::validateAge($current_age) && MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE == 'False') {
                    $error_message = utf8_decode(MODULE_PAYMENT_NOVALNET_AGE_ERROR);
                }
            }
        }

        if ($error_message != '' && MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE == 'False'){
            unset($_SESSION['novalnet'][$this->code]['process_guarantee']);
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error_message=' . $error_message, 'SSL', true, false));
        }

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
        global $order;

        $post = $_REQUEST;
        unset($_SESSION['nn_sepa_savecard']);
        if (isset($post['nn_sepa_savecard']) || $post['nn_sepa_savecard']) {
            $_SESSION['nn_sepa_savecard'] = $post['nn_sepa_savecard'];
        }
        // Sending new pin number to Novalnet server
        if (isset($post[$this->code . '_new_pin']) && $post[$this->code . '_new_pin'] == 1) {
            $new_pin_response = NovalnetUtil::doXMLCallbackRequest('TRANSMIT_PIN_AGAIN', $this->code);

            // Converting Xml response from Novalnet server
            $response = NovalnetUtil::getStatusFromXmlResponse($new_pin_response);

            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . trim(NovalnetUtil::setUtf8Mode($response['status_message'])), 'SSL', true, false));
        } elseif (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $novalnet_order_details = isset($_SESSION['novalnet'][$this->code])?$_SESSION['novalnet'][$this->code]: array();
            $_SESSION['novalnet'][$this->code] = array_merge($novalnet_order_details, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']), $post);
        } else {
          // Display error message
            $payment_error_return = 'error_message=' .MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE;
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
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
        $post = $_REQUEST;
        $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);
        $param_inputs = array_merge((array)$order, $_SESSION['novalnet'][$this->code], array('fraud_module' => $this->fraud_module,'fraud_module_status' => $this->fraud_module_status));
        $this->fraud_module_status = isset($_SESSION['novalnet'][$this->code]['nn_payment_ref_enable']) ? false : $this->fraud_module_status;

        // Sending pin number to Novalnet server
        if (isset($param_inputs['secondcall']) && $param_inputs['secondcall']) {
            $callback_response = ($this->fraud_module && in_array($this->fraud_module, array('SMS', 'CALLBACK'))) ? NovalnetUtil::doXMLCallbackRequest('PIN_STATUS', $this->code) : '';

            // Converting Xml response from Novalnet server
            $response = NovalnetUtil::getStatusFromXmlResponse($callback_response);

            // Novalnet transaction status got failure for displaying error message
            if ($response['status'] != 100) {
                if ($response['status'] == '0529006') {
                    $_SESSION[$this->code.'payment_lock_nn'] = true;
                }

                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . (!empty($response['status_message']) ? NovalnetUtil::setUtf8Mode($response['status_message']) : $response['pin_status']['status_message']), 'SSL', true, false));
            }
            $_SESSION['novalnet'][$this->code]['gateway_status'] = $response['tid_status'];
        } else {
            // Get common request parameters
            $input_params = array_merge(NovalnetUtil::getCommonRequestParams($param_inputs), $this->paymentKeyModeType($this->code));
            // Appending affiliate parameters
            NovalnetUtil::getAffDetails($input_params);
            list($payment_implementation_type, $error) = NovalnetUtil::paymentImplementationType($order, $this->code);
            // Assigning on hold parameter
            if (MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE == 'authorize' && $_SESSION['novalnet'][$this->code]['order_amount'] >= MODULE_PAYMENT_NOVALNET_SEPA_MANUAL_CHECK_LIMIT && (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE != 'ZEROAMOUNT' || $payment_implementation_type == 'guarantee' && MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ZEROAMOUNT')) {
                $input_params['on_hold']  = '1';
            }
                
            if (!empty($_SESSION['novalnet'][$this->code]['nn_payment_ref_tid_sepa']) && $_SESSION['novalnet'][$this->code]['novalnet_sepa_savecard'] == '1' && $_SESSION['novalnet'][$this->code]['novalnet_sepa_savecheck'] == 1) {
                $input_params['payment_ref']  = $_SESSION['novalnet'][$this->code]['nn_payment_ref_tid_sepa'];
                unset($_SESSION['novalnet'][$this->code]['nn_payment_ref_tid_sepa']);
            } else {
                if ((MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK' && $_SESSION['nn_sepa_savecard'] == 1) || (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ZEROAMOUNT' && $payment_implementation_type != 'guarantee')) {
                    $input_params['create_payment_ref'] = '1';
                }
                $customer_name =  ((!empty($order->customer['firstname']) ? $order->customer['firstname'] : '' ).' '.(!empty($order->customer['lastname']) ? $order->customer['lastname'] : ''));
                $input_params['bank_account_holder'] = $customer_name;
                $input_params['iban'] = $_SESSION['novalnet'][$this->code]['novalnet_sepa_iban'];
            }

            $sepa_due_date = NovalnetUtil::sepaDuedate();
            if( !empty($sepa_due_date) ) {
                $input_params['sepa_due_date'] = date('Y-m-d', strtotime('+'.$sepa_due_date.' days'));
            }
            $this->fraud_module_status = NovalnetUtil::setFraudModuleStatus($this->code, $this->fraud_module);


            $_SESSION['novalnet'][$this->code]['order_amount'] = $input_params['amount'];
            $_SESSION['novalnet'][$this->code]['zero_transaction'] = 0;

            $invalid_valid_dob = NovalnetUtil::validateAge($_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final']);

            if($payment_implementation_type == 'guarantee' && (!$invalid_valid_dob || $order->billing['company'] != '')) {
                $input_params['key']          = 40;
                $input_params['payment_type'] = 'GUARANTEED_DIRECT_DEBIT_SEPA';
                if(!empty($_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final'])) {
                    $input_params['birth_date']   = date('Y-m-d', $_SESSION['novalnet'][$this->code]['novalnet_sepa_birthdate_final']);
                } else {
                    $input_params['company'] = $order->billing['company'];
                }
            }

             else {
                // Appending zero amount details
                NovalnetUtil::zeroAmount($input_params, $this->code);

                $fraud_module_active = false;
                if ((!empty($_SESSION['novalnet'][$this->code]['fraud_module_active']) && isset($this->fraud_module) && $this->fraud_module_status) && ((MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK' && $_SESSION['novalnet'][$this->code]['novalnet_sepa_change_account'] == 'newform' ) || ( MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE != 'ONECLICK' && $_SESSION['novalnet'][$this->code]['novalnet_sepa_change_account'] == 'newform' ) )) {
                    $fraud_module_active = true;
                    // Appending parameters for Fraud module
                    if ($this->fraud_module == 'CALLBACK') {
                        $input_params['tel'] = trim($_SESSION['novalnet'][$this->code]['novalnet_sepa_fraud_tel']);
                        $input_params['pin_by_callback'] = '1';
                    } else {
                        $input_params['mobile'] = trim($_SESSION['novalnet'][$this->code]['novalnet_sepa_fraud_mobile']);
                        $input_params['pin_by_sms'] = '1';
                    }
                }
            } 
            unset($input_params['tariff_type']);
            // Send payment parameters to Novalnet server

            $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
            parse_str($response, $payment_response);

            // Novalnet transaction status got success
            if ($payment_response['status'] == '100') {
                $serialize_data = array(
                 'bankaccount_holder' => $payment_response['bankaccount_holder'],
                 'iban'               => $payment_response['iban'],
                 'tid'                => $payment_response['tid']
                );

                // Update Novalnet transaction comments
                $this->updateSessionDetails($serialize_data, $payment_response, $input_params);

                if ($fraud_module_active) {
                    // Redirect to checkout page for displaying fraud module message
                    NovalnetUtil::gotoPaymentOnCallback($this->code, $this->fraud_module, $this->fraud_module_status);
                }
            } else {
                // Novalnet transaction status got failure for displaying error message
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . NovalnetUtil::getTransactionMessage($payment_response), 'SSL', true, false));
            }
        }

        // Set order status

        $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS);

        $test_mode = (int)(!empty($_SESSION['novalnet'][$this->code]['test_mode']) || MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE == 'True');

        // Form transaction comments

        $transaction_comments = NovalnetUtil::formPaymentComments($_SESSION['novalnet'][$this->code]['tid'], $test_mode, $input_params['payment_type'], $payment_response);
        // Update Novalnet transaction comments
        $order->info['comments'] = PHP_EOL.$order->info['comments'].$transaction_comments;
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
        global $insert_id;

        $order_status = ($_SESSION['novalnet'][$this->code]['gateway_status'] == '75') ? MODULE_PAYMENT_NOVALNET_SEPA_GUARANTEE_PENDING_ORDER_STATUS : ($_SESSION['novalnet'][$this->code]['gateway_status'] == '99' ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE : MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS);


        // Update order status in TABLE_ORDERS
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET
                                    orders_status = '".$order_status."'
                                    WHERE orders_id='".$insert_id."'");

        // Update order status id in TABLE_ORDERS_STATUS_HISTORY
        xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET
                                    orders_status_id = '".$order_status."'
                                    WHERE orders_id='".$insert_id."'");

        // Update payment process based on the response.
        NovalnetUtil::doPostProcess(array(
            'payment'  => $this->code,
            'order_no' => $insert_id
        ));

        // Sending post back call to Novalnet server
        NovalnetUtil::postBackCall(array( 'payment'  => $this->code, 'order_no' => $insert_id ));
    }

    /**
     * Core Function : check()
     *
     */
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_SEPA_ALLOWED'");
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
            ('MODULE_PAYMENT_NOVALNET_SEPA_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_SEPA_STATUS\',".MODULE_PAYMENT_NOVALNET_SEPA_STATUS.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE','capture', '6', '3', 'xtc_mod_select_option(array(\'capture\' => ".MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE_CAPTURE.",\'authorize\' => ".MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE_AUTH."),\'MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE\',".MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_MANUAL_CHECK_LIMIT', '', '6', '4', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE','False', '6', '5', 'xtc_mod_select_option(array(\'False\' => MODULE_PAYMENT_NOVALNET_OPTION_NONE,\'CALLBACK\' => MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONCALLBACK,\'SMS\' => MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONSMS,),\'MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE\',".MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE.",' , '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_CALLBACK_LIMIT', '', '6', '6','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE', '', '6', '7','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE','False', '6', '8', 'xtc_mod_select_option(array(\'False\' => MODULE_PAYMENT_NOVALNET_OPTION_NONE,\'ONECLICK\' => MODULE_PAYMENT_NOVALNET_SEPA_ONE_CLICK,\'ZEROAMOUNT\' => MODULE_PAYMENT_NOVALNET_SEPA_ZERO_AMOUNT,),\'MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE\',".MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE.",' ,'',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_VISIBILITY_BYAMOUNT', '', '6', '9','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_ENDCUSTOMER_INFO', '', '6', '10','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER', '0', '6', '11', '',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS', '2',  '6', '12', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_ZONE', '0', '6', '13', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE','False', '6', '14', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE\',".MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_GUARANTEE_PENDING_ORDER_STATUS', '1',  '6', '15', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_SEPA_GUARANTEE_MIN_AMOUNT', '', '6', '16','',  '', now()),            
            ('MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE','True', '6', '17', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE\',".MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE.",' , '',now())
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

        echo '<input type="hidden" id="sepa_due_date_error" value="'.MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE_ERROR.'"><script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';

        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array (
            'MODULE_PAYMENT_NOVALNET_SEPA_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_SEPA_STATUS',
            'MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_SEPA_CAPTURE_AUTHORIZE',
            'MODULE_PAYMENT_NOVALNET_SEPA_MANUAL_CHECK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE',
            'MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_FRAUDMODULE',
            'MODULE_PAYMENT_NOVALNET_SEPA_CALLBACK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE',
            'MODULE_PAYMENT_NOVALNET_SEPA_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_SEPA_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_ZONE',
            'MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE',
            'MODULE_PAYMENT_NOVALNET_SEPA_GUARANTEE_PENDING_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_SEPA_GUARANTEE_MIN_AMOUNT',            
            'MODULE_PAYMENT_NOVALNET_SEPA_FORCE_NON_GUARANTEE'
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
        if (MODULE_PAYMENT_NOVALNET_SEPA_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_SEPA_BLOCK_TITLE')) {
            if (MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE != '' && (!is_numeric(trim(MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE))
            || MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE > 14 || MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE < 2 )) {
                // Validate SEPA due date
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_SEPA_BLOCK_TITLE, MODULE_PAYMENT_NOVALNET_SEPA_DUE_DATE_ERROR);
                }
                return true;
            }

            if (MODULE_PAYMENT_NOVALNET_SEPA_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_SEPA_VISIBILITY_BYAMOUNT)) {
                // Validate payment visibility amount
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_SEPA_BLOCK_TITLE);
                }
                return false;
            } elseif (MODULE_PAYMENT_NOVALNET_SEPA_ENABLE_GUARANTEE == 'True') {
                $this->guaranMinValid($this->code, $admin);
            }
        }
        return true;
    }

    /**
     * Update transaction comments
     * @param $serialize_data
     * @param $payment_response
     * @param $input_params
     *
     * @return void
     */
    public function updateSessionDetails($serialize_data, $payment_response, $input_params)
    {
        $zero_trxn_details = $zero_transaction = '';
        $amount = $_SESSION['novalnet'][$this->code]['order_amount'];
        if (isset($_SESSION['novalnet'][$this->code]['zerotrxndetails'])) {
            $zero_trxn_details = $_SESSION['novalnet'][$this->code]['zerotrxndetails'];
            $zero_transaction  = $_SESSION['novalnet'][$this->code]['zero_transaction'];
            $amount = '0';
        }
        $_SESSION['novalnet'][$this->code] = array_merge($this->paymentInitialParams($input_params),
            $_SESSION['novalnet'][$this->code],
            array(
                'tid'                   => $payment_response['tid'],
                'test_mode'             => (int)!empty($payment_response['test_mode']),
                'reference_transaction' => (int)isset($input_params['payment_ref']),
                'zerotrxndetails'       => $zero_trxn_details,
                'zero_transaction'      => $zero_transaction,
                'zerotrxnreference'     => $payment_response['tid'],
                'amount'                => $amount,
                'total_amount'          => $amount,
                'gateway_response'      => $payment_response,
                'gateway_status'        => ($payment_response['tid_status']) ? $payment_response['tid_status'] : '',
                'currency'              => $payment_response['currency'],
                'customer_no'           => $payment_response['customer_no'],
            )
        );

        if (MODULE_PAYMENT_NOVALNET_SEPA_SHOP_TYPE == 'ONECLICK' && $input_params['create_payment_ref'] == '1' && $_SESSION['novalnet'][$this->code]['nn_sepa_savecard']) {
            $_SESSION['novalnet'][$this->code]['payment_details'] = serialize($serialize_data);
            $_SESSION['novalnet'][$this->code]['one_click_shopping'] = 1;
        }

        $_SESSION['novalnet_sepa_callback_max_time_nn']   = time() + (30 * 60);

        return true;
    }
}
