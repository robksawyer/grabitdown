<div class="users index">
	<h2><?php echo __('Users');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('custom_path');?></th>
			<th><?php echo $this->Paginator->sort('slug');?></th>
			<th><?php echo $this->Paginator->sort('passwd');?></th>
			<th><?php echo $this->Paginator->sort('password_token');?></th>
			<th><?php echo $this->Paginator->sort('email');?></th>
			<th><?php echo $this->Paginator->sort('email_authenticated');?></th>
			<th><?php echo $this->Paginator->sort('email_token');?></th>
			<th><?php echo $this->Paginator->sort('email_token_expires');?></th>
			<th><?php echo $this->Paginator->sort('tos');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('last_login');?></th>
			<th><?php echo $this->Paginator->sort('last_activity');?></th>
			<th><?php echo $this->Paginator->sort('is_admin');?></th>
			<th><?php echo $this->Paginator->sort('role');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	foreach ($users as $user): ?>
	<tr>
		<td><?php echo h($user['User']['id']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['custom_path']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['slug']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['passwd']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['password_token']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['email']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['email_authenticated']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['email_token']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['email_token_expires']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['tos']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['active']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['last_login']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['last_activity']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['is_admin']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['role']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['created']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
