<?php
#############################################################################
## home.php - default home page                                            ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################
require_once("lib/xmlforms.php");
require_once("lib/datasource.php");
require_once("lib/session.php");

$RecentRequirementsGrid = new XMLGrid("grids/RecentRequirements.xml", array("GridName" => "recent", "LocalTransferParams" => "page=home", "GridTitle" => "Recent Requirements"));
$MyRequirementsGrid = new XMLGrid("grids/MyRequirements.xml", array("GridName" => "myreq", "LocalTransferParams" => "page=home", "GridTitle" => "My Requirements"));
$dataSrc = new DataSource;

$reqList = $dataSrc->list_all();
?>
<?php echo $RecentRequirementsGrid->render($reqList) ?>
<br>
<?php echo $MyRequirementsGrid->render($reqList) ?>
