<div class="uploads back">
		<div style="margin-top:5px; font-face: Arial sans-serif; font-size: 12px;">You will be redirected to the new page in <span id="redirect_count">5</span> seconds</div>
</div>
<script language="Javascript">
<!-- 
	//parent.opener.location.reload(); //Reload original page
	
	//change the second to start counting down from 
	var countdownfrom = 10; //in seconds
	var currentsecond = document.getElementById("redirect_count").innerHTML = countdownfrom+1;
	function countdownRedirect(){ 
		if (currentsecond != 1){ 
			currentsecond -= 1; 
			document.getElementById("redirect_count").innerHTML = currentsecond;
		} else { 
			doRedirect();
			return; 
		} 
		setTimeout("countdownRedirect()",1000) 
	}
	
	//Redirect the user to the admin area
	function doRedirect(){
		if (window.opener){
			window.close();
		}else if (top.dg.isOpen() == true){
			top.dg.closeFlow();
		}
	   parent.opener.location = "/users/add";
	}
	
	//Start the counter
	countdownRedirect();
	
//-->
</script>