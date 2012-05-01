<div class="codes view">
<h2><?php  echo __('Code');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($code['Code']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Upload'); ?></dt>
		<dd>
			<?php echo $this->Html->link($code['Upload']['name'], array('controller' => 'uploads', 'action' => 'view', $code['Upload']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Token'); ?></dt>
		<dd>
			<?php echo h($code['Code']['token']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($code['Code']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($code['Code']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($code['Code']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Code'), array('action' => 'edit', $code['Code']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Code'), array('action' => 'delete', $code['Code']['id']), null, __('Are you sure you want to delete # %s?', $code['Code']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Codes'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Code'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads'), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload'), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
