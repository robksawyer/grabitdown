<?php 
	echo $this->Html->script('https://www.paypalobjects.com/js/external/dg.js',false);
?>
<div class="uploads back">
	<h1>This window will close in 3 seconds.</h1>
</div>
<script language="javascript">
	setTimeout('delayedRedirect()', 3000);
	//parent.opener.location.reload(); //Reload original page
	//Redirect the user to the admin area
	function delayedRedirect(){
		if (window.opener){
			window.close();
		}else if (top.dg.isOpen() == true){
			top.dg.closeFlow();
		}
	    parent.opener.location = "/users/login";
	}
</script>