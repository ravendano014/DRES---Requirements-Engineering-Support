<?php
class clsFooter { //Footer class @1-D2159D74

//Variables @1-1987AB94
    var $FileName = "";
    var $Redirect = "";
    var $Tpl = "";
    var $TemplateFileName = "";
    var $BlockToParse = "";
    var $ComponentName = "";

    // Events;
    var $CCSEvents = "";
    var $CCSEventResult = "";
    var $TemplatePath;
    var $Visible;
//End Variables

//Class_Initialize Event @1-BE31E883
    function clsFooter()
    {
        $this->Visible = true;
        if($this->Visible)
        {
            $this->FileName = "Footer.php";
            $this->Redirect = "";
            $this->TemplateFileName = "Footer.html";
            $this->BlockToParse = "main";
        }
    }
//End Class_Initialize Event

//Class_Terminate Event @1-A3749DF6
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload");
    }
//End Class_Terminate Event

//BindEvents Method @1-236CCD5D
    function BindEvents()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize");
    }
//End BindEvents Method

//Operations Method @1-7E2A14CF
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
    }
//End Operations Method

//Initialize Method @1-EDD74DD5
    function Initialize()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView");
        if(!$this->Visible)
            return "";
    }
//End Initialize Method

//Show Method @1-5D780256
    function Show($Name)
    {
        global $Tpl;
        $block_path = $Tpl->block_path;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible)
            return "";
        $Tpl->LoadTemplate($this->TemplatePath . $this->TemplateFileName, $Name);
        $Tpl->block_path = $Name;
        $Tpl->Parse();
        $Tpl->SetVar($Name, $Tpl->GetVar());
        $Tpl->block_path = $block_path;
    }
//End Show Method

} //End Footer Class @1-FCB6E20C


?>
