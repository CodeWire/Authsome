<form method="post" id="wsl_setup_form" action="options.php">  
<?php settings_fields( 'wsl-settings-group' ); ?>

<table width="600" border="0" cellpadding="5" cellspacing="2" >

  <tr>
    <td width="135" align="right"> License Key:</td>
    <td>

<?php
	$license = get_option( 'wsl_licensekey' );

?>
	<input type="text" class="inputgnrc" style="padding: 4px;width: 400px;" value="<?php echo $license; ?>" name="wsl_licensekey" >
    
    </td>
  </tr>


  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" class="button-primary" value="Save" /> </td>
  </tr>
</table>

</form>