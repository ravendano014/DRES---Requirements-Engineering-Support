<?php
//BindEvents Method @1-B92698A1
function BindEvents()
{
    global $projects1;
    $projects1->ds->CCSEvents["AfterExecuteInsert"] = "projects1_ds_AfterExecuteInsert";
    $projects1->ds->CCSEvents["AfterExecuteUpdate"] = "projects1_ds_AfterExecuteUpdate";
    $projects1->ds->CCSEvents["AfterExecuteDelete"] = "projects1_ds_AfterExecuteDelete";
}
//End BindEvents Method

//projects1_ds_AfterExecuteInsert @10-5DEEE8C8
function projects1_ds_AfterExecuteInsert()
{
    $projects1_ds_AfterExecuteInsert = true;
//End projects1_ds_AfterExecuteInsert

//Custom Code @18-2A29BDB7
// -------------------------
// Write your own code here.
// -------------------------
//End Custom Code

//DEL  global $projects1;
//DEL  $new_project = mysql_insert_id();
//DEL  $projects1->ds->query("INSERT INTO folders(folder_project_id, folder_id, folder_prefix, folder_name) VALUES($new_project,'/',".$projects1->ds->ToSQL($projects1->folder_prefix->GetValue(),ccsText).",".$projects1->ds->ToSQL($projects1->folder_name->GetValue(),ccsText).")");


//Close projects1_ds_AfterExecuteInsert @10-6347B0F3
    return $projects1_ds_AfterExecuteInsert;
}
//End Close projects1_ds_AfterExecuteInsert

//projects1_ds_AfterExecuteUpdate @10-21F3420C
function projects1_ds_AfterExecuteUpdate()
{
    $projects1_ds_AfterExecuteUpdate = true;
//End projects1_ds_AfterExecuteUpdate

//Custom Code @29-2A29BDB7
// -------------------------
// Write your own code here.
// -------------------------
//End Custom Code

//DEL  global $projects1;
//DEL  $projects1->ds->query("UPDATE folders SET folder_prefix = ".$projects1->ds->ToSQL($projects1->folder_prefix->GetValue(),ccsText).", folder_name=".$projects1->ds->ToSQL($projects1->folder_name->GetValue(),ccsText)." WHERE folder_id_parent IS NULL AND folder_project_id=".CCGetParam("project_id",0));


//Close projects1_ds_AfterExecuteUpdate @10-AC6E717C
    return $projects1_ds_AfterExecuteUpdate;
}
//End Close projects1_ds_AfterExecuteUpdate

//projects1_ds_AfterExecuteDelete @10-110DDB1B
function projects1_ds_AfterExecuteDelete()
{
    $projects1_ds_AfterExecuteDelete = true;
//End projects1_ds_AfterExecuteDelete

//Custom Code @30-2A29BDB7
// -------------------------
// Write your own code here.
// -------------------------
//End Custom Code

//DEL  global $projects1;
//DEL  $projects1->ds->query("DELETE FROM folders WHERE folder_project_id=".CCGetParam("project_id",0));
//DEL  $projects1->ds->query("DELETE FROM requirements WHERE req_project_id=".CCGetParam("project_id",0));
//DEL  $projects1->ds->query("DELETE FROM project_users WHERE project_id=".CCGetParam("project_id",0));


//Close projects1_ds_AfterExecuteDelete @10-304AD70D
    return $projects1_ds_AfterExecuteDelete;
}
//End Close projects1_ds_AfterExecuteDelete







?>
