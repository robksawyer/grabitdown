<div class="uploads form">
	<h2><?php echo __("How many download codes would you like to buy?",true); ?></h2>
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
	echo $this->Form->input('id',array('value'=>$upload_id,'type'=>'hidden'));
	echo $this->Form->input('user_id',array('value'=>$user_id,'type'=>'hidden'));
?>
	</fieldset>
<?php
	echo $this->Form->submit('Cancel',array('name'=>'cancel','id'=>'cancel-pay','div' => false));
	echo $this->Form->submit('Pay now',array('name'=>'ok','id'=>'paypal-pay','div' => false));
?>
</div>
<!-- PayPal payment -->
<?php echo $this->Html->script('https://www.paypalobjects.com/js/external/dg.js',false); ?>
<script language="Javascript">
var dg = new PAYPAL.apps.DGFlow({
	// the HTML ID of the form submit button which calls setEC
	trigger: 'paypal-pay',
	// the experience type: light or mini
	expType: 'mini'
});
</script>