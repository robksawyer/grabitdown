<div class="uploads view">
<h2><?php  echo __('Upload - '.$upload['Upload']['id']);?></h2>
	<?php
		$test_link = Router::url(array('controller'=>'uploads','action'=>'getit',$upload['Upload']['test_token']),true);
	?>
	<div id="upload-overview">
		<p class="test-url">You can test your download using <?php echo $this->Html->link($test_link,$test_link,array('target'=>'_blank')); ?></p>
		<ul>
			<li>Total Downloads: </li>
			<li>Active Codes: <?php echo $this->Number->format($active_codes); ?></li>
		</ul>
	</div>
	
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Path'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['path']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Path Alt'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['path_alt']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Caption'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['caption']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Slug'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['slug']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($upload['User']['custom_path'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Test Token'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['test_token']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Test Token Count'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['test_token_count']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($upload['Upload']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Upload'), array('action' => 'edit', $upload['Upload']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Upload'), array('action' => 'delete', $upload['Upload']['id']), null, __('Are you sure you want to delete # %s?', $upload['Upload']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Codes'), array('controller' => 'codes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Code'), array('controller' => 'codes', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Codes');?></h3>
	<?php if (!empty($upload['Code'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Upload Id'); ?></th>
		<th><?php echo __('Token'); ?></th>
		<th><?php echo __('Active'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($upload['Code'] as $code): 
			$code = $code['Code'];
		?>
		<tr>
			<td><?php echo $code['id'];?></td>
			<td><?php echo $code['upload_id'];?></td>
			<td><?php echo $code['token'];?></td>
			<td><?php echo $code['active'];?></td>
			<td><?php echo $code['modified'];?></td>
			<td><?php echo $code['created'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'codes', 'action' => 'view', $code['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'codes', 'action' => 'edit', $code['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'codes', 'action' => 'delete', $code['id']), null, __('Are you sure you want to delete # %s?', $code['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		echo $this->Html->link('See all', array('controller'=>'codes','action'=>'index',$upload['Upload']['id']));
	?>
	</div>
<?php endif; ?>
	
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Code'), array('controller' => 'codes', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
