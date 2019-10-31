<?php
#############################################################################
## session.php - Ophelia/Orpheus session handling routines                 ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################
session_start();
require_once("config.php");

// dummy function that returns hardcoded user identifier
function get_session_id()
{
	global $HTTP_SESSION_VARS;
	return $HTTP_SESSION_VARS[SESSION_COOKIE];
}

function get_user_id()
{
	global $HTTP_SESSION_VARS;
	return $HTTP_SESSION_VARS[USER_COOKIE];
}

function get_project_id()
{
	global $HTTP_SESSION_VARS;
	return $HTTP_SESSION_VARS[PROJECT_COOKIE];
}

function verify_access()
{
	global $HTTP_SESSION_VARS;
	if (!$HTTP_SESSION_VARS[USER_COOKIE] || !$HTTP_SESSION_VARS[PROJECT_COOKIE] || !$HTTP_SESSION_VARS[SESSION_COOKIE])
	{
		ob_end_clean();
		$url = "login.php?message=".urlencode("Access denied.");
		//header("Location: $url");
		echo "redirect <a target=\"_top\" href=\"$url\">here</a>";
		echo "<script language=\"javascript\">if (window.parent) window.parent.location.href='$url'; else window.location.href='$url';</script>";
		exit;
	}
}

function check_access()
{
	global $HTTP_SESSION_VARS;
	return $HTTP_SESSION_VARS[USER_COOKIE] && $HTTP_SESSION_VARS[PROJECT_COOKIE] && $HTTP_SESSION_VARS[SESSION_COOKIE];
}
?>