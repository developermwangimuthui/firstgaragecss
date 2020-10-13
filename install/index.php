<?php
define('DIR_APPLICATION', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SMS', str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/');
$success_token = '';
$base_url = home_base_url();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$options = array(
		'server'		=> $base_url,
		'root'			=> DIR_SMS,
		'db_host'		=> trim($_POST['txtHostName']),
		'db_user'		=> trim($_POST['txtDBUserName']),
		'db_password'	=> trim($_POST['txtPassword']),
		'db_name'		=> trim($_POST['txtDBName'])
	);
	if(importDatabase(trim($_POST['txtHostName']),trim($_POST['txtDBName']),trim($_POST['txtDBUserName']),trim($_POST['txtPassword']))){
		write_config_files($options);
		$success_token = 'GARAGE MANAGEMENT SYSTEM SETUP SUCCESSFULLY <br/><a href="'.$base_url.'">Go to Website</a>';
	}
	else{
		$success_token = 'Error Occured Please Enter Valid Database Access Information !!!!!';
	}
}

function importDatabase($mysql_host,$mysql_database,$mysql_user,$mysql_password){
	$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
	$query = file_get_contents("sako_wms.sql");
	$stmt = $db->prepare($query);
	if ($stmt->execute())
		 return true;
	else 
		 return false;
}

function write_config_files($options) {
	$output  = '<?php' . "\n";
	//$output .= 'define(\'CURRENCY\', \'$\');' . "\n";
	$output .= 'define(\'WEB_URL\', \'' . $options['server'] . '\');' . "\n";
	$output .= 'define(\'ROOT_PATH\', \'' . $options['root'] . '\');' . "\n\n\n";
	
	$output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($options['db_host']) . '\');' . "\n";
	$output .= 'define(\'DB_USERNAME\', \'' . addslashes($options['db_user']) . '\');' . "\n";
	$output .= 'define(\'DB_PASSWORD\', \'' . addslashes($options['db_password']) . '\');' . "\n";
	$output .= 'define(\'DB_DATABASE\', \'' . addslashes($options['db_name']) . '\');' . "\n";
	$output .= '$link = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD) or die(mysql_error());';
	$output .= 'mysql_select_db(DB_DATABASE, $link) or die(mysql_error());';
	$output .= '?>';

	$file = fopen($options['root'] . 'config.php', 'w');

	fwrite($file, $output);

	fclose($file);
}

function home_base_url(){   
	$base_url = (isset($_SERVER['HTTPS']) &&
	$_SERVER['HTTPS']!='off') ? 'https://' : 'http://';
	$tmpURL = dirname(__FILE__);
	$tmpURL = str_replace(chr(92),'/',$tmpURL);
	$tmpURL = str_replace($_SERVER['DOCUMENT_ROOT'],'',$tmpURL);
	$tmpURL = ltrim($tmpURL,'/');
	$tmpURL = rtrim($tmpURL, '/');
	$tmpURL = str_replace('install','',$tmpURL);
	$base_url .= $_SERVER['HTTP_HOST'].'/'.$tmpURL;
	return $base_url; 
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SAKO AMS INSTALL</title>
</head>
<body style="background-image:url('bg.jpg');-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;">
<br/>
<br/>
<div align="center" style="width:450px;margin:0 auto;padding:0;background-color:#000; opacity: 0.8;padding:20px;border:solid 5px #ee2050;">
  <div align="center"><a href="http://sakosys.com" target="_blank"><img style="width:70%;" src="logo.png" /></a></div>
  <br/>
  <div style="font-weight:bold;font-size:22px;text-align:center;text-decoration:none;color:#ee2050;"> Garage Management System Setup Wizard </div>
  <br/>
  <?php if($success_token == ''){ ?>
  <fieldset>
  <legend style="font-weight:bold;color:#ee2050;font-size:18px;">Path Details</legend>
  <table align="center" style="color:#fff;">
    <tr>
      <td>URL : </td>
      <td><input type="text" size="50" name="txtUrl" id="txtUrl" value="<?php echo $base_url; ?>" /></td>
    </tr>
    <tr>
      <td>Root Path : </td>
      <td><input type="text" size="50" name="txtDocRoot" id="txtDocRoot" value="<?php echo DIR_SMS; ?>" /></td>
    </tr>
  </table>
  </fieldset>
  <br/>
  <fieldset>
  <legend style="font-weight:bold;color:#ee2050;font-size:18px;">Enter Database Details</legend>
  <form method="post">
    <table align="center" style="color:#fff;">
      <tr>
        <td>Host Name : </td>
        <td><input type="text" name="txtHostName" value="<?php echo $_SERVER['SERVER_NAME']; ?>" id="txtHostName" />
          &nbsp;<span style="color:red;font-weight:bold;">*</span></td>
      </tr>
      <tr>
        <td>Database UserName : </td>
        <td><input type="text" name="txtDBUserName" id="txtDBUserName" />
          &nbsp;<span style="color:red;font-weight:bold;">*</span></td>
      </tr>
      <tr>
        <td>Database Password : </td>
        <td><input type="text" name="txtPassword" id="txtPassword" />
          &nbsp;<span style="color:red;font-weight:bold;">*</span></td>
      </tr>
      <tr>
        <td>Database Name : </td>
        <td><input type="text" name="txtDBName" id="txtDBName" />
          &nbsp;<span style="color:red;font-weight:bold;">*</span></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input style="background:yellow;font-weight:bold;border:solid 2px #ee2050;" type="submit" value="Start Setup" /></td>
      </tr>
    </table>
  </form>
  </fieldset>
  <br/>
  <div style="color:yellow;">** After installtion success delete install folder from application root path.</div>
  <br/>
  <div align="center"><a target="_blank" href="http://sakosys.com" style="text-decoration:none;color:#ee2050;font-size:13px;">Copyright © 2014-2017 sakosys.com. All rights reserved. </a></div>
  <?php } else { ?>
  <div style="color:yellow;text-align:center;font-size:20px;font-weight:bold;">Setup Done successfully.</div>
  <br/><br/>
  <div style="float:left;margin:10px;text-align:center;"><img src="front.png" /><br/><br/><a target="_blank" href="<?php echo $base_url; ?>" style="color:yellow;font-weight:bold;">Visit Front</a></div>
  <div style="float:left;margin:10px;"><img src="admin.png" /><br/><br/><a target="_blank" href="<?php echo $base_url; ?>admin.php" style="color:yellow;font-weight:bold;">Visit Admin</a></div>
  <div style="clear:both;"></div>
  <?php } ?>
</div>
</body>
</html>
