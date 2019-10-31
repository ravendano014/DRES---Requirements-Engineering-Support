<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @2-3C19E0E6
include_once("./Header2.php");
//End Include Page implementation

Class clsRecordusers { //users Class @4-811DFF64

//Variables @4-6C68EA12

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
    var $ds;
    var $EditMode;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @4-02324E3F
    function clsRecordusers()
    {

        global $FileName;
        $this->Visible = true;
        $this->Errors = new clsErrors();
        $this->ds = new clsusersDataSource();
        $this->InsertAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "users";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_name = new clsControl(ccsTextBox, "user_name", "Name", ccsText, "", CCGetRequestParam("user_name", $Method));
            $this->user_name->Required = true;
            $this->user_login = new clsControl(ccsTextBox, "user_login", "Login", ccsText, "", CCGetRequestParam("user_login", $Method));
            $this->user_login->Required = true;
            $this->user_password = new clsControl(ccsTextBox, "user_password", "Password", ccsText, "", CCGetRequestParam("user_password", $Method));
            $this->user_password->Required = true;
            $this->user_email = new clsControl(ccsTextBox, "user_email", "Email", ccsText, "", CCGetRequestParam("user_email", $Method));
            $this->user_email->Required = true;
            $this->Button_Insert = new clsButton("Button_Insert");
        }
    }
//End Class_Initialize Event

//Initialize Method @4-C016A25E
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->ds->Parameters["urluser_id"] = CCGetFromGet("user_id", "");
    }
//End Initialize Method

//Validate Method @4-CCC30016
    function Validate()
    {
        $Validation = true;
        $Where = "";
        if($this->EditMode && strlen($this->ds->Where))
            $Where = " AND NOT (" . $this->ds->Where . ")";
        $this->ds->user_login->SetValue($this->user_login->GetValue());
        if(CCDLookUp("COUNT(*)", "users", "user_login=" . $this->ds->ToSQL($this->ds->user_login->GetDBValue(), $this->ds->user_login->DataType) . $Where, $this->ds) > 0)
            $this->user_login->Errors->addError("The value in field Login is already in database.");
        if(strlen($this->user_email->GetText()) && !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $this->user_email->GetText())) {
            $this->user_email->Errors->addError("Mask validation failed for field Email.");
        }
        $Validation = ($this->user_name->Validate() && $Validation);
        $Validation = ($this->user_login->Validate() && $Validation);
        $Validation = ($this->user_password->Validate() && $Validation);
        $Validation = ($this->user_email->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @4-B896D47D
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_name->Errors->Count());
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_password->Errors->Count());
        $errors = ($errors || $this->user_email->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->ds->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @4-634D8CF0
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
            $this->PressedButton = "Button_Insert";
            if(strlen(CCGetParam("Button_Insert", ""))) {
                $this->PressedButton = "Button_Insert";
            }
        }
        $Redirect = "../?" . CCGetQueryString("QueryString", Array("ccsForm", "Button_Insert"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_Insert") {
                if(!CCGetEvent($this->Button_Insert->CCSEvents, "OnClick") || !$this->InsertRow()) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//InsertRow Method @4-CF506343
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert");
        if(!$this->InsertAllowed) return false;
        $this->ds->user_name->SetValue($this->user_name->GetValue());
        $this->ds->user_login->SetValue($this->user_login->GetValue());
        $this->ds->user_password->SetValue($this->user_password->GetValue());
        $this->ds->user_email->SetValue($this->user_email->GetValue());
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

//Show Method @4-C74EADA1
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
                    echo "Error in Record users";
                }
                else if($this->ds->next_record())
                {
                    $this->ds->SetValues();
                    if(!$this->FormSubmitted)
                    {
                        $this->user_name->SetValue($this->ds->user_name->GetValue());
                        $this->user_login->SetValue($this->ds->user_login->GetValue());
                        $this->user_password->SetValue($this->ds->user_password->GetValue());
                        $this->user_email->SetValue($this->ds->user_email->GetValue());
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
            $Error .= $this->user_email->Errors->ToString();
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
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->user_name->Show();
        $this->user_login->Show();
        $this->user_password->Show();
        $this->user_email->Show();
        $this->Button_Insert->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

} //End users Class @4-FCB6E20C

class clsusersDataSource extends clsDBdres {  //usersDataSource Class @4-F78012C3

//DataSource Variables @4-B8C70248
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $InsertParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_name;
    var $user_login;
    var $user_password;
    var $user_email;
//End DataSource Variables

//Class_Initialize Event @4-BCA5EC12
    function clsusersDataSource()
    {
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_password = new clsField("user_password", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");

    }
//End Class_Initialize Event

//Prepare Method @4-BAD07477
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urluser_id", ccsInteger, "", "", $this->Parameters["urluser_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @4-DC1AA46D
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

//SetValues Method @4-FB343F1C
    function SetValues()
    {
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_password->SetDBValue($this->f("user_password"));
        $this->user_email->SetDBValue($this->f("user_email"));
    }
//End SetValues Method

//Insert Method @4-E79FECBD
    function Insert()
    {
        $user_name = new clsSQLParameter("ctrluser_name", ccsText, "", "", $this->user_name->GetValue(), "", false, $this->ErrorBlock);
        $user_login = new clsSQLParameter("ctrluser_login", ccsText, "", "", $this->user_login->GetValue(), "", false, $this->ErrorBlock);
        $user_password = new clsSQLParameter("ctrluser_password", ccsText, "", "", $this->user_password->GetValue(), "", false, $this->ErrorBlock);
        $user_email = new clsSQLParameter("ctrluser_email", ccsText, "", "", $this->user_email->GetValue(), "", false, $this->ErrorBlock);
        $user_level = new clsSQLParameter("expr15", ccsInteger, "", "", "0", "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert");
        $this->SQL = "INSERT INTO users ("
             . "user_name, "
             . "user_login, "
             . "user_password, "
             . "user_email, "
             . "user_level"
             . ") VALUES ("
             . $this->ToSQL($user_name->GetDBValue(), $user_name->DataType) . ", "
             . $this->ToSQL($user_login->GetDBValue(), $user_login->DataType) . ", "
             . $this->ToSQL($user_password->GetDBValue(), $user_password->DataType) . ", "
             . $this->ToSQL($user_email->GetDBValue(), $user_email->DataType) . ", "
             . $this->ToSQL($user_level->GetDBValue(), $user_level->DataType)
             . ")";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert");
        $this->query($this->SQL);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert");
        if($this->Errors->Count() > 0)
            $this->Errors->AddError($this->Errors->ToString());
        $this->close();
    }
//End Insert Method

} //End usersDataSource Class @4-FCB6E20C

//Include Page implementation @3-5CD56755
include_once("./Footer.php");
//End Include Page implementation

//Initialize Page @1-0339229D
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

$FileName = "register.php";
$Redirect = "";
$TemplateFileName = "register.html";
$BlockToParse = "main";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-5FFC947A
$DBdres = new clsDBdres();

// Controls
$Header2 = new clsHeader2();
$Header2->BindEvents();
$Header2->TemplatePath = "./";
$Header2->Initialize();
$users = new clsRecordusers();
$Footer = new clsFooter();
$Footer->BindEvents();
$Footer->TemplatePath = "./";
$Footer->Initialize();
$users->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");
//End Initialize Objects

//Initialize HTML Template @1-A0111C9D
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate();
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main");
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-92B6116F
$Header2->Operations();
$users->Operation();
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

//Show Page @1-DF98AE5B
$Header2->Show("Header2");
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
