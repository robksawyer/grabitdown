<div class="codes form">
<?php echo $this->Form->create('Code');?>
	<fieldset>
		<legend><?php echo __('Edit Code'); ?></legend>
	<?php
		echo $this->Form->input('id');
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

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Code.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Code.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Codes'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
