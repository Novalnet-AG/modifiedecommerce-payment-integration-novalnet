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
  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
?>

<!-- Transaction Confirm Block -->
<?php echo xtc_draw_form('novalnet_status_change', 'novalnet.php', 'oID='.$_GET['oID'].'&action=edit'); ?>
    <input type='hidden' name='novalnet_process' value='trans_confirm' />
    <div class="heading"><?php echo MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE; ?>:</div>
    <table cellspacing="0" cellpadding="2" class="table">
        <tr class="dataTableRow">
            <td class="smallText" colspan="2"><b><?php echo MODULE_PAYMENT_NOVALNET_TRANSACTION_ID. " " . $this->data['tid'] ; ?></b><br/><br/></td>
        </tr>
        <tr class="dataTableRow">
          <td class="smallText" width="15%" valign="top"><b><?php echo NovalnetUtil::setUtf8Mode(MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT); ?></b></td>
          <td class="smallText" valign="center"  colspan="2">
                <select name="trans_status" id="trans_status">
                <option value=''><?php echo MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION; ?></option>
                <option value='100'><?php echo MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT; ?></option>
                <option value='103'><?php echo MODULE_PAYMENT_NOVALNET_CANCEL_TEXT; ?></option>
                </select>
                  <br/><br/>
                <div class="flt-l">
                    <input type='submit' class="button" id="nn_manage_transaction" name='nn_trans_confirm' value='<?php echo MODULE_PAYMENT_NOVALNET_UPDATE_TEXT; ?>' onclick="return validate_status_change();" />
                </div>
            </td>
        </tr>
    </table>
</form>
