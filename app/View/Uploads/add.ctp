<div class="uploads form">
<?php echo $this->Form->create('Upload');?>
	<fieldset>
		<legend><?php echo __('Add Upload'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('path');
		echo $this->Form->input('path_alt');
		echo $this->Form->input('caption');
		echo $this->Form->input('slug');
		echo $this->Form->input('user_id');
		echo $this->Form->input('test_token');
		echo $this->Form->input('test_token_count');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Uploads'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Codes'), array('controller' => 'codes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Code'), array('controller' => 'codes', 'action' => 'add')); ?> </li>
	</ul>
</div>
