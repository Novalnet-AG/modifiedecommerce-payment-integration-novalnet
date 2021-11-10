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
 * Script : auto_config.php
 *
 */

include ('includes/external/novalnet/classes/NovalnetUtil.php');

class AutoConfig
{
        /**
        * Constructor
        *
        */
    public function __construct()
    {

        $request = array_map('trim', $_POST);

        if (!empty($request['hash'])) {
            $data = array(
                'hash'      => $request['hash'],
                'lang'      => $request['lang']
            );

            $response =  json_decode(NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/autoconfig', $data, true));
            $jsonerror = json_last_error();

            if (empty($jsonerror)) {
                    if ($response->status == '100') {
                        $merchant_details = array(
                            'vendor_id'   => $response->vendor,
                            'auth_code'   => $response->auth_code,
                            'product_id'  => $response->product,
                            'access_key'  => $response->access_key,
                            'test_mode'   => $response->test_mode,
                            'tariff'      => $response->tariff,
                        );
                        echo json_encode($merchant_details);
                        exit();
                    } else {
                        if ($response->status == '106') {
                        echo sprintf(MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE, NovalnetUtil::getIpAddress('SERVER_ADDR'));
                        $result = sprintf(MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE, NovalnetUtil::getIpAddress('SERVER_ADDR'));
                       }else{
                          $result = !empty($response->config_result) ? $response->config_result : $response->status_desc;
                        }
                }
                echo json_encode(array('status_desc' => $result));
                exit();
            }
        }
        echo json_encode(array('status_desc' => 'empty'));
        exit();
    }
}
new AutoConfig();
