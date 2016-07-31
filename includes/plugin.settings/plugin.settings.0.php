<?
add_action( 'admin_print_scripts', 'load_scripts' );
add_action( 'admin_print_styles' , 'load_styles' );
global $wpdb;
function check_license($licensekey,$localkey="") {
    $whmcsurl = "http://www.mytiein.com/members/";
    $licensing_secret_key = "Devel0per"; # Unique value, should match what is set in the product configuration for MD5 Hash Verification
    $check_token = time().md5(mt_rand(1000000000,9999999999).$licensekey);
    $checkdate = date("Ymd"); # Current date
    $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    $localkeydays = 5; # How long the local key is valid for in between remote checks
    $allowcheckfaildays = 15; # How many days to allow after local key expiry before blocking access if connection cannot be made
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n",'',$localkey); # Remove the line breaks
		$localdata = substr($localkey,0,strlen($localkey)-32); # Extract License Data
		$md5hash = substr($localkey,strlen($localkey)-32); # Extract MD5 Hash
        if ($md5hash==md5($localdata.$licensing_secret_key)) {
            $localdata = strrev($localdata); # Reverse the string
    		$md5hash = substr($localdata,0,32); # Extract MD5 Hash
    		$localdata = substr($localdata,32); # Extract License Data
    		$localdata = base64_decode($localdata);
    		$localkeyresults = unserialize($localdata);
            $originalcheckdate = $localkeyresults["checkdate"];
            if ($md5hash==md5($originalcheckdate.$licensing_secret_key)) {
                $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-$localkeydays,date("Y")));
                if ($originalcheckdate>$localexpiry) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    $validdomains = explode(",",$results["validdomain"]);
                    if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    $validips = explode(",",$results["validip"]);
                    if (!in_array($usersip, $validips)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    if ($results["validdirectory"]!=dirname(__FILE__)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $postfields["licensekey"] = $licensekey;
        $postfields["domain"] = $_SERVER['SERVER_NAME'];
        $postfields["ip"] = $usersip;
        $postfields["dir"] = dirname(__FILE__);
        if ($check_token) $postfields["check_token"] = $check_token;
        if (function_exists("curl_exec")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl."modules/servers/licensing/verify.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            $fp = fsockopen($whmcsurl, 80, $errno, $errstr, 5);
	        if ($fp) {
        		$querystring = "";
                foreach ($postfields AS $k=>$v) {
                    $querystring .= "$k=".urlencode($v)."&";
                }
                $header="POST ".$whmcsurl."modules/servers/licensing/verify.php HTTP/1.0\r\n";
        		$header.="Host: ".$whmcsurl."\r\n";
        		$header.="Content-type: application/x-www-form-urlencoded\r\n";
        		$header.="Content-length: ".@strlen($querystring)."\r\n";
        		$header.="Connection: close\r\n\r\n";
        		$header.=$querystring;
        		$data="";
        		@stream_set_timeout($fp, 20);
        		@fputs($fp, $header);
        		$status = @socket_get_status($fp);
        		while (!@feof($fp)&&$status) {
        		    $data .= @fgets($fp, 1024);
        			$status = @socket_get_status($fp);
        		}
        		@fclose ($fp);
            }
        }
        if (!$data) {
            $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-($localkeydays+$allowcheckfaildays),date("Y")));
            if ($originalcheckdate>$localexpiry) {
                $results = $localkeyresults;
            } else {
                $results["status"] = "Invalid";
                $results["description"] = "Remote Check Failed";
                return $results;
            }
        } else {
            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
            $results = array();
            foreach ($matches[1] AS $k=>$v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if ($results["md5hash"]) {
            if ($results["md5hash"]!=md5($licensing_secret_key.$check_token)) {
                $results["status"] = "Invalid";
                $results["description"] = "MD5 Checksum Verification Failed";
                return $results;
            }
        }
        if ($results["status"]=="Active") {
            $results["checkdate"] = $checkdate;
            $data_encoded = serialize($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate.$licensing_secret_key).$data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded.md5($data_encoded.$licensing_secret_key);
            $data_encoded = wordwrap($data_encoded,80,"\n",true);
            $results["localkey"] = $data_encoded;
        }
        $results["remotecheck"] = true;
    }
    unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
    return $results;
}


function get_data($url)
{
	$ch = curl_init();
	$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function remoteFileExists($url) {
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);

    return $ret;
}

define('REMOTE_VERSION', 'http://www.mytiein.com/plugins/mytiein_infusionsoft_social_login/version.txt');

// this is the version of the deployed script
define('VERSION', '1.0');

function isUpToDate()
{
	if(remoteFileExists(REMOTE_VERSION) == true){
		$remoteVersion=trim(file_get_contents(REMOTE_VERSION));
		if(VERSION == $remoteVersion){
			return 1;
		} else {
			return 0;
		}
	} else {
		return true;
	}
}
?>
<div id="msg" style="font-size:largest;">

Loading, please wait...
</div>
<div id="infusbody">
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/base/jquery-ui.css" rel="stylesheet" />
<link href="<?php echo MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL ?>/css/colorbox.css" rel="stylesheet"></link>
<link href="<?php echo MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL ?>/css/custom.css" rel="stylesheet"></link>
 <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
 <script src="<?php echo MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL ?>/js/cookie.js"></script>
 <script src="<?php echo MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL ?>/js/jquery.colorbox-min.js"></script>
 <script src="<?php echo MYTIEIN_INFUS_SOCIAL_LOGIN_PLUGIN_URL ?>/js/custom.js"></script>

<style type="text/css"> 
h3{
font-size: 1.17em;
margin: 1em 0;

display: inline-block;
}
#wsl_setup_form .inputgnrc, #wsl_setup_form select {
    font-size: 15px;
    padding: 6px 3px; 
    border: 1px solid #CCCCCC;
    border-radius: 4px 4px 4px 4px;
    color: #444444;
    font-family: arial;
    font-size: 16px;
    margin: 0;
    padding: 3px;
    width: 300px;
} 
#wsl_setup_form .inputsave {
    font-size: 15px;
    padding: 6px 3px;  
    color: #444444;
    font-family: arial;
    font-size: 18px;
    margin: 0;
    padding: 3px;
    width: 400px;
	height: 40px;
} 
#wsl_setup_form ul {
    list-style: none outside none; 
}
#wsl_setup_form .cgfparams ul {
    padding: 0;
	margin: 0;
}
#wsl_setup_form ul li {
    color: #555555;
    font-size: 13px;
    margin-bottom: 10px;
    padding: 0;
}
#wsl_setup_form ul li label {
    color: #000000;
    display: block;
    font-size: 14px;
    font-weight: bold;
	padding-bottom: 5px;
}
#wsl_setup_form .cfg { 
	background: #f5f5f5;
	float: right;
	border-radius: 2px;
	border: 1px solid #AAAAAA;
	margin: 0px;
	width:99%;
}
#wsl_setup_form .cgfparams {
   width: 330px;
   float: left;
   border-right: 1px solid black;
}
#wsl_setup_form .cgftip {
   padding-top: 1px;
	width: 411px;
	float:right;
} 

#footer {
    display:none; 
}
#wsl_setup_form p {
	font-size: 14px;
}
.wsl_label_notice {
    background-color: #BFBFBF; 
    border-radius: 3px 3px 3px 3px;
    color: #FFFFFF;
    font-size: 9.75px;
    font-weight: bold;
    padding: 1px 3px 2px;
    text-transform: uppercase;
    white-space: nowrap;
}
 .videoPlayer{
 border-left: 1px solid #AAA;
margin-left: 450px;
padding: 10px;
min-height: 202px;

padding-top: 1px;
width: 325px;
}
#wsl_setup_form .inputgnrc, #wsl_setup_form select { 
    font-size: 14px; 
}
#infusbody{
	display:none;
}
	
#tabs{
width: 755px;
float: left;
}
.ui-tabs .ui-tabs-panel {
padding:0px !important;
}
.licenseinvalid{
text-align: center;
display: block;
font-size: 17px;
line-height: 41px;
}
.wsl_update { 
    margin: 6px; 
    margin-top:0px;
    margin-right:10px;
    position: relative;
    width: 723px;
    z-index: 200;
} 
.wsl_notice {
    line-height: 1;
    padding: 8px; 
	background-color: #EDEFF4;
	border:1px solid #6B84B4; 
	border-radius: 3px;
	padding: 10px;      
}
.hiddenli li
{
	display:none !important
}
.hiddenli li:first-child, .providersetup .showli li
{
	display:block !important;
}
.providersetup #wsl_setup_form .cgfparams
{
	padding:10px 0 0 10px;
	width:320px;
}
.providersetup #wsl_setup_form .cgftip
{
	padding:0 0 0 10px;
	width:400px;
}
</style> 
<script type="text/javascript" charset="utf8" >
	jQuery(document).ready(function($) {
		var currenttab=0;
		if($.cookie("currenttab"))
		{
			currenttab=parseInt($.cookie("currenttab"));
		}
		else
		{
			currenttab=1;
		}
		$("#tabs li a").click(function(){
		var currenttab=parseInt($.cookie("currenttab",$(this).attr('alt')));
		})
		$("#tabs").tabs({active: currenttab});
	 	$('#infusbody').css('display','block');
    	$('#msg').hide();
    	
		
	});
</script>
<h2 style="padding-bottom: 10px;">MyTieIn Infusionsoft Social Login 
	<span class="wsl_label_notice">Beta</span>
	
<?php
	if( get_option( 'wsl_settings_development_mode_enabled' ) ){
		?>
			<small style="color:red;font-size: 14px;">(Development Mode On)</small>
		<?php
	}
?>
</h2>  
<?
$licensekey = get_option( 'wsl_licensekey' );
$localkey = get_option('localkeydata');

# The call below actually performs the license check. You need to pass in the license key and the local key data
$results = check_license($licensekey,$localkey);

if ($results["status"]=="Active") {
	$isactive = true;
    # Allow Script to Run
    if ($results["localkey"]) {
        # Save Updated Local Key to DB or File
        $localkeydata = $results["localkey"];
        $localkey = get_option('localkeydata');
        if(!$localkey){
        	add_option('localkeydata', $localkeydata);
        } else {
        	update_option('localkeydata', $localkeydata);
        }
    }
}
?>
<? 

if(isUpToDate() == false){?>
<div style="clear:both" class="wsl_notice  wsl_update">
    <h3 style="margin: 0 0 5px;">A new version is available.</h3>
	<p style="line-height: 19px;">
		Pleae login to your account at <a href="http://www.mytiein.com/portal/clientarea.php">MyTieIn</a> to grab for the latest version.
	</p>
</div>


<? } ?>

<div id="tabs" stye="width: 900px;">
	<ul class="tabNavigation">
		<li><a href="#tab1" alt="0">Overview</a></li>
		<li><a href="#tab2" alt="1">Providers setup</a></li>
		<li><a href="#tab3" alt="2">Customize</a></li>
		<li><a href="#tab4" alt="3">Insights</a></li>
		<li><a href="#tab5" alt="4">Diagnostics</a></li>
		<li><a href="#tab6" alt="5">Help</a></li>
	</ul>
	
	<div id="tab1">
		<?php include('overview.php');?>
	</div>
	<div id="tab2">
		<?php include('providersetup.php');?>	
	</div>
	<div id="tab3">
		<?php include('customizer.php');?>		
	</div>
	<div id="tab4">
		<?php include('insights.php');?>	
	</div>
	<div id="tab5">
		<?php include('diagnose.php');?>
	</div>
	<div id="tab6">
		<?php include('info.php');?>	
	</div>
	
	</div>
	<? if($isactive == true){?>
	<div style="float:right;">
	<?php include('sidebar.php');?>
	</div>
	<? }?>
</div>
