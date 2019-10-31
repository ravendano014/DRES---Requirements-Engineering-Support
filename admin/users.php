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

Class clsRecordusersSearch { //usersSearch Class @5-4B2A360C

//Variables @5-E2EC6027

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

//Class_Initialize Event @5-35890FA8
    function clsRecordusersSearch()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        if($this->Visible)
        {
            $this->ComponentName = "usersSearch";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->s_name = new clsControl(ccsTextBox, "s_name", "s_name", ccsText, "", CCGetRequestParam("s_name", $Method));
            $this->s_user_level = new clsControl(ccsListBox, "s_user_level", "s_user_level", ccsInteger, "", CCGetRequestParam("s_user_level", $Method));
            $this->s_user_level->DSType = dsTable;
            list($this->s_user_level->BoundColumn, $this->s_user_level->TextColumn, $this->s_user_level->DBFormat) = array("level_id", "level_name", "");
            $this->s_user_level->ds = new clsDBdres();
            $this->s_user_level->ds->SQL = "SELECT *  " .
"FROM levels";
            $this->ClearParameters = new clsControl(ccsLink, "ClearParameters", "ClearParameters", ccsText, "", CCGetRequestParam("ClearParameters", $Method));
            $this->ClearParameters->Parameters = CCGetQueryString("QueryString", Array("s_user_name", "s_user_level", "ccsForm"));
            $this->ClearParameters->Page = "users.php";
            $this->Button_DoSearch = new clsButton("Button_DoSearch");
        }
    }
//End Class_Initialize Event

//Validate Method @5-0A491FA8
    function Validate()
    {
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_name->Validate() && $Validation);
        $Validation = ($this->s_user_level->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @5-23DF4830
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_name->Errors->Count());
        $errors = ($errors || $this->s_user_level->Errors->Count());
        $errors = ($errors || $this->ClearParameters->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @5-2FFEA396
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
            $this->PressedButton = "Button_DoSearch";
            if(strlen(CCGetParam("Button_DoSearch", ""))) {
                $this->PressedButton = "Button_DoSearch";
            }
        }
        $Redirect = "users.php?" . CCGetQueryString("Form", Array("ccsForm", "Button_DoSearch"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_DoSearch") {
                if(!CCGetEvent($this->Button_DoSearch->CCSEvents, "OnClick")) {
                    $Redirect = "";
                } else {
                    $Redirect = "users.php?" . CCMergeQueryStrings(CCGetQueryString("Form", Array("Button_DoSearch")));
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @5-EB8474E4
    function Show()
    {
        global $Tpl;
        global $FileName;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");

        $this->s_user_level->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        if(!$this->FormSubmitted)
        {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error .= $this->s_name->Errors->ToString();
            $Error .= $this->s_user_level->Errors->ToString();
            $Error .= $this->ClearParameters->Errors->ToString();
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
        $this->s_name->Show();
        $this->s_user_level->Show();
        $this->ClearParameters->Show();
        $this->Button_DoSearch->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End usersSearch Class @5-FCB6E20C

class clsGridusers { //users class @4-0CB76799

//Variables @4-8FF08345

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
    var $AltRowControls;
    var $IsAltRow;
    var $Sorter_user_name;
    var $Sorter_user_login;
    var $Sorter_user_level;
    var $Sorter_user_date_registered;
    var $Sorter_user_date_logged;
    var $Navigator;
//End Variables

//Class_Initialize Event @4-27513138
    function clsGridusers()
    {
        global $FileName;
        $this->ComponentName = "users";
        $this->Visible = True;
        $this->IsAltRow = false;
        $this->Errors = new clsErrors();
        $this->ds = new clsusersDataSource();
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 30;
        else
            $this->PageSize = intval($this->PageSize);
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("usersOrder", "");
        $this->SorterDirection = CCGetParam("usersDir", "");

        $this->user_name = new clsControl(ccsLink, "user_name", "user_name", ccsText, "", CCGetRequestParam("user_name", ccsGet));
        $this->user_login = new clsControl(ccsLabel, "user_login", "user_login", ccsText, "", CCGetRequestParam("user_login", ccsGet));
        $this->user_level = new clsControl(ccsLabel, "user_level", "user_level", ccsText, "", CCGetRequestParam("user_level", ccsGet));
        $this->user_date_registered = new clsControl(ccsLabel, "user_date_registered", "user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("user_date_registered", ccsGet));
        $this->user_date_logged = new clsControl(ccsLabel, "user_date_logged", "user_date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("user_date_logged", ccsGet));
        $this->Alt_user_name = new clsControl(ccsLink, "Alt_user_name", "Alt_user_name", ccsText, "", CCGetRequestParam("Alt_user_name", ccsGet));
        $this->Alt_user_login = new clsControl(ccsLabel, "Alt_user_login", "Alt_user_login", ccsText, "", CCGetRequestParam("Alt_user_login", ccsGet));
        $this->Alt_user_level = new clsControl(ccsLabel, "Alt_user_level", "Alt_user_level", ccsText, "", CCGetRequestParam("Alt_user_level", ccsGet));
        $this->Alt_user_date_registered = new clsControl(ccsLabel, "Alt_user_date_registered", "Alt_user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("Alt_user_date_registered", ccsGet));
        $this->Alt_user_date_logged = new clsControl(ccsLabel, "Alt_user_date_logged", "Alt_user_date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), CCGetRequestParam("Alt_user_date_logged", ccsGet));
        $this->users_TotalRecords = new clsControl(ccsLabel, "users_TotalRecords", "users_TotalRecords", ccsText, "", CCGetRequestParam("users_TotalRecords", ccsGet));
        $this->Sorter_user_name = new clsSorter($this->ComponentName, "Sorter_user_name", $FileName);
        $this->Sorter_user_login = new clsSorter($this->ComponentName, "Sorter_user_login", $FileName);
        $this->Sorter_user_level = new clsSorter($this->ComponentName, "Sorter_user_level", $FileName);
        $this->Sorter_user_date_registered = new clsSorter($this->ComponentName, "Sorter_user_date_registered", $FileName);
        $this->Sorter_user_date_logged = new clsSorter($this->ComponentName, "Sorter_user_date_logged", $FileName);
        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link1->Parameters = CCGetQueryString("QueryString", Array("user_id", "ccsForm"));
        $this->Link1->Page = "user_edit.php";
        $this->Navigator = new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered);
    }
//End Class_Initialize Event

//Initialize Method @4-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @4-F9D5A4A3
    function Show()
    {
        global $Tpl;
        if(!$this->Visible) return;

        $ShownRecords = 0;

        $this->ds->Parameters["urls_name"] = CCGetFromGet("s_name", "");
        $this->ds->Parameters["urls_user_level"] = CCGetFromGet("s_user_level", "");

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
                if(!$this->IsAltRow)
                {
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                    $this->user_name->SetValue($this->ds->user_name->GetValue());
                    $this->user_name->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                    $this->user_name->Parameters = CCAddParam($this->user_name->Parameters, "user_id", $this->ds->f("user_id"));
                    $this->user_name->Page = "user_edit.php";
                    $this->user_login->SetValue($this->ds->user_login->GetValue());
                    $this->user_level->SetValue($this->ds->user_level->GetValue());
                    $this->user_date_registered->SetValue($this->ds->user_date_registered->GetValue());
                    $this->user_date_logged->SetValue($this->ds->user_date_logged->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                    $this->user_name->Show();
                    $this->user_login->Show();
                    $this->user_level->Show();
                    $this->user_date_registered->Show();
                    $this->user_date_logged->Show();
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                    $Tpl->parse("Row", true);
                }
                else
                {
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/AltRow";
                    $this->Alt_user_name->SetValue($this->ds->Alt_user_name->GetValue());
                    $this->Alt_user_name->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                    $this->Alt_user_name->Parameters = CCAddParam($this->Alt_user_name->Parameters, "user_id", $this->ds->f("user_id"));
                    $this->Alt_user_name->Page = "user_edit.php";
                    $this->Alt_user_login->SetValue($this->ds->Alt_user_login->GetValue());
                    $this->Alt_user_level->SetValue($this->ds->Alt_user_level->GetValue());
                    $this->Alt_user_date_registered->SetValue($this->ds->Alt_user_date_registered->GetValue());
                    $this->Alt_user_date_logged->SetValue($this->ds->Alt_user_date_logged->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                    $this->Alt_user_name->Show();
                    $this->Alt_user_login->Show();
                    $this->Alt_user_level->Show();
                    $this->Alt_user_date_registered->Show();
                    $this->Alt_user_date_logged->Show();
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                    $Tpl->parseto("AltRow", true, "Row");
                }
                $this->IsAltRow = (!$this->IsAltRow);
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
        $this->Navigator->PageNumber = $this->ds->AbsolutePage;
        $this->Navigator->TotalPages = $this->ds->PageCount();
        $this->users_TotalRecords->Show();
        $this->Sorter_user_name->Show();
        $this->Sorter_user_login->Show();
        $this->Sorter_user_level->Show();
        $this->Sorter_user_date_registered->Show();
        $this->Sorter_user_date_logged->Show();
        $this->Link1->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @4-0AE8B71B
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->user_name->Errors->ToString();
        $errors .= $this->user_login->Errors->ToString();
        $errors .= $this->user_level->Errors->ToString();
        $errors .= $this->user_date_registered->Errors->ToString();
        $errors .= $this->user_date_logged->Errors->ToString();
        $errors .= $this->Alt_user_name->Errors->ToString();
        $errors .= $this->Alt_user_login->Errors->ToString();
        $errors .= $this->Alt_user_level->Errors->ToString();
        $errors .= $this->Alt_user_date_registered->Errors->ToString();
        $errors .= $this->Alt_user_date_logged->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End users Class @4-FCB6E20C

class clsusersDataSource extends clsDBdres {  //usersDataSource Class @4-F78012C3

//DataSource Variables @4-57DB5F8D
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $user_name;
    var $user_login;
    var $user_level;
    var $user_date_registered;
    var $user_date_logged;
    var $Alt_user_name;
    var $Alt_user_login;
    var $Alt_user_level;
    var $Alt_user_date_registered;
    var $Alt_user_date_logged;
//End DataSource Variables

//Class_Initialize Event @4-A1A5EF0B
    function clsusersDataSource()
    {
        $this->ErrorBlock = "Grid users";
        $this->Initialize();
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_level = new clsField("user_level", ccsText, "");
        $this->user_date_registered = new clsField("user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));
        $this->user_date_logged = new clsField("user_date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));
        $this->Alt_user_name = new clsField("Alt_user_name", ccsText, "");
        $this->Alt_user_login = new clsField("Alt_user_login", ccsText, "");
        $this->Alt_user_level = new clsField("Alt_user_level", ccsText, "");
        $this->Alt_user_date_registered = new clsField("Alt_user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));
        $this->Alt_user_date_logged = new clsField("Alt_user_date_logged", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

    }
//End Class_Initialize Event

//SetOrder Method @4-0B075FA0
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "user_date_logged desc";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_user_name" => array("user_name", ""), 
            "Sorter_user_login" => array("user_login", ""), 
            "Sorter_user_level" => array("user_level", ""), 
            "Sorter_user_date_registered" => array("user_date_registered", ""), 
            "Sorter_user_date_logged" => array("user_date_logged", "")));
    }
//End SetOrder Method

//Prepare Method @4-61585B0E
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_name", ccsText, "", "", $this->Parameters["urls_name"], "", false);
        $this->wp->AddParameter("2", "urls_name", ccsText, "", "", $this->Parameters["urls_name"], "", false);
        $this->wp->AddParameter("3", "urls_user_level", ccsInteger, "", "", $this->Parameters["urls_user_level"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "user_name", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opContains, "level_name", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "user_level", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->Where = $this->wp->opAND(false, $this->wp->opOR(true, $this->wp->Criterion[1], $this->wp->Criterion[2]), $this->wp->Criterion[3]);
    }
//End Prepare Method

//Open Method @4-16FA985A
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM users INNER JOIN levels ON users.user_level = levels.level_id";
        $this->SQL = "SELECT users.*, level_role  " .
        "FROM users INNER JOIN levels ON users.user_level = levels.level_id";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @4-C19A8360
    function SetValues()
    {
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_level->SetDBValue($this->f("level_role"));
        $this->user_date_registered->SetDBValue(trim($this->f("user_date_registered")));
        $this->user_date_logged->SetDBValue(trim($this->f("user_date_logged")));
        $this->Alt_user_name->SetDBValue($this->f("user_name"));
        $this->Alt_user_login->SetDBValue($this->f("user_login"));
        $this->Alt_user_level->SetDBValue($this->f("level_role"));
        $this->Alt_user_date_registered->SetDBValue(trim($this->f("user_date_registered")));
        $this->Alt_user_date_logged->SetDBValue(trim($this->f("user_date_logged")));
    }
//End SetValues Method

} //End usersDataSource Class @4-FCB6E20C

//Include Page implementation @3-5CD56755
include_once("./Footer.php");
//End Include Page implementation

//Initialize Page @1-1D1DBCBA
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

$FileName = "users.php";
$Redirect = "";
$TemplateFileName = "users.html";
$BlockToParse = "main";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4CBB5244
CCSecurityRedirect("10", "");
//End Authenticate User

//Initialize Objects @1-C6D0288D
$DBdres = new clsDBdres();

// Controls
$Header = new clsHeader();
$Header->BindEvents();
$Header->TemplatePath = "./";
$Header->Initialize();
$usersSearch = new clsRecordusersSearch();
$users = new clsGridusers();
$Footer = new clsFooter();
$Footer->BindEvents();
$Footer->TemplatePath = "./";
$Footer->Initialize();
$users->Initialize();

// Events
include("./users_events.php");
BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");
//End Initialize Objects

//Initialize HTML Template @1-A0111C9D
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate();
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main");
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-25003A86
$Header->Operations();
$usersSearch->Operation();
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

//Show Page @1-D35F5D65
$Header->Show("Header");
$usersSearch->Show();
$users->Show();
$Footer->Show("Footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-1BAB0CD6
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBdres->close();
unset($Tpl);
//End Unload Page


?>
