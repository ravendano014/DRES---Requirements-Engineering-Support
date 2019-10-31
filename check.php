<?php
require_once("config.php");

function failure($msg, $suggestion="")
{
	echo "<font color=red><b>failure ".($msg?"[$msg]":"")."</b></font>";
	if ($suggestion)
	{
		echo "<br><br><font color=red><b>$suggestion</b></font>";
		closescript();
	}
}
function success($msg)
{
	echo "<font color=green><b>success ".($msg?"[$msg]":"")."</b></font>";
}
function phpinicheck($lib)
{
?>
	<li>check you have <b>php.ini</b> file copied into your Windows directory, base file is provided as <b>php.ini.dist</b> in your PHP installation's directory</li>
	<li>check <b>extension_dir</b> variable in <b>php.ini</b> is properly set (currently <b><?=ini_get("extension_dir") ?></b>), it should point to your PHP's <b>extensions</b> subdirectory</li>
	<li>check you have <b>extension=php_<?=$lib ?>.dll</b> option enabled in <b>[Extensions]</b> sectin or type it if not present, it shouldn't be commented with <b>;</b> in your <b>php.ini</b> file</li>
<?php
}
function closescript()
{
	echo "<hr><br><br><b>If you're having problems configuring environment please contact the <a href=\"mailto:krzysztof.kowalczykiewicz@cs.put.poznan.pl\">author</a> sending this page's contents</b><br><br><br>";
	echo "<hr><b>Dumping PHP information</b><br>";
	phpinfo();
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>DRES environment checker...</title>
	<link rel="stylesheet" href="css/site.css">
</head>
<body>
<hr>
<b>Current config.php configuration</b><br>
<?php
$SETTINGS = array(
	"VERSION",
	"TEMP_DIR",
	"DATASOURCE",
	"RM_XMLRPC_HOST",
	"RM_XMLRPC_PORT",
	"MYSQL_HOST",
	"MYSQL_USER",
	"MYSQL_PASSWORD",
	"MYSQL_DATABASE",
	"XSLT_MODE",
	"SABCMD_PATH",
	"DISPLAY_USERS_COMBO",
	"DISPLAY_PROJECTS_COMBO",
	"DEFAULT_PROJECT",
	"XMLDOM_VERSION",
	"SESSION_COOKIE",
	"USER_COOKIE",
	"PROJECT_COOKIE");
?>
<table border="0">
<?php foreach($SETTINGS as $setting): ?>
	<tr><td nowrap><b><?=$setting ?></b></td><td><?=strpos($setting,"PASSWORD")?"&lt;HIDDEN&gt;":constant($setting) ?></td></tr>
<?php endforeach; ?>
</table>
<hr>
<b>Checking PHP configuration settings</b><br>

<li>PHP version...</li>
<?php
$ver = phpversion();
if ($ver >= "4.1.0")
	success($ver);
else
	failure($ver, "download new version from <a href=\"http://www.php.net\">PHP website</a>");
?>
<li>DOMXML extension...</li>
<?php
$present = extension_loaded("domxml");
if ($present)
{
	$ver = domxml_version();
	if ($ver >= "2.4.0")
		success($ver);
	else
		failure($ver, "recompile your PHP with more recent version of DOMXML library or download new PHP version from <a href=\"http://www.php.net\">PHP website</a>");
}
else
{
	failure("required-extension not installed");
?>
	<br><br>
	<font color="red">
<?php if(substr(php_uname(), 0, 7) == "Windows"): ?>
	<?php phpinicheck("domxml") ?>
<?php else: ?>
	<li>recompile your PHP installation with <b>--with-dom</b> option</li>
	<li>you may need DOMXML library from <a href="http://www.xmlsoft.org/">GNOME libxml website</a> or other site distributing packages for your system</li>
<?php endif; ?>
	</font>
<?php
	closescript();
}
?>
<li>MySQL extension</li>
<?php
$required = (DATASOURCE == "mysql");
$present = extension_loaded("mysql");
if ($present >= $required)
{
	success(($required?"required":"not required")."-".($present?"present":"not present"));
}
else
{
	failure("required-extension not installed");
?>
	<br><br>
	<font color="red">
<?php if(substr(php_uname(), 0, 7) == "Windows"): ?>
	<?php phpinicheck("mysql") ?>
<?php else: ?>
	<li>recompile your PHP installation with <b>--with-mysql</b> option</li>
<?php endif; ?>
	</font>
<?php
	closescript();
}
?>
<li>XSLT extension...</li>
<?php
$required = (XSLT_MODE == "internal");
$present = extension_loaded("xslt");
if ($present>=$required)
{
	success(($required?"required":"not required")."-".($present?"present":"not present"));
}
else
{
	failure("required-extension not installed");
?>
	<br><br>
	<font color="red">
<?php if(substr(php_uname(), 0, 7) == "Windows"): ?>
	<?php phpinicheck("xslt") ?>
	<li>check you have <b>Sablotron</b> XSLT processor libraries accessible, they are located in PHP's <b>dlls</b> subdirectory</li>
	<ul>
		<li>you need to copy <b>sablot.dll</b>, <b>iconv.dll</b> files to your <b>windows\system</b> or <b>winnt\system32</b> directory</li>
		<li>alternatively you may download recent Windows <b>FullPack</b> from <a href="http://www.gingerall.com/charlie/ga/xml/d_sab.xml">Sablotron website</a></li>
		<li>alternatively you may download dll libraries from <a href="http://ophelia.cs.put.poznan.pl/xdre/dlls">DRES website</a></li>
	</ul>
	<li>check you have <b>Expat</b> library accessible, it is located in PHP's <b>dlls</b> subdirectory</li>
	<ul>
		<li>you need to copy <b>expat.dll</b> file to your <b>windows\system</b> or <b>winnt\system32</b> directory</li>
		<li>alternatively you may download <b>expat.dll</b> library from <a href="http://ophelia.cs.put.poznan.pl/xdre/dlls">here</a></li>
	</ul>
<?php else: ?>
	<li>recompile your PHP installation with <b>--enable-xslt</b>, --with-xslt-sablot option</li>
	<li>you may download Sablotron files from <a href="http://www.gingerall.com/charlie/ga/xml/d_sab.xml">Sablotron website</a></li>
	<li>additional required libraries are available as well on their <a href="http://www.gingerall.com/charlie/ga/xml/d_related.xml">site</a></li>
<?php endif; ?>
	</font>
<?php
	closescript();
}
?>
<li>XMLRPC extension...</li>
<?php
$required = (DATASOURCE == "xmlrpc");
$present = extension_loaded("xmlrpc");
if ($present>=$required)
{
	success(($required?"required":"not required")."-".($present?"present":"not present"));
}
else
{
	failure("required-extension not installed");
?>
	<br><br>
	<font color="red">
<?php if(substr(php_uname(), 0, 7) == "Windows"): ?>
	<?php phpinicheck("xmlrpc") ?>
<?php else: ?>
	<li>recompile your PHP installation with <b>--with-xmlrpc</b> option</li>
<?php endif; ?>
	</font>
<?php
	closescript();
}
?>
<li>Session support...</li>
<?php
$dir = ini_get("session.save_path");
$fname = $dir."/writetest.tmp";
if ($file = @fopen($fname, "w"))
{
	success($dir." writable");
	fclose($file);
	unlink($fname);
}
else
{
	failure($dir." not writable");
?>
	<br><br>
	<font color="red">
	<li>open your <b>php.ini</b> file and set <b>session.save_path</b> property to a valid writable directory, for instance <b>c:\windows\temp</b>, <b>c:\winnt\temp</b> or <b>/tmp</b></li>
	</font>
<?php
	closescript();
}
?>
<hr>
<b>Checking DRES configuration settings</b><br>
<li>Temporary directory writable...</li>
<?php
$dir = TEMP_DIR;
$fname = $dir."/writetest.tmp";
if ($file = @fopen($fname, "w"))
{
	success($dir." writable");
	fclose($file);
	unlink($fname);
}
else
{
	failure($dir." not writable");
?>
	<br><br>
	<font color="red">
	<li>open <b>config.php</b> file and set <b>TEMP_DIR</b> property to a valid writable directory, for instance <b>c:\windows\temp</b>, <b>c:\winnt\temp</b> or <b>/tmp</b></li>
	</font>
<?php
	closescript();
}
?>
<li>Cache directory writable...</li>
<?php
$dir = "./cache";
$fname = $dir."/writetest.tmp";
if ($file = @fopen($fname, "w"))
{
	success($dir." writable");
	fclose($file);
	unlink($fname);
}
else
{
	failure($dir." not writable");
?>
	<br><br>
	<font color="red">
	<li>make DRES' <b>cache</b> subdirectory writable using NTFS security settings or <b>chmod</b> command on Unix</li>
	</font>
<?php
	closescript();
}
?>
<li>Orpheus Requirements Server connectivity...</li>
<?php
$required = (DATASOURCE == "xmlrpc");
$present = (fsockopen(RM_XMLRPC_HOST, RM_XMLRPC_PORT,&$errno, &$errstr, 5) !== FALSE);
if ($present>=$required)
{
	success(($required?"required":"not required")."-".($present?"succeeded":"failed"));
}
else
{
	failure("required-server not running");
?>
	<br><br>
	<font color="red">
	<li>XMLRPC server connectivity is currently set to <b><?=RM_XMLRPC_HOST ?></b> on port <b><?=RM_XMLRPC_PORT ?></b></li>
	<li>check <b>Orpheus</b> instance is running on that machine and Requirements Module's XMLRPC port is set to <b><?=RM_XMLRPC_PORT ?></b></li>
	<li>switch <b>DATASOURCE</b> property in <b>config.php</b> file to <b>mysql</b> if you don't want to use <b>Orpheus</b> instance and work in stand-alone mode</li>
	</font>
<?php
	closescript();
}
?>
<li>MySQL connectivity...</li>
<?php
$required = (DATASOURCE == "mysql");
$msg="";
if ($required)
{
	eval("\$link=@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);");
	eval("\$msg=mysql_error();");
	if (!$msg)
	{
		eval("@mysql_select_db(MYSQL_DATABASE);");
		eval("\$msg=mysql_error();");
		if (!$msg)
		{
			eval("\$result=@mysql_query(\"SELECT * FROM requirements\");");
			eval("\$msg=mysql_error();");
			if (!$msg)
				$present = 1;
		}
	}
}
if ($present>=$required)
{
	success(($required?"required":"not required")."-".($present?"succeeded":"failed"));
}
else
{
	failure("required-$msg");
?>
	<br><br>
	<font color="red">
	<li>create a database and a user account to access it setting appropriate values to <b>MYSQL_HOST</b>, <b>MYSQL_USER</b>, <b>MYSQL_PASSWORD</b> and <b>MYSQL_DATABASE</b> properties in <b>config.php</b> file</li>
	<li>make sure your mysql database on <b><?=MYSQL_HOST ?></b> host is running</li>
	<li>make sure you've created a database called <b><?=MYSQL_DATABASE ?></b></li>
	<li>make sure you've granted access to this database to <b><?=MYSQL_USER ?></b> user identified by the password specified in <b>MYSQL_PASSWORD</b> property in <b>config.php</b> file</li>
	<li>make sure you've imported <b>dres.sql</b> database script to your <b><?=MYSQL_DATABASE ?></b> database and all required tables have been created</li>
	<li>switch <b>DATASOURCE</b> property in <b>config.php</b> file to <b>xmlrpc</b> if you don't want to use <b>MySQL</b> database but <b>Orpheus</b> instead</li>
	</font>
<?php
	closescript();
}
?>
<li>Sablotron sabcmd binary path...</li>
<?php
$required = (XSLT_MODE == "command");
exec(SABCMD_PATH,$out,$ret);
$present = (sizeof($out) > 0);
if ($present>=$required)
{
	success(($required?"required":"not required")."-".($present?"succeeded":"failed"));
}
else
{
	failure("required-command failed");
?>
	<br><br>
	<font color="red">
	<li>please specify path to <b>sabcmd</b> binary in the <b>config.php</b> file <b>SABCMD_PATH</b> variable</li>
	<li>the path is currently set to <b><?=SABCMD_PATH ?></b></li>
	<li>alternatively switch <b>XSLT_MODE</b> to <b>internal</b> in <b>config.php</b> to use PHP XSLT extension (not stable on Unix)</li>
	</font>
<?php
	closescript();
}
?>
<hr>
<br><br>
<div align="center">
	<b>Configuration check succeeded!<br>
	<font color=red>Remove check.php file to prevent others from seeing your configuration.</font></b>
</div>
</body>
</html>
