<div class="uploads back">
	<h1>This window will close in 3 seconds.</h1>
</div>
<script language="javascript">
	setTimeout('delayedRedirect()', 3000);
	//Redirect the user to the add area
	function delayedRedirect(){
		if (window.opener){
			window.close();
		}else if (top.dg.isOpen() == true){
			top.dg.closeFlow();
		}
	    parent.opener.location = "/uploads/add";
	}
</script>