<?php
require_once("config.php");
require_once("lib/session.php");
require_once("lib/datasource.php");
require_once("lib/lib_xslt.php");
require_once("lib/xmlutil.php");

verify_access();

$req_id = $HTTP_GET_VARS["req_id"];
$folder = $HTTP_GET_VARS["folder"];
if (!$req_id && !$folder) redirect_error("Required parameter missing: req_id or folder (page: report)");

$DataSrc = new DataSource();
if ($req_id)
	$req_data = $DataSrc->get_requirement($req_id);
else
	$req_data = $DataSrc->dump_folder($folder);
header("Content-type: text/xml");
echo $req_data;
?>