<?php
#############################################################################
## requirement.php - requirement details page                              ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

// include required libraries
require_once("lib/xmlforms.php");
require_once("lib/datasource.php");
require_once("lib/url.php");
require_once("lib/session.php");
require_once("lib/time.php");

// initialize session
session_start();
session_register("CHECKOUT");
session_register("CHECKOUT_FILE");
session_register("CHECKOUT_TOKEN");

// tabs available on requirements page
// name => key to use in URL
$TABS = array(
	"Summary"=>"summary",
	"Details"=>"details",
	"Keywords"=>"keywords",
	"Definition"=>"definition",
	"Scenarios"=>"scenarios",
	"Estimates"=>"estimates",
	"Relations"=>"relations",
	"Test&nbsp;Cases"=>"testcases",
	"Custom"=>"custom"
);

// initalize variables

$req_id 		= $HTTP_GET_VARS["req_id"];
$tab			= $HTTP_GET_VARS["tab"];
$folder			= $HTTP_GET_VARS["folder"];
$requirement	= "";
$CHECKOUT 		= &$HTTP_SESSION_VARS["CHECKOUT"];
$CHECKOUT_FILE 	= &$HTTP_SESSION_VARS["CHECKOUT_FILE"];
$CHECKOUT_TOKEN	= &$HTTP_SESSION_VARS["CHECKOUT_TOKEN"];
$ERRORS			= array();
$REDIRECT		= "";

// initialize default values if not set
if (!$tab || !in_array($tab, $TABS))
	$tab = "summary";

// initialize data source
$DataSrc = new DataSource();

// PROCESS BUTTONS SUBMITS
// checkout requirement
if ($HTTP_POST_VARS["req_checkout"] && !$CHECKOUT)
{
	$token = md5(uniqid(rand(), 1));

	if ($DataSrc->edit_requirement($req_id))
	{
		$CHECKOUT = $req_id;
		$CHECKOUT_TOKEN = $token;
		$CHECKOUT_FILE = "cache/$CHECKOUT_TOKEN.xml";

		// get requirement and save as file
		$requirement = $DataSrc->get_requirement($req_id);
		$requirement = ereg_replace("<revision[^>]*>.*</revision>", "<revision date=\"".get_current_date()."\"><author id=\"".get_user_id()."\">".$DataSrc->get_user_name()."</author></revision>", $requirement);
		save_file($CHECKOUT_FILE, $requirement);
	}
	else
		$ERRORS[] = "Cannot gain lock. Requirement already locked for editing.";
}
// create new requirement
if ($HTTP_POST_VARS["req_commit"] && !$CHECKOUT)
{
	if (file_exists($CHECKOUT_FILE))
	{
		if ($folder)
		{
			if ($DataSrc->create_requirement($folder, load_file($CHECKOUT_FILE)))
			{
				@unlink($CHECKOUT_FILE);
				$REDIRECT = "main.php?page=folder&folder=".urlencode($folder);
			}
			else
				$ERRORS[] = "Failed creating requirement in this folder.";
		}
		else
			$ERRORS[] = "Folder not specified. Cannot commit new requirement.";
	}
	else
	{
		$ERRORS[] = "Cannot commit requirement. Temporary file has been removed.";
	}
}
// cancel adding requirement
if ($HTTP_POST_VARS["req_undo"] && !$CHECKOUT)
{
	if (file_exists($CHECKOUT_FILE))
		@unlink($CHECKOUT_FILE);
	unset($CHECKOUT_TOKEN);
}
// commit requirement
if ($HTTP_POST_VARS["req_commit"] && $CHECKOUT)
{
	if (file_exists($CHECKOUT_FILE))
	{
		$new_id = $DataSrc->commit_requirement($req_id, load_file($CHECKOUT_FILE));
		if ($new_id)
		{
			@unlink($CHECKOUT_FILE);
			$REDIRECT = "main.php?page=requirement&req_id=".$new_id;
		}
		else
			$ERRORS[] = "Failed to commit requirement.";
	}
	else
	{
		$DataSrc->unedit_requirement($req_id);
		$ERRORS[] = "Cannot commit requirement. Temporary file has been removed.";
	}

	$CHECKOUT="";
	$CHECKOUT_TOKEN = "";
	$CHECKOUT_FILE = "";
}
// undo checkout requirement
if ($HTTP_POST_VARS["req_undo"] && $CHECKOUT)
{
	$DataSrc->unedit_requirement($req_id);
	if (file_exists($CHECKOUT_FILE))
		@unlink($CHECKOUT_FILE);

	$CHECKOUT="";
	$CHECKOUT_TOKEN = "";
	$CHECKOUT_FILE = "";
}
// new requirement
if (!$req_id && !$CHECKOUT_TOKEN)
{
	$token = md5(uniqid(rand(), 1));
	$CHECKOUT_TOKEN = $token;
	$CHECKOUT_FILE = "cache/$CHECKOUT_TOKEN.xml";
	$requirement = "<requirement><revision date=\"".get_current_date()."\"><author id=\"".get_user_id()."\">".$DataSrc->get_user_name()."</author></revision></requirement>";
	save_file($CHECKOUT_FILE, $requirement);
}

if ($REDIRECT)
{
	header("Location: $REDIRECT");
	exit;
}

// initalize tab parameters array
$PARAMS = array();
// if not checked out display in read-only mode
if ($req_id != $CHECKOUT)
	$PARAMS["Display"] = "readonly";
elseif ($req_id)
	$PARAMS["ChangeHandler"] = "control_onchange(this)";

if ($req_id)
{
	// create grid to display requirement revisions
	$RevisionsGrid = new XMLGrid("grids/RequirementRevisions.xml",array("GridName" => "revisions", "SelectedID" => $req_id, "LocalTransferParams" => "req_id=$req_id", "TransferParams" => "page=$page&tab=$tab"));
	
	// retrieve revisions list from data source
	$revisions = $DataSrc->list_revisions($req_id);
}
?>
<script language="javascript">
<!--
var forms_dirty=false;

function window_onbeforeunload()
{
	if (forms_dirty)
		return "Changes have not been saved.";
}
function control_onchange(control)
{
	control.style.backgroundColor='yellow';
	forms_dirty=true;
}
window.onbeforeunload=window_onbeforeunload;
//-->
</script>
<?php show_error($ERRORS) ?>
<?php if ($req_id && $RevisionsGrid) echo $RevisionsGrid->render($revisions) ?>
<table width="100%" class="TabTable">
	<tr class="TabRow">
<?php
// display tabs
foreach($TABS as $tabtitle => $tabname)
	echo "<td width=\"100\" align=\"center\" class=\"".($tab == $tabname ? "ActiveTab" : "Tab")."\" onMouseOver=\"this.originalClass=this.className;this.className='ActiveTab'\" onMouseOut=\"this.className=this.originalClass\" onClick=\"\"><a class=\"TabLink\" href=\"main.php?".getQueryString(array("tab"))."&tab=$tabname\">$tabtitle</a></td>";
?>
		<td>&nbsp;</td>
	</tr>
</table>
<?php include("pages/requirement_$tab.php") ?>
<br>
<table width="100%" class="StatusTable">
	<form method="post">
	<tr>
		<td class="StatusCell" nowrap>
<?php if (!$req_id && !$CHECKOUT): ?>
			<input type="submit" name="req_commit" value="Commit Requirement" class="CommitButton" onclick="forms_dirty=false;return confirm('This will create new requirement.\nAre you sure you want to COMMIT?')">
			<input type="submit" name="req_undo" value="Cancel Requirement" class="UndoButton" onclick="forms_dirty=false;return confirm('You will loose all your modifications on ALL tabs.\nAre you sure you want to UNDO?')">
<?php elseif (!$CHECKOUT): ?>
			<input type="submit" name="req_checkout" value="Revise Requirement" class="CheckoutButton" onclick="forms_dirty=false;return confirm('This will prevent other user from editing this requirement.\nAre you sure you want to EDIT?')">
<?php elseif ($CHECKOUT != $req_id): ?>
<?php else: ?>
			<input type="submit" name="req_commit" value="Commit Requirement" class="CommitButton" onclick="forms_dirty=false;return confirm('This will create new requirement revision.\nAre you sure you want to COMMIT?')">
			<input type="submit" name="req_undo" value="Undo Checkout" class="UndoButton" onclick="forms_dirty=false;return confirm('You will loose all your modifications on ALL tabs.\nAre you sure you want to UNDO?')">
<?php endif; ?>
		</td>
<?php if ($CHECKOUT && $CHECKOUT != $req_id): ?>
		<td class="StatusCell" nowrap>
			You have some requirement checked out, commit it or undo to revise this one.<br>
			<a href="main.php?page=requirement&req_id=<?=$CHECKOUT ?>">Click here to return to that requirement</a>
		</td>
<?php endif; ?>
<?php if ($req_id): ?>
		<td class="StatusCell" align="right" nowrap>
			<a href="preview.php?req_id=<?=$req_id ?>" target="_blank">preview</a> | <a href="exportxml.php?req_id=<?=$req_id ?>" target="_blank">export XML</a>
		</td>
<?php endif; ?>
	</tr>
	</form>
</table>
