<div class="codes index">
	<h2><?php echo __('You are viewing the codes for upload <span class="upload-name">'.$upload['Upload']['name'].'</span>.');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<!--<th><?php //echo $this->Paginator->sort('id');?></th>-->
			<!--<th><?php //echo $this->Paginator->sort('upload_id');?></th>-->
			<th><?php echo $this->Paginator->sort('token');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('last_download_time');?></th>
			<th><?php echo $this->Paginator->sort('downloaded_count');?></th>
			<th><?php echo $this->Paginator->sort('ipAddress');?></th>
			<th><?php echo $this->Paginator->sort('User\'s Location');?></th>
			<!--<th><?php //echo $this->Paginator->sort('comment');?></th>-->
			<!--<th class="actions"><?php //echo __('Actions');?></th>-->
	</tr>
	<?php
	foreach ($codes as $code): ?>
	<tr>
		<!--<td><?php //echo h($code['Code']['id']); ?>&nbsp;</td>-->
		<!--<td>
			<?php //echo $this->Html->link($code['Upload']['name'], array('controller' => 'uploads', 'action' => 'view', $code['Upload']['id'])); ?>
		</td>-->
		<td><?php echo $this->Html->link($code['Code']['token'],array('controller'=>'codes','action'=>'getit',$upload['User']['custom_path'],$code['Upload']['id'],$code['Code']['token'])); ?>&nbsp;</td>
		<td><?php echo h($code['Code']['active']); ?>&nbsp;</td>
		<td><?php echo h($code['Code']['last_download_time']); ?>&nbsp;</td>
		<td><?php echo h($code['Code']['download_count']); ?>&nbsp;</td>
		<td><?php echo h($code['Code']['ipAddress']); ?>&nbsp;</td>
		<td><?php 
			$location = ucwords(strtolower($code['cityName'].', '.$code['regionName'].' '.$code['countryName']));
			echo $location;
		?></td>
		<!--<td><?php //echo h($code['Code']['comment']); ?>&nbsp;</td>-->
		<!--<td class="actions">
			<?php //echo $this->Html->link(__('View'), array('action' => 'view', $code['Code']['id'])); ?>
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $code['Code']['id'])); ?>
			<?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $code['Code']['id']), null, __('Are you sure you want to delete # %s?', $code['Code']['id'])); ?>
		</td>-->
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
		<li><?php echo $this->Html->link(__('Add More Codes'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Your Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
