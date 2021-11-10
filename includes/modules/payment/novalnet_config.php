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
 * Script : novalnet_config.php
 *
 */

include_once(DIR_FS_CATALOG . 'includes/external/novalnet/classes/NovalnetUtil.php');
class novalnet_config
{
    var $title,
        $description;

    /**
     * Constructor
     *
     */
    function __construct()
    {
        $this->code        = 'novalnet_config';
        $this->enabled     = true;
        $this->sort_order  = 0;
        $this->title       = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION :'';
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
     * Core Function : check()
     *
     */
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /**
     * Core Function : selection()
     *
     */
    public function selection()
    {
        return false;
    }

    /**
     *  Core Function : install()
     *
     */
    public function install()
    {
        global $request_type;

        $tmp_status_id = $this->installQuery();

        xtc_db_query("INSERT INTO ". TABLE_CONFIGURATION ."
        (configuration_key, configuration_value, configuration_group_id, sort_order,set_function, use_function, date_added)
        VALUES
        ('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY', '', '6', '0', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_VENDOR_ID', '', '6', '1', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_AUTHCODE', '', '6', '2', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_PRODUCT_ID', '', '6', '3', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_TARIFF_ID', '', '6', '4', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY', '', '6', '5', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY', '', '6', '6', '', '', now()),
        ('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE', '0',  '6', '7', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now()),
        ('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED', '4',  '6', '8', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name',now()),
        ('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE','False', '6', '9', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE\',".MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE.",' , '',now()),
        ('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND','False', '6', '10', 'xtc_mod_select_option(array(\'True\' => MODULE_PAYMENT_NOVALNET_TRUE,\'False\' => MODULE_PAYMENT_NOVALNET_FALSE),\'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND\',".MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND.",' , '',now()),
        ('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO', '', '6', '11','','', now()),        
        ('MODULE_PAYMENT_NOVALNET_CALLBACK_URL', '".((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG) . 'callback/novalnet/callback.php'."','6', '12','','', now()),
        ('MODULE_PAYMENT_NOVALNET_CONFIG_ALLOWED', '', '6', '13','','', now()),
        ('MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS', '". $tmp_status_id ."', '6', '14','','', now())");
    }

    /**
     * Core Function : remove()
     *
     */
    public function remove()
    {
        $keys = array_merge($this->keys(), array('MODULE_PAYMENT_NOVALNET_CONFIG_ALLOWED', 'MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS'));
        xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $keys)."')");
    }

    /**
     * Core Function : keys()
     *
     */
    public function keys()
    {
        // Assign hidden values
        echo '<input type="hidden" id="nn_api_shoproot" value="'. DIR_WS_CATALOG .'" />
        <input type="hidden" id="novalnet_ajax_complete" value="1" />
        <input type="hidden" id="nn_client_key" name="nn_client_key" value="" />
        <input type="hidden" id="nn_language" value="'. strtoupper($_SESSION['language_code']) .'" />
        <script src="'.DIR_WS_CATALOG . 'includes/external/novalnet/js/novalnet_api.js" type="text/javascript"></script>';

        // Check global configuration
        NovalnetUtil::checkMerchantConfiguration(true);

        return array (
            'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY',
            'MODULE_PAYMENT_NOVALNET_VENDOR_ID',
            'MODULE_PAYMENT_NOVALNET_AUTHCODE',
            'MODULE_PAYMENT_NOVALNET_PRODUCT_ID',
            'MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY',
            'MODULE_PAYMENT_NOVALNET_PAYMENT_CLIENT_KEY',
            'MODULE_PAYMENT_NOVALNET_TARIFF_ID',                                  
            'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE',
            'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO',            
            'MODULE_PAYMENT_NOVALNET_CALLBACK_URL',

        );
    }

    /**
     * Installing Novalnet tables
     *
     */
    public function installQuery()
    {
        $novalnet_check = xtc_db_query('DESC '. TABLE_ADMIN_ACCESS);
        $novalet_alter_table = false;
        while ($check_column = xtc_db_fetch_array($novalnet_check)) {
            if ($check_column['Field'] == 'novalnet') {
                $novalet_alter_table = true;
                break;
            }
        }
        if (!$novalet_alter_table) {
            xtc_db_query("ALTER TABLE ". TABLE_ADMIN_ACCESS ." ADD novalnet int(1) NOT NULL DEFAULT '1',COMMENT='Novalnet Admin page'");
        }
        $insert_novalnet_tables = true;
        $tables_sql = xtc_db_query('select table_name from information_schema.columns where table_schema = "' . DB_DATABASE . '" AND table_name= "novalnet_transaction_detail"');
        $result = xtc_db_fetch_array($tables_sql);
        if (!empty($result)) {
            $insert_novalnet_tables = false;
        }

        if (!$insert_novalnet_tables) {
            $sql = xtc_db_fetch_array(xtc_db_query('show columns from novalnet_transaction_detail like "payment_ref"'));
            if (empty($sql)) {
                $sql_file = DIR_FS_CATALOG . 'includes/external/novalnet/install/update_10.sql';
                $sql_lines = file_get_contents($sql_file);
                $sql_linesArr = explode(";", $sql_lines);
                foreach ($sql_linesArr as $sql) {
                    if (trim($sql) > '') {
                          xtc_db_query($sql);
                    }
                }
            }
            $sql_query = xtc_db_query('show columns from novalnet_transaction_detail like "reference_transaction"');
            $alter_sql = xtc_db_fetch_array($sql_query);
            $sql_query_vendor = xtc_db_query('show columns from novalnet_transaction_detail like "vendor"');
            $alter_sql_vendor = xtc_db_fetch_array($sql_query_vendor);
            if (empty($alter_sql) || !empty($alter_sql_vendor)) {
                //Import Novalnet version 11 package SQL tables
                  $sql_file = DIR_FS_CATALOG . 'includes/external/novalnet/install/update_10_to_11.sql';
                  $sql_lines = file_get_contents($sql_file);
                  $sql_linesArr = explode(";", $sql_lines);
                foreach ($sql_linesArr as $sql) {
                    if (trim($sql) > '') {
                        xtc_db_query($sql);
                    }
                }
            }
        } else {
             //Import Novalnet package SQL tables
            $sql_file = DIR_FS_CATALOG . 'includes/external/novalnet/install/install_11.sql';
            $sql_lines = file_get_contents($sql_file);
            $sql_linesArr = explode(";", $sql_lines);
            foreach ($sql_linesArr as $sql) {
                if (trim($sql) > '') {
                    xtc_db_query($sql);
                }
            }
        }

        return $this->createCustomOrderStatus();
    }

    /**
     * Create the custom order status
     *
     * @return int
     */
    public function createCustomOrderStatus()
    {
        if (!defined('MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS')) {
            $languages = xtc_db_query("select * from " . TABLE_LANGUAGES . " order by sort_order");
            $query = xtc_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);

            $status = xtc_db_fetch_array($query);
            $status_id = $status['status_id']+1;

            while ($language = xtc_db_fetch_array($languages)) {
                if (file_exists(DIR_FS_LANGUAGES . $language['directory'].'/modules/payment/novalnet.php')) {
                    include_once(DIR_FS_LANGUAGES . $language['directory'].'/modules/payment/novalnet.php');
                }

                if (empty($novalnet_temp_status_text)) {
                    $novalnet_temp_status_text = 'NN payment pending';
                }

                $query = xtc_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = '" . NovalnetUtil::setUtf8Mode($novalnet_temp_status_text) . "' AND language_id='".$language['languages_id']."' limit 1");

                if (xtc_db_num_rows($query) < 1) {
                    $insert_values = array(
                        'orders_status_id' => $status_id,
                        'language_id' => $language['languages_id'],
                        'orders_status_name' => NovalnetUtil::setUtf8Mode($novalnet_temp_status_text),
                    );

                    $sql_query = xtc_db_query('SHOW COLUMNS from ' . TABLE_ORDERS_STATUS . ' LIKE "sort_order"');
                    $column_exist = xtc_db_fetch_array($sql_query);
                    if (!empty($column_exist)) {
                        $insert_values['sort_order'] = $status_id;
                    }
                    xtc_db_perform(TABLE_ORDERS_STATUS, $insert_values);
                } else {
                    $status = xtc_db_fetch_array($query);
                    return $status['orders_status_id'];
                }
            }
            return NovalnetUtil::checkDefaultOrderStatus($status_id);
        }
        return MODULE_PAYMENT_NOVALNET_PAYMENT_PENDING_STATUS;
    }
}
