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
 * Script : novalnet_cc.php
 *
 */
include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetPayment.php');
class novalnet_cc extends NovalnetPayment
{
    var $code = 'novalnet_cc',
        $title,
        $description,
        $enabled,
        $key = 6,
        $payment_type = 'CREDITCARD',
        $sort_order = 0,
        $is_redirect;

    /**
     * Constructor
     *
     */
    function __construct()
    {
        global $order, $code;
        $post = $_REQUEST;
        $this->setPaymentDetails();        
		if($this->enabled) {
			$lang = ((isset($_SESSION['language_code'])) ? strtolower($_SESSION['language_code']) : 'de');
			if (MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX == 'True') {
				$this->public_title .= ' <a href="'.($lang == 'en' ? 'https://www.novalnet.com/credit-card' : 'https://www.novalnet.de/zahlungsart-kreditkarte').'" title="Credit Card" target="_blank">'. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_amex.png').'</a>';
			}
			if (MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO == 'True') {
				$this->public_title .= ' <a href="'.($lang == 'en' ? 'https://www.novalnet.com/credit-card' : 'https://www.novalnet.de/zahlungsart-kreditkarte').'" title="Credit Card" target="_blank">'. xtc_image(DIR_WS_ICONS.'novalnet/novalnet_cc_maestro.png').'</a>';
			}
			
			$this->is_redirect = (MODULE_PAYMENT_NOVALNET_CC_3D_SECURE == 'True' || MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE == 'True');
            $this->customer_name =  ((!empty($order->customer['firstname']) ? $order->customer['firstname'] : '' ).' '.(!empty($order->customer['lastname']) ? $order->customer['lastname'] : ''));
			
			if ($this->is_redirect && (empty($post['novalnet_cc_change_account']))) {
				$this->tmpOrders =  true;
				$this->form_action_url = 'https://payport.novalnet.de/pci_payport';
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
        $post = $_REQUEST;
        // Validate the based on the configuration
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() || !NovalnetUtil::hidePaymentVisibility(NovalnetUtil::getPaymentAmount((array)$order), MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT)) {
            return false;
        }

        // Assign payment in session for last successful order
        NovalnetUtil::getLastSuccessPayment($this->code);

        // Test mode notification to the end
        if (MODULE_PAYMENT_NOVALNET_CC_TEST_MODE == 'True') {
            $notification = MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG;
        }

        // Information to the end user
        $notification = !empty($notification) ? $notification :'';

        $desc = (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ZEROAMOUNT') ? MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS_ZERO_AMOUNT_BOOKING : '';
		$notification_info .= trim(strip_tags(MODULE_PAYMENT_NOVALNET_CC_ENDCUSTOMER_INFO));
        $selection = $this->checkoutSelectionDetails();
        
        $selection['description'] .= ($this->is_redirect ? MODULE_PAYMENT_NOVALNET_CC_REDIRECTION_TEXT_DESCRIPTION .MODULE_PAYMENT_NOVALNET_REDIRECT_NOTICE_MSG : $this->description).$notification. $desc.'<br>'. $notification_info;

        // Get masked card details
        $payment_details = NovalnetUtil::getPaymentRefDetails($_SESSION['customer_id'], $this->code);
        

        if (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ONECLICK' && ! $this->is_redirect && !empty($payment_details)) {
                $selection['description'] .=  '<input type="hidden" id="nn_lang_cc_new_account" value="' . MODULE_PAYMENT_NOVALNET_CC_NEW_ACCOUNT . '"/><input type="hidden" id="nn_lang_cc_given_account" value="' . MODULE_PAYMENT_NOVALNET_CC_GIVEN_ACCOUNT . '"/>';
            if ($payment_details) {
                $selection['fields'][] = array(
                'title' => '',
                'field' => '<div id="saved_nn_cc_new">'.NovalnetUtil::showAlternateShoppingType('saved_novalnet_cc_newcard', MODULE_PAYMENT_NOVALNET_CC_NEW_ACCOUNT).'
					<div id="nn_cc_ref_details">
					<table class="paymentmoduledata">'.
						NovalnetUtil::showMaskedDetails(array(
							array(
								'label' => MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_TYPE,
								'value' => $payment_details['cc_card_type'],
							),
							array(
								'label' => MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_HOLDER,
								'value' => NovalnetUtil::setUtf8Mode($payment_details['cc_holder']),
							),
							array(
								'label' => MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_NO,
								'value' => NovalnetUtil::setUtf8Mode($payment_details['cc_no']),
							),
							array(
								'label' => MODULE_PAYMENT_NOVALNET_CC_FORM_CARD_VALID_DATE,
								'value' => $payment_details['cc_exp_month'] . ' / ' . $payment_details['cc_exp_year'],
							),
						)) .'
					</table>
					<input type="hidden" id="nn_payment_ref_tid" name="nn_payment_ref_tid" value="'.$payment_details['tid'].'"/>
					<input type="hidden" name="novalnet_cc_savecard" id="novalnet_cc_savecard" value="1"/>
					</div></div>');
			}

			$selection['fields'][] = array(
					'title' => '',
					'field' => '<div id="novalnet_cc_new_acc"><u><b><a id="novalnet_cc_givencard" style="color:blue;display:none">'.MODULE_PAYMENT_NOVALNET_CC_GIVEN_ACCOUNT.'</a></b></u></div>'
			   );

			// Displaying iframe form type
			$selection['fields'][] = array(
				'title' => '',
				'field' => '<div id="nn_cc_acc" style="display:none">'.$this->renderIframe() .'<input type="hidden" name="novalnet_cc_savedcard" id="novalnet_cc_savedcard" value="saved"/> </div>'
			);
        } else {
            // Displaying iframe form type
            $selection['fields'][] = array(
                'title' => '',
                'field' => $this->renderIframe().'<input type="hidden" name="novalnet_cc_newcard" id="novalnet_cc_newcard" value="new"/>'
            );
        }
        
        if (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ONECLICK' && MODULE_PAYMENT_NOVALNET_CC_3D_SECURE != 'True') {
			$selection['fields'][] = array(
                      'title' => '',
                      'field' => '<div id="nn_cc_savecard">'.xtc_draw_checkbox_field('nn_cc_savecard', 1, false, '').MODULE_PAYMENT_NOVALNET_CC_SAVECARD_DETAILS
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
        if ($post['novalnet_cc_newcard'] == 'new' && (empty($post['nn_cc_pan_hash']) || empty($post['nn_cc_uniqueid']))) {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='. NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS), 'SSL', true, false));
        }

        if (isset($post['novalnet_cc_savecard']) && $post['novalnet_cc_savecard'] == '1') {
            $_SESSION['novalnet'][$this->code]['nn_payment_ref_tid'] = $post['nn_payment_ref_tid'];
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
        unset($_SESSION['nn_cc_savecard']);
        if (isset($post['nn_cc_savecard']) || $post['nn_cc_savecard']) {
            $_SESSION['nn_cc_savecard'] = $post['nn_cc_savecard'];
        }
        unset($_SESSION['novalnet_cc_savedcard']);
        if (isset($post['novalnet_cc_savedcard']) || $post['novalnet_cc_savedcard']) {
            $_SESSION['novalnet_cc_savedcard'] = $post['novalnet_cc_savedcard'];
        }
        if (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $novalnet_order_details = isset($_SESSION['novalnet'][$this->code]) ? $_SESSION['novalnet'][$this->code] : array();
            $_SESSION['novalnet'][$this->code] = array_merge($novalnet_order_details, $post, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));
        }

        return false;
    }

    /**
     * Core Function : before_process()
     *
     */
    public function before_process()
    {
        global $order;

        $post = $_REQUEST;
        $secure_false = (MODULE_PAYMENT_NOVALNET_CC_3D_SECURE == 'False' && MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE == 'False');
        if (isset($post['tid'])) {
            if (isset($post['status']) && $post['status'] == '100') {
                $input_params = $_SESSION['novalnet'][$this->code]['input_params'];

                // Hash Validation failed
                if (NovalnetUtil::validateHashResponse($post)) {
                    NovalnetUtil::transactionFailure($post, $this->code, NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR));
                }

                // Decoding Novalnet server response
                $payment_response = array_merge($post, NovalnetUtil::generateDecodedata($input_params));
            }
        } else {
            if ($secure_false) {
                $input_params = $this->formParameters();
                //Send all parameters to Novalnet Server
                $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $input_params);
                parse_str($response, $payment_response);
            } else {
                $order->info['order_status'] = MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
            }
        }
        if (isset($payment_response['tid'])) {
            // Novalnet transaction status got success
            if (isset($payment_response['status']) && $payment_response['status'] == '100') {
                // Set order status
                $this->creditCardSessionDetails($payment_response, $input_params);

                $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_CC_TEST_MODE == 'True');

                // Get order id
                $order_id = NovalnetUtil::getOrderId($payment_response, $this->code);

                $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

                // Set order status
                $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus(($payment_response['tid_status'] == '100') ? MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS : MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE);

                if (!empty($order_id)) {
                    // Update payment process based on the response.
                    NovalnetUtil::doPostProcess(array(
                        'payment'      => $this->code,
                        'order_no'     => $order_id,
                    ));

                    NovalnetUtil::updateComments(array(
                        'order_status' => $order->info['order_status'],
                        'order_no'     => $order_id,
                        'comments'     => $transaction_comments
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
			$input_params = $this->formParameters();

			$secure_true_false = (MODULE_PAYMENT_NOVALNET_CC_3D_SECURE == 'True' && MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE == 'False');
			$secure_true_true = (MODULE_PAYMENT_NOVALNET_CC_3D_SECURE == 'True' && MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE == 'True');
			
			if ($secure_true_false || $secure_true_true) {
				$input_params['cc_3d'] = 1;
			}

			// Form redirection parameters
			NovalnetUtil::formRedirectionParams($input_params);
			unset($input_params['user_variable_0']);
			if (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ONECLICK' && $input_params['cc_3d'] != 1) {
				  unset($input_params['create_payment_ref']);
			}

			// Form encoded parameters
			NovalnetUtil::generateEncodeValue($input_params);

			$_SESSION['novalnet'][$this->code]['order_no'] = $input_params['order_no'];

			$_SESSION['novalnet'][$this->code]['input_params'] = $input_params;
			xtc_redirect(xtc_href_link('checkout_novalnet_redirect.php', '', 'SSL', true, false));
    }

    /**
     * Core Function : after_process()
     *
     */
    public function after_process()
    {
        global $insert_id;

        if (empty($_SESSION['novalnet'][$this->code]['order_no'])) {
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
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_CC_ALLOWED'");
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
            ('MODULE_PAYMENT_NOVALNET_CC_ALLOWED', '', '6', '0', '', '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_STATUS','False', '6', '1', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_STATUS\',".MODULE_PAYMENT_NOVALNET_CC_STATUS.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE','False', '6', '2', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_CC_TEST_MODE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE','capture', '6', '3', 'xtc_mod_select_option(array(\'capture\' => ".MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_CAPTURE.",\'authorize\' => ".MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE_AUTH."),\'MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE\',".MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT', '', '6', '4', '', '', now()),           
            ('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE','False', '6', '5', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_3D_SECURE\',".MODULE_PAYMENT_NOVALNET_CC_3D_SECURE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE','False', '6', '6', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE\',".MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE','False', '6', '7', 'xtc_mod_select_option(array(\'False\' => MODULE_PAYMENT_NOVALNET_OPTION_NONE,\'ONECLICK\' => MODULE_PAYMENT_NOVALNET_CC_ONE_CLICK,\'ZEROAMOUNT\' => MODULE_PAYMENT_NOVALNET_CC_ZERO_AMOUNT,),\'MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE\',".MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE.",' ,'',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT', '', '6', '8','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX','False', '6', '9', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX\',".MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO','False', '6', '10', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO\',".MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO.",' , '',now()),
            ('MODULE_PAYMENT_NOVALNET_CC_ENDCUSTOMER_INFO', '', '6', '11','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER', '0', '6', '12', '',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS', '1',  '6', '13', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_PAYMENT_ZONE', '0', '6', '14', 'xtc_cfg_pull_down_zone_classes(', 'xtc_get_zone_class_title',now()),            
            ('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL', '', '6', '15','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT', '', '6', '16','',  '', now()),
            ('MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT', 'body{color: #555;font-family: Verdana,Arial,sans-serif;font-size:12px;line-height: 1.5;}.label-group{width:152px !important;float:unset !important;}.row{float:unset !important;}', '6', '17','',  '', now())
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

        // Validate admin configuration
        $this->validateAdminConfiguration(true);

        return array (
            'MODULE_PAYMENT_NOVALNET_CC_ALLOWED',
            'MODULE_PAYMENT_NOVALNET_CC_STATUS',
            'MODULE_PAYMENT_NOVALNET_CC_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE',
            'MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT',            
            'MODULE_PAYMENT_NOVALNET_CC_3D_SECURE',
            'MODULE_PAYMENT_NOVALNET_CC_3D_SECURE_FRAUDMODULE',
            'MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE',
            'MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT',
            'MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_AMEX',
            'MODULE_PAYMENT_NOVALNET_CC_PAYMENT_LOGO_CONFIGURATION_MAESTRO',
            'MODULE_PAYMENT_NOVALNET_CC_ENDCUSTOMER_INFO',
            'MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER',
            'MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS',
            'MODULE_PAYMENT_NOVALNET_CC_PAYMENT_ZONE',            
            'MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL',
            'MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT',
            'MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT',
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
        if (MODULE_PAYMENT_NOVALNET_CC_STATUS == 'True' && defined('MODULE_PAYMENT_NOVALNET_CC_BLOCK_TITLE')) {
            // Validate payment visibility amount
            if (MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT != '' && !preg_match('/^\d+$/', MODULE_PAYMENT_NOVALNET_CC_VISIBILITY_BYAMOUNT)) {
                if ($admin) {
                    echo NovalnetUtil::novalnetBackEndShowError(MODULE_PAYMENT_NOVALNET_CC_BLOCK_TITLE);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Display Iframe form
     * @return void
     */
    public function renderIframe()
    {
        // Get language
        $lang = ((isset($_SESSION['language_code'])) ? strtolower($_SESSION['language_code']) : 'de');
		
        $vendor = MODULE_PAYMENT_NOVALNET_VENDOR_ID;
        $product = MODULE_PAYMENT_NOVALNET_PRODUCT_ID;

        $server_ip = NovalnetUtil::getIpAddress('SERVER_ADDR');

        // Form signature
        $encodedkey = base64_encode("vendor=$vendor&product=$product&server_ip=$server_ip") . '&ln=' . $lang;
 
        // Iframe URL path
        $nniframe_source = 'https://secure.novalnet.de/cc?api=' . $encodedkey;

        // include Script file
        $script_name = DIR_WS_CATALOG.'includes/external/novalnet/js/novalnet_cc.js';
        
        // Assign Iframe style configuration fields in hidden
        $cc_hidden_field = '<input type="hidden" value="'.MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_LABEL.'" id="nn_label">
        <input type="hidden" value="'.MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_CSSTEXT.'" id="nn_css_text">
        <input type="hidden" value="'.MODULE_PAYMENT_NOVALNET_CC_IFRAME_STANDARD_CONFIGURATION_INPUT.'" id="nn_input">
        
        <input type="hidden" value="" id="nn_cc_pls_wait">
        <input type="hidden" value="" id="nn_cvc_hint">
        <input type="hidden" value="" id="nn_iframe_error">
        <input type="hidden" value="" id="nn_holder_label">
        <input type="hidden" value="" id="nn_holder_input">
        <input type="hidden" value="" id="nn_number_label">
        <input type="hidden" value="" id="nn_number_input">
        <input type="hidden" value="" id="nn_expiry_label">
        <input type="hidden" value="" id="nn_expiry_input">
        <input type="hidden" value="" id="nn_cvc_label">
        <input type="hidden" value="" id="nn_cvc_input">';
        
        return '<iframe id="nnIframe" width="100%;" src="'.$nniframe_source.'" onload="getFormValue()" frameBorder="0" scrolling="none"></iframe><input type="hidden" id="nn_root_cc_catalog" value="'.DIR_WS_CATALOG.'"/><input type="hidden" id="nn_cc_pan_hash" name="nn_cc_pan_hash" value="" /> <input type="hidden" id="nn_cc_uniqueid" name="nn_cc_uniqueid" value="" />'.$cc_hidden_field.'<script src="'. $script_name.'" type="text/javascript"></script>';
    }

    /**
     * Update Novalnet transaction comments in Order table
     * @param $payment_response
     * @param $input_params
     *
     * @return void
     */
    public function creditCardSessionDetails($payment_response, $input_params)
    {
        $_SESSION['novalnet'][$this->code] = array_merge($_SESSION['novalnet'][$this->code], $this->paymentInitialParams($input_params), array(
          'tid'                   => $payment_response['tid'],
          'amount'                => str_replace('.', '', $payment_response['amount']),
          'total_amount'          => $_SESSION['novalnet'][$this->code]['order_amount'],
          'currency'              => $payment_response['currency'],
          'gateway_response'      => $payment_response,
          'test_mode'             => (int)!empty($payment_response['test_mode']),
          'customer_id'           => $payment_response['customer_no'],
          'reference_transaction' => (int)!empty($input_params['payment_ref']),
          'gateway_status'        => $payment_response['tid_status'],
          'zerotrxnreference'     => $_SESSION['novalnet'][$this->code]['zerotrxnreference'],
          'zerotrxndetails'       => $_SESSION['novalnet'][$this->code]['zerotrxndetails'],
          'zero_transaction'      => $_SESSION['novalnet'][$this->code]['zero_transaction'],
          'order_no'              => !empty($_SESSION['novalnet'][$this->code]['order_no']) ? $_SESSION['novalnet'][$this->code]['order_no'] : '',
        ));

        if (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ONECLICK' && $_SESSION['novalnet'][$this->code]['nn_cc_savecard'] == 1 && MODULE_PAYMENT_NOVALNET_CC_3D_SECURE != 'True') {
            $_SESSION['novalnet'][$this->code]['payment_details'] =  serialize(array(
              'cc_holder'    => $payment_response['cc_holder'],
              'cc_no'        => $payment_response['cc_no'],
              'cc_exp_year'  => $payment_response['cc_exp_year'],
              'cc_exp_month' => $payment_response['cc_exp_month'],
              'tid_status'   => $payment_response['tid_status'],
              'cc_card_type' => $payment_response['cc_card_type'],
              'amount'       => $payment_response['amount'],
              'currency'     => $payment_response['currency'],
              'tid'          => $payment_response['tid']
            ));
            $_SESSION['novalnet'][$this->code]['one_click_shopping'] = 1;
        }
    }


    /**
     * Build the request parameters
     *
     * @return array
     */
    public function formParameters()
    {
        global $order;
        $post = $_REQUEST;
        $params = array_merge((array)$order, $post, array('order_amount' => $_SESSION['novalnet'][$this->code]['order_amount']));

        // Get common request parameters
        $input_params = array_merge(NovalnetUtil::getCommonRequestParams($params), $this->paymentKeyModeType($this->code));

        // Appending affiliate parameters
        NovalnetUtil::getAffDetails($input_params);

        $input_params['nn_it'] = 'iframe';

        // Assigning on hold parameter
        if (MODULE_PAYMENT_NOVALNET_CC_CAPTURE_AUTHORIZE == 'authorize' && $_SESSION['novalnet'][$this->code]['order_amount'] >= MODULE_PAYMENT_NOVALNET_CC_MANUAL_CHECK_LIMIT) {
             $input_params['on_hold']  = '1';
        }

        // Appending zero amount details
        NovalnetUtil::zeroAmount($input_params, $this->code);

        if (MODULE_PAYMENT_NOVALNET_CC_SHOP_TYPE == 'ONECLICK' && $_SESSION['nn_cc_savecard'] == 1 && ($_SESSION['novalnet_cc_savedcard'] == 'saved'  || $_SESSION['novalnet'][$this->code]['novalnet_cc_newcard'] == 'new')) {
            $input_params['create_payment_ref'] = '1';
        }

        // Appending masking parameters
        if (!empty($_SESSION['novalnet'][$this->code]['nn_payment_ref_tid']) && $_SESSION['novalnet'][$this->code]['novalnet_cc_savecard'] == '1') {
            //checkpay
            $input_params['payment_ref'] = $_SESSION['novalnet'][$this->code]['nn_payment_ref_tid'];
            unset($_SESSION['novalnet'][$this->code]['nn_payment_ref_tid'], $input_params['nn_it']);
        } else {
            // Update hash values
            $input_params['pan_hash']  = $_SESSION['novalnet'][$this->code]['nn_cc_pan_hash'];
            $input_params['unique_id'] = $_SESSION['novalnet'][$this->code]['nn_cc_uniqueid'];
        }

        // Store values in session
        $_SESSION['novalnet'][$this->code]['input_params'] = $input_params;

        unset($input_params['tariff_type']);

        return $input_params;
    }
}