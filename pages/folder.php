<?php
#############################################################################
## folder.php - folder contents page                                       ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

require_once("lib/xmlforms.php");
require_once("lib/datasource.php");

$path = $HTTP_GET_VARS["folder"];
$parent = $HTTP_GET_VARS["parent"];
$PARAMS = array();
$dataSrc = new DataSource();

$PARAMS["Display"] = "readonly";

if ($HTTP_POST_VARS["folder_edit"])
	$PARAMS["Display"] = "edit";
elseif ($HTTP_POST_VARS["folder_save"])
	if ($HTTP_POST_VARS["folder_id"])
	{
		$folder_id = $HTTP_POST_VARS["folder_id"];
		$folder_name = $HTTP_POST_VARS["folder_name"];
		$folder_prefix = $HTTP_POST_VARS["folder_prefix"];

		$dataSrc->rename_folder($folder_id, $folder_name, $folder_prefix);
	}
	elseif ($HTTP_POST_VARS["folder_parent"])
	{
		$folder_parent = $HTTP_POST_VARS["folder_parent"];
		$folder_name = $HTTP_POST_VARS["folder_name"];
		$folder_prefix = $HTTP_POST_VARS["folder_prefix"];

		$newID = $dataSrc->create_folder($folder_parent, $folder_name, $folder_prefix);

		$newID = urlencode($newID);
		header("Location: main.php?page=folder&folder=$newID&refreshtree=true");
		exit;
	}

if ($path)
{
	$FoldersGrid = new XMLGrid("grids/FolderSubFolders.xml", array("GridName" => "folders", "LocalTransferParams" => "page=folder&folder=$path", "InsertTransferParams" => "parent=$path"));
	$RequirementsGrid = new XMLGrid("grids/FolderRequirements.xml", array("GridName" => "req", "LocalTransferParams" => "page=folder", "TransferParams" => "folder=$path", "GridTitle" => "Requirements"));

	$foldersList = $dataSrc->list_folders($path);
	$folderInfo = $foldersList;
	$reqsList = $dataSrc->list_requirements($path);
}
elseif (!$path && $parent)
{
	$folderInfo = '<folder parent="'.$parent.'" name="" prefix=""/>';

	$PARAMS["Display"] = "edit";
}
else
	header("Location: ./");
$FolderForm = new XMLForm("forms/FolderInfo.xml", $PARAMS);
?>
<?php if ($HTTP_GET_VARS["refreshtree"] == "true"): ?>
<script language="javascript">
<!--
window.parent.head.location.reload();
//-->
</script>
<?php endif; ?>
<?php echo $FolderForm->render($folderInfo) ?>
<?php if ($path): ?>
<?php echo $FoldersGrid->render($foldersList) ?>
<?php echo $RequirementsGrid->render($reqsList) ?>
<?php endif; ?>
<br>
<table width="100%" class="StatusTable">
	<tr>
		<td class="StatusCell" align="right">
			<a href="preview.php?folder=<?=$path ?>" target="_blank">preview</a> | <a href="exportxml.php?folder=<?=$path ?>" target="_blank">export XML</a>
		</td>
	</tr>
</table>
