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
		echo $this->Form->input('fileName', array('type' => 'file'));
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Upload'));?>
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