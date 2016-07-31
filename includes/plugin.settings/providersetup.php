<div class="providersetup">
<form method="post" id="wsl_setup_form" action="options.php"> 

	<?php settings_fields( 'wsl-settings-group' ); 
	$assets_base_url1 = MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL . '/assets';
	?>

<p style="margin:10px;line-height: 22px;" align="justify">
Except for OpenID providers, each social network and identities provider will require that you create an external application linking your Web site to theirs apis. These external applications ensures that users are logging into the proper Web site and allows identities providers to send the user back to the correct Web site after successfully authenticating their Accounts.

</p>
	
<ul style="list-style:circle inside;margin-left:30px;">
	<li style="color: #000000;font-size: 14px;">To correctly setup these Identity Providers please carefully follow the help section of each one.</li>
	<li style="color: #000000;font-size: 14px;">If a <b>Provider Satus</b> is set to <b style="color:red">NO</b> then users will not be able to login with that provider on you website.</li>
</ul>

<br />

<?php 
	foreach( $WORDPRESS_SOCIAL_LOGIN_PROVIDERS_CONFIG AS $item ):
		$provider_id                = @ $item["provider_id"];
		$provider_name              = @ $item["provider_name"];

		$require_client_id          = @ $item["require_client_id"];
		$provide_email              = @ $item["provide_email"];
		
		$provider_new_app_link      = @ $item["new_app_link"];
		$provider_userguide_section = @ $item["userguide_section"];

		$provider_callback_url      = "" ;

		if( isset( $item["callback"] ) && $item["callback"] ){
			$provider_callback_url  = '<span style="color:green">' . WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL	 . '?hauth.done=' . $provider_id . '</span>';
		}

		$setupsteps = 0;

		$assets_base_url = MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL . '/assets/img/16x16/';
?> 
	<h3 style="margin-left:10px;"><img alt="<?php echo $provider_name ?>" title="<?php echo $provider_name ?>" src="<?php echo $assets_base_url . strtolower( $provider_id ) . '.png' ?>" style="vertical-align: top;" /> <span><?php echo $provider_name ?></span> <a class="providersetup_inline" href="#colorbox_<?php echo $provider_name ?>"></a></h3> 
    <div style="display:none">
    <div id="colorbox_<?php echo $provider_name ?>">
    <iframe width="560" height="315" src="//www.youtube.com/embed/qVIwHGI2e1U" frameborder="0" allowfullscreen></iframe>
    </div>
    
    </div>
		<div class="cfg">
		   <div class="cgfparams">
			  <ul>
			  <?php if ( $provider_id == "Infusionsoft" ) { ?>
				 <li><label>Log users to infusionsoft?</label>
					<select name="<?php echo 'wsl_settings_' . $provider_id . '_enabled' ?>">
						<option value="1" <?php if(   get_option( 'wsl_settings_' . $provider_id . '_enabled' ) ) echo "selected"; ?> >Yes</option>
						<option value="0" <?php if( ! get_option( 'wsl_settings_' . $provider_id . '_enabled' ) ) echo "selected"; ?> >No</option>
					</select>
				</li>
				<? } else { ?>
				 <li><label>Allow users to sign on with <?php echo $provider_name ?>?</label>
					<select name="<?php echo 'wsl_settings_' . $provider_id . '_enabled' ?>">
						<option value="1" <?php if(   get_option( 'wsl_settings_' . $provider_id . '_enabled' ) ) echo "selected"; ?> >Yes</option>
						<option value="0" <?php if( ! get_option( 'wsl_settings_' . $provider_id . '_enabled' ) ) echo "selected"; ?> >No</option>
					</select>
				</li>
				<? }?>

				<?php if ( $provider_new_app_link ){ ?>
            	
					<?php if ( $provider_id == "Infusionsoft" ) { ?>

					<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Application Name</label>
						<input type="text" class="inputgnrc"
						value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_applicationName' ); ?>"
						name="<?php echo 'wsl_settings_' . $provider_id . '_applicationName' ?>" ></li>
					<!--<li><label>Application PassPhrase</label>
						<input type="text" class="inputgnrc" 
							value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_passphrase' ); ?>"
							name="<?php echo 'wsl_settings_' . $provider_id . '_passphrase' ?>" ></li>-->
							<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Application Encrypted Key</label>
					<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><input type="text" class="inputgnrc" 
							value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_encryptedkey' ); ?>"
							name="<?php echo 'wsl_settings_' . $provider_id . '_encryptedkey' ?>" ></li>
                   
					<?php }elseif ( $require_client_id ){ // key or id ? ?>
						<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Application ID</label>
						<input type="text" class="inputgnrc"
						value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_app_id' ); ?>"
						name="<?php echo 'wsl_settings_' . $provider_id . '_app_id' ?>" ></li>
					<?php } else { ?>
						<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Application Key</label>
						<input type="text" class="inputgnrc" 
							value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_app_key' ); ?>"
							name="<?php echo 'wsl_settings_' . $provider_id . '_app_key' ?>" ></li>
					<?php }; ?>	 
				<?php if ( $provider_id != "Infusionsoft" ) { ?>
					<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Application Secret</label>
					<input type="text" class="inputgnrc"
						value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_app_secret' ); ?>" 
						name="<?php echo 'wsl_settings_' . $provider_id . '_app_secret' ?>" ></li>
						<li>

						<li style="<?php if(! get_option( 'wsl_settings_' . $provider_id . '_enabled' ))
				{ echo "display:none;"; } ?>"><label>Infusionsoft ActionSet ID</label>
						<input type="text" class="inputgnrc"
						value="<?php echo get_option( 'wsl_settings_' . $provider_id . '_actionset' ); ?>" 
						name="<?php echo 'wsl_settings_' . $provider_id . '_actionset' ?>" ></li>
				<?php }
				} // if require registration ?>
			  </ul> 
		   </div>
		   <div class="cgftip">

				<?php if ( $provider_new_app_link  ) : ?> 
					<?php if ( $provider_id == "Infusionsoft" ) {
					$appname=get_option('wsl_settings_' . $provider_id . '_applicationName');	
						 ?>
					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Go to <a href="<?php if($appname){echo "https://".$appname.".infusionsoft.com/app/miscSetting/itemWrapper?systemId=nav.admin&settingModuleName=Application&settingTabName=Application";} else {echo $provider_new_app_link; } ?>" target ="_blank"><?php echo $provider_new_app_link; ?></a> and generate your api keys.</p>				
					<? } else { ?>
				
					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Go to <a href="<?php echo $provider_new_app_link ?>" target ="_blank"><?php echo $provider_new_app_link ?></a> and <b>create a new application</b>.</p>

					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Fill out any required fields such as the application name and description.</p>
					<? }?>
					<?php if ( $provider_id == "google" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> On the <b>"Create Client ID"</b> popup switch to advanced settings by clicking on <b>(more options)</b>.</p>
					<?php endif; ?>	

					<?php if ( $provider_callback_url ) : ?>
						<p>
							<?php echo "<b>" . ++$setupsteps . "</b>." ?> Provide this URL as the Callback URL for your application:
							<br />
							<?php echo $provider_callback_url ?>
						</p>
					<?php endif; ?> 

					<?php if ( $provider_id == "MySpace" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Put your website domain in the <b>External Url</b> and <b>External Callback Validation</b> fields. This should match with the current hostname <em style="color:#CB4B16;"><?php echo $_SERVER["SERVER_NAME"] ?></em>.</p>
					<?php endif; ?> 

					<?php if ( $provider_id == "Live" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Put your website domain in the <b>Redirect Domain</b> field. This should match with the current hostname <em style="color:#CB4B16;"><?php echo $_SERVER["SERVER_NAME"] ?></em>.</p>
					<?php endif; ?> 

					<?php if ( $provider_id == "Facebook" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Put your website domain in the <b>Site Url</b> field. This should match with the current hostname <em style="color:#CB4B16;"><?php echo $_SERVER["SERVER_NAME"] ?></em>.</p> 
					<?php endif; ?>	

					<?php if ( $provider_id == "LinkedIn" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Put your website domain in the <b>Integration URL</b> field. This should match with the current hostname <em style="color:#CB4B16;"><?php echo $_SERVER["SERVER_NAME"] ?></em>.</p> 
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Set the <b>Application Type</b> to <em style="color:#CB4B16;">Web Application</em>.</p> 
					<?php endif; ?>	

					<?php if ( $provider_id == "Twitter" ) : ?>
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Put your website domain in the <b>Application Website</b> and <b>Application Callback URL</b> fields. This should match with the current hostname <em style="color:#CB4B16;"><?php echo $_SERVER["SERVER_NAME"] ?></em>.</p> 
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Set the <b>Application Type</b> to <em style="color:#CB4B16;">Browser</em>.</p> 
						<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Set the <b>Default Access Type</b> to <em style="color:#CB4B16;">Read</em>.</p> 
					<?php endif; ?>	
					<?php if ( $provider_id == "Infusionsoft" ) { ?>
					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Once you have got them, copy and paste the created application credentials into this setup page.</p>  
                    <p class="provider_action_set"><?php echo "<b>" . ++$setupsteps . "</b>." ?> Communication<br /><br />
                    <input type="checkbox" name="wsl_settings_Infusionsoft_checktags" class="check_tags" value="<?php echo get_option( 'wsl_settings_Infusionsoft_checktags' ); ?>"/><span>Tags</span>
                    <input type="checkbox" name="wsl_settings_Infusionsoft_checkaction" class="check_action" value="<?php echo get_option( 'wsl_settings_Infusionsoft_checkaction' ); ?>"/><span>Action Sets<label>(Legacy)</label></span>
                    </p>
					<? } else {?>
					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Once you have registered, copy and paste the created application credentials into this setup page.</p>  
					<? } ?>
					<?php if ( $provider_id != "Infusionsoft" ) { ?>
					<p><?php echo "<b>" . ++$setupsteps . "</b>." ?> Enter the infusionsoft tag number to be used for assigning users to which login they used.</p>  
					<? } ?>
				<?php else: ?>	
					<p>No registration required for OpenID based providers</p> 
				<?php endif; ?> 
				</div>
		</div>  
<br/>
<br/>
<br/>
<?php
	endforeach;
?>
	<br /> 
	<div style="margin-left:30px;">
		
		<br />
		<input type="submit" class="inputsave" value="Save" /> 
	</div> 

</form>

</div>