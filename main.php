<?php
#############################################################################
## main.php - xDRE main frame content                                      ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################
?>
<?php
// use output buffering to allow cookie setting from included pages
require_once("lib/session.php");
require_once("lib/error.php");
session_start();
ob_start();

verify_access();

// test page to include exists
$page = $HTTP_GET_VARS["page"];

if (!isset($page))
	$page = "home";
if (!file_exists("pages/$page.php"))
	redirect_error("Invalid page: $page");
?>
<html>
<head>
	<title>DRES - Distributed Requirements Engineering System</title>
	<link rel="stylesheet" href="css/site.css">
	<link rel="stylesheet" href="css/form.css">
	<link rel="stylesheet" href="css/grid.css">
	<link rel="stylesheet" href="css/<?=$page ?>.css">
</head>
<body>
<?php include("pages/$page.php") ?>
<br><br>
<div align="center" class="SiteFooter">Distributed Requirements Engineering System<br></div>
</body>
</html>
<?php ob_end_flush(); ?>