<?php
//BindEvents Method @1-B3413F62
function BindEvents()
{
    global $user_details;
    global $user_projects;
    global $CCSEvents;
    $user_details->ds->CCSEvents["AfterExecuteInsert"] = "user_details_ds_AfterExecuteInsert";
    $user_details->CCSEvents["AfterDelete"] = "user_details_AfterDelete";
    $user_projects->projects->CCSEvents["BeforeShow"] = "user_projects_projects_BeforeShow";
    $user_projects->Button_Update->CCSEvents["OnClick"] = "user_projects_Button_Update_OnClick";
    $CCSEvents["BeforeShow"] = "Page_BeforeShow";
}
//End BindEvents Method

//user_details_ds_AfterExecuteInsert @5-38AA0057
function user_details_ds_AfterExecuteInsert()
{
    $user_details_ds_AfterExecuteInsert = true;
//End user_details_ds_AfterExecuteInsert

//Custom Code @27-2A29BDB7
global $Redirect, $user_details;
$Redirect .= "user_id=".mysql_insert_id();
//End Custom Code

//Close user_details_ds_AfterExecuteInsert @5-779DB11E
    return $user_details_ds_AfterExecuteInsert;
}
//End Close user_details_ds_AfterExecuteInsert

//user_details_AfterDelete @5-E55CD39A
function user_details_AfterDelete()
{
    $user_details_AfterDelete = true;
//End user_details_AfterDelete

//Custom Code @28-2A29BDB7
global $Redirect;
$Redirect = "users.php";
//End Custom Code

//Close user_details_AfterDelete @5-859FBD98
    return $user_details_AfterDelete;
}
//End Close user_details_AfterDelete

//user_projects_projects_BeforeShow @17-02E3C017
function user_projects_projects_BeforeShow()
{
    $user_projects_projects_BeforeShow = true;
//End user_projects_projects_BeforeShow

//Custom Code @21-2A29BDB7

	global $user_projects;
	$rs = new clsDBdres();
	$rs->connect();
	$rs->query("SELECT * FROM project_users WHERE user_id=".CCGetParam("user_id", 0));
	while ($rs->next_record())
		$user_projects->projects->Value[] = $rs->f("project_id");
	if (is_array($user_projects->projects->Value))
		$user_projects->selected->Value = join(":", $user_projects->projects->Value);
//End Custom Code

//Close user_projects_projects_BeforeShow @17-39066590
    return $user_projects_projects_BeforeShow;
}
//End Close user_projects_projects_BeforeShow

//user_projects_Button_Update_OnClick @20-F1D8C106
function user_projects_Button_Update_OnClick()
{
    $user_projects_Button_Update_OnClick = true;
//End user_projects_Button_Update_OnClick

//Custom Code @22-2A29BDB7
	global $user_projects, $DBdres;

	$selected = split(":", $user_projects->selected->Value);
	$values = $user_projects->projects->Value;
	if (!is_array($selected)) $selected = array();
	if (!is_array($values)) $values = array();

	for ($i = 0; $i < sizeof($selected); $i++)
		if ($selected[$i] && !in_array($selected[$i], $values))
			$DBdres->query("DELETE FROM project_users WHERE user_id=".CCGetParam("user_id", 0)." AND project_id=".$selected[$i]);

	for ($i = 0; $i < sizeof($values); $i++)
		if ($values[$i] && !in_array($values[$i], $selected))
			$DBdres->query("INSERT INTO project_users SET user_id=".CCGetParam("user_id", 0).", project_id=".$values[$i]);

//End Custom Code

//Close user_projects_Button_Update_OnClick @20-3317AF64
    return $user_projects_Button_Update_OnClick;
}
//End Close user_projects_Button_Update_OnClick

//Page_BeforeShow @1-D8BD2467
function Page_BeforeShow()
{
    $Page_BeforeShow = true;
//End Page_BeforeShow

//Custom Code @26-2A29BDB7
global $user_projects;
if (!CCGetParam("user_id",""))
	$user_projects->Visible = false;
//End Custom Code

//Close Page_BeforeShow @1-4BC230CD
    return $Page_BeforeShow;
}
//End Close Page_BeforeShow

?>
