<?php 
  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
?>
   
   <!-- Refund amount block -->
   <?php echo xtc_draw_form('novalnet_trans_refund', 'novalnet.php', 'oID='.$_GET['oID'].'&action=edit'); ?>
	    <input type='hidden' name='novalnet_process' value='refund' />
		<div class="heading"><?php echo MODULE_PAYMENT_NOVALNET_REFUND_TITLE; ?>:</div>
		<table cellspacing="0" cellpadding="2" class="table">
			<tr class="dataTableRow">
				<td class="smallText" colspan="2"><b><?php echo MODULE_PAYMENT_NOVALNET_TRANSACTION_ID. " " . $this->data['tid'] ; ?></b><br/><br/></td>
			</tr>
			<tr class="dataTableRow">
			  <td class="smallText" valign="top"><b><?php echo MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE; ?></b></td>
			  <td class="smallText" valign="top"><input type='text' style='width:100px;' name='refund_trans_amount' id='refund_trans_amount' onkeypress='return novalnetAllowNumeric(event)' autocomplete="off" value='<?php echo $this->data['amount']; ?>' /> <?php echo MODULE_PAYMENT_NOVALNET_AMOUNT_EX;?></td>
			</tr>
			<?php
			 $order_date = strtotime(date("Y-m-d",strtotime($this->data['date'])));
				if ( strtotime(date('Y-m-d')) > $order_date ) { ?>
			<tr class="dataTableRow">
			  <td  class="smallText" valign="top"><b><?php echo MODULE_PAYMENT_NOVALNET_REFUND_REFERENCE_TEXT; ?></b></td>
			  <td class="smallText" valign="top"><input type='text' style='width:200px;' name='refund_ref' id='refund_ref' /> </td>
			</tr>
			<?php
			}
			?>
			<tr  class="dataTableRow">
				<td class="dataTableContent" valign="top">
					<div class="flt-l">
					</div>
				</td>
				<td class="dataTableContent" valign="top"> 
					<div class="flt-r">
						<input type='submit' class="button" name='novalnet_refund_process' value='<?php echo MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT; ?>' onclick="return validate_refund_amount();" />
					</div>
				</td>
			</tr>
		</table>
	</form>
