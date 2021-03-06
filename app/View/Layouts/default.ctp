<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 $siteName = 'Band Spreader';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $siteName; ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('main.css');
		
		//echo $this->Html->script('jquery-1.7.2.min'); // Include jQuery library
		
		//Google libraries
		echo $this->Html->script('https://www.google.com/jsapi');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<script type="text/javascript">
	  google.load("search", "1");
	  google.load("jquery", "1.4.2");
	  google.load("jqueryui", "1.7.2");
	</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link($siteName, '/'); ?></h1>
			<?php
			$hide_user_panel = false;
			//Check to make sure the panel should show on the view
			$ignored_views = array('/users/login','/uploads/add');
			foreach($ignored_views as $view){
				if($view == $this->request->here){
					$hide_user_panel = true;
				}
			}
			
			if(!$hide_user_panel):
			?>
			<div id="user-panel">
			<?php
			//Check to see if the user is logged in
			if(!empty($logged_in)):	
				echo 'Welcome '.$this->Html->link($current_user['fullname'],array('controller'=>'uploads','action'=>'index','admin'=>false))."! ".$this->Html->link('Logout',array('admin'=>false,'controller'=>'users','action'=>'logout'));
			else:
				echo $this->Html->link('Login',array('admin'=>false,'controller'=>'users','action'=>'login'));
				echo " | ".$this->Html->link('Create Account',array('admin'=>false,'controller'=>'uploads','action'=>'add'));
			endif; 
			?>
			</div>
			<?php endif; ?>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $siteName, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
	<?php 
		echo $this->Js->writeBuffer(); // Write cached scripts
		echo $this->element('sql_dump'); 
	?>
</body>
</html>
