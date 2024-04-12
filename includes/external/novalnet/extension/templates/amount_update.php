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
    <!-- Amount update process block -->
<?php echo xtc_draw_form('novalnet_amount_change', 'novalnet.php', 'oID='.$_GET['oID'].'&action=edit'); ?>
        <input type='hidden' name='novalnet_process' value='amount_update' />
        <div class="heading"><?php echo ($this->data['payment_id']== 37) ? MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_TITLE : ($this->data['payment_id']== 27 ?  MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_DUE_DATE_TITLE : MODULE_PAYMENT_NOVALNET_AMOUNT_CHANGE_SLIP_EXPIRY_DATE_TITLE); ?>:</div>
        <table cellspacing="0" cellpadding="2" class="table">
            <tr class="dataTableRow">
                <td class="smallText" colspan="2"><b><?php echo MODULE_PAYMENT_NOVALNET_TRANSACTION_ID. " " . $this->data['tid'] ; ?></b><br/><br/></td>
            </tr>
            <tr class="dataTableRow">
              <td class="smallText" valign="top"><b><?php echo MODULE_PAYMENT_NOVALNET_TRANS_AMOUNT_TITLE; ?></b></td>
              <td class="smallText" valign="top"><input type='text' name='new_amount' id='new_amount' autocomplete="off" onkeypress='return novalnetAllowNumeric(event)' value='<?php echo $this->data['amount']; ?>' /> <?php echo MODULE_PAYMENT_NOVALNET_AMOUNT_EX; ?></td>
            </tr>
            <?php $invoice_payment = 0;
            if (in_array($this->data['payment_id'], array(27, 59))) {
               $invoice_payment = 1; ?>
            <tr class="dataTableRow">
              <td class="smallText" valign="top"><?php echo ($this->data['payment_id'] == 27) ? MODULE_PAYMENT_NOVALNET_TRANS_DUE_DATE_TITLE : MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE_TITLE; ?>:</td>
              <td class="smallText" valign="top">
                <div class="SumoSelect amount_change_day" tabindex="0">
                    <select name='amount_change_day' id='amount_change_day'>
                        <?php for ($i = 1; $i <= 31; $i++) { ?>
                            <option <?php echo (($this->data['input_day'] == $i)?'selected':''); ?> value="<?php echo (($i < 10)?'0'.$i:$i); ?>"><?php echo (($i < 10)?'0'.$i:$i); ?></option>
                        <?php } ?>
                    </select>
                    <select name='amount_change_month' id='amount_change_month'>
                        <?php for($i = 1; $i <= 12; $i++) { ?>
                            <option id="date<?php echo $i;?>" <?php echo (($this->data['input_month'] == $i)?'selected':''); ?> value="<?php echo (($i < 10)?'0'.$i:$i); ?>"><?php echo (($i < 10)?'0'.$i:$i); ?></option>
                        <?php } ?>
                    </select>
                    <select name='amount_change_year' id='amount_change_year'>
                        <?php $year_val = date('Y');
                            for($i = $year_val; $i <= ($year_val+1); $i++) { ?>
                        <option <?php echo (($this->data['input_year'] == $i)?'selected':''); ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
              </td>
            </tr>
            <?php } ?>
            <tr  class="dataTableRow">
                <td class="dataTableContent" valign="top">
                    <div class="flt-l">
                    </div>
                </td>
                <td class="dataTableContent" valign="top">
                    <div class="flt-r">
                        <input type='hidden' id='invoice_payment' value='<?php echo $invoice_payment; ?>'>
                        <input type='submit' class="button" id='nn_amount_update' name='nn_trans_confirm' value='<?php echo MODULE_PAYMENT_NOVALNET_UPDATE_TEXT; ?>' onclick="return validate_amount_change(<?php echo $this->data['payment_id']?>);" />
                    </div>
                </td>
            </tr>
        </table>
    </form>
