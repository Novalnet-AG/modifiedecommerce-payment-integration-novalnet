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

    <!-- Transaction Booking process block -->
<?php echo xtc_draw_form('novalnet_book_amount', 'novalnet.php', 'oID='.$_GET['oID'].'&action=edit'); ?>
        <input type='hidden' name='novalnet_process' value='trans_book' />
        <div class="heading"><?php echo MODULE_PAYMENT_NOVALNET_BOOK_TITLE; ?>:</div>
        <table cellspacing="0" cellpadding="2" class="table">
            <tr class="dataTableRow">
                <td class="smallText" colspan="2"><b><?php echo MODULE_PAYMENT_NOVALNET_TRANSACTION_ID. " " . $this->data['tid'] ; ?></b><br/><br/></td>
            </tr>
         <?php
           if ($this->data['amount'] == 0) {?>
            <tr class="dataTableRow">
              <td class="smallText" valign="top"><b><?php echo MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE; ?>:</b></td>
              <td class="smallText" valign="top">
                  <input type='text' style='width:100px;' name='book_amount' id='book_amount' onkeypress='return novalnetAllowNumeric(event)' autocomplete="off" value='<?php global $order; echo filter_var($order->info['total'], FILTER_SANITIZE_NUMBER_INT); ?>' /> <?php echo MODULE_PAYMENT_NOVALNET_AMOUNT_EX;?></td>
            </tr>
            <?php } ?>
            <tr  class="dataTableRow">
                <td class="dataTableContent" valign="top">
                    <div class="flt-l">
                    </div>
                </td>
                <td class="dataTableContent" valign="top">
                    <div class="flt-r">
                        <input type='submit' class="button" id='nn_trans_book_confirm' name='nn_trans_confirm' value='<?php echo MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT; ?>' onclick=" return validate_book_amount(event);" />
                    </div>
                </td>
            </tr>
        </table>
    </form>
