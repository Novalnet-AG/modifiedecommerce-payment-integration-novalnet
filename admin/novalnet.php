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
 * Script : novalnet.php
 *
 */

ob_start();
include_once('includes/application_top.php');
include_once(DIR_FS_CATALOG.'includes/external/novalnet/classes/NovalnetUtil.php');
include_once(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet.php');
include_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');

class NovalnetExtension {

    /**
     * @var array
     */
    public $supports = array(
            'payment_id'             => array(6, 27, 33, 34, 37, 40, 41, 49, 50, 59, 69, 78),
            'on_hold_status'         => array(85, 91, 98, 99),
            'amount_update'          => array(27, 59),
            'amount_update_on_hold'  => array(37),
            'refund'                 => array(6, 27, 33, 34, 37, 40, 41, 49, 50, 59, 69, 78),
            'trans_book'             => array(6, 34, 37),
        );

    public $payporturl = 'https://payport.novalnet.de/paygate.jsp';

    /**
     * Constructor
     * @return void
     */
    function __construct () {
        $request = $_REQUEST;
        global $messageStack, $order;
        $this->data = NovalnetUtil::getNovalnetTransDetails($request['oID']);
        if(! empty($request ['novalnet_process'])) {
            include(DIR_WS_CLASSES . 'order.php');
            $order = new order((int)$_GET['oID']);
            $method = 'process_' . $request ['novalnet_process'];
            $this->$method($request);
        } else {
            if (empty($this->data['callback_amount'])) {
                $tables_sql = xtc_db_query('select table_name from information_schema.columns where table_schema = "' . DB_DATABASE . '" AND table_name= "novalnet_callback_history"');
                $result = xtc_db_fetch_array($tables_sql);
                if ($result['table_name'] == 'novalnet_callback_history') {
                    $order_totalqry = xtc_db_query("select sum(amount) as callback_amount from novalnet_callback_history where order_no = '".xtc_db_input($request['oID'])."'");
                    $result = xtc_db_fetch_array($order_totalqry);
                    $this->data['callback_amount'] = $result['callback_amount'];
                }
            }
        }
    }

    /**
     * Function : show_manage_transaction()
     * Transaction Confirm block
     */
    function show_manage_transaction () {
        if(in_array($this->data['gateway_status'], $this->supports['on_hold_status'])) {
            include_once (DIR_FS_CATALOG. 'includes/external/novalnet/extension/templates/manage_transaction.php');
        }
    }

    /**
     * Function : show_amount_update()
     * Amount update process block
     */
    function show_amount_update () {
        global $order;
        $refund_amount = !empty($this->data['refund_amount']) ? $this->data['refund_amount'] : 0;
        $amount = $this->data['amount'] - $refund_amount;
        
        if(($this->data['payment_id'] == 37 && $this->data['gateway_status'] == 99) || (in_array($this->data['payment_id'], array(27,59)) && $this->data['gateway_status'] == 100 && $this->data['callback_amount'] <  $amount)) {
           $input_due_date = ($this->data['payment_id'] == 27) ? $this->data['payment_details']['due_date'] : $this->data['payment_details']['cp_due_date'];
           if ($input_due_date != '0000-00-00') {
            $strtotime_input_date = strtotime($input_due_date);
            $this->data['input_day'] = date('d',$strtotime_input_date);
            $this->data['input_month'] = date('m',$strtotime_input_date);
            $this->data['input_year'] = date('Y',$strtotime_input_date);
          }
            include_once (DIR_FS_CATALOG. 'includes/external/novalnet/extension/templates/amount_update.php');
        }
    }

    /**
     * Function : show_refund()
     * Refund amount block
     */
    function show_refund() {
        global $order;
        if ($this->data['amount'] != '0' && $this->data['gateway_status'] == '100') {
            include_once (DIR_FS_CATALOG. 'includes/external/novalnet/extension/templates/refund.php');
        }
    }

    /**
     * Function : show_trans_book()
     * Transaction Booking process block
     */
    function show_trans_book() {
        if ($this->data['amount'] == '0' && in_array($this->data['payment_id'], $this->supports['trans_book']) && $this->data['gateway_status'] == '100' && $this->data['zero_transaction'] == '1') {
            include_once (DIR_FS_CATALOG. 'includes/external/novalnet/extension/templates/trans_book.php');
        }
    }
    /**
     * Function : nn_shop_params()
     *  @return array
     */
    function nn_shop_params () {
        $tariff_details = explode('-', MODULE_PAYMENT_NOVALNET_TARIFF_ID);
        return array(
        'vendor'          => !empty($this->data['payment_details']['vendor']) ? $this->data['payment_details']['vendor'] : MODULE_PAYMENT_NOVALNET_VENDOR_ID,
        'product'         => !empty($this->data['payment_details']['product']) ? $this->data['payment_details']['product'] : MODULE_PAYMENT_NOVALNET_PRODUCT_ID,
        'tariff'          => !empty($this->data['payment_details']['tariff']) ? $this->data['payment_details']['tariff'] : $tariff_details['1'],
        'auth_code'       => !empty($this->data['payment_details']['auth_code']) ? $this->data['payment_details']['auth_code'] : MODULE_PAYMENT_NOVALNET_AUTHCODE,
        );
    }

    /**
     * Function : process_trans_confirm()
     * Perform Transaction Booking process request
     * @param $request
     */
    function process_trans_confirm ($request) {
        global $messageStack, $order;
        $comm_trans_params = self::nn_shop_params($this->data);
        //process

        $novalnet_order_status = array(
                6  => MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS,
                27 => MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS,
                41 => MODULE_PAYMENT_NOVALNET_INVOICE_CALLBACK_ORDER_STATUS, 
                34 => MODULE_PAYMENT_NOVALNET_PP_ORDER_STATUS,
                37 => MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS,
                40 => MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS,
        );

        // Send request to Novalnet server
        $response = NovalnetUtil::doPaymentCurlCall($this->payporturl, array_merge($comm_trans_params ,array(
            'edit_status' => '1',
            'key'         => $this->data['payment_id'],
            'tid'         => $this->data['tid'],
            'status'      => $request['trans_status'],
            'remote_ip'   => NovalnetUtil::getIpAddress('REMOTE_ADDR'))
        ));
        parse_str($response, $data);

        if ($data['status'] == '100') {
            $param['gateway_status'] = $data['tid_status'];

            // Get order status
            $order_status = ($request['trans_status'] == '100') ? NovalnetUtil::checkDefaultOrderStatus($novalnet_order_status[$this->data['payment_id']]) : NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED) ;

            // Update paypal transaction ID
            if (!empty($data['paypal_transaction_id']) && !empty($this->data['payment_details'])) {
                $param['payment_details'] = serialize(array(
                    'paypal_transaction_id' => $data['paypal_transaction_id'],
                    'tid'                   => $this->data['tid'],
                ));
            }

            $message = '';

            $confirm_message = (in_array($this->data['payment_id'], array(27, 41))) ?  sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_WITH_DUEDATE, $this->data['tid'], $data['due_date']) : sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE, date(DATE_FORMAT), date('H:i:s'));

             // Message to be updated
            $message .= (($request['trans_status'] == '100') ?  PHP_EOL .PHP_EOL. $confirm_message : PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, date(DATE_FORMAT), date('H:i:s'))).PHP_EOL;
                $account_info = $this->data['payment_details'];
                if ($request['trans_status'] == '100' && in_array($this->data['payment_id'], array(27, 41))) {
                $payment_type = ($this->data['payment_id'] == 41) ? 'GUARANTEED_INVOICE' : 'INVOICE';
                $message .= NovalnetUtil::formPaymentComments($this->data['tid'], $this->data['test_mode'], $payment_type);

                $param['amount'] = $this->data['amount'];

                // Form Transaction details
                list($transaction_details, $bank_details) = NovalnetUtil::formInvoicePrepaymentComments(array(
                    'test_mode' => $account_info['test_mode'],
                    'invoice_account_holder' => $account_info['account_holder'],
                    'invoice_bankname'      => $account_info['bank_name'],
                    'invoice_bankplace'     => $account_info['bank_city'],
                    'amount'                => $account_info['amount']/100,
                    'currency'              => $account_info['currency'],
                    'tid'                   => $account_info['tid'],
                    'invoice_iban'          => $account_info['bank_iban'],
                    'invoice_bic'           => $account_info['bank_bic'],
                    'tid_status'            => $data['tid_status'],
                    'due_date'              => $data['due_date']));
                $message .= $transaction_details;

                $message .= NovalnetUtil::novalnetReferenceComments($request['oID'], $account_info);
            } 

            // Perform db operations to update the table
            xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$this->data['tid']."'");
            xtc_db_perform(TABLE_ORDERS, array(
                'orders_status' => $order_status,
                'comments' => $message
            ), 'update', 'orders_id="'.$request['oID'].'"');
            if ($request['trans_status'] == '100' && in_array($this->data['payment_id'], array(27, 41))) {
                NovalnetUtil::sendPaymentNotificationMail($request['oID'], $message);
            }
            $invoice_duedate = !empty($data['due_date']) ? date(DATE_FORMAT, strtotime($data['due_date'])) : date(DATE_FORMAT, strtotime(date('Y-m-d')));
            (in_array($this->data['payment_id'], array(27, 41)) && $data['tid_status'] == '100') ? (NovalnetUtil::updateOrderStatus($request['oID'], $order_status, PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode($message), $this->data['tid'], $invoice_duedate))) : (NovalnetUtil::updateOrderStatus($request['oID'], $order_status, PHP_EOL.NovalnetUtil::setUtf8Mode($message).PHP_EOL));

        } else {
            $process_result = $this->getStatusText($data);
        }
        $this->displayMessageText($request, $process_result, $messageStack, 'trans_confirm');
    }

    /**
     * Function : process_refund()
     * Perform Refund amount process request
     * @param $request
     */
    function process_refund ($request) {
        global $messageStack, $order;

        $comm_trans_params = self::nn_shop_params($this->data);

        $refund_params = array(
            'key'            => $this->data['payment_id'],
            'refund_request' => '1',
            'tid'            => $this->data['tid'],
            'refund_ref'     => isset($request['refund_ref']) ? trim($request['refund_ref']) : '',
            'refund_param'   => $request['refund_trans_amount'],
            'remote_ip'      => NovalnetUtil::getIpAddress('REMOTE_ADDR')
        );

        // Check for refund reference
        if ($request['refund_ref'] != '') {
            $refund_params['refund_ref'] = $request['refund_ref'];
        }
        // Send request to Novalnet server
        $response = NovalnetUtil::doPaymentCurlCall($this->payporturl, array_merge($comm_trans_params, $refund_params));
        parse_str($response, $data);

        // Get transaction status
        $param['gateway_status'] = $data['tid_status'];
        if ($data['status'] == '100') {
            // Message to be updated
            $message = PHP_EOL.PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG), $this->data['tid'], xtc_format_price_order(($request['refund_trans_amount']/100), 1, $order->info['currency']));

            // Check for refund TID
            if (!empty($data['tid'])) {
                $message .= sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG), $data['tid'], xtc_format_price_order(($request['refund_trans_amount']/100), 1, $order->info['currency']));
            }

            // Check for PayPal refund TID
            if ($request['payment_id'] == 34 && !empty($data['paypal_refund_tid'])) {
                $message .= MODULE_PAYMENT_NOVALNET_REFUND_TITLE."[ ". $request['refund_trans_amount'] ." ] - PayPal Ref: ". $data['paypal_refund_tid'];
            }

            $message .= PHP_EOL;
            $order_status_value = ($param['gateway_status'] != '100') ? NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED) : $order->info['orders_status'];

            if($this->data['tid']) {
				
				$query = xtc_db_query("select refund_amount from novalnet_transaction_detail where tid = '".xtc_db_input($this->data['tid'])."' ");
				$refund_amount = xtc_db_fetch_array($query);

                $refund_amount = !empty($refund_amount['refund_amount']) ?  $refund_amount['refund_amount'] : 0;
                $amount = $refund_amount + $request['refund_trans_amount'];
                xtc_db_query("UPDATE novalnet_transaction_detail SET gateway_status = '".$data['tid_status']."', refund_amount= '" . $amount . "' where tid='".$this->data['tid']."'");

               // Perform db operations to update the table
                xtc_db_perform('novalnet_transaction_detail', array('gateway_status' => $data['tid_status']), "update", "tid='".$this->data['tid']."'");
                xtc_db_perform(TABLE_ORDERS, array(
                    'orders_status' => $order_status_value
                ), 'update', 'orders_id="'.$request['oID'].'"');

                // Update the order details
                NovalnetUtil::updateOrderStatus($request['oID'], $order_status_value, $message);
            }
        } else {
             $process_result = $this->getStatusText($data);
    }
        $this->displayMessageText($request, $process_result, $messageStack, 'amount_refund');
    }

    /**
     * Function : process_amount_update()
     * Perform amount update process request
     * @param $request
     */
    function process_amount_update ($request) {
        global $messageStack, $order;
        $comm_trans_params = self::nn_shop_params($this->data);
        if (!in_array($this->data['payment_id'], array(27, 37, 59))) {
            header('Location: '.DIR_WS_ADMIN.'orders.php?page=1&oID='.$request['oID'].'&action=edit');
            exit;
        }
        if (!empty($request['new_amount'])) {
            $input_due_date = $this->data['due_date'];
            if ($request['amount_change_year'] != '' && $request['amount_change_month'] != '' && $request['amount_change_day'] != '') {
                $input_due_date = $request['amount_change_year'].'-'.$request['amount_change_month'].'-'.$request['amount_change_day'];
            }

            if (in_array($this->data['payment_type'], array('novalnet_invoice','novalnet_prepayment', 'novalnet_barzahlen')) && !$this->checkmydate($input_due_date)) {

               $process_result = NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_INVALID_DATE);
               $this->displayMessageText($request, $process_result, $messageStack, 'amount_change');
            }
            if ($input_due_date != '0000-00-00' && strtotime($input_due_date) < strtotime(date('Y-m-d')) && in_array($this->data['payment_type'], array('novalnet_invoice','novalnet_prepayment', 'novalnet_barzahlen'))) {
                $process_result = MODULE_PAYMENT_NOVALNET_VALID_DUEDATE_MESSAGE;
            } else {
                $amount_change_request = array_merge($comm_trans_params, array(
                'key'               => $this->data['payment_id'],
                'edit_status'       => '1',
                'tid'               => $this->data['tid'],
                'status'            => '100',
                'update_inv_amount' => '1',
                'amount'            => $request['new_amount'],
                'remote_ip'         => NovalnetUtil::getIpAddress('REMOTE_ADDR')
                ));
                if (in_array($this->data['payment_id'], array(27, 59)) && $input_due_date != '0000-00-00' && $input_due_date != '') {
                    $amount_change_request['due_date'] = date('Y-m-d', strtotime($input_due_date));
                }

                // Send request to Novalnet server
                $response = NovalnetUtil::doPaymentCurlCall($this->payporturl, $amount_change_request);
                parse_str($response, $data);
                if ($data['status'] == '100') {
                    // Message to be updated
                    $message = ($this->data['payment_id'] == 37) ? PHP_EOL.PHP_EOL. sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SEPA_TRANS_UPDATED_MESSAGE), xtc_format_price_order(($request['new_amount']/100), 1, $order->info['currency']), date(DATE_FORMAT, strtotime(date('Y-m-d'))), date('H:i:s')).PHP_EOL : (($this->data['payment_id'] == 59) ? PHP_EOL.PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_CASH_PAYMENT_TRANS_UPDATED_MESSAGE), xtc_format_price_order(($request['new_amount']/100), 1, $order->info['currency']), date(DATE_FORMAT, strtotime($input_due_date))).PHP_EOL : PHP_EOL.PHP_EOL.sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANS_UPDATED_MESSAGE), xtc_format_price_order(($request['new_amount']/100), 1, $order->info['currency']), date(DATE_FORMAT, strtotime($input_due_date))).PHP_EOL);
                    $param = array(
                        'gateway_status' => $data['tid_status'],
                        'amount'         => $request['new_amount'],
                    );

                    if ($this->data['payment_id'] == 37) {
                        $param['callback_amount'] = $request['new_amount'];
                    }

                    if (in_array($this->data['payment_id'], array(27, 59))) {
                       $param['total_amount'] = $request['new_amount'];
                    }

                    // Perform db operations to update the table
                    xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$this->data['tid']."'");

                    if (in_array($this->data['payment_id'], array(27, 59))) {
                        $message .= NovalnetUtil::formPaymentComments($this->data['tid'], $this->data['test_mode']);
                    }

                    if ($this->data['payment_id'] == 59) {
                        list($barzahlen_comments, $nearest_store) = NovalnetUtil::formBarzahlenComments($this->data['payment_details'], $input_due_date);
                        $message .= $barzahlen_comments;
                        $param ['payment_details'] = serialize($nearest_store);

                         xtc_db_perform(TABLE_ORDERS, array(
                            'comments' => $message
                         ), 'update', 'orders_id="'.$request['order_id'].'"');
                    }

                    // Form bank details comments
                    if ($this->data['payment_id'] == 27) {
                        $account_info = $this->data['payment_details'];

                        $param['amount'] = $request['new_amount'];
                        $param['payment_details'] = serialize(array_merge($this->data['payment_details'], array('due_date' => $amount_change_request['due_date'])));

                        // Form Transaction details
                        list($transaction_details, $bank_details) = NovalnetUtil::formInvoicePrepaymentComments(array(
                            'invoice_account_holder' => $account_info['account_holder'],
                            'invoice_bankname'      => $account_info['bank_name'],
                            'invoice_bankplace'     => $account_info['bank_city'],
                            'amount'                => sprintf("%.2f", ($amount_change_request['amount']/100)),
                            'currency'              => $account_info['currency'],
                            'tid'                   => $account_info['tid'],
                            'invoice_iban'          => $account_info['bank_iban'],
                            'invoice_bic'           => $account_info['bank_bic'],
                            'tid_status'            => $data['tid_status'],
                            'due_date'              => $amount_change_request['due_date']));
                        $message .= $transaction_details;
                        $message .= NovalnetUtil::novalnetReferenceComments($request['oID'], $account_info).PHP_EOL;
                        xtc_db_perform(TABLE_ORDERS, array(
                            'comments' => $message
                        ), 'update', 'orders_id="'.$request['oID'].'"');
                    }

                    // Perform db operations to update the table
                    xtc_db_perform('novalnet_transaction_detail', $param, "update", "tid='".$this->data['tid']."'");

                    // Update the order details
                    NovalnetUtil::updateOrderStatus($request['oID'], $order->info['orders_status'], $message);
                } else {
                    $process_result = $this->getStatusText($data);
                }
                $this->displayMessageText($request, $process_result, $messageStack, 'amount_change');
            }
            $this->displayMessageText($request, $process_result, $messageStack, 'amount_change');
        }
    }

    /**
     * Function : process_trans_book()
     * Perform Transaction Booking process request
     * @param $request
     */
    function process_trans_book ($request) {
        global $messageStack, $order;
        $comm_trans_params = self::nn_shop_params($this->data);
        $book_params  = unserialize($this->data['zerotrxndetails']);
        $book_params['amount']      = $request['book_amount'];
        $book_params['order_no']    = $request['oID'];
        $book_params['payment_ref'] = $this->data['tid'];
        $book_params['remote_ip']   = NovalnetUtil::getIpAddress('REMOTE_ADDR');
        unset($book_params['create_payment_ref']);
        // Send request to Novalnet server
        $response = NovalnetUtil::doPaymentCurlCall($this->payporturl, $book_params);
        parse_str($response, $data);
        if ($data['status'] == '100') {
            $test_mode_notification = (!empty($this->data['test_mode'])) ? PHP_EOL . MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE . PHP_EOL : PHP_EOL;

            // Message to be updated
            $message = PHP_EOL.PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS . PHP_EOL . MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $data['tid'] . $test_mode_notification;
            $message .= sprintf(NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE), xtc_format_price_order(($request['book_amount']/100), 1, $order->info['currency']), $data['tid']);
            $message .= PHP_EOL;
            $param = array(
                'tid'             => $data['tid'],
                'amount'          => $book_params['amount'],
                'callback_amount' => $book_params['amount'],
                'gateway_status'  => $data['tid_status'],
            );
            xtc_db_perform('novalnet_transaction_detail', $param, "update", "order_no='". xtc_db_input($request['oID']) ."'");

            xtc_db_perform(TABLE_ORDERS, array('comments' => $message), "update", "orders_id='".$request['oID']."'");

            // Update the order details
            NovalnetUtil::updateOrderStatus($request['oID'], $order->info['orders_status'], $message);
        } else {
            $process_result = $this->getStatusText($data);
        }
        $this->displayMessageText($request, $process_result, $messageStack, 'book_amount');
    }

    /**
     * Function : displayMessageText()
     * @param $request
     * @param $content
     * @param $messageStack
     * @param $type
     */
    function displayMessageText ($request, $content, $messageStack, $type) {
        if ($content == '') {
            $messageStack->add_session(MODULE_PAYMENT_NOVALNET_ORDER_UPDATE, 'success');
            header('Location:'.xtc_href_link(FILENAME_ORDERS, $type.'=1&oID='.$request['oID'].'&action=edit&message='. MODULE_PAYMENT_NOVALNET_ORDER_UPDATE));
            exit;
        }
        $messageStack->add_session($content);
        header('Location: '.xtc_href_link(FILENAME_ORDERS, $type.'=1&oID='.$request['oID'].'&action=edit&message='. $content));
        exit;
    }

    /**
     * Function : checkmydate()
     * @param $date
     * @return date
     */
    function checkmydate ($date) {
        $tempDate = explode('-', $date);
        //checkdate(month, day, year)
        return (checkdate($tempDate['1'], $tempDate['2'], $tempDate['0']));
    }

    /**
     * Function : getStatusText()
     * @param $data
     * @return void
     */
    public function getStatusText($data)
    {
        return (!empty($data['status_message']) ? NovalnetUtil::setUtf8Mode($data['status_message']) : !empty($data['status_desc']) ? NovalnetUtil::setUtf8Mode($data['status_desc']) : (!empty($data['status_text']) ? NovalnetUtil::setUtf8Mode($data['status_text']) : MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR)).'( ' . $data['status'].' )';
    }

}

$NovalnetExtension = new NovalnetExtension();
$NovalnetExtension->show_manage_transaction();
$NovalnetExtension->show_refund();
$NovalnetExtension->show_trans_book();
$NovalnetExtension->show_amount_update();
?>
<script type='text/javascript'>

    // To check whether number or not
    function novalnetAllowNumeric(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        var keycode = ('which' in evt) ? evt.which : evt.keyCode;
        var reg = /^(?:[0-9]+$)/;
        return (reg.test(String.fromCharCode(keycode)) || keycode == 0 || keycode == 8 || (evt.ctrlKey == true && keycode == 114)) ? true : false;
    }

    // To validate void capture option
    function validate_status_change() {
        if (document.getElementById('trans_status').value == '') {
            alert('<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT); ?>');
            return false;
        } else {
             display_text = document.getElementById('trans_status').value == '100' ? '<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SELECT_CONFIRM_TEXT); ?>' : '<?php echo MODULE_PAYMENT_NOVALNET_SELECT_CANCEL_TEXT; ?>';
            if (!confirm(display_text)) {
                return false;
            }
            document.getElementsByName('novalnet_status_change')[0].submit();
            document.getElementById('nn_manage_transaction').disabled = true;
        }
        return true;
    }

    // To validate the refund amount field
    function validate_refund_amount() {
         if (document.getElementById('refund_trans_amount').value == '') {
            var amount = document.getElementById('refund_trans_amount').value;
            if (amount.trim() == '' || amount == 0) {
                alert('<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE); ?>');
                return false;
            }
        }

        if (document.getElementById('refund_ref') != '') {
        display_text = '<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_REFUND_AMOUNT_TEXT); ?>';
        if (!confirm(display_text)) {
            return false;
        }

        document.getElementsByName('novalnet_trans_refund')[0].submit();
        document.getElementById('novalnet_refund_process').disabled = true;
            var refund_ref = document.getElementById('refund_ref').value;
            refund_ref = refund_ref.trim();
            var re = /[\/\\#,+!^()$~%.":*?<>{}]/g;
            if (re.test(refund_ref)) {
                evt.preventDefault();
                alert('<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR); ?>');
                return false;
            }
        }
    }

    // To validate the amount update process
    function validate_amount_change(payment_id) {
        var changeamount = (document.getElementById('new_amount').value).trim();
        if (changeamount == '' || changeamount <= 0 || isNaN(changeamount)) {
            alert('<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE); ?>');
            return false;
        }
        display_text =  (payment_id == 59) ? '<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_ORDER_AMT_SLIP_EXPIRY_DATE_UPDATE_TEXT); ?>' : (document.getElementById('invoice_payment').value == 1 ? '<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_ORDER_AMT_DATE_UPDATE_TEXT); ?>' : '<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_ORDER_AMT_UPDATE_TEXT); ?>');
        if (!confirm(display_text)) {
            return false;
        }
        document.getElementsByName('novalnet_amount_change')[0].submit();
        document.getElementById('nn_amount_update').disabled = true;
        return true;
    }

    // To validate the booking amount process
    function validate_book_amount(evt) {
        var bookamount = document.getElementById('book_amount').value;
        if (bookamount.trim() == '' || bookamount == 0) {
            evt.preventDefault();
            alert('<?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE); ?>');
            return false;
        }
        display_text = '<?php echo MODULE_PAYMENT_NOVALNET_BOOK_AMOUNT_TEXT; ?>';
        if (!confirm(display_text)) {
            return false;
        }
        document.getElementsByName('novalnet_book_amount')[0].submit();
        document.getElementById('nn_trans_book_confirm').disabled = true;
    }

    //Function : redirect_orders_list_page()
    function redirect_orders_list_page() {

        window.location="<?php echo xtc_href_link('orders.php'); ?>";
    }

</script>

