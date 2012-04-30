<div class="users view">
<h2><?php  echo __('User');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Slug'); ?></dt>
		<dd>
			<?php echo h($user['User']['slug']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Passwd'); ?></dt>
		<dd>
			<?php echo h($user['User']['passwd']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password Token'); ?></dt>
		<dd>
			<?php echo h($user['User']['password_token']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email Authenticated'); ?></dt>
		<dd>
			<?php echo h($user['User']['email_authenticated']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email Token'); ?></dt>
		<dd>
			<?php echo h($user['User']['email_token']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email Token Expires'); ?></dt>
		<dd>
			<?php echo h($user['User']['email_token_expires']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Tos'); ?></dt>
		<dd>
			<?php echo h($user['User']['tos']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($user['User']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Last Login'); ?></dt>
		<dd>
			<?php echo h($user['User']['last_login']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Last Activity'); ?></dt>
		<dd>
			<?php echo h($user['User']['last_activity']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Is Admin'); ?></dt>
		<dd>
			<?php echo h($user['User']['is_admin']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Role'); ?></dt>
		<dd>
			<?php echo h($user['User']['role']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($user['User']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Uploads');?></h3>
	<?php if (!empty($user['Upload'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Path'); ?></th>
		<th><?php echo __('Path Alt'); ?></th>
		<th><?php echo __('Caption'); ?></th>
		<th><?php echo __('Slug'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Test Token'); ?></th>
		<th><?php echo __('Test Token Count'); ?></th>
		<th><?php echo __('Active'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Upload'] as $upload): ?>
		<tr>
			<td><?php echo $upload['id'];?></td>
			<td><?php echo $upload['name'];?></td>
			<td><?php echo $upload['path'];?></td>
			<td><?php echo $upload['path_alt'];?></td>
			<td><?php echo $upload['caption'];?></td>
			<td><?php echo $upload['slug'];?></td>
			<td><?php echo $upload['user_id'];?></td>
			<td><?php echo $upload['test_token'];?></td>
			<td><?php echo $upload['test_token_count'];?></td>
			<td><?php echo $upload['active'];?></td>
			<td><?php echo $upload['modified'];?></td>
			<td><?php echo $upload['created'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'uploads', 'action' => 'view', $upload['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'uploads', 'action' => 'edit', $upload['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'uploads', 'action' => 'delete', $upload['id']), null, __('Are you sure you want to delete # %s?', $upload['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
