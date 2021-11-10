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
 * Script : checkout_novalnet_redirect.php
 *
 */

include('includes/application_top.php');

$smarty = new Smarty;
require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');

// if the customer is not logged on, redirect them to the login page
if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() <= 0) {
    xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
    if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

// if no payment method has been selected, redirect to the payment page
if (empty($_SESSION['payment']) || (isset($_SESSION['payment']) && strpos($_SESSION['payment'], 'novalnet'))) {
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

$novalnet_payment = $_SESSION['payment'];

// if no parameters not formed, redirect to the payment page
if (empty($_SESSION['novalnet'][$novalnet_payment]['input_params'])) {
    xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

include_once(DIR_WS_INCLUDES . 'header.php');
$smarty->assign('language', $_SESSION['language']);
$fields = '';
// load selected payment module
require_once(DIR_WS_CLASSES.'payment.php');
$payment_modules = new payment($_SESSION['payment']);

if (isset($_SESSION['novalnet'][$novalnet_payment]['input_params'])) {
    foreach ($_SESSION['novalnet'][$novalnet_payment]['input_params'] as $key => $value) {
        $fields .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
    }
    $fields .= '<input type="hidden" name="' . xtc_session_name() . '" value="' . xtc_session_id() . '" />';

    $form_action_url = ['novalnet_giropay' => 'https://payport.novalnet.de/giropay',
                        'novalnet_sofortbank' => 'https://payport.novalnet.de/online_transfer_payport',
                        'novalnet_ideal' => 'https://payport.novalnet.de/online_transfer_payport',
                        'novalnet_przelewy24' => 'https://payport.novalnet.de/globalbank_transfer',
                        'novalnet_pp' => 'https://payport.novalnet.de/paypal_payport',
                        'novalnet_eps' => 'https://payport.novalnet.de/giropay'
                       ];

    $description = ($_SESSION['payment'] == 'novalnet_cc') ? constant("MODULE_PAYMENT_" . strtoupper($_SESSION['payment']) . "_REDIRECTION_TEXT_DESCRIPTION") : constant("MODULE_PAYMENT_" . strtoupper($_SESSION['payment']) . "_TEXT_DESCRIPTION");

    $smarty->assign('main_content', '<span id="novalnet_loader" style="margin:auto; left:50%; top:50%; height:100%; width:100%; z-index:10; position:fixed;">
                                <img src="'.DIR_WS_CATALOG.'images/icons/novalnet/nn_loader.gif'.'" alt="Novalnet AG" />
                                 </span>
                                 <p>'. $description .'</p>
                                 <form id="novalnet_payment_form" name="novalnet_payment_form" action="'. $form_action_url[$payment_modules->selected_module] .'" method="post">'
                                    . $fields
                                    . '<input type="submit" class="button" id="submit_novalnet_payment_form" value="'. MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT .'" />
                                </form>
                                <script>

                                    window.onload = function () {
                                        document.forms["novalnet_payment_form"].submit();
                                    }

                                </script>');
}
$smarty->caching = 0;
if (!defined(RM)) {
    $smarty->load_filter('output', 'note');
}
$smarty->display(CURRENT_TEMPLATE . '/index.html');
include('includes/application_bottom.php');
