<?php
require_once("lib/datasource.php");
require_once("lib/session.php");
require_once("lib/xmlforms.php");

verify_access();

import_request_variables("gp", "in_");

$dataSrc = new DataSource();

$ReportForm = new XMLForm("forms/Report.xml", array("FormTitle" => "Edit report"));
$report_errors = array();

if ($in_report_id)
	$report = $dataSrc->get_report($in_report_id);
else 
	$report = "<report/>";

if ($HTTP_POST_VARS["report_save"] && $HTTP_POST_VARS["action"] == "report")
{
	$report = $ReportForm->get_submit($report, &$report_errors);
	if(sizeof($report_errors) == 0)
	{
		$dataSrc->save_report($in_report_id, $report);
		header("Location: main.php?page=reports");
		exit;
	}
}
else
	$report = $ReportForm->get_defaults($report, $HTTP_GET_VARS);

show_error($report_errors);
//dump_xml($report);
echo $ReportForm->render($report, array("folders"=>$dataSrc->list_all_folders()));
?>