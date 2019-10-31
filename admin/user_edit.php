<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @2-39DC296A
include_once("./Header.php");
//End Include Page implementation

Class clsRecorduser_details { //user_details Class @5-23D3B63D

//Variables @5-6C307B82

    // Public variables
    var $ComponentName;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $Recordset;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $InsertAllowed;
    var $UpdateAllowed;
    var $DeleteAllowed;
    var $ds;
    var $EditMode;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @5-61A5DB12
    function clsRecorduser_details()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        $this->ds = new clsuser_detailsDataSource();
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "user_details";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_name = new clsControl(ccsTextBox, "user_name", " Name", ccsText, "", CCGetRequestParam("user_name", $Method));
            $this->user_login = new clsControl(ccsTextBox, "user_login", " Login", ccsText, "", CCGetRequestParam("user_login", $Method));
            $this->user_password = new clsControl(ccsTextBox, "user_password", " Password", ccsText, "", CCGetRequestParam("user_password", $Method));
            $this->user_level = new clsControl(ccsListBox, "user_level", " Level", ccsInteger, "", CCGetRequestParam("user_level", $Method));
            $this->user_level->DSType = dsTable;
            list($this->user_level->BoundColumn, $this->user_level->TextColumn, $this->user_level->DBFormat) = array("level_id", "level_role", "");
            $this->user_level->ds = new clsDBdres();
            $this->user_level->ds->SQL = "SELECT *  " .
"FROM levels";
            $this->user_level->Required = true;
            $this->user_date_registered = new clsControl(ccsLabel, "user_date_registered", " Date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("user_date_registered", $Method));
            $this->user_date_logged = new clsControl(ccsLabel, "user_date_logged", " Date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "s"), CCGetRequestParam("user_date_logged", $Method));
            $this->Button_Insert = new clsButton("Button_Insert");
            $this->Button_Update = new clsButton("Button_Update");
            $this->Button_Delete = new clsButton("Button_Delete");
        }
    }
//End Class_Initialize Event

//Initialize Method @5-C016A25E
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->ds->Parameters["urluser_id"] = CCGetFromGet("user_id", "");
    }
//End Initialize Method

//Validate Method @5-57D31B83
    function Validate()
    {
        $Validation = true;
        $Where = "";
        $Validation = ($this->user_name->Validate() && $Validation);
        $Validation = ($this->user_login->Validate() && $Validation);
        $Validation = ($this->user_password->Validate() && $Validation);
        $Validation = ($this->user_level->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @5-9677D6FC
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_name->Errors->Count());
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_password->Errors->Count());
        $errors = ($errors || $this->user_level->Errors->Count());
        $errors = ($errors || $this->user_date_registered->Errors->Count());
        $errors = ($errors || $this->user_date_logged->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->ds->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @5-81BE918F
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->ds->Prepare();
        $this->EditMode = $this->ds->AllParametersSet;
        if(!$this->FormSubmitted)
            return;

        if($this->FormSubmitted) {
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Button_Insert";
            if(strlen(CCGetParam("Button_Insert", ""))) {
                $this->PressedButton = "Button_Insert";
            } else if(strlen(CCGetParam("Button_Update", ""))) {
                $this->PressedButton = "Button_Update";
            } else if(strlen(CCGetParam("Button_Delete", ""))) {
                $this->PressedButton = "Button_Delete";
            }
        }
        $Redirect = "user_edit.php?" . CCGetQueryString("QueryString", Array("ccsForm", "Button_Insert", "Button_Update", "Button_Delete"));
        if($this->PressedButton == "Button_Delete") {
            if(!CCGetEvent($this->Button_Delete->CCSEvents, "OnClick") || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Button_Insert") {
                if(!CCGetEvent($this->Button_Insert->CCSEvents, "OnClick") || !$this->InsertRow()) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Button_Update") {
                if(!CCGetEvent($this->Button_Update->CCSEvents, "OnClick") || !$this->UpdateRow()) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//InsertRow Method @5-932C8B0A
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert");
        if(!$this->InsertAllowed) return false;
        $this->ds->user_name->SetValue($this->user_name->GetValue());
        $this->ds->user_login->SetValue($this->user_login->GetValue());
        $this->ds->user_password->SetValue($this->user_password->GetValue());
        $this->ds->user_level->SetValue($this->user_level->GetValue());
        $this->ds->user_date_registered->SetValue($this->user_date_registered->GetValue());
        $this->ds->user_date_logged->SetValue($this->user_date_logged->GetValue());
        $this->ds->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert");
        if($this->ds->Errors->Count() > 0) {
            echo "Error in Record " . $this->ComponentName . " / Insert Operation";
            $this->ds->Errors->Clear();
            $this->Errors->AddError("Database command error.");
        }
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @5-67E9CECC
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate");
        if(!$this->UpdateAllowed) return false;
        $this->ds->user_name->SetValue($this->user_name->GetValue());
        $this->ds->user_login->SetValue($this->user_login->GetValue());
        $this->ds->user_password->SetValue($this->user_password->GetValue());
        $this->ds->user_level->SetValue($this->user_level->GetValue());
        $this->ds->user_date_registered->SetValue($this->user_date_registered->GetValue());
        $this->ds->user_date_logged->SetValue($this->user_date_logged->GetValue());
        $this->ds->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate");
        if($this->ds->Errors->Count() > 0) {
            echo "Error in Record " . $this->ComponentName . " / Update Operation";
            $this->ds->Errors->Clear();
            $this->Errors->AddError("Database command error.");
        }
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @5-EA88835F
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete");
        if(!$this->DeleteAllowed) return false;
        $this->ds->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete");
        if($this->ds->Errors->Count() > 0) {
            echo "Error in Record " . ComponentName . " / Delete Operation";
            $this->ds->Errors->Clear();
            $this->Errors->AddError("Database command error.");
        }
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @5-C3804278
    function Show()
    {
        global $Tpl;
        global $FileName;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");

        $this->user_level->Prepare();

        $this->ds->open();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        if($this->EditMode)
        {
            if($this->Errors->Count() == 0)
            {
                if($this->ds->Errors->Count() > 0)
                {
                    echo "Error in Record user_details";
                }
                else if($this->ds->next_record())
                {
                    $this->ds->SetValues();
                    $this->user_date_registered->SetValue($this->ds->user_date_registered->GetValue());
                    $this->user_date_logged->SetValue($this->ds->user_date_logged->GetValue());
                    if(!$this->FormSubmitted)
                    {
                        $this->user_name->SetValue($this->ds->user_name->GetValue());
                        $this->user_login->SetValue($this->ds->user_login->GetValue());
                        $this->user_password->SetValue($this->ds->user_password->GetValue());
                        $this->user_level->SetValue($this->ds->user_level->GetValue());
                    }
                }
                else
                {
                    $this->EditMode = false;
                }
            }
        }
        if(!$this->FormSubmitted)
        {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error .= $this->user_name->Errors->ToString();
            $Error .= $this->user_login->Errors->ToString();
            $Error .= $this->user_password->Errors->ToString();
            $Error .= $this->user_level->Errors->ToString();
            $Error .= $this->user_date_registered->Errors->ToString();
            $Error .= $this->user_date_logged->Errors->ToString();
            $Error .= $this->Errors->ToString();
            $Error .= $this->ds->Errors->ToString();
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $this->Button_Insert->Visible = !$this->EditMode;
        $this->Button_Update->Visible = $this->EditMode;
        $this->Button_Delete->Visible = $this->EditMode;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->user_name->Show();
        $this->user_login->Show();
        $this->user_password->Show();
        $this->user_level->Show();
        $this->user_date_registered->Show();
        $this->user_date_logged->Show();
        $this->Button_Insert->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

} //End user_details Class @5-FCB6E20C

class clsuser_detailsDataSource extends clsDBdres {  //user_detailsDataSource Class @5-E1F75E00

//DataSource Variables @5-335D0AD8
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $InsertParameters;
    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_name;
    var $user_login;
    var $user_password;
    var $user_level;
    var $user_date_registered;
    var $user_date_logged;
//End DataSource Variables

//Class_Initialize Event @5-01A39D65
    function clsuser_detailsDataSource()
    {
        $this->ErrorBlock = "Record user_details/Error";
        $this->Initialize();
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_password = new clsField("user_password", ccsText, "");
        $this->user_level = new clsField("user_level", ccsInteger, "");
        $this->user_date_registered = new clsField("user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));
        $this->user_date_logged = new clsField("user_date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));

    }
//End Class_Initialize Event

//Prepare Method @5-BAD07477
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urluser_id", ccsInteger, "", "", $this->Parameters["urluser_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @5-DC1AA46D
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->SQL = "SELECT *  " .
        "FROM users";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @5-E1D3F743
    function SetValues()
    {
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_password->SetDBValue($this->f("user_password"));
        $this->user_level->SetDBValue(trim($this->f("user_level")));
        $this->user_date_registered->SetDBValue(trim($this->f("user_date_registered")));
        $this->user_date_logged->SetDBValue(trim($this->f("user_date_logged")));
    }
//End SetValues Method

//Insert Method @5-16AACA12
    function Insert()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert");
        $this->SQL = "INSERT INTO users ("
             . "user_name, "
             . "user_login, "
             . "user_password, "
             . "user_level"
             . ") VALUES ("
             . $this->ToSQL($this->user_name->GetDBValue(), $this->user_name->DataType) . ", "
             . $this->ToSQL($this->user_login->GetDBValue(), $this->user_login->DataType) . ", "
             . $this->ToSQL($this->user_password->GetDBValue(), $this->user_password->DataType) . ", "
             . $this->ToSQL($this->user_level->GetDBValue(), $this->user_level->DataType)
             . ")";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Insert Method

//Update Method @5-52A4B9B4
    function Update()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate");
        $this->SQL = "UPDATE users SET "
             . "user_name=" . $this->ToSQL($this->user_name->GetDBValue(), $this->user_name->DataType) . ", "
             . "user_login=" . $this->ToSQL($this->user_login->GetDBValue(), $this->user_login->DataType) . ", "
             . "user_password=" . $this->ToSQL($this->user_password->GetDBValue(), $this->user_password->DataType) . ", "
             . "user_level=" . $this->ToSQL($this->user_level->GetDBValue(), $this->user_level->DataType);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Update Method

//Delete Method @5-E470FFB8
    function Delete()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete");
        $this->SQL = "DELETE FROM users WHERE " . $this->Where;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Delete Method

} //End user_detailsDataSource Class @5-FCB6E20C

Class clsRecorduser_projects { //user_projects Class @16-95D7AAFC

//Variables @16-E2EC6027

    // Public variables
    var $ComponentName;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $Recordset;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $ds;
    var $EditMode;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @16-18AD2997
    function clsRecorduser_projects()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        if($this->Visible)
        {
            $this->ComponentName = "user_projects";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->projects = new clsControl(ccsCheckBoxList, "projects", "projects", ccsText, "", CCGetRequestParam("projects", $Method));
            $this->projects->Multiple = true;
            $this->projects->DSType = dsTable;
            list($this->projects->BoundColumn, $this->projects->TextColumn, $this->projects->DBFormat) = array("project_id", "project_name", "");
            $this->projects->ds = new clsDBdres();
            $this->projects->ds->SQL = "SELECT *  " .
"FROM projects";
            $this->selected = new clsControl(ccsHidden, "selected", "selected", ccsText, "", CCGetRequestParam("selected", $Method));
            $this->Button_Update = new clsButton("Button_Update");
        }
    }
//End Class_Initialize Event

//Validate Method @16-DA0DD5F4
    function Validate()
    {
        $Validation = true;
        $Where = "";
        $Validation = ($this->projects->Validate() && $Validation);
        $Validation = ($this->selected->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @16-9590390B
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->projects->Errors->Count());
        $errors = ($errors || $this->selected->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @16-880C0FCF
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->EditMode = false;
        if(!$this->FormSubmitted)
            return;

        if($this->FormSubmitted) {
            $this->PressedButton = "Button_Update";
            if(strlen(CCGetParam("Button_Update", ""))) {
                $this->PressedButton = "Button_Update";
            }
        }
        $Redirect = $FileName . "?" . CCGetQueryString("QueryString", Array("ccsForm", "Button_Update"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_Update") {
                if(!CCGetEvent($this->Button_Update->CCSEvents, "OnClick")) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @16-3316551B
    function Show()
    {
        global $Tpl;
        global $FileName;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");

        $this->projects->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        if(!$this->FormSubmitted)
        {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error .= $this->projects->Errors->ToString();
            $Error .= $this->selected->Errors->ToString();
            $Error .= $this->Errors->ToString();
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->projects->Show();
        $this->selected->Show();
        $this->Button_Update->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End user_projects Class @16-FCB6E20C

//Include Page implementation @3-5CD56755
include_once("./Footer.php");
//End Include Page implementation

//Initialize Page @1-C3D7D235
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = "user_edit.php";
$Redirect = "";
$TemplateFileName = "user_edit.html";
$BlockToParse = "main";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4CBB5244
CCSecurityRedirect("10", "");
//End Authenticate User

//Initialize Objects @1-7078610A
$DBdres = new clsDBdres();

// Controls
$Header = new clsHeader();
$Header->BindEvents();
$Header->TemplatePath = "./";
$Header->Initialize();
$user_details = new clsRecorduser_details();
$user_projects = new clsRecorduser_projects();
$Footer = new clsFooter();
$Footer->BindEvents();
$Footer->TemplatePath = "./";
$Footer->Initialize();
$user_details->Initialize();

// Events
include("./user_edit_events.php");
BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");
//End Initialize Objects

//Initialize HTML Template @1-A0111C9D
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate();
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main");
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-6B65428F
$Header->Operations();
$user_details->Operation();
$user_projects->Operation();
$Footer->Operations();
//End Execute Components

//Go to destination page @1-ABE6B9FF
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBdres->close();
    header("Location: " . $Redirect);
    exit;
}
//End Go to destination page

//Show Page @1-B718A38D
$Header->Show("Header");
$user_details->Show();
$user_projects->Show();
$Footer->Show("Footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-1BAB0CD6
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBdres->close();
unset($Tpl);
//End Unload Page


?>
