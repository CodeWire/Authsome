<style>
.wsl_aside { 
    float: right;
    margin: 6px; 
    margin-top:0px;
    margin-right:10px;
    position: relative;
    width: 250px;
    z-index: 200;
} 
.wsl_help {
    line-height: 1;
    padding: 8px;
	
	background-color: #FFFFE0;
	border:1px solid #E6DB55; 
	border-radius: 3px;
	padding: 10px; 
}
.wsl_notice {
    line-height: 1;
    padding: 8px; 
	background-color: #EDEFF4;
	border:1px solid #6B84B4; 
	border-radius: 3px;
	padding: 10px;      
}
.wsl_alert {
    line-height: 1;
    padding: 8px; 
	background-color: #FFEBE8;
	border:1px solid #CC0000; 
	border-radius: 3px;
	padding: 10px;      
}

.wsl_asideImages{  float: right;
    margin: 6px; 
    margin-top:0px;
    margin-right:10px;
    position: relative;
    width: 272px;
    z-index: 200;}
</style>
<div style="float:right;">

<?
$includePath = "http://www.mytiein.com/plugins/mytiein_infusionsoft_social_login/sidebar.php";
$exists = remoteFileExists($includePath);
if ($exists) {
   echo get_data($includePath);
} 
?>
<?php  
	if( get_option( 'wsl_settings_development_mode_enabled' ) ){
?>
<div style="clear:both" class="wsl_alert wsl_aside">
    <h3 style="margin: 0 0 5px;">Warning</h3>

	<p style="line-height: 19px;">
		<b>Development Mode is On</b> 
		<br />
		Its recommend to <b style="color:red">disable</b> the <a href="options-general.php?page=mytiein-infusionsoft-social-login&wslp=3">development mode </a> on production. 
	</p>
</div>
<?php
	}
?>


<?php 
	$nok = true;

	foreach( $WORDPRESS_SOCIAL_LOGIN_PROVIDERS_CONFIG AS $item ){
		$provider_id = @ $item["provider_id"];
		
		if( get_option( 'wsl_settings_' . $provider_id . '_enabled' ) ){
			$nok = false;
		}
	}

	if( $nok ){
?>
<div style="clear:both" class="wsl_alert wsl_aside">
    <h3 style="margin: 0 0 5px;">Important</h3>

	<p style="line-height: 19px;">
		<b>No provider registered yet!</b> 
		<br />
		Please go to <b><a href="options-general.php?page=mytiein-infusionsoft-social-login&wslp=4">Providers setup</a></b> to get started.
	</p>
</div>
<?php
	}
?>
</div>