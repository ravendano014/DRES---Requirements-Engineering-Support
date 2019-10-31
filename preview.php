<?php
require_once("config.php");
require_once("lib/session.php");
require_once("lib/datasource.php");
require_once("lib/lib_xslt.php");
require_once("lib/xmlutil.php");

verify_access();

$req_id = $HTTP_GET_VARS["req_id"];
$folder = $HTTP_GET_VARS["folder"];
if (!$req_id && !$folder) redirect_error("Required parameter missing: req_id or folder (page: preview)");

$DataSrc = new DataSource();
if ($req_id)
	$req_data = $DataSrc->get_requirement($req_id);
else
	$req_data = $DataSrc->dump_folder($folder);
//dump_xml($req_data);
echo xslt_transform($req_data, load_file("xslt/report.xslt"), array("Title" => ($req_id ? "Requirement preview" : "Folder preview")));
?>
