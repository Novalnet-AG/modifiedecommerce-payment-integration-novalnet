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
			if(defined('MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS')) {
                $this->tmpOrders = true;
                $this->form_action_url = 'https://payport.novalnet.de/paypal_payport';
                $this->tmpStatus       =  MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
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

        // Information to the end user
        $notification = !empty($notification) ? $notification.'<br/>' :'';
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO));
        $selection = $this->checkoutSelectionDetails();
        $selection['description'] .= $this->description . $notification;
        return $selection;
    }

    /**
     * Core Function : pre_confirmation_check()
     *
     */
    public function pre_confirmation_check()
    {
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
            } else {
                // Transaction failure process.
                NovalnetUtil::transactionFailure($post, $this->code);
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
	
	public function get_error(){
		if ($_GET['error']) {
				$error = [
							'title' => $this->code,
							'error' => stripslashes(urldecode($_GET['error']))
						 ];			
				return $error;
		}
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
            ('MODULE_PAYMENT_NOVALNET_PP_STATUS', 'False',  '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ','', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_TEST_MODE', 'False',  '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ','', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE', 'capture', '6', '3', 'xtc_cfg_select_option(array(\'capture\', \'authorize\'), ','', now()),
            ('MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT', '', '6', '4', '', '', now()),
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
        echo '<script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';       
        return array(
            'MODULE_PAYMENT_NOVALNET_PP_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_PP_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_PP_CAPTURE_AUTHORIZE',
            'MODULE_PAYMENT_NOVALNET_PP_MANUAL_CHECK_LIMIT',
            'MODULE_PAYMENT_NOVALNET_PP_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_PP_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_PP_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_PP_PENDING_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PP_PAYMENT_ZONE'
        );
        
         // Validate admin configuration
         $this->validateAdminConfiguration(true);

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
          'gateway_response'      => $payment_response,
          'test_mode'             => $test_mode,
          'customer_id'           => $payment_response['customer_no'],
          'gateway_status'        => $payment_response['tid_status'],
          'total_amount'          => !empty($_SESSION['novalnet'][$this->code]['total_amount']) ? $_SESSION['novalnet'][$this->code]['total_amount'] : $_SESSION['novalnet'][$this->code]['order_amount'],
         ));
    }
}
