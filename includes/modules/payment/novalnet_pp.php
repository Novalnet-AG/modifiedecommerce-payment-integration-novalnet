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
 * Script : novalnet_pp.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_pp extends NovalnetPayment
{
    var $code = 'novalnet_pp',
        $title,
        $description,
        $enabled,
        $key = 34,
        $sort_order = 0,
        $payment_type = 'PAYPAL';        
    /**
     * Constructor
     *
     */
    function __construct()
    {
        global $order;
        $post = $_REQUEST;
        // Payment title
        $this->setPaymentDetails();

        if (strpos(MODULE_PAYMENT_INSTALLED, $this->code) !== false) {
            // Assign form action URL for redirection process
            if (empty($post['novalnet_pp_change_account']) && $_SESSION['novalnet'][$this->code]['novalnet_pp_change_account'] != 1) {
                $this->tmpOrders = true;
                $this->form_action_url = 'https://payport.novalnet.de/paypal_payport';
                $this->tmpStatus       = MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
            }

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
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT)) {
            return false;
        }

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_PP_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        if (MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ZEROAMOUNT') {
            $notification .=  NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT_COMMENTS);
        }

        // Information to the end user
        $notification = !empty($notification) ? $notification.'<br/>' :'';
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO));
        $selection = $this->checkoutSelectionDetails();
        $selection['description'] .= '<span id="novalnet_pp_description">'.$this->description .'</span>' .$notification.'<script src="'.DIR_WS_CATALOG.'includes/external/novalnet/js/novalnet_pp.js'.'" type="text/javascript"></script>';

        // Get masked card details
        $payment_details = NovalnetUtil::getPaymentRefDetails($_SESSION['customer_id'], $this->code);

        if (MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ONECLICK' && !empty($payment_details)) {
                $form_show = isset($_SESSION['novalnet'][$this->code]['novalnet_pp_change_account']) ? $_SESSION['novalnet'][$this->code]['novalnet_pp_change_account'] : 1;
        //pponeclick
            if ($payment_details && MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ONECLICK') {
                
                if (!empty($payment_details['paypal_transaction_id'])) {
                        $paypal_transaction_id = NovalnetUtil::showMaskedDetails(array(
                            array(
                                'label' => MODULE_PAYMENT_NOVALNET_PP_TRANSACTION_ID,
                                'value' => $payment_details['paypal_transaction_id'],
                            )
                        ));
                 }
                    
                $selection['fields'][] = array(
                            'title' => '',
                            'field' => NovalnetUtil::showAlternateShoppingType('novalnet_pp_oneclick_proceed', MODULE_PAYMENT_NOVALNET_ONECLICK_REF_PROCEED).'
                    <table class="paymentmoduledata" id="novalnet_paypal_old_details">'.
                        NovalnetUtil::showMaskedDetails(array(
                            array(
                                'label' => MODULE_PAYMENT_NOVALNET_PP_REF_TRANSACTION_ID,
                                'value' => $payment_details['tid'],
                            )                           
                        )) .$paypal_transaction_id.'
                    </table>
                            <input type="hidden" name="novalnet_pp_old_acc" id="novalnet_pp_old_acc" style="border:none" value="'.$payment_details['tid'].'" readonly><input type="hidden" name="novalnet_pp_old_value" id="novalnet_pp_old_value" value="1">',
                       );
            }
            $selection['fields'][] = array(
            'title' => '',
            'field' => '<div id="nn_pp_acc" style="display:none"></div>
            <input type="hidden" name="novalnet_pp_change_account" id="novalnet_pp_change_account" value="'.$form_show.'"/>
            <input type="hidden" id="nn_lang_pp_one_click_desc" value="' . MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK_DESC . '"/>            
            <input type="hidden" id="nn_root_catalog" value="'.DIR_WS_CATALOG.'"/>
            <a style="display:none;color:blue;font-weight:bold;" id="nn_lang_pp_new_account">' . MODULE_PAYMENT_NOVALNET_PP_GIVEN_ACCOUNT . '</a>
            <p id="nn_old" style="display:none">'.MODULE_PAYMENT_NOVALNET_PP_TEXT_DESCRIPTION. '</p>
            <input type="hidden" id="nn_lang_pp_given_account" value="' . MODULE_PAYMENT_NOVALNET_PP_GIVEN_ACCOUNT . '"/>'
            );
        } else {
                $selection = $this->checkoutSelectionDetails();
                $selection['description'] .= '<span id="novalnet_pp_description">'.$this->description .'</span>' .$notification.'<input type="hidden" name="novalnet_pp_change_account" id="novalnet_pp_change_account" value="0"/>';
        }

        if (MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ONECLICK') {
               $selection['fields'][] = array(
                    'title' => '',
                    'field' => '<div id="nn_pp_savecard">'.xtc_draw_checkbox_field('nn_pp_savecard', 1, false, 'id=nn_pp_savecard').NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_PP_SAVECARD_DETAILS) .'</div>'
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
        $post = $_REQUEST;

        $_SESSION['novalnet'][$this->code]['novalnet_pp_change_account'] = (MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ONECLICK') ? $post['novalnet_pp_change_account'] : 0;
        return false;
    }

    /**
     * Core Function : confirmation()
     *
     */
    public function confirmation()
    {
        global $order;

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
        if ($post['novalnet_pp_old_value'] == 1) {
            $_SESSION['novalnet'][$this->code]['nn_pp_payment_ref_tid'] = filter_var($post['novalnet_pp_old_acc'], FILTER_SANITIZE_NUMBER_INT);
            $_SESSION['novalnet'][$this->code]['novalnet_pp_old_value'] = filter_var($post['novalnet_pp_old_value'], FILTER_SANITIZE_NUMBER_INT);
        }
        unset($_SESSION['nn_pp_savecard']);
        if (isset($post['nn_pp_savecard']) || $post['nn_pp_savecard']) {
            $_SESSION['nn_pp_savecard'] = $post['nn_pp_savecard'];
        }
        if (isset($_SESSION['novalnet'][$this->code]['novalnet_pp_change_account']) && $_SESSION['novalnet'][$this->code]['novalnet_pp_change_account'] == '1') {
            $datas = array_merge($post, (array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

            // Get common request parameters
            $redirect_data  = array_merge(NovalnetUtil::getCommonRequestParams($datas), $this->paymentKeyModeType($this->code));

            // Appending affiliate parameters
            NovalnetUtil::getAffDetails($redirect_data);

            // Assigning on hold parameter
            if (MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE == 'authorize' && $_SESSION['novalnet'][$this->code]['order_amount'] >= MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT) {
                $redirect_data['on_hold']  = '1';
            }

            // Appending zero amount details
            NovalnetUtil::zeroAmount($redirect_data, $this->code);

            unset($redirect_data['tariff_type']);

            $_SESSION['novalnet'][$this->code]['input_params'] = $redirect_data;
        }

        if (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $novalnet_order_details = isset($_SESSION['novalnet'][$this->code]) ? $_SESSION['novalnet'][$this->code] : array();
            $_SESSION['novalnet'][$this->code] = array_merge($novalnet_order_details, $post, array(
                'order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));
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
        if (isset($post['tid'])) {
            if (in_array($post['status'], array('100', '90'))) {
                $input_params = $_SESSION['novalnet'][$this->code]['nnparams'];

                // Hash Validation failed
                if (NovalnetUtil::validateHashResponse($post)) {
                    NovalnetUtil::transactionFailure($post, $this->code, NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR));
                }

                // Decoding Novalnet server response
                $payment_response = NovalnetUtil::generateDecodedata($post);
                if (!empty($_SESSION['novalnet'][$this->code]['zero_transaction'])) {
                    $_SESSION['novalnet'][$this->code] = array(
                        'zerotrxnreference'     => $payment_response['tid'],
                        'zerotrxndetails'   => $_SESSION['novalnet'][$this->code]['zerotrxndetails'],
                        'zero_transaction'  => $_SESSION['novalnet'][$this->code]['zero_transaction'],
                        'total_amount'      => $_SESSION['novalnet'][$this->code]['order_amount'],
                    );
                }
            } else {
                // Transaction failure process.
                NovalnetUtil::transactionFailure($post, $this->code);
            }
        } else {
            // Appending masking parameters
            if (!empty($_SESSION['novalnet'][$this->code]['nn_pp_payment_ref_tid']) && $_SESSION['novalnet'][$this->code]['novalnet_pp_old_value'] == 1) {
                $input_params = array_merge($_SESSION['novalnet'][$this->code]['input_params'], array(
                    'payment_ref' => $_SESSION['novalnet'][$this->code]['nn_pp_payment_ref_tid'],
                ));
                //Send all parameters to Novalnet Server
                $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
                parse_str($response, $payment_response);
                
                unset($_SESSION['novalnet'][$this->code]['nn_pp_payment_ref_tid']);
            } else {
                $order->info['order_status'] = MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
            }
        }

        if (isset($payment_response['tid'])) {
            // Novalnet transaction status got success
            if (isset($payment_response['status']) && in_array($payment_response['status'], array('100', '90'))) {
                // Get order status
                $order_status = (isset($payment_response['tid_status']) && $payment_response['tid_status'] == '85') ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE : ((isset($payment_response['tid_status']) && $payment_response['tid_status'] == '90') ? MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS : MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS);

                // Set order status
                $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus($order_status);

                $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_PP_TEST_MODE == 'True');

                // Get order id
                $order_id = NovalnetUtil::getOrderId($payment_response, $this->code);
 
                // Update Novalnet transaction comments

                $this->paypalSessionDetails($payment_response, $input_params, $test_mode);

                $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

                if (!empty($order_id)) {
                    // Update payment process based on the response.
                    NovalnetUtil::doPostProcess(array(
                        'payment'      => $this->code,
                        'order_no'     => isset($order_id) ? $order_id : $payment_response['order_no'],
                    ));

                    NovalnetUtil::updateComments(array(
                        'order_status' => $order->info['order_status'],
                        'order_no'     => $order_id,
                        'comments'     => PHP_EOL.$transaction_comments
                    ));
                }

                // Update Novalnet transaction comments
                $order->info['comments'] = PHP_EOL.$order->info['comments'].$transaction_comments;
            } else {
                // Transaction failure process.
                NovalnetUtil::transactionFailure($payment_response, $this->code);
            }
        }
    }

    /**
     * Core Function : payment_action()
     *
     */
    public function payment_action()
    {
        global $order;
        $post = $_REQUEST;
        $datas = array_merge($post, (array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));
        // Get common request parameters
        $redirect_data  = array_merge(NovalnetUtil::getCommonRequestParams($datas), $this->paymentKeyModeType($this->code));
        // Appending affiliate parameters
        NovalnetUtil::getAffDetails($redirect_data);

        // Assigning on hold parameter
        if (MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE == 'authorize' && $_SESSION['novalnet'][$this->code]['order_amount'] >= MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT) {
            $redirect_data['on_hold']  = '1';
        }

        if (in_array(MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE, array('ONECLICK', 'ZEROAMOUNT')) && $_SESSION['novalnet'][$this->code]['novalnet_pp_change_account'] == '0' && $_SESSION['nn_pp_savecard'] == 1) {
            $redirect_data['create_payment_ref']  = '1';
        }
        // Appending zero amount details
        NovalnetUtil::zeroAmount($redirect_data, $this->code);

        $_SESSION['novalnet'][$this->code]['nnparams'] = $redirect_data;
        unset($redirect_data['tariff_type']);
        // Form redirection parameters
        NovalnetUtil::formRedirectionParams($redirect_data);
        // Form encoded parameters
        NovalnetUtil::generateEncodeValue($redirect_data);

        $_SESSION['novalnet'][$this->code]['order_no'] = $redirect_data['order_no'];
        $_SESSION['novalnet'][$this->code]['input_params'] = $redirect_data;
        xtc_redirect(xtc_href_link('checkout_novalnet_redirect.php', '', 'SSL', true, false));
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
        global $insert_id;
 
        if (empty($_SESSION['novalnet'][$this->code]['gateway_response']['order_no'])) {
            // Update payment process based on the response.
            NovalnetUtil::doPostProcess(array(
                'payment'      => $this->code,
                'order_no'     => $insert_id,
            ));

            NovalnetUtil::postBackCall(array(
                'order_no' => $insert_id,
                'payment'  => $this->code,
            ));
        }

        // Unset all Novalnet session value
        if (isset($_SESSION['novalnet'])) {
            unset($_SESSION['novalnet']);
        }
    }

    /**
     * Core Function : check()
     *
     */
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_PP_ALLOWED'");
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
            ('MODULE_PAYMENT_NOVALNET_PP_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_PP_STATUS\',".MODULE_PAYMENT_NOVALNET_PP_STATUS.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_PP_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_PP_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE','capture', '6', '3', 'xtc_mod_select_option(array(\'capture\' => MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_CAPTURE,\'authorize\' => MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE_AUTH),\'MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE\',".MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT', '', '6', '4', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE','False', '6', '5', 'xtc_mod_select_option(array(\'False\' => MODULE_PAYMENT_NOVALNET_OPTION_NONE,\'ONECLICK\' => MODULE_PAYMENT_NOVALNET_PP_ONE_CLICK,\'ZEROAMOUNT\' => MODULE_PAYMENT_NOVALNET_PP_ZERO_AMOUNT,),\'MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE\',".MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE.",' ,'',now()),
            ('MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT', '', '6', '6','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO', '', '6', '7','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_SORT_ORDER', '0', '6', '8', '',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS', '1',  '6', '9', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS', '2',  '6', '10', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_PAYMENT_ZONE', '0', '6', '11', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now())
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
        
        echo '<script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';

        echo '<input type="hidden" id="nn_root_catalog" value="'. DIR_WS_CATALOG .'" /><input type="hidden" id="nn_pp_message" value="'.MODULE_PAYMENT_NOVALNET_PP_SHOW_MESSAGE.'"/><script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet_pp.js" type="text/javascript"></script>';

        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array(
            'MODULE_PAYMENT_NOVALNET_PP_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_PP_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE',
            'MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE',
            'MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_PP_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_PAYMENT_ZONE'
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
        if (MODULE_PAYMENT_NOVALNET_PP_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_PP_BLOCK_TITLE')) {
            if (MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_PP_BLOCK_TITLE);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Update Novalnet transaction comments
     * @param $payment_response
     * @param $input_params
     * @param $test_mode
     *
     * @return void
     */
    public function paypalSessionDetails($payment_response, $input_params, $test_mode)
    {
        $_SESSION['novalnet'][$this->code] = array_merge($this->paymentInitialParams($payment_response), $_SESSION['novalnet'][$this->code], array(
          'tid'                   => $payment_response['tid'],
          'amount'                => str_replace('.', '', $payment_response['amount']),
          'currency'              => $payment_response['currency'],
          'reference_transaction' => (int)isset($input_params['payment_ref']),
          'gateway_response'      => $payment_response,
          'test_mode'             => $test_mode,
          'customer_id'           => $payment_response['customer_no'],
          'gateway_status'        => $payment_response['tid_status'],
          'total_amount'          => !empty($_SESSION['novalnet'][$this->code]['total_amount']) ? $_SESSION['novalnet'][$this->code]['total_amount'] : $_SESSION['novalnet'][$this->code]['order_amount'],
         ));

        // Update PayPal transaction ID
        if (MODULE_PAYMENT_NOVALNET_PP_SHOP_TYPE == 'ONECLICK' && !empty($input_params['create_payment_ref']) && $_SESSION['novalnet'][$this->code]['nn_pp_savecard'] == 1) {
            $serialize_data = array(
                'paypal_transaction_id' => $payment_response['paypal_transaction_id'],
                'tid'   => $payment_response['tid']
            );

            $_SESSION['novalnet'][$this->code]['payment_details']  = serialize($serialize_data);
            $_SESSION['novalnet'][$this->code]['one_click_shopping'] = 1;
        }
    }
}
