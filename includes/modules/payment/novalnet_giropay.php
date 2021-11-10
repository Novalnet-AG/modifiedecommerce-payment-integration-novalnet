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
 * Script : novalnet_giropay.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_giropay extends NovalnetPayment
{
    var $code = 'novalnet_giropay',
        $title,
        $description,
        $enabled,
        $key = 69,
        $sort_order = 0,
        $tmpOrders = true,
        $payment_type = 'GIROPAY';

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
            $this->form_action_url = 'https://payport.novalnet.de/giropay';
            $this->tmpStatus       = MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;

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
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_GIROPAY_VISIBILITY_BYAMOUNT)) {
            return false;
        }

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        // Information to the end user
        $notification .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_GIROPAY_ENDCUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

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
            // Novalnet transaction status got success
                 //Hash Validation failed
                if (NovalnetUtil::validateHashResponse($post)) {
                    NovalnetUtil::transactionFailure($post, $this->code, NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR));
                }

                // Decoding Novalnet server response
                $payment_response = NovalnetUtil::generateDecodedata($post);

                // Get order id
                $order_id = NovalnetUtil::getOrderId($payment_response, $this->code);

                $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE == 'True');

                $_SESSION['novalnet'][$this->code] = array_merge($this->redirectInitialParams($payment_response), $this->paymentInitialParams($payment_response), array(
                  'test_mode'             => $test_mode,
                  'total_amount'          => $_SESSION['novalnet'][$this->code]['order_amount']
                ));
			if (isset($post['status']) && $post['status'] == '100') {
                // Update payment process based on the response.
                NovalnetUtil::doPostProcess(array(
                    'payment'      => $this->code,
                    'order_no'     => $order_id,
                ));

                NovalnetUtil::updateComments(array(
                    'order_status' => NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_GIROPAY_ORDER_STATUS),
                    'order_no'     => $order_id,
                    'comments'     => PHP_EOL.NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode)
                ));
            } else {
				// Insert datas in Novalnet transaction detail table
				NovalnetUtil::logInitialTransaction(array(
                    'payment'      => $this->code,
                    'order_no'     => $order_id,
                ));
				
                // Transaction failure process.
                NovalnetUtil::transactionFailure($post, $this->code);
            }
        } else {
            $order->info['order_status'] = MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
        }
    }

    /**
     * Core Function : payment_action()
     *
     */
    public function payment_action()
    {
        global $order;
        $datas = array_merge((array)$order, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

        // Get common request parameters
        $redirect_data  = array_merge(NovalnetUtil::getCommonRequestParams($datas), $this->paymentKeyModeType($this->code));
        unset($redirect_data['tariff_type']);

        // Appending affiliate parameters
        NovalnetUtil::getAffDetails($redirect_data);

        // Form redirection parameters
        NovalnetUtil::formRedirectionParams($redirect_data);

        // Form encoded parameters
        NovalnetUtil::generateEncodeValue($redirect_data);

        $_SESSION['novalnet'][$this->code]['input_params'] = $redirect_data;
        xtc_redirect(xtc_href_link('checkout_novalnet_redirect.php', '', 'SSL', true, false));
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
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
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_GIROPAY_ALLOWED'");
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
			('MODULE_PAYMENT_NOVALNET_GIROPAY_ALLOWED', '', '6', '0', '', '', now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_GIROPAY_STATUS\',".MODULE_PAYMENT_NOVALNET_GIROPAY_STATUS.",' , '',now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE.",' , '',now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_VISIBILITY_BYAMOUNT', '', '6', '3','',  '', now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_ENDCUSTOMER_INFO', '', '6', '4','',  '', now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_SORT_ORDER', '0', '6', '5', '',  '', now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_ORDER_STATUS', '2',  '6', '6', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
			('MODULE_PAYMENT_NOVALNET_GIROPAY_PAYMENT_ZONE', '0', '6', '7', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now())
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

    /** Core Function : keys()
     *
     */
    public function keys()
    {
        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array(
            'MODULE_PAYMENT_NOVALNET_GIROPAY_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_STATUS',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_GIROPAY_PAYMENT_ZONE'
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
        if (MODULE_PAYMENT_NOVALNET_GIROPAY_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_GIROPAY_TITLE')) {
            // Validate payment visibility amount
            if (MODULE_PAYMENT_NOVALNET_GIROPAY_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_GIROPAY_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_GIROPAY_TITLE);
                }

                return false;
            }
        }

        return true;
    }
}
