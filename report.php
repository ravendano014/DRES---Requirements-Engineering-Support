<?php
require_once("config.php");
require_once("lib/session.php");
require_once("lib/datasource.php");
require_once("lib/lib_xslt.php");
require_once("lib/xmlutil.php");

verify_access();

$report_id = $HTTP_GET_VARS["report_id"];
if (!$report_id) redirect_error("Required parameter missing: report_id (page: report)");

$DataSrc = new DataSource();
$rep_data = $DataSrc->get_report($report_id);
//dump_xml($rep_data);
$req_data = $DataSrc->filter_query($rep_data);
//dump_xml($req_data);
echo xslt_transform($req_data, load_file("xslt/report.xslt"), array("Title" => get_xpath_text($rep_data, "/report/@name")));
?>
