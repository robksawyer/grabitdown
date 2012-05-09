<div class="uploads form">
<?php echo $this->Form->create('Code');?>
	<fieldset>
		<legend><?php echo __('Your file should download shortly.'); ?></legend>
	<?php
		//echo $this->Form->input('user_location',array('type'=>'hidden'));
		echo $this->Form->input('comment');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Add Comment'));?>
</div>

<script type="text/javascript">
/*google.load("maps", "3",  {callback: initialize, other_params:"sensor=false"});
function initialize() {
  // Initialize default values
  var latlng; var location;

  // If ClientLocation was filled in by the loader, use that info instead
  if (google.loader.ClientLocation) {
    latlng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
    location = getFormattedLocation();
  }
  document.getElementById("CodeUserLocation").value = location;
}

function getFormattedLocation() {
  if (google.loader.ClientLocation.address.country_code == "US" &&
    google.loader.ClientLocation.address.region) {
    return google.loader.ClientLocation.address.city + ", " 
        + google.loader.ClientLocation.address.region.toUpperCase();
  } else {
    return  google.loader.ClientLocation.address.city + ", "
        + google.loader.ClientLocation.address.country_code;
  }
}*/
</script>