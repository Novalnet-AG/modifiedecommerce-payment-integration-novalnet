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
 * Script : novalnet.php
 *
 */
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE', '<b>Manage transaction process</b>');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT', 'Please select status');
define('MODULE_PAYMENT_NOVALNET_SELECT_CONFIRM_TEXT', 'Are you sure you want to capture the payment?');
define('MODULE_PAYMENT_NOVALNET_SELECT_CANCEL_TEXT', 'Are you sure you want to cancel the payment?');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMOUNT_TEXT', 'Are you sure you want to refund the amount?');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION', '--Select--');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE', 'The transaction has been confirmed on %s, %s');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_WITH_DUEDATE', 'The transaction has been confirmed successfully for the TID: %s and the due date updated as %s');
define('MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE', 'The transaction has been canceled on %s, %s');
define('MODULE_PAYMENT_NOVALNET_TRANS_UPDATED_MESSAGE', 'The transaction has been updated with amount ( %s )  and due date %s');
define('MODULE_PAYMENT_NOVALNET_CASH_PAYMENT_TRANS_UPDATED_MESSAGE', 'The transaction has been updated with amount ( %s ) and slip expiry date %s');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE', 'Please enter the refund amount');
define('MODULE_PAYMENT_NOVALNET_REFUND_TITLE', '<b>Refund process</b>');
define('MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG', 'Refund has been initiated for the TID: %s with the amount %s');
define('MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG', ' .New TID: %s for the refunded amount: %s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_EX', ' (in minimum unit of currency. E.g. enter 100 which is equal to 1.00)');
define('MODULE_PAYMENT_NOVALNET_UPDATE_TEXT', 'Update');
define('MODULE_PAYMENT_NOVALNET_CANCEL_TEXT', 'Cancel');
define('MODULE_PAYMENT_NOVALNET_ORDER_UPDATE', 'Successful');
define('MODULE_PAYMENT_NOVALNET_BOOK_TITLE', '<b>Book transaction</b>');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMOUNT_TEXT', 'Are you sure you want to book the order amount?');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE', 'Transaction booking amount');
define('MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE', 'Your order has been booked with the amount of %s. Your new TID for the booked amount:%s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_TITLE', '<b>Amount update</b>');
define('MODULE_PAYMENT_NOVALNET_TRANS_AMOUNT_TITLE', 'Update transaction amount');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_DUE_DATE_TITLE', '<b>Change the  amount / due date</b>');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_SLIP_EXPIRY_DATE_TITLE', '<b>Change the amount / slip expiry date</b>');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_SLIP_EXPIRY_DATE_BUTTON', 'Change the amount / slip expiry date');
define('MODULE_PAYMENT_NOVALNET_TRANS_DUE_DATE_TITLE', '<b>Transaction due date</b>');
define('MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE_TITLE', '<b>Slip expiry date</b>');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_UPDATE_TEXT', 'Are you sure you want to change the order amount?');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_DATE_UPDATE_TEXT', 'Are you sure you want to change the order amount / due date?');
define('MODULE_PAYMENT_NOVALNET_ORDER_AMT_SLIP_EXPIRY_DATE_UPDATE_TEXT', 'Are you sure you want to change the order amount / slip expiry date?');
define('MODULE_PAYMENT_NOVALNET_VALID_DUEDATE_MESSAGE', 'The date should be in future');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR', 'Unfortunately, this order could not be processed. Please, place a new order');
define('MODULE_PAYMENT_NOVALNET_REFUND_REFERENCE_TEXT', 'Refund reference');
define('MODULE_PAYMENT_NOVALNET_INVALID_DATE', 'Invalid due date');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SLIP_EXPIRY_DATE_TEXT', 'Slip expiry date');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_NEAREST_STORE_DETAILS_TEXT', 'Store(s) near you');
define('MODULE_PAYMENT_NOVALNET_SEPA_TRANS_UPDATED_MESSAGE', 'The transaction amount ( %s ) has been updated successfully on %s , %s.');
define('MODULE_PAYMENT_NOVALNET_MAIL_MESSAGE', 'Dear Mr./Ms./Mrs.');
define('MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE', 'You need to configure your outgoing server IP address (%s) at Novalnet. Please configure it in Novalnet Merchant Administration portal or contact technic@novalnet.de');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_REF', 'Add new account details for later purchases');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_ACC', 'Account holder');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_SEPA_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_ONECLICK_REF_PROCEED', 'Proceed with new PayPal account details');
define('MODULE_PAYMENT_NOVALNET_TRUE', 'True');
define('MODULE_PAYMENT_NOVALNET_FALSE', 'False');
define('MODULE_PAYMENT_NOVALNET_CONFIG_BLOCK_TITLE', '<b>Merchant API Configuration</b>');
define('MODULE_PAYMENT_NOVALNET_ALLOWED_TITLE', 'Allowed zone(-s)');
define('MODULE_PAYMENT_NOVALNET_ALLOWED_DESC', 'This payment method will be allowed for the mentioned zone(-s). Enter the zone(-s) in the following format E.g: DE,AT,CH. In case if the field is empty, all the zones will be allowed');
define('MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_TITLE', 'Display payment method');
define('MODULE_PAYMENT_NOVALNET_ENABLE_MODULE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE', 'Enable test mode');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC', '');
define('MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_TITLE', '<div id="set_limit_title">Minimum transaction limit for authorization</div>');
define('MODULE_PAYMENT_NOVALNET_MANUAL_CHECK_LIMIT_DESC', '<div id="set_limit_desc">In case the order amount exceeds the mentioned limit, the transaction will be set on-hold till your confirmation of the transaction. You can leave the field empty if you wish to process all the transactions as on-hold.</div>');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TITLE', 'Enable fraud prevention');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_DESC', 'Automatic PIN generation to authenticate buyers in DE, AT, and CH. Refer the Installation Guide for more information');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_TITLE', 'Minimum value of goods for the fraud module (in minimum unit of currency. E.g. enter 100 which is equal to 1.00)');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_LIMIT_DESC', 'Enter the minimum value of goods from which the fraud module should be activated');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_TITLE', 'Notification for the buyer');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_INFO_DESC', 'The entered text will be displayed on the checkout page');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE', 'Define a sorting order');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC', 'This payment method will be sorted among others (in the ascending order) as per the given sort number.');
define('MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_TITLE', 'Payment pending order status');
define('MODULE_PAYMENT_NOVALNET_PENDING_ORDER_STATUS_DESC', 'Status to be used for pending transactions');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE', 'Completed order status');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC', 'Status to be used for successful orders');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE', 'Payment zone');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC', 'This payment method will be displayed for the mentioned zone(-s)');
define('MODULE_PAYMENT_NOVALNET_SHOP_TYPE_TITLE', 'Shopping type');
define('MODULE_PAYMENT_NOVALNET_SHOP_TYPE_DESC', 'Select shopping type');
define('MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_TITLE', 'Minimum order amount');
define('MODULE_PAYMENT_NOVALNET_VISIBILITY_BYAMOUNT_DESC', 'Minimum order amount to display the selected payment method (s) at during checkout ');
define('MODULE_PAYMENT_NOVALNET_SELECT', '-- SELECT -- ');
define('MODULE_PAYMENT_NOVALNET_OPTION_NONE', 'None');
define('MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONCALLBACK', 'PIN by callback');
define('MODULE_PAYMENT_NOVALNET_FRAUD_OPTIONSMS', 'PIN by SMS');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS_GUARANTEE_PAYMENT', 'This is processed as a guarantee payment');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS', 'Novalnet transaction details');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet transaction ID: ');
define('MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE', 'Test order');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG', '<span style="color:red;">The payment will be processed in the test mode therefore amount for this transaction will not be charged<br/></span>');
define('MODULE_PAYMENT_NOVALNET_JS_DEACTIVATE_ERROR', 'Please enable the Javascript in your browser to proceed further with the payment');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE', 'The amount is invalid');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR', 'While redirecting some data has been changed. The hash check failed');
define('MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH', 'Please transfer the amount to the below mentioned account details of our payment processor Novalnet');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Account holder');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_DUE_DATE', 'Due date'); 
define('MODULE_PAYMENT_NOVALNET_BANK', 'Bank');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', 'Amount');
define('MODULE_PAYMENT_NOVALNET_SWIFT_BIC', 'BIC');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF1', 'Payment Reference 1');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF2', 'Payment Reference 2');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT', 'Please use any one of the following references as the payment reference, as only through this way your payment is matched and assigned to the order:');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_INFO', 'You will shortly receive a transaction PIN through phone call to complete the payment');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_PIN_INFO', 'You will shortly receive an sms containing your transaction PIN to complete the payment');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_REQUEST_DESC', 'Transaction PIN');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_NEW_PIN', '&nbsp; Forgot your PIN?');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_EMPTY', 'Enter your PIN');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_PIN_NOTVALID', 'The PIN you entered is incorrect');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_AMOUNT_CHANGE_ERROR', 'The order amount has been changed, please proceed with the new order');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_TELEPHONE_ERROR', 'Please enter your telephone number ');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_ERROR', 'Please enter your mobile number');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE', 'Your date of birth');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_ERROR_MSG', 'Please enter valid birth date');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_INVOICE_CREDIT_COMMENTS', 'Novalnet Callback Script executed successfully for the TID: %s with amount: %s on %s & %s. Please refer PAID transaction in our Novalnet Merchant Administration with the TID: %s');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY24_CANCELLED_COMMENT', 'The transaction has been canceled due to:');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_UPDATE_COMMENTS', 'Novalnet Callback Script executed successfully for the TID: %s with amount %s on %s & %s.');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK_COMMENTS', 'Novalnet callback received. Chargeback executed successfully for the TID: %s amount: %s on %s & %s. The subsequent TID: %s');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK_COMMENTS', 'Novalnet callback received. Refund/Bookback executed successfully for the TID: %s amount: %s on %s & %s. The subsequent TID: %s');
define('MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR', 'Your account details are invalid');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'Please fill in all the mandatory fields');
define('MODULE_PAYMENT_NOVALNET_AGE_ERROR', 'You need to be at least 18 years old');
define('MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_TITLE', '<h2>Payment guarantee configuration</h2><h3>Basic requirements for payment guarantee</h3><ul>
    <li>Allowed countries: DE, AT, CH</li>
    <li>Allowed currency: EUR</li>
    <li>Minimum amount of order >= 9,99 EUR</li>
    <li>Minimum age of end customer >= 18 Years</li>
    <li>The billing address must be the same as the shipping address</li>    
</ul><br/>Enable payment guarantee');
define('MODULE_PAYMENT_NOVALNET_ENABLE_GUARANTEE_DESC', 'Even if payment guarantee is enabled, payments will still be processed as non-guarantee payments if the payment guarantee requirements are not met. Review the requirements under "Enable Payment Guarantee" in the Installation Guide. ');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_TITLE', 'Minimum order amount for payment guarantee');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_DESC', 'Enter the minimum amount (in cents) for the transaction to be processed with payment guarantee. For example, enter 100 which is equal to 1,00. By default, the amount will be 9,99 EUR');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_AMOUNT_ERROR', 'The amount is invalid');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MIN_AMOUNT_ERROR', 'The minimum amount should be at least 9,99 EUR');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_EMPTY_ERROR', 'Please enter your date of birth');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT_ERROR', 'The date format is invalid');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_TITLE', 'Force Non-Guarantee payment');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_FORCE_DESC', 'Even if payment guarantee is enabled, payments will still be processed as non-guarantee payments if the payment guarantee requirements are not met. Review the requirements under "Enable Payment Guarantee" in the Installation Guide');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY', '<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee havent been met (Only Germany, Austria or Switzerland are allowed)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY', '<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee haven&#39;t been met (Only EUR currency allowed)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS', '<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee haven&#39;t been met (The shipping address must be the same as the billing address)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT', "<span style='color:red;'>The payment cannot be processed, because the basic requirements for the payment guarantee haven't been met (Minimum order amount must be %s )</span>");
define('MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT', 'Confirm');
define('MODULE_PAYMENT_NOVALNET_CONFIG_INSTALL_ERROR', 'Please configure Novalnet Global Configuration to enable the Novalnet Payment method');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE', 'Your order is under verification and once confirmed, we will send you our bank details to where the order amount should be transferred. Please note that this may take upto 24 hours');
define('MODULE_PAYMENT_NOVALNET_GUARANTEED_SEPA_MESSAGE', 'Your order is under verification and we will soon update you with the order status. Please note that this may take upto 24 hours.');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION', 'Order Confirmation - Your Order ');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION1', ' with ');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION2', ' has been confirmed');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION4', ' has been canceled');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION3', '<br><br>We are pleased to inform you that your order has been confirmed');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_INFORMATION', 'Payment Information:');
define('MODULE_PAYMENT_NOVALNET_METHOD_COMMENT', 'Payment Method:');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT', 'DD-MM-YYYY');

define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_CALLBACK_INPUT_TITLE', 'Telephone number');
define('MODULE_PAYMENT_NOVALNET_FRAUDMODULE_SMS_INPUT_TITLE', 'Mobile number');

define('MODULE_PAYMENT_NOVALNET_PENDING_TO_ONHOLD', 'Novalnet callback received. The transaction status has been changed from pending to on hold for the TID: %s on %s.');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CONFIRMED', 'Novalnet callback received. The transaction has been confirmed on %s');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CANCELLED', 'Novalnet callback received. The transaction has been canceled on %s');

$novalnet_temp_status_text = 'NN payment pending';
