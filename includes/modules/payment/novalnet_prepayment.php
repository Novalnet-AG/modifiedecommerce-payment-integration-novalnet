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
 * Script : novalnet_prepayment.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_prepayment extends NovalnetPayment
{
    var $code = 'novalnet_prepayment',
        $title,
        $description,
        $enabled,
        $key = 27,
        $sort_order = 0,
        $payment_type = 'PREPAYMENT';

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
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_PREPAYMENT_VISIBILITY_BYAMOUNT)) {
            return false;
        }

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;

        }
        // Display payment description and notification of the buyer message
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_PREPAYMENT_ENDCUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

        $selection = $this->checkoutSelectionDetails();
        $selection['description'] .= $this->description .$notification;

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

            $param_inputs = array_merge((array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

            // Get common request parameters
            $input_params = array_merge(NovalnetUtil::getCommonRequestParams($param_inputs), $this->paymentKeyModeType($this->code));

            // Appending affiliate parameters
            NovalnetUtil::getAffDetails($input_params);
            $input_params['invoice_type'] = $this->payment_type;
            $due_date = trim(MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE);
            if ($due_date != '' && $due_date <= 28 && $due_date >= 7)
            {
                $input_params['due_date'] = date('Y-m-d', strtotime('+'.$due_date.' days'));
            }
            unset($input_params['tariff_type']);

            //Send all parameters to Novalnet Server
            $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
            parse_str($response, $payment_response);

            // Novalnet transaction status got success
        if ($payment_response['status'] == '100') {
            $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS);

            // Get Invoice / Prepayment Comments and Bank details
            list($invoice_comments,$bank_details) = NovalnetUtil::formInvoicePrepaymentComments($payment_response);

            $_SESSION['novalnet'][$this->code] = array_merge($this->paymentInitialParams($input_params), array(
            'tid'                 => $payment_response['tid'],
            'amount'              => $input_params['amount'],
            'currency'            => $payment_response['currency'],
            'gateway_response'    => $payment_response,
            'test_mode'           => $test_mode,
            'customer_id'         => $payment_response['customer_no'],
            'comments'            => PHP_EOL.$order->info['comments'].$transaction_comments.$invoice_comments,
            'payment_details'     => serialize($bank_details),
            'gateway_status'      => $payment_response['tid_status'],
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

        // Get Prepayment payment reference comments
        $reference_comments = NovalnetUtil::novalnetReferenceComments($insert_id, $_SESSION['novalnet'][$this->code]);

        $comments = $_SESSION['novalnet'][$this->code]['comments'].$reference_comments;

        // Update order comments in TABLE_ORDERS
        xtc_db_query("UPDATE ".TABLE_ORDERS." SET
                                    comments ='".$comments."'
                                    WHERE orders_id='".$insert_id."'");

        // Update order comments in TABLE_ORDERS_STATUS_HISTORY
        xtc_db_query("UPDATE ".TABLE_ORDERS_STATUS_HISTORY." SET
                                    comments ='".$comments."'
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
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED'");
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
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS\',".MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE', '', '6', '3','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_VISIBILITY_BYAMOUNT', '', '6', '4','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ENDCUSTOMER_INFO', '', '6', '5','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER', '0', '6', '6', '',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS', '2',  '6', '7', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_CALLBACK_ORDER_STATUS', '3',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_ZONE', '0', '6', '9', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now())
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

        echo '<input type="hidden" id="prepayment_due_date_error" value="'.MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE_ERROR.'"><script src="' . DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet.js" type="text/javascript"></script>';

        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array(
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_CALLBACK_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMENT_ZONE'
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
        if (MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_PREPAYMENT_BLOCK_TITLE')) {
			 if (MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE != '' && (!is_numeric(trim(MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE))
            || MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE > 28 || MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE < 7 )) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_PREPAYMENT_BLOCK_TITLE, MODULE_PAYMENT_NOVALNET_PREPAYMENT_DUE_DATE_ERROR);
                }
                return true;
            } elseif (MODULE_PAYMENT_NOVALNET_PREPAYMENT_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_PREPAYMENT_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_PREPAYMENT_BLOCK_TITLE);
                }
                return false;
            }
        }
        return true;
    }
}
