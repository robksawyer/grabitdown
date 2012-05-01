<div class="uploads form">
<?php 
	echo $this->Form->create('Upload',array('url'=>array('action'=>'paypal_set_ec')));
?>
	<fieldset>
<?php
	echo $this->Form->input('total_codes',array(
		'type' => 'select',
		'options' => $payment_options,
		'selected' => 0,
		'label' => 'Total codes to generate',
		'before' => '<ul class="info"><li>10 = <span class="price">$5</span></li><li>100 = <span class="price">$25</span></li><li>1,000 = <span class="price">$50</span></li><li>10,000 = <span class="price">$125</span></li><li>100,000 = <span class="price">$250</span></li></ul>'
	));
?>
	</fieldset>
<?php
	echo $this->Form->submit('Pay now',array('id'=>'paypal-pay')); 
?>
</div>
<!-- PayPal payment -->
<?php echo $this->Html->script('https://www.paypalobjects.com/js/external/dg.js',false); ?>
<script>
var dg = new PAYPAL.apps.DGFlow({
	// the HTML ID of the form submit button which calls setEC
	trigger: 'paypal-pay',
	// the experience type: instant or mini
	expType: 'instant'
});
</script>