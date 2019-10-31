<?php
require_once("lib/datasource.php");
require_once("lib/xmlforms.php");
require_once("lib/lib_xslt.php");

import_request_variables("gp", "in_");

$PARAMETERS = array("folder", "recursive", "priority", "status", "keywords", "text", "versions", "search");

function options($items, $sel)
{
	foreach ($items as $item=>$val)
		$result .= "<option value=\"$val\"".($val==$sel?" selected":"").">".($val)."</option>";
	return $result;
}

function buildQS($filter)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	foreach ($filter as $var)
	{
		$val = $HTTP_POST_VARS[$var]?$HTTP_POST_VARS[$var]:$HTTP_GET_VARS[$var];
		if ($val)
			$qs .= ($qs?"&":"")."$var=".urlencode($val);
	}
	return $qs;
}

function buildParams($filter)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	foreach ($filter as $var)
	{
		$val = $HTTP_POST_VARS[$var]?$HTTP_POST_VARS[$var]:$HTTP_GET_VARS[$var];
		if ($val)
			$params[$var] = $val;
	}
	return $params;
}
?>
<link rel="stylesheet" type="text/css" href="css/form.css">
<table class="FormTable">
<form method="post">
	<tr>
		<td class="FormTitle" colspan="3">
			Search requirements
		</td>
	</tr>
	<tr>
		<td class="FormLabel" nowrap>
			Search folder:
		</td>
		<td class="FormData" nowrap>
			<select name="folder" class="FormControl" style="width:100%">
<?php 
$dataSrc = new DataSource();
$xml = $dataSrc->list_all_folders();
echo xslt_transform($xml, load_file("xslt/folderslistbox.xslt"), array("selected" => $in_folder));
?>			
			</select>
		</td>
		<td class="FormData" nowrap>
			<input type="checkbox" name="recursive" <?=$in_recursive?"checked":"" ?>> recursive
		</td>
	</tr>
	<tr>
		<td class="FormLabel">
			Priority:
		</td>
		<td class="FormData" colspan="2">
			<select name="priority" style="width:200px">
				<option value="">any</option>
<?=options(array("high"=>"high", "medium"=>"medium", "low"=>"low"), $in_priority) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="FormLabel">
			Status:
		</td>
		<td class="FormData" colspan="2">
			<select name="status" style="width:200px">
				<option value="">any</option>
<?=options(array("draft"=>"draft", "proposed"=>"proposed", "approved"=>"approved"), $in_status) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="FormLabel">
			Keywords:
		</td>
		<td class="FormData" colspan="2">
			<input type="text" name="keywords" class="FormControl" value="<?=$in_keywords ?>" style="width:100%">
		</td>
	</tr>
	<tr>
		<td class="FormLabel">
			Text:
		</td>
		<td class="FormData" colspan="2">
			<input type="text" name="text" class="FormControl" value="<?=$in_text ?>" style="width:100%">
		</td>
	</tr>
	<tr>
		<td class="FormLabel" nowrap>
			Range:
		</td>
		<td class="FormData" colspan="2">
			<input type="radio" name="versions" value="recent" <?=$in_versions!="all"?"checked":"" ?>> most recent versions only
			<input type="radio" name="versions" value="all" <?=$in_versions=="all"?"checked":"" ?>> all versions
		</td>
	</tr>
	<tr>
		<td class="FormFooter" colspan="3">
			<input type="submit" name="search" value="Search" class="FormButton">
		</td>
	</tr>
</form>
</table>
<?php
$results = false;
if ($in_search)
{
	$xml = $dataSrc->search_criteria(buildParams($PARAMETERS));
	$results = strlen($xml) > 0;
}
?>
<?php if($results): ?>
<br>
<?php
$ResultsGrid = new XMLGrid("grids/SearchResults.xml", array("GridName" => "search", "LocalTransferParams" => "page=search", "TransferParams" => buildQS($PARAMETERS)));
echo $ResultsGrid->render($xml)
?>
<?php endif; ?>