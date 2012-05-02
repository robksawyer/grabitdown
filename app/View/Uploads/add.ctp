<div class="uploads form">
<?php echo $this->Form->create('Upload', array('type' => 'file'));?>
	<fieldset>
		<legend><?php echo __('Add your track or an archive of \'em all.'); ?></legend>
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
<div class="actions">
	<h3><?php echo __('Already have an account?'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?> </li>
	</ul>
</div>
<div class="footnote">
	<p>The following filetypes are accepted: aif,aifc,aiff,au,kar,mid,midi,mp2,mp3,mpga,ra,ram,rm,rpm,snd,tsi,wav,wma,gz,gtar,z,tgz,zip,rar,rev,tar,7z</p>
</div>