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
 * Script : callback.php
 *
 */

chdir('../../');
include_once('includes/application_top.php');
include_once (DIR_FS_INC.'xtc_php_mail.inc.php');
include_once(DIR_FS_INC.'xtc_format_price_order.inc.php');
include_once(DIR_WS_INCLUDES.'external/novalnet/classes/NovalnetUtil.php');

// Assign Callback parameters
$callback_request_params = array_map('trim',$_REQUEST);
$process_testmode  = MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE == 'True';

// Getting instance of NovalnetVendorScript Class
$nn_vendor_script = new NovalnetVendorScript($callback_request_params);

if (!empty($callback_request_params['vendor_activation'])) {

    // Insert the affiliate details into 'novalnet_aff_account_detail' table
    xtc_db_perform('novalnet_aff_account_detail', array(
        'vendor_id'       => $callback_request_params['vendor_id'],
        'vendor_authcode' => $callback_request_params['vendor_authcode'],
        'product_id'      => $callback_request_params['product_id'],
        'product_url'     => $callback_request_params['product_url'],
        'aff_accesskey'   => $callback_request_params['aff_accesskey'],
        'activation_date' => (($callback_request_params['activation_date'] != '') ? date('Y-m-d H:i:s', strtotime($callback_request_params['activation_date'])) : date('Y-m-d H:i:s')),
        'aff_id'          => $callback_request_params['aff_id'],
        'aff_authcode'    => $callback_request_params['aff_authcode'])
    );

    // Send notification mail to Merchant
    $nn_vendor_script->sendNotifyMail('Novalnet callback script executed successfully with Novalnet account activation information.');

} else {

    // Get transaction details
    $nn_trans_history = $nn_vendor_script->getOrderReference();

    // Include language file
    include_once(DIR_WS_LANGUAGES . $nn_trans_history['nn_order_lang'] . '/modules/payment/novalnet.php');

    // Collects vendor script request parameters
    $nn_vendor_params   = $nn_vendor_script->getCaptureParams();

    $payment_type_level = $nn_vendor_script->getPaymentTypeLevel();

    // Transaction success status
    $success_status = ($nn_vendor_params['tid_status'] == '100' && $nn_vendor_params['status'] == '100');

    // Format the request amount
    $formated_amount = xtc_format_price_order(($nn_vendor_params['amount']/100), 1, $nn_vendor_params['currency']);

    switch($payment_type_level) {

        // Level 2 payments - Types of Collections available
        case 2:

            if ($success_status) {

                if (in_array($nn_vendor_params['payment_type'], array('INVOICE_CREDIT', 'CASHPAYMENT_CREDIT', 'ONLINE_TRANSFER_CREDIT'))) {

                   $refund_amount = !empty($nn_trans_history['refund_amount']) ? $nn_trans_history['refund_amount'] : 0;
                   $order_total_amount = $nn_trans_history['order_total_amount'] - $refund_amount;

                    // Check for paid amount
                    if (($nn_trans_history['order_paid_amount']+$nn_trans_history['callback_old_amount']) < $order_total_amount) {
                        // Prepare callback comments
                        $callback_comments = PHP_EOL.PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CALLBACK_INVOICE_CREDIT_COMMENTS), $nn_vendor_params['shop_tid'], $formated_amount, date('Y-m-d'), date('H:i:s'), $nn_vendor_params['tid']).PHP_EOL;

                        $total_amount = $nn_trans_history['order_paid_amount'] + $nn_trans_history['callback_old_amount']+$nn_vendor_params['amount'];

                        // Check for fully paid amount
                        if ($order_total_amount <= $total_amount) {

                            // Callback order status
                            $callback_order_status = !empty(constant('MODULE_PAYMENT_'.strtoupper($nn_trans_history['payment_type']).'_CALLBACK_ORDER_STATUS')) ? constant('MODULE_PAYMENT_'.strtoupper($nn_trans_history['payment_type']).'_CALLBACK_ORDER_STATUS') : constant('MODULE_PAYMENT_'.strtoupper($nn_trans_history['payment_type']).'_ORDER_STATUS');

                            if ($nn_vendor_params['payment_type'] == 'ONLINE_TRANSFER_CREDIT') {
                                $callback_order_status = $nn_trans_history['order_current_status'];
                            }
                            // Form Novalnet comments after full payment receival
                            $novalnet_comments  = PHP_EOL . MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS;
                            $novalnet_comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $nn_vendor_params['shop_tid'];
                            $novalnet_comments .= PHP_EOL . (!empty($nn_vendor_params['test_mode']) ? MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE : '') . PHP_EOL;

                            // Update callback comments
                            xtc_db_perform(TABLE_ORDERS, array('comments' => $novalnet_comments), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

                            // Update callback order status due to full payment
                            xtc_db_perform(TABLE_ORDERS, array(
                                'orders_status' => $callback_order_status
                                ), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');
                        } else {
                            // On every partial payment, maintain the current order status
                            $callback_order_status = $nn_trans_history['order_current_status'];
                        }

                         // Update/ Notify callback process
                        $nn_vendor_script->callbackFinalProcess($nn_trans_history['order_no'], $callback_comments, $callback_order_status,$total_amount);
                    } else {
                        $nn_vendor_script->displayMessage('Novalnet Callback script received. Order already Paid');
                    }
                } else {
                    // Callback order status

                    $callback_order_status = $nn_trans_history['gateway_status'] != '100' ? constant('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE') : constant('MODULE_PAYMENT_'.strtoupper($nn_trans_history['payment_type']).'_ORDER_STATUS');

                    // Prepare callback comments
                    $callback_comments = PHP_EOL.PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CALLBACK_INVOICE_CREDIT_COMMENTS), $nn_vendor_params['shop_tid'], $formated_amount, date('Y-m-d'), date('H:i:s'), $nn_vendor_params['tid']).PHP_EOL;

                    // Update callback order status due to full payment
                    xtc_db_perform(TABLE_ORDERS, array(
                        'orders_status' => $callback_order_status
                        ), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

                    // Update/ Notify callback process
                    $nn_vendor_script->callbackFinalProcess($nn_trans_history['order_no'], $callback_comments, $callback_order_status);

                    $nn_vendor_script->sendNotifyMail('Novalnet callback script executed successfully with Novalnet account activation information.');

                }
                $nn_vendor_script->displayMessage('Novalnet Callbackscript received. Payment type ( ' . $nn_vendor_params['payment_type'] . ' ) is not applicable for this process!', $nn_trans_history['order_no']);
            }

            break;

        // Level 1 payments - Types of Chargebacks
        case 1:
            if ($success_status) {

                $formatted_amount = xtc_format_price_order(($nn_vendor_params['amount']/100), 1, $nn_vendor_params['currency'], false);
                $formatted_amount .= ' ' . $nn_vendor_params['currency'];

                // Prepare callback comments
                $comments = (in_array($nn_vendor_params['payment_type'],array('CREDITCARD_BOOKBACK','PAYPAL_BOOKBACK','PRZELEWY24_REFUND', 'GUARANTEED_INVOICE_BOOKBACK', 'GUARANTEED_SEPA_BOOKBACK', 'REFUND_BY_BANK_TRANSFER_EU', 'CASHPAYMENT_REFUND'))) ? NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK_COMMENTS) : NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK_COMMENTS);

                $callback_comments = PHP_EOL . PHP_EOL. sprintf($comments, $nn_vendor_params['tid_payment'],$formatted_amount, date('Y-m-d'), date('H:i:s'), $nn_vendor_params['tid']) . PHP_EOL;

                // Update/ Notify callback process
                $nn_vendor_script->callbackFinalProcess($nn_trans_history['order_no'], $callback_comments, $nn_trans_history['order_current_status']);

                }
            break;

        // Level 0 payments - Types of payments
        case 0:

            $formatted_amount = xtc_format_price_order(($nn_vendor_params['amount']/100), 1, $nn_vendor_params['currency'], false);
            $formatted_amount .= ' ' . $nn_vendor_params['currency'];

            if ($nn_vendor_params['payment_type'] == 'PAYPAL' && in_array($nn_trans_history['gateway_status'], array(85, 90))) {

                 if ($nn_trans_history['gateway_status'] == '90' && $nn_vendor_params['tid_status'] == '85') {
                    $callback_order_status = MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE;
                    $callback_comments = PHP_EOL.PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_PENDING_TO_ONHOLD, $nn_vendor_params['shop_tid'], date('Y-m-d H:i:s')).PHP_EOL;                 

                } elseif(in_array($nn_trans_history['gateway_status'], array(85, 90)) && $nn_vendor_params['tid_status'] == 100) {
                    $callback_order_status = MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS;
                    $callback_comments = PHP_EOL.PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_PAYMENT_CONFIRMED, date('Y-m-d H:i:s')).PHP_EOL;
                }
                $param['gateway_status'] = $nn_vendor_params['tid_status'];

                // Update paypal transaction ID
                if (!empty($nn_vendor_params['paypal_transaction_id']) && !empty($nn_trans_history['payment_details'])) {
                    $param['payment_details'] = serialize(array(
                        'paypal_transaction_id' => $nn_vendor_params['paypal_transaction_id'],
                        'tid'                   => $nn_vendor_params['shop_tid'],
                    ));
                }
                // Perform db operations to update the table
                xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$nn_vendor_params['shop_tid']."'");

                $callback_order_status = ($callback_order_status > 0) ? $callback_order_status : DEFAULT_ORDERS_STATUS_ID;
                
                xtc_db_perform(TABLE_ORDERS, array(
                    'orders_status' => $callback_order_status
                ), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

                $nn_vendor_script->callbackFinalProcess($nn_trans_history['order_no'], $callback_comments, $callback_order_status, '', $nn_trans_history['order_total_amount']);

             } elseif (in_array($nn_vendor_params['payment_type'], array('PAYPAL','PRZELEWY24')) && $nn_vendor_params['tid_status'] == '100') {

                // Check for paid amount
                if ($nn_trans_history['order_paid_amount'] < $nn_trans_history['order_total_amount']) {
                    if ($nn_trans_history['payment_type'] == 'novalnet_paypal') {
                        $nn_trans_history['payment_type'] = 'novalnet_pp';
                    }
                    // Get order status to be updated
                    $callback_order_status = constant('MODULE_PAYMENT_'. strtoupper($nn_trans_history['payment_type']) .'_ORDER_STATUS');

                    $callback_order_status = ($callback_order_status > 0) ? $callback_order_status : DEFAULT_ORDERS_STATUS_ID;

                    // Update callback order status
                    xtc_db_perform(TABLE_ORDERS, array(
                        'orders_status' => $callback_order_status
                    ), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

                    $formatted_amount = xtc_format_price_order(($nn_vendor_params['amount']/100), 1, $nn_vendor_params['currency'], false);
                    $formatted_amount .= ' ' . $nn_vendor_params['currency'];

                    // Prepare callback comments
                     $callback_comments = PHP_EOL . PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CALLBACK_UPDATE_COMMENTS), $nn_vendor_params['shop_tid'], $formatted_amount, date('Y-m-d'), date('H:i:s')) . PHP_EOL;

                    // Update transaction status
                    xtc_db_query('UPDATE novalnet_transaction_detail SET gateway_status= '.$nn_vendor_params['tid_status'].' where order_no='.$nn_trans_history['order_no']);

                    $nn_vendor_params['amount'] = $nn_trans_history['order_total_amount'];

                    // Update/ Notify callback process
                    $nn_vendor_script->callbackFinalProcess($nn_trans_history['order_no'], $callback_comments, $callback_order_status, '', $nn_trans_history['order_total_amount']);
                }

                $nn_vendor_script->displayMessage( 'Novalnet Callbackscript received. Order already Paid' );

            } elseif (in_array($nn_vendor_params['payment_type'], array('CREDITCARD', 'INVOICE_START', 'GUARANTEED_INVOICE','GUARANTEED_DIRECT_DEBIT_SEPA', 'DIRECT_DEBIT_SEPA')) && in_array($nn_trans_history['gateway_status'], array('75', '91', '98', '99')) && in_array($nn_vendor_params['tid_status'], array('91', '98', '99', '100'))) {
                $callback_comments = '';
                if ($nn_trans_history['gateway_status'] == '75' && in_array($nn_vendor_params['tid_status'], array('91', '99'))) {
                    $order_status = MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE;
                    $callback_comments .= PHP_EOL.PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_PENDING_TO_ONHOLD, $nn_vendor_params['shop_tid'], date('Y-m-d H:i:s')).PHP_EOL;
                } elseif (in_array($nn_trans_history['gateway_status'], array('75', '91', '98', '99')) && $nn_vendor_params['tid_status'] == '100') {
                    $order_status = ($nn_vendor_params['payment_type'] == 'GUARANTEED_INVOICE' ) ? MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS : constant('MODULE_PAYMENT_'.strtoupper($nn_trans_history['payment_type']).'_ORDER_STATUS');

                    $callback_comments .= PHP_EOL.PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_PAYMENT_CONFIRMED, date('Y-m-d H:i:s')). PHP_EOL;
                }
               if (in_array($nn_trans_history['payment_type'], array('novalnet_invoice')) && in_array($nn_vendor_params['payment_type'], array('INVOICE_START', 'GUARANTEED_INVOICE')) && $nn_vendor_params['tid_status'] == 100) {
                    $param['gateway_status'] = $nn_vendor_params['tid_status'];
                    xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$nn_vendor_params['shop_tid']."'");
                    $invoice_comments .= PHP_EOL. PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_PAYMENT_CONFIRMED, date('Y-m-d H:i:s'));
                    $invoice_comments = $nn_vendor_script->getTransactionComments($nn_vendor_params);

                    $invoice_comments .= $nn_vendor_script->getReferenceTransaction($nn_trans_history);

                    NovalnetUtil::sendPaymentNotificationMail($nn_trans_history['order_no'], $invoice_comments);
                }
                $param['gateway_status'] = $nn_vendor_params['tid_status'];
                $param['payment_details'] = $nn_trans_history['payment_details'];
                // Perform db operations to update the table
                xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$nn_vendor_params['shop_tid']."'");
                $callback_comments = $callback_comments.$invoice_comments;
                 // Update callback comments

                $nn_vendor_script->updateCallbackComments(array(
                        'order_no'         => $nn_trans_history['order_no'],
                        'comments'         => $callback_comments.PHP_EOL,
                        'orders_status_id' => $order_status
                ));


               // Update order status in orders table
                xtc_db_perform(TABLE_ORDERS, array(
                'orders_status' => $order_status
                ),'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

                // Send E-mail notification
                $nn_vendor_script->sendNotifyMail($callback_comments);

                $nn_vendor_script->displayMessage($callback_comments);

            } elseif ($nn_vendor_params['payment_type'] == 'PRZELEWY24'  && $nn_vendor_params['tid_status'] != '86') {

                // PRZELEWY24 cancel process
                $nn_vendor_script->updatePrzelewy24CancelComments($nn_trans_history);
            }
            $nn_vendor_script->displayMessage('Novalnet Callbackscript received. Payment type ( '.$nn_vendor_params['payment_type'].' ) is not applicable for this process!');
            break;
    }

    // Check for transaction status other than 100
    $message = (( $nn_vendor_params['tid_status'] != '100' || $nn_vendor_params['status'] != '100' ) ? 'Novalnet callback received. Status is not valid.' : 'Novalnet callback received. Callback Script executed already.');
    $nn_vendor_script->displayMessage($message);
}
class NovalnetVendorScript {

    /**
     * @Type of payments available - Level : 0
     * @var array
     */
    protected $array_payment = array('CREDITCARD','INVOICE_START','DIRECT_DEBIT_SEPA','GUARANTEED_INVOICE','PAYPAL','ONLINE_TRANSFER','IDEAL','EPS','GIROPAY','GUARANTEED_DIRECT_DEBIT_SEPA','PRZELEWY24','CASHPAYMENT');

    /**
     * @Type of Chargebacks available - Level : 1
     * @var array
     */
    protected $array_chargeback = array('RETURN_DEBIT_SEPA','CREDITCARD_BOOKBACK','CREDITCARD_CHARGEBACK','PAYPAL_BOOKBACK','REFUND_BY_BANK_TRANSFER_EU','PRZELEWY24_REFUND','REVERSAL', 'CASHPAYMENT_REFUND', 'GUARANTEED_INVOICE_BOOKBACK', 'GUARANTEED_SEPA_BOOKBACK');

    /**
     * @Type of Credit Entry payment and Collections available - Level : 2
     * @var array
     */
    protected $array_collection = array('INVOICE_CREDIT','CREDIT_ENTRY_CREDITCARD','CREDIT_ENTRY_SEPA','DEBT_COLLECTION_SEPA','DEBT_COLLECTION_CREDITCARD','ONLINE_TRANSFER_CREDIT', 'CASHPAYMENT_CREDIT' , 'DEBT_COLLECTION_DE', 'CREDIT_ENTRY_DE');

    /**
     * @Payment type array for various payment methods
     * @var array
     */
    protected $array_payment_group = array(
        'novalnet_cc'           => array('CREDITCARD', 'CREDITCARD_BOOKBACK', 'CREDITCARD_CHARGEBACK', 'CREDIT_ENTRY_CREDITCARD', 'DEBT_COLLECTION_CREDITCARD'),
        'novalnet_sepa'         => array('DIRECT_DEBIT_SEPA', 'RETURN_DEBIT_SEPA', 'DEBT_COLLECTION_SEPA','CREDIT_ENTRY_SEPA','GUARANTEED_DIRECT_DEBIT_SEPA','REFUND_BY_BANK_TRANSFER_EU', 'TRANSACTION_CANCELLATION', 'GUARANTEED_SEPA_BOOKBACK'),
        'novalnet_ideal'        => array('IDEAL','REFUND_BY_BANK_TRANSFER_EU','REVERSAL','ONLINE_TRANSFER_CREDIT','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_sofortbank'   => array('ONLINE_TRANSFER','ONLINE_TRANSFER_CREDIT','REFUND_BY_BANK_TRANSFER_EU','REVERSAL','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_pp'           => array('PAYPAL', 'PAYPAL_BOOKBACK'),
        'novalnet_paypal'       => array('PAYPAL', 'PAYPAL_BOOKBACK'),
        'novalnet_prepayment'   => array('INVOICE_START', 'INVOICE_CREDIT', 'REFUND_BY_BANK_TRANSFER_EU'),
        'novalnet_invoice'      => array('INVOICE_START', 'INVOICE_CREDIT', 'GUARANTEED_INVOICE', 'TRANSACTION_CANCELLATION', 'REFUND_BY_BANK_TRANSFER_EU', 'GUARANTEED_INVOICE_BOOKBACK','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_eps'          => array('EPS', 'REFUND_BY_BANK_TRANSFER_EU','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE', 'ONLINE_TRANSFER_CREDIT', 'REVERSAL'),
        'novalnet_giropay'      => array('GIROPAY', 'REFUND_BY_BANK_TRANSFER_EU','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE', 'ONLINE_TRANSFER_CREDIT', 'REVERSAL'),
        'novalnet_przelewy24'   => array('PRZELEWY24', 'PRZELEWY24_REFUND'),
        'novalnet_barzahlen'    => array('CASHPAYMENT','CASHPAYMENT_REFUND', 'CASHPAYMENT_CREDIT'),
   );

    /**
     * Required parameters for transaction process
     * @var array
     */
    protected $params_required = array(
        'vendor_id',
        'tid',
        'payment_type',
        'status',
        'tid_status'
    );

    /**
     * Required parameters for affiliate process
     * @var array
     */
    protected $affiliate_params_required = array(
        'vendor_id',
        'vendor_authcode',
        'product_id',
        'aff_id',
        'aff_accesskey',
        'aff_authcode'
    );

    /**
     * @Array Allowed transaction status.
     */
    protected $success_tid_status = array(
        '100',
        '99',
        '98',
        '91',
        '90',
        '85',
        '86'
    );

    /**
     * @var Mail ID to be notify to technic
     */
    protected $technic_notify_mail = 'technic@novalnet.de';

    /**
     *
     * Constructor
     *
     */
    function __construct($arycapture = array()) {

        // Validates callback parameters before processing
        $this->callback_request_params = $this->validateCaptureParams($arycapture);
    }

    /**
     * Return capture parameters
     *
     * @return array
     */
    function getCaptureParams() {

        return $this->callback_request_params;
    }

    /**
     * Validate IP address
     * @param none
     *
     * @return void
     */
    function validateIpAddress() {
        global $process_testmode;

        $real_host_ip = gethostbyname('pay-nn.de');

        if (empty($real_host_ip)) {
            $this->displayMessage('Novalnet HOST IP missing');
        }

        $client_ip = xtc_get_ip_address();

        if (($client_ip != $real_host_ip) && !$process_testmode) {
            $this->displayMessage('Novalnet callback received. Unauthorised access from the IP '.$client_ip);
        }
    }

    /**
     * Perform parameter validation process
     * @param $arycapture
     *
     * @return array
     */
    function validateCaptureParams($arycapture) {

        // Function to check whether the callback is called from authorized IP
        $this->validateIpAddress();

        // Validates basic callback parameters
        if (!isset($arycapture['vendor_activation'])) {

            // Add additional required parameters
            if (isset($arycapture['payment_type']) && in_array($arycapture['payment_type'], array_merge($this->array_chargeback, $this->array_collection))) {
                array_push($this->params_required, 'tid_payment');
            }

            // Validate required parameters
            foreach ($this->params_required as $v) {

                if ($arycapture[$v] == '') {
                    $this->displayMessage('Required param ( ' . $v . '  ) missing!');
                }

                // Validate corresponding TID
                if (in_array($v, array('tid', 'tid_payment', 'signup_tid')) && !preg_match('/^\d{17}$/', $arycapture[$v])) {
                    $this->displayMessage('Novalnet callback received. Invalid TID ['.$arycapture[$v] . '] for Order.');
                }
            }

            // Get the Corresponding TID
            if (isset($arycapture['signup_tid']) && !empty($arycapture['signup_tid'])) {
                $arycapture['shop_tid'] = $arycapture['signup_tid'];
            } elseif (in_array($arycapture['payment_type'], array_merge($this->array_chargeback, $this->array_collection))) {
                $arycapture['shop_tid'] = $arycapture['tid_payment'];
            } else {
                $arycapture['shop_tid'] = $arycapture['tid'];
            }

        } else {

            // Validates required affiliate parameters
            foreach ($this->affiliate_params_required as $v) {
                if (empty($arycapture[$v])) {
                    $this->displayMessage('Required param ( ' . $v . '  ) missing!');
                }
            }
        }
        return $arycapture;
    }

    /**
     * Update Callback comments in orders_status_history table
     * @param $datas
     *
     * @return void
     */
    function updateCallbackComments($datas) {

        $comments = ((isset($datas['comments']) && $datas['comments'] != '') ? $datas['comments'] : '');

        $column_exist = NovalnetUtil::commentSentColumnExist();

        ($column_exist) ? xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, comments_sent) VALUES ('".$datas['order_no']."', '".$datas['orders_status_id']."', NOW(), '1','".$comments."','1')") : xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES ('".$datas['order_no']."', '".$datas['orders_status_id']."', NOW(), '1','".$comments."')");
    }

    /**
     * Log callback process in novalnet_callback_history table
     * @param $order_no
     * @param $amount
     *
     * @return void
     */
    function logCallbackProcess($order_no, $amount = 0) {

        if (!empty($this->callback_request_params['amount'])) {

            $this->callback_request_params['amount'] = !empty($amount) ? $amount : $this->callback_request_params['amount'];

            xtc_db_query("UPDATE novalnet_transaction_detail SET callback_amount= ".$this->callback_request_params['amount']." where order_no=$order_no");
        }
    }

    /**
     * Display message
     * @param $message
     * @param $order_no
     *
     * @return void
     */
    function displayMessage($message, $order_no = '') {
        echo !empty($order_no) ? 'message='. NovalnetUtil::setUtf8Mode($message).'&order_no='.$order_no : 'message='.NovalnetUtil::setUtf8Mode($message);
        exit;
    }

    /**
     * Get given payment_type level for process
     *
     * @return integer
     */
    function getPaymentTypeLevel() {
        if (in_array($this->callback_request_params['payment_type'], $this->array_payment)) {
            return 0;
        }
        if (in_array($this->callback_request_params['payment_type'], $this->array_chargeback)) {
            return 1;
        }
        if (in_array($this->callback_request_params['payment_type'], $this->array_collection)) {
            return 2;
        }
    }

    /**
     * Get order reference from the novalnet_transaction_detail table on shop database
     *
     * @return array
     */
    function getOrderReference() {

        if (!empty($this->callback_request_params['order_no']) && !empty($this->callback_request_params['status']) && in_array($this->callback_request_params['status'], array('100','90'))) {

            $query = xtc_db_query("select payment_method from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($this->callback_request_params['order_no'])."' ");
            $row = xtc_db_fetch_array($query);
            if (empty($row['payment_method']) || strpos($row['payment_method'], 'novalnet') === false) {

                list($subject, $message) = $this->buildNotificationMessage();

                // Send E-mail, if transaction not found
                xtc_php_mail(EMAIL_FROM, STORE_NAME, $this->technic_notify_mail , 'Technic' , '', '', '', '', '', $subject, $message , '');

                $this->displayMessage($message);
            }
        }


        // Get transaction details
        $sql_query = xtc_db_query("select order_no, amount, payment_id, payment_type, gateway_status, callback_amount, payment_details,total_amount,refund_amount from novalnet_transaction_detail where tid = '".xtc_db_input($this->callback_request_params['shop_tid'])."' ");
        $order_values = xtc_db_fetch_array($sql_query);

        // Handle transaction cancellation
        $this->transactionCancellation($order_values);

        if(empty($order_values['order_no']) && !empty($this->callback_request_params['order_no'])) {
            // Handle communication failure.
            $this->handleCommunicationFailure();
        }

        if (!empty($order_values['order_no'])) {
            $order_values['tid'] = $this->callback_request_params['shop_tid'];

            // Validate payment type
            if (!in_array($this->callback_request_params['payment_type'], $this->array_payment_group[$order_values['payment_type']])) {
                $this->displayMessage('Novalnet callback received. Payment Type [' . $this->callback_request_params['payment_type'] . '] is not valid.');
            }
            // Validate Order number
            if (!empty($this->callback_request_params['order_no']) && $this->callback_request_params['order_no'] != $order_values['order_no']) {
                $this->displayMessage('Novalnet callback received. Order Number is not valid/ Found.');
            }

            // Get shop details
            $query = xtc_db_query("select orders_status, language from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($order_values['order_no'])."' ");

            $additional_values = xtc_db_fetch_array($query);

            $order_values['order_current_status'] = $additional_values['orders_status'];
            $order_values['nn_order_lang'] = $additional_values['language'];
            // Get old order information
            if (in_array($order_values['payment_type'], array('novalnet_invoice','novalnet_prepayment', 'novalnet_barzahlen'))) {
                $tables_sql = xtc_db_query('select table_name from information_schema.columns where table_schema = "' . DB_DATABASE . '" AND table_name= "novalnet_callback_history"');
                $result = xtc_db_fetch_array($tables_sql);
                if (empty($order_values['callback_amount']) && $result['table_name'] == 'novalnet_callback_history') {
                    $order_totalqry = xtc_db_query("select sum(amount) as amount_total from novalnet_callback_history where order_no = '".xtc_db_input($order_values['order_no'])."'");
                    $result = xtc_db_fetch_array($order_totalqry);
                    $callback_amount_value = $result['amount_total'];
                }
                // Get callback order status
                $order_values['callback_script_status'] = constant('MODULE_PAYMENT_'.strtoupper($order_values['payment_type']).'_CALLBACK_ORDER_STATUS');
            }

            $order_values['order_total_amount']  = $order_values['amount'];
            $order_values['callback_old_amount'] = isset( $callback_amount_value) ?  $callback_amount_value : 0;
            $order_values['order_paid_amount']   = isset($order_values['callback_amount']) ? $order_values['callback_amount'] : 0;
        } else {
            list($subject, $message) = $this->buildNotificationMessage();

            // Send E-mail, if transaction not found
            xtc_php_mail(EMAIL_FROM, STORE_NAME, $this->technic_notify_mail , 'Technic' , '', '', '', '', '', $subject, $message , '');

            $this->displayMessage($message);
        }
        return $order_values;
    }

    /**
     * Send notification mail to Merchant
     * @param $message
     *
     * @return void
     */
    function sendNotifyMail($message) {
        // Check for callback notification
        if (MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND == 'True' && NovalnetUtil::validateEmail(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO)) {
            // Get E-mail to address
            $email_to        = ((MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO != '') ? MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO : STORE_OWNER_EMAIL_ADDRESS);

            // Get E-mail to name
            $email_to_name   = (strpos($email_to,',')) ? '' : STORE_OWNER;

            // Assign Mail subject
            $email_subject   = 'Novalnet Callback script notification - '. STORE_NAME;

            if ($email_to != '') {
                // Send E-mail
                xtc_php_mail(EMAIL_FROM, STORE_NAME, $email_to , $email_to_name , '', '', '', '', '', $email_subject, $message , '');
                echo 'Mail sent!<br>';
            } else {
                echo 'Mail not sent!';
            }
        }

        // Display message
        $this->displayMessage($message);
    }

    /**
     * Performs final callback process
     * @param $order_id
     * @param $comments
     * @param $callback_status_id
     * @param $total_amount
     *
     * @return void
     */
    function callbackFinalProcess($order_id, $comments, $callback_status_id, $total_amount = '') {

        // Update callback comments
        $this->updateCallbackComments(array(
            'order_no'          => $order_id,
            'comments'          => $comments,
            'orders_status_id'  => $callback_status_id
        ));

        // Log the callback process
        $this->logCallbackProcess($order_id, $total_amount);

        // Send E-mail notification
        $this->sendNotifyMail($comments);
    }

    /**
     * Update Przelewy24 cancel status
     * $param $nn_trans_history
     *
     * @return void
     */
    function updatePrzelewy24CancelComments($nn_trans_history) {
        $nn_vendor_params = $this->getCaptureParams();

        $cancel_order_status = MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED;

        $cancel_order_status = ($cancel_order_status > 0 || $cancel_order_status != '' ? $cancel_order_status : DEFAULT_ORDERS_STATUS_ID);

        // Assign przelewy24 payment status
        xtc_db_perform(TABLE_ORDERS, array(
                'orders_status' => $cancel_order_status
           ), 'update', 'orders_id="'.$nn_trans_history['order_no'].'"');

        // Form failure comments
        $callback_comments = MODULE_PAYMENT_NOVALNET_PRZELEWY24_CANCELLED_COMMENT. $this->getRequestMessage($nn_vendor_params);

        // Update callback comments
        $this->updateCallbackComments(array(
            'order_no'         => $nn_trans_history['order_no'],
            'comments'         => $callback_comments,
            'orders_status_id' => $cancel_order_status
        ));

        // Send E-mail notification
        $this->sendNotifyMail($callback_comments);
    }

    /**
     * Get server message.
     * $param $nn_vendor_params
     *
     * @return string
     */
    function getRequestMessage($nn_vendor_params) {
        return !empty($nn_vendor_params['termination_reason']) ? $nn_vendor_params['termination_reason'] : ( !empty($nn_vendor_params['status_desc']) ? $nn_vendor_params['status_desc'] : (!empty($nn_vendor_params['status_text']) ? $nn_vendor_params['status_text'] :  (!empty($nn_vendor_params['status_message']) ? $nn_vendor_params['status_message'] : MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR)));
    }

    /**
     * Handle TRANSACTION_CANCELLATION process
     * $param $order_values
     *
     * @return void
     */
    function transactionCancellation($order_values) {
        // Get shop details
        $query = xtc_db_query("select payment_method, customers_id, language, orders_status from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($order_values['order_no'])."' ");
        $order_details = xtc_db_fetch_array($query);

        // Include language file.
        include_once DIR_WS_LANGUAGES . $order_details['language'] . '/modules/payment/novalnet.php';

        $nn_vendor_params = $this->getCaptureParams();

         if ($nn_vendor_params['payment_type'] == 'TRANSACTION_CANCELLATION' && $order_values['gateway_status'] != '100') {

           $callback_comments = PHP_EOL.PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_PAYMENT_CANCELLED, date('Y-m-d H:i:s'));
            // Update transaction status
            xtc_db_query('UPDATE novalnet_transaction_detail SET gateway_status= '.$nn_vendor_params['tid_status'].' where order_no='.$order_values['order_no']);

            $cancel_order_status = MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED;
            // Update callback comments
            $this->updateCallbackComments(array(
                'order_no'         => $order_values['order_no'],
                'comments'         => $callback_comments,
                'orders_status_id' => $cancel_order_status
            ));

            // Assign przelewy24 payment status
            xtc_db_perform(TABLE_ORDERS, array(
                    'orders_status' => $cancel_order_status
               ), 'update', 'orders_id="'.$order_values['order_no'].'"');

            // Send E-mail notification
            $this->sendNotifyMail($callback_comments);

            $this->displayMessage($callback_comments);
        }
    }

    /**
     * Handling communication failure for the orders
     */
    function handleCommunicationFailure() {

        // Get shop details
        $query = xtc_db_query("select payment_method, customers_id, language, orders_status from ".TABLE_ORDERS." where orders_id = '".xtc_db_input($this->callback_request_params['order_no'])."' ");
        $order_details = xtc_db_fetch_array($query);

        if (!empty($order_details['payment_method']) && strpos($order_details['payment_method'], 'novalnet') !== false) {

            // Include language file.
            include_once DIR_WS_LANGUAGES . $order_details['language'] . '/modules/payment/novalnet.php';

            // Get test mode value.
            $test_mode = (int)(!empty($this->callback_request_params['test_mode']) || constant('MODULE_PAYMENT_' . $payment_type . '_TEST_MODE' == 'True'));

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($this->callback_request_params['shop_tid'], $test_mode);

            // Validate order based on payment type.
            if (! in_array($this->callback_request_params['payment_type'], $this->array_payment_group[$order_details['payment_method']])) {
                $this->displayMessage('Novalnet callback received. Payment Type ['.$this->callback_request_params['payment_type'].'] is not valid.');
            }

            // Check for success transaction.
            if(!empty($this->callback_request_params['tid_status']) && in_array($this->callback_request_params['tid_status'], $this->success_tid_status)) {

                // Get vendor and authcode details.
                $vendor_id        = trim(MODULE_PAYMENT_NOVALNET_VENDOR_ID);
                $vendor_authcode = trim(MODULE_PAYMENT_NOVALNET_AUTHCODE);
                if ($vendor_id != $this->callback_request_params['vendor_id']) {
                $affiliate = xtc_db_query('SELECT aff_authcode FROM  '.DB_PREFIX.'novalnet_aff_account_detail WHERE aff_id = "'.xtc_db_input($this->callback_request_params['vendor_id']).'"');
                    if(!empty($affiliate['aff_authcode'])) {
                        $vendor_id        = $this->callback_request_params['vendor_id'];
                        $vendor_authcode = $affiliate['aff_authcode'];
                    }
                }

                $payment_type = strtoupper($order_details['payment_method']);

                $payment_id = $this->getPaymentType($order_details['payment_method']);

                $pending_payment = (in_array($this->callback_request_params['payment_type'], array('PAYPAL', 'PRZELEWY24')) && in_array($this->callback_request_params['tid_status'], array('90', '86')));

                // Insert the values in novalnet_transaction_detail table.
                $table_values = array(
                    'tid'                   => $this->callback_request_params['shop_tid'],
                    'payment_id'            => $payment_id,
                    'payment_type'          => $order_details['payment_method'],
                    'amount'                => $this->callback_request_params['amount'],
                    'gateway_status'        => $this->callback_request_params['tid_status'],
                    'order_no'              => $this->callback_request_params['order_no'],
                    'date'                  => date('Y-m-d H:i:s'),
                    'test_mode'             => $test_mode,
                    'customer_id'           => $order_details['customer_id'],
                    'reference_transaction' => '',
                    'zerotrxnreference'     => '',
                    'zerotrxndetails'       => '',
                    'zero_transaction'      => '',
                    'callback_amount'       => ($pending_payment || in_array($this->callback_request_params['payment_type'], array('INVOICE_START', 'ONLINE_TRANSFER_CREDIT'))) ? '0' :  $this->callback_request_params['amount'],
                    'total_amount'          => $this->callback_request_params['amount'],
                );

                xtc_db_perform('novalnet_transaction_detail', $table_values);

                // Set paypal pending status.
                if(($this->callback_request_params['payment_type'] == 'PAYPAL' && $this->callback_request_params['tid_status'] == '90') || ($this->callback_request_params['payment_type'] == 'PRZELEWY24' && $this->callback_request_params['tid_status'] == '86')) {
                    $order_status = constant('MODULE_PAYMENT_' . $payment_type . '_PENDING_ORDER_STATUS');
                } else {
                    $order_status = constant('MODULE_PAYMENT_' . $payment_type . '_ORDER_STATUS');
                }

                if($this->callback_request_params['payment_type'] == 'PAYPAL' && $this->callback_request_params['tid_status'] == '85') {
                    $order_status = constant('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE');
                }

                // Update Novalnet transaction comments and status.
                xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".NovalnetUtil::checkDefaultOrderStatus($order_status)."', comments='".$transaction_comments."' WHERE orders_id='".xtc_db_input($this->callback_request_params['order_no'])."'");

                xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, array (
                    'orders_id'         => $this->callback_request_params['order_no'],
                    'orders_status_id'  => $order_status,
                    'date_added'        => 'now()',
                    'customer_notified' => (int) (SEND_EMAILS == 'true'),
                    'comments'          => PHP_EOL.PHP_EOL.$transaction_comments,
                    'comments_sent'     => '1',
                ));

                $this->displayMessage('Novalnet Callback Script executed successfully, Transaction details are updated' .$transaction_comments);
            } else {
                // Update Novalnet transaction comments and status.
                xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED)."' WHERE orders_id='".xtc_db_input($this->callback_request_params['order_no'])."'");

                xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, array (
                    'orders_id'         => $this->callback_request_params['order_no'],
                    'orders_status_id'  => MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED,
                    'date_added'        => 'now()',
                    'customer_notified' => (int) (SEND_EMAILS == 'true'),
                ));

                $this->displayMessage('Novalnet Callback Script executed successfully, Transaction details are updated');

            }
        }
    }

    /**
     * Build the Notification Message
     *
     * @return array
     */
    function buildNotificationMessage() {

        $subject = 'Critical error on shop system '.STORE_NAME.': order not found for TID: ' . $this->callback_request_params['shop_tid'];
        $message = "Dear Technic team,<br/><br/>Please evaluate this transaction and contact our payment module team at Novalnet.<br/><br/>";
        $message .= 'Merchant ID: ' . $this->callback_request_params['vendor_id'] . '<br/>';
        $message .= 'Project ID: ' . $this->callback_request_params['product_id'] . '<br/>';
        $message .= 'TID: ' . $this->callback_request_params['shop_tid'] . '<br/>';
        $message .= 'TID status: ' . $this->callback_request_params['tid_status'] . '<br/>';
        $message .= 'Order no: ' . $this->callback_request_params['order_no'] . '<br/>';
        $message .= 'Payment type: ' . $this->callback_request_params['payment_type'] . '<br/>';
        $message .= 'E-mail: ' . $this->callback_request_params['email'] . '<br/>';

        $message .= '<br/><br/>Regards,<br/>Novalnet Team';

        return array($subject, $message);
    }

    /**
     * Get the payment key
     * @param string $payment_name
     *
     * @return integer
     */
    function getPaymentType($payment_name) {
        if(in_array($this->callback_request_params['payment_type'], array('GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA'))) {
            $payment_name = $this->callback_request_params['payment_type'];
        }
        switch($payment_name) {
            case 'novalnet_invoice' :
            case 'novalnet_prepayment' :
                $payment_id = 27;
                break;
            case 'novalnet_cc' :
                $payment_id = 6;
                break;
            case 'novalnet_sepa' :
                $payment_id = 37;
                break;
            case 'novalnet_sofortbank' :
                $payment_id = 33;
                break;
            case 'novalnet_paypal' :
                $payment_id = 34;
                break;
            case 'novalnet_ideal' :
                $payment_id = 49;
                break;
            case 'novalnet_eps' :
                $payment_id = 50;
                break;
            case 'novalnet_giropay' :
                $payment_id = 69;
                break;
            case 'novalnet_przelewy24' :
                $payment_id = 78;
                break;
            case 'novalnet_barzahlen' :
                $payment_id = 59;
                break;
            case 'GUARANTEED_INVOICE' :
                $payment_id = 41;
                break;
            case 'GUARANTEED_DIRECT_DEBIT_SEPA' :
                $payment_id = 40;
                break;
        }
        return $payment_id;
    }

    /**
     * Get the transaction comments
     * @param array $nn_vendor_params
     *
     * @return string
     */
    function getTransactionComments($nn_vendor_params) {
        $transaction_comments = PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS.PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $nn_vendor_params['shop_tid'];
        // Add test_mode text
        if ($nn_vendor_params['test_mode']) {
            $transaction_comments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE;
        }
        if ($nn_vendor_params['payment_type'] == 'GUARANTEED_INVOICE') {
            $transaction_comments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS_GUARANTEE_PAYMENT;
        }

        return $transaction_comments;
    }

    /**
     * Get the reference transaction comments
     * @param array $nn_trans_history
     *
     * @return string
     */
    function getReferenceTransaction($nn_trans_history) {
        $nn_vendor_params = $this->getCaptureParams();

        $data = unserialize($nn_trans_history['payment_details']);
        $nn_vendor_params['payment_id'] = $nn_trans_history['payment_id'];
        $nn_vendor_params['product']  = $nn_vendor_params['product_id'];
        $trans_comments = PHP_EOL.PHP_EOL.NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH).PHP_EOL;
        $invoice_due_date = $nn_vendor_params['due_date'];
        $trans_comments .= ($invoice_due_date != '') ? NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_DUE_DATE).': '.date(DATE_FORMAT, strtotime($invoice_due_date)).PHP_EOL : '';
        $trans_comments .= MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER.': '. (!empty($data['account_holder']) ? $data['account_holder'] : $nn_vendor_params['invoice_account_holder']). PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_IBAN.': '. (!empty($data['bank_iban']) ? $data['bank_iban'] : $nn_vendor_params['invoice_iban']) . PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_SWIFT_BIC.': '. (!empty($data['bank_bic']) ? $data['bank_bic'] : $nn_vendor_params['invoice_bic']) . PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_BANK.': '. (!empty($nn_vendor_params['invoice_bankname']) ? $nn_vendor_params['invoice_bankname'] : NovalnetUtil::setUtf8Mode($data['bank_name'])) .' '.NovalnetUtil::setUtf8Mode($data['bank_city']).PHP_EOL;
        $trans_comments .= MODULE_PAYMENT_NOVALNET_AMOUNT.': '.xtc_format_price_order(((!empty($nn_vendor_params['amount']) ? $nn_vendor_params['amount'] : $nn_vendor_params['amount'] )/100), 1, $nn_vendor_params['currency']).PHP_EOL;

        // Add Payment reference notification
        $trans_comments .= NovalnetUtil::novalnetReferenceComments($nn_trans_history['order_no'], $nn_vendor_params);

        return $trans_comments;
    }
}
