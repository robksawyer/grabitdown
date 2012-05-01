<?php echo $this->Html->script('https://www.paypalobjects.com/js/external/dg.js',false); ?>
<script>
	if (window.opener){
		window.close();
	}else if (top.dg.isOpen() == true){
		top.dg.closeFlow();
	}
	parent.opener.location.reload();
</script>