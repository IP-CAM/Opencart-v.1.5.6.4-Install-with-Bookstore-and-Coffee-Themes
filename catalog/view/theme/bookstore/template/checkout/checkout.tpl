<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>

  <h1><?php echo $heading_title; ?></h1>
  <div class="checkout">


    <div id="payment">
      <div class="checkout-heading"><span><?php echo $text_checkout_account; ?></span></div>
      <div class="checkout-content">
	  <div class="left">
		  <h2><?php echo $text_your_details; ?></h2>
		  <span class="required">*</span> <?php echo $entry_firstname; ?><br />
		  <input type="text" name="firstname" value="<?php echo $firstname; ?>" class="large-field" />
		  <br />
		  <br />
		  <span class="required">*</span> <?php echo $entry_lastname; ?><br />
		  <input type="text" name="lastname" value="<?php echo $lastname; ?>" class="large-field" />
		  <br />
		  <br />
		  <span class="required">*</span> <?php echo $entry_email; ?><br />
		  <input type="text" name="email" value="<?php echo $email; ?>" class="large-field" />
		  <br />
		  <br />
		  <span class="required">*</span> <?php echo $entry_telephone; ?><br />
		  <input type="text" name="telephone" value="<?php echo $telephone; ?>" class="large-field" />
		  <br />
		  <br />  
		  <span class="required">*</span> <?php echo $entry_address_1; ?><br />
		  <input type="text" name="address_1" value="<?php echo $address_1; ?>" class="large-field" />
		  <br />
		  <br />
         <?php echo $entry_address_2; ?><br />
		 <input type="text" name="address_2" value="<?php echo $address_2; ?>" class="large-field" />
		 <br />
		 <br />	
		 <p><?php echo $text_shipping_method; ?></p>
		<table class="radio">
		  <?php foreach ($shipping_methods as $shipping_method) { ?>
		 
		  <?php if (!$shipping_method['error']) {?>
		  <?php foreach ($shipping_method['quote'] as $quote) { ?>
		  <tr class="highlight">
			<td><?php if ($quote['code'] == $shipping_methods_code || !$shipping_methods_code) { ?>
			  <?php $shipping_methods_code = $quote['code']; ?>
			  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
			  <?php } else { ?>
			  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
			  <?php } ?></td>
			<td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
			<td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
		  </tr>
		  <?php } ?>
		  <?php } else { ?>
		  <tr>
			<td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
		  </tr>
		  <?php } ?>
		  <?php } ?>
		</table>
		<br />
		<?php if ($payment_methods) { ?>
			<p><?php echo $text_payment_method; ?></p>
			<table class="radio">
			  <?php foreach ($payment_methods as $payment_method) { ?>
			  <tr class="highlight">
				<td><?php if ($payment_method['code'] == $payment_methods_code || !$payment_methods_code) { ?>
				  <?php $payment_methods_code = $payment_method['code']; ?>
				  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
				  <?php } else { ?>
				  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
				  <?php } ?></td>
				<td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label></td>
			  </tr>
			  <?php } ?>
			</table>
			<br />
		<?php } ?>
	  </div>
	  </div>
    </div>

   
    <div id="shipping-address">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_address; ?></div>
      <div class="checkout-content"></div>
    </div>
    <div id="shipping-method">
      <div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
      <div class="checkout-content"></div>
    </div>
 
    <div id="payment-method">
      <div class="checkout-heading"><?php echo $text_checkout_payment_method; ?></div>
      <div class="checkout-content"></div>
    </div>
    <div id="confirm">
      <div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
      <div class="checkout-content"></div>
    </div>
	<div class="buttons">
	  <div class="right">
		<input type="button" value="<?php echo $button_continue; ?>" id="button-confirm" class="button" />
	  </div>
	</div>
  </div>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--

function processErrors(Errors){
	for (err in Errors){
	
					$('#payment input[name=\''+err+'\']').after('<span class="error">' + Errors[err] + '</span>');
				}		err
}
	
		


$(document).ready( function() {
	$('#button-confirm').live('click', function() {
			$.ajax({
				url: 'index.php?route=checkout/checkout/confirm&XDEBUG_SESSION_START=netbeans-xdebug', 
				type: 'post',
				data: $('#payment input[type=\'text\'], #payment input[type=\'password\'], #payment input[type=\'checkbox\']:checked, #payment input[type=\'radio\']:checked, #payment input[type=\'hidden\'], #payment select'),
				dataType: 'json',
				beforeSend: function() {
					$('#button-confirm').attr('disabled', true);
					$('#button-confirm').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
				},	
				complete: function() {
					$('#button-confirm').attr('disabled', false);
					$('.wait').remove();
				},			
				success: function(json) {
					$('.warning, .error').remove();
					
					if (json['redirect']) {
						location = json['redirect'];
					} else if (json['error']) {
						processErrors(json['error']);
						if (json['error']['warning']) {
							$('#payment .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
							
							$('.warning').fadeIn('slow');
						}			
					} else {
						alert('success');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
		});	
	});	
});






//--></script> 
<?php echo $footer; ?>