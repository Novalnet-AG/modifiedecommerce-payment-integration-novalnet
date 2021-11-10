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
 * Script : novalnet_barzahlen.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_barzahlen extends NovalnetPayment
{
    var $code = 'novalnet_barzahlen',
        $title,
        $description,
        $enabled,
        $key = 59,
        $sort_order = 0,
        $payment_type = 'CASHPAYMENT';

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
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_BARZAHLEN_VISIBILITY_BYAMOUNT)) {
            return false;
        }

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        // Display payment description and notification of the buyer message
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_BARZAHLEN_ENDCUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

        $selection = $this->checkoutSelectionDetails();
        $selection['description'] .= $this->description .$notification;

        return $selection;
    }

    /**
     * Core Function : keys()
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
        $post = $_REQUEST;

        if (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $_SESSION['novalnet'][$this->code] = array_merge($post, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));
        } else {
          // Displaying error message
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

            $param_inputs = array_merge((array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

            // Get common request parameters
            $input_params = array_merge(NovalnetUtil::getCommonRequestParams($param_inputs), $this->paymentKeyModeType($this->code));

            // Appending affiliate parameters
            NovalnetUtil::getAffDetails($input_params);

            $cashpayment_duedate = MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE;
        if (!empty($cashpayment_duedate) && preg_match('/^\d+$/', $cashpayment_duedate)) {
            $input_params['cp_due_date'] = date('Y-m-d', strtotime('+' . $cashpayment_duedate . ' days'));
        }
			
            unset($input_params['tariff_type']);

            //Send all parameters to Novalnet Server
            $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
            parse_str($response, $payment_response);
            // Novalnet transaction status got success
        if ($payment_response['status'] == '100') {
            $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_BARZAHLEN_ORDER_STATUS);

            // Get Barzahlen Comments
            list($barzahlen_comments,$nearest_store)  = NovalnetUtil::formBarzahlenComments($payment_response);
            $_SESSION['novalnet_cp_token'] = $payment_response['cp_checkout_token'].'|'. $payment_response['test_mode'];
            $_SESSION['novalnet'][$this->code] = array_merge($this->paymentInitialParams($input_params), array(
            'tid'                 => $payment_response['tid'],
            'amount'              => $input_params['amount'],
            'currency'            => $payment_response['currency'],
            'gateway_response'    => $payment_response,
            'customer_id'         => $payment_response['customer_no'],
            'comments'            => PHP_EOL.$order->info['comments'].$transaction_comments.$barzahlen_comments,
            'gateway_status'      => $payment_response['tid_status'],
            'payment_details'     => serialize($nearest_store),
            'total_amount'        => $_SESSION['novalnet'][$this->code]['order_amount']
            ));

         // Update Novalnet order comments in TABLE_ORDERS
            $order->info['comments'] = $_SESSION['novalnet'][$this->code]['comments'];
        } else {
            // Novalnet transaction status got failure displaying error message
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . NovalnetUtil::getTransactionMessage($payment_response), 'SSL', true, false));
        }
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
        global $insert_id;
        // Update order comments in TABLE_ORDERS
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET
									comments ='".$_SESSION['novalnet'][$this->code]['comments']."'
									WHERE orders_id='".$insert_id."'");

        // Update order comments in TABLE_ORDERS_STATUS_HISTORY
         xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET
									comments ='".$_SESSION['novalnet'][$this->code]['comments']."'
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
     * Core Function : success()
     *
     */
    public function success()
    {
        if (!empty($_SESSION['novalnet_cp_token'])) {
            $token = explode('|', $_SESSION['novalnet_cp_token']);
            $database_version = xtc_db_query('SELECT version from database_version');
            $shop_version = xtc_db_fetch_array($database_version);
            $shop_version = explode('_', $shop_version['version']);
            $barzahlen_link = ($token[1] == 1) ? 'https://cdn.barzahlen.de/js/v2/checkout-sandbox.js' : 'https://cdn.barzahlen.de/js/v2/checkout.js';
            $info_success = '<script src="'.$barzahlen_link.'"
            class="bz-checkout"
            data-token="'.$token[0].' ">
            </script>';
            if ($shop_version[1] >= '1.0.6.4') {
                return array(
                array ('title' => $this->title.': ',
                 'class' => $this->code,
                 'fields' => array(array('title' => '',
                            'field' => '<style type="text/css">
                #bz-checkout-modal { position: fixed !important; }</style>'.$info_success.'<button class="bz-checkout-btn">
  '.MODULE_PAYMENT_NOVALNET_BARZAHLEN_BUTTON.'
</button>'))));
            } else {
                return $info_success. '<style type="text/css">
                            #bz-checkout-modal { position: fixed !important; }</style>
                            <button class="bz-checkout-btn">'.MODULE_PAYMENT_NOVALNET_BARZAHLEN_BUTTON.'</button>';
            }
        }
    }

    /**
     * Core Function : check()
     *
     */
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_BARZAHLEN_ALLOWED'");
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
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_BARZAHLEN_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_BARZAHLEN_STATUS\',".MODULE_PAYMENT_NOVALNET_BARZAHLEN_STATUS.",' , '',now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE', '', '6', '3','',  '', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_VISIBILITY_BYAMOUNT', '', '6', '4','',  '', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_ENDCUSTOMER_INFO', '', '6', '5','',  '', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SORT_ORDER', '0', '6', '6', '',  '', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_ORDER_STATUS', '2',  '6', '7', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_CALLBACK_ORDER_STATUS', '3',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
			('MODULE_PAYMENT_NOVALNET_BARZAHLEN_PAYMENT_ZONE', '0', '6', '9', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now())
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

        echo '<input type="hidden" id="cashpayment_due_date_error" value="'.MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE_ERROR.'"><script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';
        
        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array(
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_STATUS',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_CALLBACK_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_BARZAHLEN_PAYMENT_ZONE',
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
        if (MODULE_PAYMENT_NOVALNET_BARZAHLEN_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_BARZAHLEN_BLOCK_TITLE')) {
            // Validate payment visibility amount
            if (MODULE_PAYMENT_NOVALNET_BARZAHLEN_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_BARZAHLEN_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_BARZAHLEN_BLOCK_TITLE);
                }
                return false;
            } elseif (trim(MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE) != '' && !preg_match('/^\d+$/', trim(MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE))) {
                // Validate Barzahlen slip expiry date
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_BARZAHLEN_BLOCK_TITLE, MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE_ERROR);
                }
                return false;
            }

            return true;
        }
    }
}
