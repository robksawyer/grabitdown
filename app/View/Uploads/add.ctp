<div class="uploads form">
<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
	<fieldset>
		<legend><?php echo __('Do it!'); ?></legend>
	<?php
		echo $this->Form->input('User.custom_path', array(
				'label' => 'Custom URL (This will be in the final file URL.)'
			)
		);
		echo $this->Form->input('User.email', array(
							'label' => __('E-mail (used as login)',true)
						)
					);
		echo $this->Form->input('User.tos', array(
					'label' => __('I have read and agreed to ') . $this->Html->link(__('Terms of Service'), array('controller' => 'pages', 'action' => 'tos')), 
					'error' => __('You must verify you have read the Terms of Service')
					)
				);
		$options = array('10'=>'10','100'=>'100','1000'=>'1,000','10000'=>'10,000','100000'=>'100,000');
		echo $this->Form->input('total_codes',array(
			'type' => 'select',
			'options' => $options,
			'selected' => 0,
			'label' => 'Total codes to generate',
			'before' => '<ul class="info"><li>10 = <span class="price">$5</span></li><li>100 = <span class="price">$25</span></li><li>1,000 = <span class="price">$50</span></li><li>10,000 = <span class="price">$125</span></li><li>100,000 = <span class="price">$250</span></li></ul>'
		));
		echo $this->Form->input('fileName', array('type' => 'file'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Upload'));?>
</div>
<div class="actions" style="display: none;">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Uploads'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Codes'), array('controller' => 'codes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Code'), array('controller' => 'codes', 'action' => 'add')); ?> </li>
	</ul>
</div>
