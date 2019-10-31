<?php
require_once("lib/datasource.php");
require_once("lib/xmlforms.php");
$dataSrc = new DataSource();

$ReportsGrid = new XMLGrid("grids/Reports.xml", array("GridName" => "reports", "LocalTransferParams" => "page=reports"));
$xml = $dataSrc->list_reports();
echo $ReportsGrid->render($xml)
?>
