<div class="uploads index">
	<h2><?php echo __('Your Uploads');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<!--<th><?php //echo $this->Paginator->sort('path');?></th>-->
			<!--<th><?php //echo $this->Paginator->sort('user_id');?></th>-->
			<th><?php echo $this->Paginator->sort('test_token');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('upload date');?></th>
	</tr>
	<?php
	foreach ($uploads as $upload): ?>
	<tr>
		<td><?php echo $this->Html->link($upload['Upload']['name'], array('action' => 'view', $upload['Upload']['id']),array('title'=>'Click for more details about the upload.')); ?>&nbsp;</td>
		<!--<td><?php //echo h($upload['Upload']['path']); ?>&nbsp;</td>-->
		<!--<td>
			<?php //echo $this->Html->link($upload['User']['custom_path'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])); ?>
		</td>-->
		<td><?php 
			$test_link = Router::url(array('controller'=>'codes','action'=>'getit',$upload['User']['custom_path'],$upload['Upload']['id'],$upload['Upload']['test_token']),true);
			echo $this->Html->link($upload['Upload']['test_token'],$test_link,array('title'=>'Download the file')); 
		?>&nbsp;</td>
		<td><?php echo h($upload['Upload']['active']); ?>&nbsp;</td>
		<td><?php echo $this->Time->timeAgoInWords($upload['Upload']['created']); ?>&nbsp;</td>
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
		<li><?php echo $this->Html->link(__('New Upload'), array('action' => 'add')); ?></li>
	</ul>
</div>
