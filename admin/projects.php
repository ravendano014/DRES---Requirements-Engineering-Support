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

class clsGridprojects { //projects class @5-55C24418

//Variables @5-A9EB2EBA

    // Public variables
    var $ComponentName;
    var $Visible; var $Errors;
    var $ds; var $PageSize;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;

    var $CCSEvents = "";
    var $CCSEventResult;

    // Grid Controls
    var $StaticControls; var $RowControls;
    var $Sorter_project_name;
//End Variables

//Class_Initialize Event @5-9A2DDACD
    function clsGridprojects()
    {
        global $FileName;
        $this->ComponentName = "projects";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ds = new clsprojectsDataSource();
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 100;
        else
            $this->PageSize = intval($this->PageSize);
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("projectsOrder", "");
        $this->SorterDirection = CCGetParam("projectsDir", "");

        $this->project_name = new clsControl(ccsLink, "project_name", "project_name", ccsText, "", CCGetRequestParam("project_name", ccsGet));
        $this->Sorter_project_name = new clsSorter($this->ComponentName, "Sorter_project_name", $FileName);
        $this->projects_Insert = new clsControl(ccsLink, "projects_Insert", "projects_Insert", ccsText, "", CCGetRequestParam("projects_Insert", ccsGet));
        $this->projects_Insert->Parameters = CCGetQueryString("QueryString", Array("project_id", "ccsForm"));
        $this->projects_Insert->Page = "projects.php";
    }
//End Class_Initialize Event

//Initialize Method @5-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @5-B63A716D
    function Show()
    {
        global $Tpl;
        if(!$this->Visible) return;

        $ShownRecords = 0;


        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");


        $this->ds->Prepare();
        $this->ds->Open();

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) return;

        $GridBlock = "Grid " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $GridBlock;


        $is_next_record = $this->ds->next_record();
        if($is_next_record && $ShownRecords < $this->PageSize)
        {
            do {
                    $this->ds->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->project_name->SetValue($this->ds->project_name->GetValue());
                $this->project_name->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->project_name->Parameters = CCAddParam($this->project_name->Parameters, "project_id", $this->ds->f("project_id"));
                $this->project_name->Page = "projects.php";
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->project_name->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
                $ShownRecords++;
                $is_next_record = $this->ds->next_record();
            } while ($is_next_record && $ShownRecords < $this->PageSize);
        }
        else // Show NoRecords block if no records are found
        {
            $Tpl->parse("NoRecords", false);
        }

        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->Sorter_project_name->Show();
        $this->projects_Insert->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @5-0F640453
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->project_name->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End projects Class @5-FCB6E20C

class clsprojectsDataSource extends clsDBdres {  //projectsDataSource Class @5-99339FE0

//DataSource Variables @5-C91FCCA6
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $project_name;
//End DataSource Variables

//Class_Initialize Event @5-3BB09864
    function clsprojectsDataSource()
    {
        $this->ErrorBlock = "Grid projects";
        $this->Initialize();
        $this->project_name = new clsField("project_name", ccsText, "");

    }
//End Class_Initialize Event

//SetOrder Method @5-C7859EDC
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_project_name" => array("project_name", "")));
    }
//End SetOrder Method

//Prepare Method @5-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @5-3111E6D2
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM projects";
        $this->SQL = "SELECT *  " .
        "FROM projects";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @5-B330F057
    function SetValues()
    {
        $this->project_name->SetDBValue($this->f("project_name"));
    }
//End SetValues Method

} //End projectsDataSource Class @5-FCB6E20C

Class clsRecordprojects1 { //projects1 Class @10-4C973668

//Variables @10-6C307B82

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

//Class_Initialize Event @10-647A3E59
    function clsRecordprojects1()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        $this->ds = new clsprojects1DataSource();
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "projects1";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->project_name = new clsControl(ccsTextBox, "project_name", " Name", ccsText, "", CCGetRequestParam("project_name", $Method));
            $this->project_name->Required = true;
            $this->folder_prefix = new clsControl(ccsTextBox, "folder_prefix", "Folder prefix", ccsText, "", CCGetRequestParam("folder_prefix", $Method));
            $this->folder_prefix->Required = true;
            $this->folder_name = new clsControl(ccsTextBox, "folder_name", "Folder name", ccsText, "", CCGetRequestParam("folder_name", $Method));
            $this->folder_name->Required = true;
            $this->Button_Insert = new clsButton("Button_Insert");
            $this->Button_Update = new clsButton("Button_Update");
            $this->Button_Delete = new clsButton("Button_Delete");
        }
    }
//End Class_Initialize Event

//Initialize Method @10-581062E4
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->ds->Parameters["urlproject_id"] = CCGetFromGet("project_id", "");
    }
//End Initialize Method

//Validate Method @10-48CFF722
    function Validate()
    {
        $Validation = true;
        $Where = "";
        $Validation = ($this->project_name->Validate() && $Validation);
        $Validation = ($this->folder_prefix->Validate() && $Validation);
        $Validation = ($this->folder_name->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @10-86C97B9B
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->project_name->Errors->Count());
        $errors = ($errors || $this->folder_prefix->Errors->Count());
        $errors = ($errors || $this->folder_name->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->ds->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @10-45EB6FAD
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
        $Redirect = "projects.php?" . CCGetQueryString("QueryString", Array("ccsForm", "Button_Insert", "Button_Update", "Button_Delete"));
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

//InsertRow Method @10-52019A82
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert");
        if(!$this->InsertAllowed) return false;
        $this->ds->project_name->SetValue($this->project_name->GetValue());
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

//UpdateRow Method @10-8BB0ABCE
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate");
        if(!$this->UpdateAllowed) return false;
        $this->ds->project_name->SetValue($this->project_name->GetValue());
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

//DeleteRow Method @10-EA88835F
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

//Show Method @10-F201F82F
    function Show()
    {
        global $Tpl;
        global $FileName;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");


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
                    echo "Error in Record projects1";
                }
                else if($this->ds->next_record())
                {
                    $this->ds->SetValues();
                    if(!$this->FormSubmitted)
                    {
                        $this->project_name->SetValue($this->ds->project_name->GetValue());
                        $this->folder_prefix->SetValue($this->ds->folder_prefix->GetValue());
                        $this->folder_name->SetValue($this->ds->folder_name->GetValue());
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
            $Error .= $this->project_name->Errors->ToString();
            $Error .= $this->folder_prefix->Errors->ToString();
            $Error .= $this->folder_name->Errors->ToString();
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
        $this->project_name->Show();
        $this->folder_prefix->Show();
        $this->folder_name->Show();
        $this->Button_Insert->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

} //End projects1 Class @10-FCB6E20C

class clsprojects1DataSource extends clsDBdres {  //projects1DataSource Class @10-85E9B0A6

//DataSource Variables @10-BB8F10FB
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $InsertParameters;
    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $project_name;
    var $folder_prefix;
    var $folder_name;
//End DataSource Variables

//Class_Initialize Event @10-2472D3D2
    function clsprojects1DataSource()
    {
        $this->ErrorBlock = "Record projects1/Error";
        $this->Initialize();
        $this->project_name = new clsField("project_name", ccsText, "");
        $this->folder_prefix = new clsField("folder_prefix", ccsText, "");
        $this->folder_name = new clsField("folder_name", ccsText, "");

    }
//End Class_Initialize Event

//Prepare Method @10-EC1AB7D2
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlproject_id", ccsInteger, "", "", $this->Parameters["urlproject_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "project_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @10-BB433E20
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->SQL = "SELECT project_name, folder_name, folder_prefix  " .
        "FROM folders INNER JOIN projects ON folders.folder_project_id = projects.project_id";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @10-23B4D83F
    function SetValues()
    {
        $this->project_name->SetDBValue($this->f("project_name"));
        $this->folder_prefix->SetDBValue($this->f("folder_prefix"));
        $this->folder_name->SetDBValue($this->f("folder_name"));
    }
//End SetValues Method

//Insert Method @10-C65E9477
    function Insert()
    {
        $project_name = new clsSQLParameter("ctrlproject_name", ccsText, "", "", $this->project_name->GetValue(), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert");
        $this->SQL = "INSERT INTO projects ("
             . "project_name"
             . ") VALUES ("
             . $this->ToSQL($project_name->GetDBValue(), $project_name->DataType)
             . ")";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Insert Method

//Update Method @10-094C8D54
    function Update()
    {
        $project_name = new clsSQLParameter("ctrlproject_name", ccsText, "", "", $this->project_name->GetValue(), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlproject_id", ccsInteger, "", "", CCGetFromGet("project_id", ""), "", false);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate");
        $wp->Criterion[1] = $wp->Operation(opEqual, "project_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = $wp->Criterion[1];
        $this->SQL = "UPDATE projects SET "
             . "project_name=" . $this->ToSQL($project_name->GetDBValue(), $project_name->DataType);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Update Method

//Delete Method @10-4468C6B3
    function Delete()
    {
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlproject_id", ccsInteger, "", "", CCGetFromGet("project_id", ""), "", false);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete");
        $wp->Criterion[1] = $wp->Operation(opEqual, "project_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = $wp->Criterion[1];
        $this->SQL = "DELETE FROM projects";
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Delete Method

} //End projects1DataSource Class @10-FCB6E20C

//Include Page implementation @3-5CD56755
include_once("./Footer.php");
//End Include Page implementation

//Initialize Page @1-113052AF
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

$FileName = "projects.php";
$Redirect = "";
$TemplateFileName = "projects.html";
$BlockToParse = "main";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4CBB5244
CCSecurityRedirect("10", "");
//End Authenticate User

//Initialize Objects @1-85041990
$DBdres = new clsDBdres();

// Controls
$Header = new clsHeader();
$Header->BindEvents();
$Header->TemplatePath = "./";
$Header->Initialize();
$projects = new clsGridprojects();
$projects1 = new clsRecordprojects1();
$Footer = new clsFooter();
$Footer->BindEvents();
$Footer->TemplatePath = "./";
$Footer->Initialize();
$projects->Initialize();
$projects1->Initialize();

// Events
include("./projects_events.php");
BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");
//End Initialize Objects

//Initialize HTML Template @1-A0111C9D
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate();
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main");
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-48D6290F
$Header->Operations();
$projects1->Operation();
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

//Show Page @1-D5C85C26
$Header->Show("Header");
$projects->Show();
$projects1->Show();
$Footer->Show("Footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-1BAB0CD6
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBdres->close();
unset($Tpl);
//End Unload Page


?>
