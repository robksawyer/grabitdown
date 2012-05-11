<div class="download form">
<?php //echo $this->Form->create('Code');?>
	<fieldset>
		<!--<legend><?php //echo __('Your file should download shortly.'); ?></legend>-->
	<?php
		//echo $this->Form->input('user_location',array('type'=>'hidden'));
		//echo $this->Form->input('comment');
	?>
	<div class="actions">
		<ul>
			<li class="file-details"><?php 
				//Find the correct icon
				$audio_types = array('aif','aifc','aiff','au','kar','mid','midi','mp2','mp3','m4a','m4b','m4p','mpga','ra','ram','rm','rpm','snd','tsi','wav','wma');
				$package_types = array('gz','gtar','z','tgz','zip','rar','rev','tar','7z');
				$icon_path = '';
				foreach ($audio_types as $type) {
					if($type == $upload['Upload']['ext']){
						$icon_path = 'icons/icon_audio.gif';
						break;
					}
				}
				if(empty($icon_path)){
					foreach ($package_types as $type) {
						if($type == $upload['Upload']['ext']){
							$icon_path = 'icons/icon_zip.gif';
							break;
						}
					}
				}
				if(!empty($icon_path)) echo $this->Html->image($icon_path).' ';
				echo $this->Text->truncate($upload['Upload']['name'])." (".$upload['Upload']['filesize'].")"; ?>
			<li><?php echo $this->Html->link(__('Download'), array('controller' => 'codes', 'action' => 'sendFile',$token,$upload['Upload']['id'])); ?> </li>
		</ul>
		<div class="clear"></div>
		<div class="file-expiration">
			Your file will be available online until <span><?php 
			$creation_date = $this->Time->fromString($upload['Upload']['created']);
			$expiration_date = date("F jS, Y",strtotime("+6 months",$creation_date));
			echo $expiration_date; 
			?></span>
		</div>
	</div>
	</fieldset>
<?php //echo $this->Form->submit(__('Add Comment'));?>
</div>