<div class="codes form">
<?php echo $this->Form->create('Code');?>
	<fieldset>
		<legend><?php echo __('Add Code'); ?></legend>
	<?php
		echo $this->Form->input('upload_id');
		echo $this->Form->input('token');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Codes'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
