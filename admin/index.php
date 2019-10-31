<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @14-39DC296A
include_once("./Header.php");
//End Include Page implementation

class clsGridusers { //users class @7-0CB76799

//Variables @7-E04B4E0A

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
    var $Sorter_user_date_registered;
//End Variables

//Class_Initialize Event @7-1C0C56F5
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
            $this->PageSize = 100;
        else
            $this->PageSize = intval($this->PageSize);
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("usersOrder", "");
        $this->SorterDirection = CCGetParam("usersDir", "");

        $this->user_name = new clsControl(ccsLink, "user_name", "user_name", ccsText, "", CCGetRequestParam("user_name", ccsGet));
        $this->user_date_registered = new clsControl(ccsLabel, "user_date_registered", "user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("user_date_registered", ccsGet));
        $this->Alt_user_name = new clsControl(ccsLink, "Alt_user_name", "Alt_user_name", ccsText, "", CCGetRequestParam("Alt_user_name", ccsGet));
        $this->Alt_user_date_registered = new clsControl(ccsLabel, "Alt_user_date_registered", "Alt_user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"), CCGetRequestParam("Alt_user_date_registered", ccsGet));
        $this->Sorter_user_name = new clsSorter($this->ComponentName, "Sorter_user_name", $FileName);
        $this->Sorter_user_date_registered = new clsSorter($this->ComponentName, "Sorter_user_date_registered", $FileName);
    }
//End Class_Initialize Event

//Initialize Method @7-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @7-91BFD151
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
                if(!$this->IsAltRow)
                {
                    $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                    $this->user_name->SetValue($this->ds->user_name->GetValue());
                    $this->user_name->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                    $this->user_name->Parameters = CCAddParam($this->user_name->Parameters, "user_id", $this->ds->f("user_id"));
                    $this->user_name->Page = "user_edit.php";
                    $this->user_date_registered->SetValue($this->ds->user_date_registered->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                    $this->user_name->Show();
                    $this->user_date_registered->Show();
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
                    $this->Alt_user_date_registered->SetValue($this->ds->Alt_user_date_registered->GetValue());
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                    $this->Alt_user_name->Show();
                    $this->Alt_user_date_registered->Show();
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
        $this->Sorter_user_name->Show();
        $this->Sorter_user_date_registered->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @7-9D802AE5
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->user_name->Errors->ToString();
        $errors .= $this->user_date_registered->Errors->ToString();
        $errors .= $this->Alt_user_name->Errors->ToString();
        $errors .= $this->Alt_user_date_registered->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End users Class @7-FCB6E20C

class clsusersDataSource extends clsDBdres {  //usersDataSource Class @7-F78012C3

//DataSource Variables @7-E76048B5
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $user_name;
    var $user_date_registered;
    var $Alt_user_name;
    var $Alt_user_date_registered;
//End DataSource Variables

//Class_Initialize Event @7-33C49FDC
    function clsusersDataSource()
    {
        $this->ErrorBlock = "Grid users";
        $this->Initialize();
        $this->user_name = new clsField("user_name", ccsText, "");
        $this->user_date_registered = new clsField("user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));
        $this->Alt_user_name = new clsField("Alt_user_name", ccsText, "");
        $this->Alt_user_date_registered = new clsField("Alt_user_date_registered", ccsDate, Array("yyyy", "-", "mm", "-", "dd", " ", "H", ":", "nn", ":", "ss"));

    }
//End Class_Initialize Event

//SetOrder Method @7-374A7BFD
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "user_date_registered";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_user_name" => array("user_name", ""), 
            "Sorter_user_date_registered" => array("user_date_registered", "")));
    }
//End SetOrder Method

//Prepare Method @7-90CD7D9A
    function Prepare()
    {
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->Criterion[1] = "user_level = 0";
        $this->Where = $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @7-28C412B2
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM users";
        $this->SQL = "SELECT *  " .
        "FROM users";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @7-7B66A7EC
    function SetValues()
    {
        $this->user_name->SetDBValue($this->f("user_name"));
        $this->user_date_registered->SetDBValue(trim($this->f("user_date_registered")));
        $this->Alt_user_name->SetDBValue($this->f("user_name"));
        $this->Alt_user_date_registered->SetDBValue(trim($this->f("user_date_registered")));
    }
//End SetValues Method

} //End usersDataSource Class @7-FCB6E20C

//Include Page implementation @15-5CD56755
include_once("./Footer.php");
//End Include Page implementation

//Initialize Page @1-FE486DDE
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

$FileName = "index.php";
$Redirect = "";
$TemplateFileName = "index.html";
$BlockToParse = "main";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-4CBB5244
CCSecurityRedirect("10", "");
//End Authenticate User

//Initialize Objects @1-156F9E3B
$DBdres = new clsDBdres();

// Controls
$Header = new clsHeader();
$Header->BindEvents();
$Header->TemplatePath = "./";
$Header->Initialize();
$users = new clsGridusers();
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

//Execute Components @1-351F985C
$Header->Operations();
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

//Show Page @1-8D0414C5
$Header->Show("Header");
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
