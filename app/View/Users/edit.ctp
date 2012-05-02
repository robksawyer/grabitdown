<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		//echo $this->Form->input('id');
		echo $this->Form->input('fullname');
		//echo $this->Form->input('email');
		//echo $this->Form->input('custom_path'); //Changing this will need to update all file paths. This could cause problems if the user has already shared the links with everyone. I'd have to do some kind of fancy redirect.
		//echo $this->Form->input('slug');
	?>
	<h2><?php echo __("Change your password"); ?></h2>
	<?php
		echo $this->Form->input('old_password',array('label'=>'Old password'));
		echo $this->Form->input('new_passwd',array('label'=>'New password'));
		echo $this->Form->input('new_passwd_confirm',array('label'=>'Confirm your new password'));
		//echo $this->Form->input('email_authenticated');
		//echo $this->Form->input('email_token');
		//echo $this->Form->input('email_token_expires');
		//echo $this->Form->input('tos');
		//echo $this->Form->input('active');
		//echo $this->Form->input('last_login');
		//echo $this->Form->input('last_activity');
		//echo $this->Form->input('is_admin');
		//echo $this->Form->input('role');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('User.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('User.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
