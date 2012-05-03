<?php
/**
 * Copyright 2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<fieldset>
	<legend><?php echo __('Log in to your account'); ?></legend>
	<?php
		echo $this->Form->create('User');
		echo $this->Form->input('email', array('label' => __d('users', 'Email', true)));
		echo $this->Form->input('passwd', array('label' => __d('users', 'Password', true),
															'after'=>'<div>Forgot your password? Reset it '.$this->Html->link('here',array('admin'=>false,'controller'=>'users','action'=>'request_password_change')
															).'.</div>'
														)
													);
		//echo __d('users', 'Remember Me') . $this->Form->checkbox('remember_me');
		//echo $this->Form->hidden('User.return_to', array('value' => $return_to));
		echo $this->Form->end(__d('users', 'Login', true));
	?>
</fieldset>
</div>
<div class="actions">
	<h3><?php echo __('Don\'t have an account?'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Set one up'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>