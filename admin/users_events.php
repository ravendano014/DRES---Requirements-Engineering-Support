<?php
//BindEvents Method @1-F2E78F0D
function BindEvents()
{
    global $users;
    $users->users_TotalRecords->CCSEvents["BeforeShow"] = "users_users_TotalRecords_BeforeShow";
}
//End BindEvents Method

//users_users_TotalRecords_BeforeShow @10-BB7BE430
function users_users_TotalRecords_BeforeShow()
{
    $users_users_TotalRecords_BeforeShow = true;
//End users_users_TotalRecords_BeforeShow

//Retrieve number of records @11-EC56BBC5
    global $users;
    $users->users_TotalRecords->SetValue($users->ds->RecordsCount);
//End Retrieve number of records

//Close users_users_TotalRecords_BeforeShow @10-ADD8CAEB
    return $users_users_TotalRecords_BeforeShow;
}
//End Close users_users_TotalRecords_BeforeShow


?>
