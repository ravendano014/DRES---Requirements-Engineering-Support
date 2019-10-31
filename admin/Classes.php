<?php
        
//File Description @0-AEB1E349
//======================================================
//
//  This file contains the following classes:
//      class clsSQLParameters
//      class clsSQLParameter
//      class clsControl
//      class clsField
//      class clsButton
//      class clsFileUpload
//      class clsDatePicker
//      class clsErrors
//
//======================================================
//End File Description

//Constant List @0-EB100734

// ------- Controls ---------------
define("ccsLabel",        1);
define("ccsLink",         2);
define("ccsTextBox",      3);
define("ccsTextArea",     4);
define("ccsListBox",      5);
define("ccsRadioButton",  6);
define("ccsButton",       7);
define("ccsCheckBox",     8);
define("ccsImage",        9);
define("ccsImageLink",    10);
define("ccsHidden",       11);
define("ccsCheckBoxList", 12);

$ControlTypes = array(
  "", "Label","Link","TextBox","TextArea","ListBox","RadioButton",
  "Button","CheckBox","Image","ImageLink","Hidden","CheckBoxList"
);


// ------- Operators --------------
define("opEqual",              1);
define("opNotEqual",           2);
define("opLessThan",           3);
define("opLessThanOrEqual",    4);
define("opGreaterThan",        5);
define("opGreaterThanOrEqual", 6);
define("opBeginsWith",         7);
define("opNotBeginsWith",      8);
define("opEndsWith",           9);
define("opNotEndsWith",        10);
define("opContains",           11);
define("opNotContains",        12);
define("opIsNull",             13);
define("opNotNull",            14);

// ------- Datasource types -------
define("dsTable",        1);
define("dsSQL",          2);
define("dsProcedure",    3);
define("dsListOfValues", 4);
define("dsEmpty",        5);

// ------- CheckBox states --------
define("ccsChecked", true);
define("ccsUnchecked", false);


//End Constant List

//CCCheckValue @0-962BACE6
function CCCheckValue($Value, $DataType)
{
  $result = false;
  if($DataType == ccsInteger)
    $result = is_int($Value); 
  else if($DataType == ccsFloat)
    $result = is_float($Value);
  else if($DataType == ccsDate)
    $result = (is_array($Value) || is_int($Value));
  else if($DataType == ccsBoolean)
    $result = is_bool($Value); 
  return $result;
}
//End CCCheckValue

//clsSQLParameters Class @0-6A4B5147

class clsSQLParameters
{
  
  var $Connection;
  var $Criterion;
  var $AssembledWhere;
  var $Errors;
  var $DataSource;
  var $AllParametersSet;
  var $ErrorBlock;

  var $Parameters;

  function clsSQLParameters($ErrorBlock = "")
  {
    $this->ErrorBlock = $ErrorBlock;
  }

  function SetParameters($Name, $NewParameter)
  {
    $this->Parameters[$Name] = $NewParameter;
  }

  function AddParameter($ParameterID, $ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull = false)
  {
    $this->Parameters[$ParameterID] = new clsSQLParameter($ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull, $this->ErrorBlock);
  }

  function AllParamsSet()
  {
    $blnResult = true;

    if(isset($this->Parameters) && is_array($this->Parameters))
    {
      reset($this->Parameters);
      while ($blnResult && list ($key, $Parameter) = each ($this->Parameters)) 
      {
        if($Parameter->GetValue() === "" && $Parameter->GetValue() !== false && $Parameter->UseIsNull === false)
          $blnResult = false;
      }
    }
     return $blnResult;
  }

  function GetDBValue($ParameterID)
  {
    return $this->Parameters[$ParameterID]->GetDBValue();
  }

  function opAND($Brackets, $strLeft, $strRight)
  {
    $strResult = "";
    if (strlen($strLeft))
    {
      if (strlen($strRight)) 
      {
        $strResult = $strLeft . " AND " . $strRight;
        if ($Brackets) 
          $strResult = " (" . $strResult . ") ";
      }
      else
      {
        $strResult = $strLeft;
      }
    }
    else
    {
      if (strlen($strRight)) 
        $strResult = $strRight;
    }
    return $strResult;
  }

  function opOR($Brackets, $strLeft, $strRight)
  {
    $strResult = "";
    if (strlen($strLeft))
    {
      if (strlen($strRight))
      {
        $strResult = $strLeft . " OR " . $strRight;
        if ($Brackets) 
          $strResult = " (" . $strResult . ") ";
      }
      else
      {
        $strResult = $strLeft;
      }
    }
    else
    {
      if (strlen($strRight))
        $strResult = $strRight;
    }
    return $strResult;
  }

  function Operation($Operation, $FieldName, $DBValue, $SQLText, $UseIsNull = false)
  {
    $Result = "";

    if(strlen($DBValue) || $DBValue === false)
    {
      $SQLValue = $SQLText;
      if(substr($SQLValue, 0, 1) == "'")
        $SQLValue = substr($SQLValue, 1, strlen($SQLValue) - 2);

      switch ($Operation)
      {
        case opEqual:
          $Result = $FieldName . " = " . $SQLText;
          break;
        case opNotEqual:
          $Result = $FieldName . " <> " . $SQLText;
          break;
        case opLessThan:
          $Result = $FieldName . " < " . $SQLText;
          break;
        case opLessThanOrEqual:
          $Result = $FieldName . " <= " . $SQLText;
          break;
        case opGreaterThan:
          $Result = $FieldName . " > " . $SQLText;
          break;
        case opGreaterThanOrEqual:
          $Result = $FieldName . " >= " . $SQLText;
          break;                                
        case opBeginsWith:
          $Result = $FieldName . " like '" . $SQLValue . "%'";
          break;
        case opNotBeginsWith:
          $Result = $FieldName . " not like '" . $SQLValue . "%'";
          break;
        case opEndsWith:
          $Result = $FieldName . " like '%" . $SQLValue . "'";
          break;
        case opNotEndsWith:
          $Result = $FieldName . " not like '%" . $SQLValue . "'";
          break;
        case opContains:
          $Result = $FieldName . " like '%" . $SQLValue . "%'";
          break;
        case opNotContains:
          $Result = $FieldName . " not like '%" . $SQLValue . "%'";
          break;
        case opIsNull:
          $Result = $FieldName . " IS NULL";
          break;
        case opNotNull:
          $Result = $FieldName . " IS NOT NULL";
          break;
      }
    } 
    else if ($UseIsNull) 
    {
      switch ($Operation)
      {
        case opEqual:
        case opLessThan:
        case opLessThanOrEqual:
        case opGreaterThan:
        case opGreaterThanOrEqual:
        case opBeginsWith:
        case opEndsWith:
        case opContains:
        case opIsNull:
          $Result = $FieldName . " IS NULL";
          break;
        case opNotEqual:
        case opNotEndsWith:
        case opNotBeginsWith:
        case opNotContains:
        case opNotNull:
          $Result = $FieldName . " IS NOT NULL";
          break;
      }

    }

    return $Result;
  }
}
//End clsSQLParameters Class

//clsSQLParameter Class @0-C4D9E48A
class clsSQLParameter
{
  var $Errors;
  var $DataType;
  var $Format;
  var $DBFormat;
  var $Link;
  var $Caption;
  var $ErrorBlock;
  var $UseIsNull;

  var $Value;
  var $DBValue;
  var $Text;
  

  function clsSQLParameter($ParameterSource, $DataType, $Format, $DBFormat, $InitValue, $DefaultValue, $UseIsNull = false, $ErrorBlock = "")
  {
    $this->Errors = new clsErrors();
    $this->ErrorBlock = $ErrorBlock;
    $this->UseIsNull = $UseIsNull;

    $this->Caption = $ParameterSource;
    $this->DataType = $DataType;
    $this->Format = $Format;
    $this->DBFormat = $DBFormat;
    if(is_array($InitValue) && $DataType != ccsDate)
      $this->SetText(join(",", $InitValue));
    else if(is_array($InitValue) || strlen($InitValue))
      $this->SetText($InitValue);
    else
      $this->SetText($DefaultValue);
  }

  function GetParsedValue($ParsingValue, $Format)
  {
    global $Tpl;
    $varResult = "";

    if (strlen($ParsingValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          if (CCValidateDateMask($ParsingValue, $Format))
          {
            $varResult = CCParseDate($ParsingValue, $Format);
            if(!CCValidateDate($varResult))
            {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. ($ParsingValue)");
              $varResult = "";
            }
          }
          else
          {
            if (is_array($Format))
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . join("", $this->Format) . " ($ParsingValue)");
            else 
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. ($ParsingValue)");
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($ParsingValue, $Format))
            $varResult = CCParseBoolean($ParsingValue, $Format);
          else
          {
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid. ($ParsingValue)");
          }
          break;
        case ccsInteger:
          if (CCValidateNumber($ParsingValue, $Format))
            $varResult = CCParseInteger($ParsingValue, $Format);
          else
          {
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid. ($ParsingValue)");
          }
          break;
        case ccsFloat:
          if (CCValidateNumber($ParsingValue, $Format) )
            $varResult = CCParseFloat($ParsingValue, $Format);
          else 
          {
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid. ($ParsingValue)");
          }
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($ParsingValue);
          break;
      }
      if($this->Errors->Count() > 0)
      {
        if(strlen($this->ErrorBlock))
          $Tpl->replaceblock($this->ErrorBlock, $this->Errors->ToString());
        else
          echo $this->Errors->ToString();
      }
    }

    return $varResult;
  }

  function GetFormatedValue($Format)
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $Format);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $Format);
        break;
      case ccsInteger:
      case ccsFloat:
        $strResult = CCFormatNumber($this->Value, $Format);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function SetValue($Value)
  {
    $this->Value = $Value;
    $this->Text = $this->GetFormatedValue($this->Format);
    $this->DBValue = $this->GetFormatedValue($this->DBFormat);
  }

  function SetText($Text)
  {
    if(CCCheckValue($Text, $this->DataType)) {
      $this->SetValue($Text);
    } else {
      $this->Text = $Text;
      $this->Value = $this->GetParsedValue($this->Text, $this->Format);
      $this->DBValue = $this->GetFormatedValue($this->DBFormat);
    }
  }

  function SetDBValue($DBValue)
  {
    $this->DBValue = $DBValue;
    $this->Value = $this->GetParsedValue($this->DBValue, $this->DBFormat);
    $this->Text = $this->GetFormatedValue($this->Format);
  }

  function GetValue()
  {
    return $this->Value;
  }

  function GetText()
  {
    return $this->Text;
  }

  function GetDBValue()
  {
    return $this->DBValue;
  }

}

//End clsSQLParameter Class

//clsControl Class @0-7AA90D23
Class clsControl
{
  var $Errors;
  var $DataType;
  var $DSType;
  var $Format;
  var $DBFormat;
  var $Caption;
  var $ControlType;
  var $ControlTypeName;
  var $Name;
  var $BlockName;
  var $HTML;
  var $Required;
  var $CheckedValue;
  var $UncheckedValue;
  var $State;
  var $BoundColumn;
  var $TextColumn;
  var $Multiple;
  var $Visible;

  var $Page;
  var $Parameters;

  var $Value;
  var $Text;
  var $Values;

  var $CCSEvents;
  var $CCSEventResult;

  function clsControl($ControlType, $Name, $Caption, $DataType, $Format, $InitValue = "")
  {
    global $ControlTypes;

    $this->Value = "";
    $this->Text = "";
    $this->Page = "";
    $this->Parameters = "";
    $this->CCSEvents = "";
    $this->Values = "";
    $this->BoundColumn = "";
    $this->TextColumn = "";
    $this->Visible = true;

    $this->Required = false;
    $this->HTML = false;
    $this->Multiple = false;

    $this->Errors = new clsErrors;

    $this->Name = $Name;
    $this->BlockName = $ControlTypes[$ControlType] . " " . $Name;
    $this->ControlType = $ControlType;
    $this->DataType = $DataType;
    $this->DSType = dsEmpty;
    $this->Format = $Format;
    $this->Caption = $Caption;
    if(is_array($InitValue))
      $this->Value = $InitValue;
    else if(strlen($InitValue))
      $this->SetText($InitValue);
  }

  function Validate()
  {
    $validation = true;
    if($this->Required && $this->Value === "" && $this->Errors->Count() == 0)
    {
      $FieldName = strlen($this->Caption) ? $this->Caption : $this->Name;
      $this->Errors->addError("The value in field " . $FieldName . " is required.");
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
    return ($this->Errors->Count() == 0);
  }

  function GetParsedValue($ParsingValue)
  {
    $varResult = "";
    if($this->Multiple && is_array($ParsingValue)) {
      $ParsingValue = $ParsingValue[0];
    }
    if(CCCheckValue($ParsingValue, $this->DataType))
      $varResult = $ParsingValue;
    else if(strlen($ParsingValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          if (CCValidateDateMask($ParsingValue, $this->Format))
          {
            $varResult = CCParseDate($ParsingValue, $this->Format);
            if(!CCValidateDate($varResult))
            {
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
              $varResult = "";
            }
          }
          else if($this->Errors->Count() == 0)
          {
            if (is_array($this->Format))
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid. Use the following format: " . join("", $this->Format) . "");
            else 
              $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($ParsingValue, $this->Format))
            $varResult = CCParseBoolean($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0)
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          break;
        case ccsInteger:
          if (CCValidateNumber($ParsingValue, $this->Format))
            $varResult = CCParseInteger($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0)
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          break;
        case ccsFloat:
          if (CCValidateNumber($ParsingValue, $this->Format))
            $varResult = CCParseFloat($ParsingValue, $this->Format);
          else if($this->Errors->Count() == 0)
            $this->Errors->addError("The value in field " . $this->Caption . " is not valid.");
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($ParsingValue);
          break;
      }
    }

    return $varResult;
  }

  function GetFormatedValue()
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $this->Format);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $this->Format);
        break;
      case ccsInteger:
      case ccsFloat:
        $strResult = CCFormatNumber($this->Value, $this->Format);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function Prepare()
  {
    if($this->DSType == dsTable || $this->DSType == dsSQL || $this->DSType == dsProcedure)
    {
      if(!isset($this->ds->CCSEvents)) $this->ds->CCSEvents = "";
      if(!strlen($this->BoundColumn)) $this->BoundColumn = 0;
      if(!strlen($this->TextColumn)) $this->TextColumn = 1;
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "BeforeBuildSelect");
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "BeforeExecuteSelect");
      $FieldName = strlen($this->Caption) ? $this->Caption : $this->Name;
      list($this->Values, $this->Errors) = CCGetListValues($this->ds, $this->ds->SQL, $this->ds->Where, $this->ds->Order, $this->BoundColumn, $this->TextColumn, $this->DBFormat, $this->DataType, $this->Errors, $FieldName);
      $this->ds->close();
      $this->EventResult = CCGetEvent($this->ds->CCSEvents, "AfterExecuteSelect");
    }
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    $this->EventResult = CCGetEvent($this->CCSEvents, "BeforeShow");

    $ControlName = ($RowNumber === "") ? $this->Name : $this->Name . "_" . $RowNumber;
    if($this->Multiple) $ControlName = $ControlName . "[]";

    if(!$this->Visible) {
      $Tpl->SetVar($this->Name . "_Name", $ControlName);
      $Tpl->SetVar($this->Name, "");
      if($Tpl->BlockExists($this->BlockName))
        $Tpl->setblockvar($this->BlockName, "");
      return;
    }

    $Tpl->SetVar($this->Name . "_Name", $ControlName);
    switch($this->ControlType)
    {
      case ccsLabel:
        if ($this->HTML)
          $Tpl->SetVar($this->Name, $this->GetText());
        else 
          $Tpl->SetVar($this->Name, nl2br(htmlspecialchars($this->GetText())));
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsTextBox:
      case ccsTextArea:
      case ccsImage:
      case ccsHidden:
        $Tpl->SetVar($this->Name, htmlspecialchars($this->GetText()));
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsLink:
        if ($this->HTML)
          $Tpl->SetVar($this->Name, $this->GetText());
        else 
          $Tpl->SetVar($this->Name, nl2br(htmlspecialchars($this->GetText())));
        $Tpl->SetVar($this->Name . "_Src", $this->GetLink());
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsImageLink:
        $Tpl->SetVar($this->Name . "_Src", htmlspecialchars($this->GetText()));
        $Tpl->SetVar($this->Name, $this->GetLink());
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsCheckBox:
        if($this->Value)
          $Tpl->SetVar($this->Name, "CHECKED");
        else
          $Tpl->SetVar($this->Name, "");
        $Tpl->ParseSafe($this->BlockName, false);
        break;
      case ccsRadioButton:
        $BlockToParse = "RadioButton " . $this->Name;
        $Tpl->SetBlockVar($BlockToParse, "");
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $Text = $this->HTML ? $this->Values[$i][1] : htmlspecialchars($this->Values[$i][1]);
            $Selected = ($Value == $this->Value) ? " CHECKED" : "";
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType));
            $Tpl->SetVar("Value", $TextValue);
            $Tpl->SetVar("Check", $Selected);
            $Tpl->SetVar("Description", $Text);
            $Tpl->Parse($BlockToParse, true);
          }
        }
        break;
      case ccsCheckBoxList:
        $BlockToParse = "CheckBoxList " . $this->Name;
        $Tpl->SetBlockVar($BlockToParse, "");
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType));
            $Text = $this->HTML ? $this->Values[$i][1] : htmlspecialchars($this->Values[$i][1]);
            if($this->Multiple && is_array($this->Value)) {
              $Selected = "";
              for($j = 0; $j < sizeof($this->Value); $j++)
                if($Value == $this->Value[$j])
                  $Selected = " CHECKED";
            } else
              $Selected = ($Value == $this->Value) ? " CHECKED" : "";
            $Tpl->SetVar("Value", $TextValue);
            $Tpl->SetVar("Check", $Selected);
            $Tpl->SetVar("Description", $Text);
            $Tpl->Parse($BlockToParse, true);
          }
        }
        break;
      case ccsListBox:
        $Options = "";
        if(is_array($this->Values))
        {
          for($i = 0; $i < sizeof($this->Values); $i++)
          {
            $Value = $this->Values[$i][0];
            $TextValue = htmlspecialchars(CCFormatValue($Value, $this->Format, $this->DataType));
            $Text = htmlspecialchars($this->Values[$i][1]);
            if($this->Multiple && is_array($this->Value)) {
              $Selected = "";
              for($j = 0; $j < sizeof($this->Value); $j++)
                if($Value == $this->Value[$j])
                  $Selected = " SELECTED";
            } else
              $Selected = ($Value == $this->Value) ? " SELECTED" : "";
            $Options .= "<OPTION VALUE=\"" . $TextValue . "\"" . $Selected . ">" . $Text . "</OPTION>\n";
          }
        }
        $Tpl->SetVar($this->Name . "_Options", $Options);
        $Tpl->ParseSafe($this->BlockName, false);
        break;
    }
  }

  function SetValue($Value)
  {
    if($this->ControlType == ccsCheckBox)
      $this->Value = ($Value == $this->CheckedValue) ? true : false;
    else
      $this->Value = $Value;
    $this->Text = $this->GetFormatedValue();
  }

  function SetText($Text)
  {
    if(CCCheckValue($Text, $this->DataType)) {
      $this->SetValue($Text);
    } else {
      $this->Text = $Text;
      if($this->ControlType == ccsCheckBox)
        $this->Value = strlen($Text) ? true : false;
      else
        $this->Value = $this->GetParsedValue($this->Text);
    }
  }

  function GetValue()
  {
    if($this->ControlType == ccsCheckBox)
      $value = ($this->Value) ? $this->CheckedValue : $this->UncheckedValue;
    else if($this->Multiple && is_array($this->Value))
      $value = $this->Value[0];
    else
      $value = $this->Value;

    return $value;
  }

  function GetText()
  {
    if(!strlen($this->Text))
      $this->Text = $this->GetFormatedValue();
    return $this->Text;
  }

  function GetLink()
  {
    if(substr($this->Page, 0, 2) == "./")
      $this->Page = substr($this->Page, 2);
    if($this->Parameters == "")
      return $this->Page;
    else
      return $this->Page . "?" . $this->Parameters;
  }

  function SetLink($Link)
  {
    if(!strlen($Link))
    {
      $this->Page = "";
      $this->Parameters = "";
    }
    else
    {
      $LinkParts = explode("?", $Link);
      $this->Page = $LinkParts[0];
      $this->Parameters = (sizeof($LinkParts) == 2) ? $LinkParts[1] : "";
    }
  }

}

//End clsControl Class

//clsField Class @0-E4D1FC47
class clsField
{
  var $DataType;
  var $DBFormat;
  var $Name;
  var $Errors;

  var $Value;
  var $DBValue;

  function clsField($Name, $DataType, $DBFormat)
  {
    $this->Value = "";
    $this->DBValue = "";

    $this->Name = $Name;
    $this->DataType = $DataType;
    $this->DBFormat = $DBFormat;

    $this->Errors = new clsErrors;
  }

  function GetParsedValue()
  {
    $varResult = "";

    if (strlen($this->DBValue))
    {
      switch ($this->DataType)
      {
        case ccsDate:
          if (CCValidateDateMask($this->DBValue, $this->DBFormat))
          {
            $varResult = CCParseDate($this->DBValue, $this->DBFormat);
            if(!CCValidateDate($varResult))
            {
              $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
              $varResult = "";
            }
          }
          else
          {
            if (is_array($this->DBFormat))
              $this->Errors->addError("The value in field " . $this->Name . " is not valid. Use the following format: " . join("", $this->DBFormat) . "");
            else 
              $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          }
          break;
        case ccsBoolean:
          if (CCValidateBoolean($this->DBValue, $this->DBFormat))
            $varResult = CCParseBoolean($this->DBValue, $this->DBFormat);
          else
            $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          break;
        case ccsInteger:
          if (CCValidateNumber($this->DBValue, $this->DBFormat))
            $varResult = CCParseInteger($this->DBValue, $this->DBFormat);
          else 
            $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          break;
        case ccsFloat:
          if (CCValidateNumber($this->DBValue, $this->DBFormat) )
            $varResult = CCParseFloat($this->DBValue, $this->DBFormat);
          else 
            $this->Errors->addError("The value in field " . $this->Name . " is not valid.");
          break;
        case ccsText:
        case ccsMemo:
          $varResult = strval($this->DBValue);
          break;
      }
    }

    return $varResult;
  }

  function GetFormatedValue()
  {
    $strResult = "";
    switch($this->DataType)
    {
      case ccsDate:
        $strResult = CCFormatDate($this->Value, $this->DBFormat);
        break;
      case ccsBoolean:
        $strResult = CCFormatBoolean($this->Value, $this->DBFormat);
        break;
      case ccsInteger:
      case  ccsFloat:
        $strResult = CCFormatNumber($this->Value, $this->DBFormat);
        break;
      case ccsText:
      case ccsMemo:
        $strResult = strval($this->Value);
        break;
    }
    return $strResult;
  }

  function SetDBValue($DBValue)
  {
    $this->DBValue = $DBValue;
    $this->Value = $this->GetParsedValue();
  }

  function SetValue($Value)
  {
    $this->Value = $Value;
    $this->DBValue = $this->GetFormatedValue();
  }

  function GetValue()
  {
    return $this->Value;
  }

  function GetDBValue()
  {
    return $this->DBValue;
  }
}

//End clsField Class

//clsButton Class @0-41BABE2E
Class clsButton
{
  var $Name;
  var $Visible;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsButton($Name)
  {
    $this->Name = $Name;
    $this->Visible = true;
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    if($this->Visible)
    {
      $ControlName = ($RowNumber === "") ? $this->Name : $this->Name . "_" . $RowNumber;
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
      $Tpl->SetVar("Button_Name", $ControlName);
      $Tpl->Parse("Button " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("Button " . $this->Name, "");
    }
  }

}

//End clsButton Class

//clsFileUpload Class @0-75FBBE13
Class clsFileUpload
{
  var $Name;
  var $Caption;
  var $Visible;
  var $Required;

  var $TemporaryFolder;
  var $ProcessedFolder;
  var $AllowedMask;
  var $DisallowedMask;
  var $FileSizeLimit;
  var $Value;
  var $Text;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsFileUpload($Name, $Caption, $TemporaryFolder, $ProcessedFolder, $AllowedMask, $DisallowedMask, $FileSizeLimit)
  {
    $this->Errors = new clsErrors;

    $this->Name            = $Name;
    $this->Visible         = true;
    $this->Caption         = $Caption;
    $this->TemporaryFolder = $TemporaryFolder;
    $this->ProcessedFolder = $ProcessedFolder;
    $this->AllowedMask     = $AllowedMask;
    $this->DisallowedMask  = $DisallowedMask;
    $this->FileSizeLimit   = $FileSizeLimit;
    //$this->Value           = $InitValue;
    //$this->Text            = $InitValue;
    

  }

  function Validate()
  {
    $validation = true;
    if($this->Required && $this->Value === "" && $this->Errors->Count() == 0)
    {
      $FieldName = $this->Caption;
      $this->Errors->addError("The file attachment in field " . $FieldName . " is required.");
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate");
    return ($this->Errors->Count() == 0);
  }


  function Upload($RowNumber = "")
  {
    global $HTTP_POST_FILES;
    $FieldName = $this->Caption;
    if (strlen($RowNumber)) {
      $ControlName = $this->Name . "_" . $RowNumber;
      $DeleteControlName = $this->Name . "_Delete_" . $RowNumber;
    } else {
      $ControlName = $this->Name;
      $DeleteControlName = $this->Name . "_Delete";
    }
    if (strlen(CCGetParam($DeleteControlName))) { 
      // delete file from folder
      $ActualFileName = CCGetParam($ControlName);
      if( file_exists($this->ProcessedFolder . $ActualFileName) ) {
        unlink($this->ProcessedFolder . $ActualFileName);
      } else if ( file_exists($this->TemporaryFolder . $ActualFileName) ) {
        unlink($this->TemporaryFolder . $ActualFileName);
      }
      $this->Value = ""; $this->Text = "";
    } else if (isset ($HTTP_POST_FILES[$ControlName]) 
        && $HTTP_POST_FILES[$ControlName]["tmp_name"] != "none" 
        && strlen ($HTTP_POST_FILES[$ControlName]["tmp_name"])) {
      $this->Value = ""; $this->Text = "";
      $FileName = $HTTP_POST_FILES[$ControlName]["name"];
      if( !is_dir($this->TemporaryFolder) ) {
        $this->Errors->addError("Unable to upload file " . $FileName . " in field " . $FieldName . " - the temporary upload folder doesn't exist.");
      } else if ( !is_dir($this->ProcessedFolder) ) {
        $this->Errors->addError("Unable to upload file " . $FileName . " specified in field " . $FieldName . " - upload folder doesn't exist.");
      } else if($HTTP_POST_FILES[$ControlName]["size"] > $this->FileSizeLimit) {
        $this->Errors->addError("The file size in field " . $FieldName . " is too large.");
      } else if (($this->AllowedMask && !preg_match($this->AllowedMask, $FileName)) ||
        ($this->DisallowedMask && preg_match($this->DisallowedMask, $FileName)) ) {
        $this->Errors->addError("The file type specified in field " . $FieldName . " is not allowed.");
      } else {
        // move uploaded file to temporary folder
        $file_exists = true;
        $index = 0;
        while($file_exists) {
          $ActualFileName = $FileName . "." . date("YmdHis") . $index;
          $file_exists = file_exists($ActualFileName);
          $index++;
        }
        if( copy($HTTP_POST_FILES[$ControlName]["tmp_name"], $this->TemporaryFolder . $ActualFileName) ) {
          $this->Value = $ActualFileName;
          $this->Text = $ActualFileName;
        } else {
          $this->Errors->addError("Insufficient file system permissions to upload file " . $FileName . " in field " . $FieldName . " into the temporary folder.");
        }
      }
    } else {
      $this->SetValue(CCGetParam($ControlName));
    }
  }

  function Move()
  {
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeProcessFile ");
    if (strlen($this->Value) && !file_exists($this->ProcessedFolder . $this->Value)) {
      $FileName = $this->GetFileName();
      $FieldName = $this->Caption;
      if (!file_exists($this->TemporaryFolder . $this->Value)) {
        $this->Errors->addError("The file " . $FileName . " in field " . $FieldName . " was not found.");
      } else if (!@rename($this->TemporaryFolder . $this->Value, $this->ProcessedFolder . $this->Value)) {
        $this->Errors->addError("Insufficient file system permissions to upload file " . $FileName . " specified in field " . $FieldName . ".");
      }
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterProcessFile ");
  }

  function Delete()
  {
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDeleteFile");
    if( !is_dir($this->ProcessedFolder . $this->Value) && file_exists($this->ProcessedFolder . $this->Value) ) {
      unlink($this->ProcessedFolder . $this->Value);
    } else if ( !is_dir($this->TemporaryFolder . $this->Value) && file_exists($this->TemporaryFolder . $this->Value) ) {
      unlink($this->TemporaryFolder . $this->Value);
    }
    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDeleteFile");
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    if($this->Visible)
    {
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");

      if(!$this->Visible) {
        $Tpl->setblockvar("FileUpload " . $this->Name, "");
        return;
      }

      if(strlen($RowNumber)) {
        $ControlName = $this->Name . "_" . $RowNumber;
        $DeleteControlName = $this->Name . "_Delete_" . $RowNumber;
      } else {
        $ControlName = $this->Name;
        $DeleteControlName = $this->Name . "_Delete";
      }
      $Tpl->SetVar("ControlName", $ControlName);
      $Tpl->SetVar("DeleteControl", $DeleteControlName);
      if (strlen($this->Value) ) {
        $Tpl->SetVar("ActualFileName", $this->Value);
        $Tpl->SetVar("FileName", $this->GetFileName());
        $Tpl->SetVar("FileSize", $this->GetFileSize());
        $Tpl->parse("FileUpload " . $this->Name . "/Uploaded", false);
        $Tpl->setblockvar("FileUpload " . $this->Name . "/NotUploaded", "");
      } else {
        $Tpl->parse("FileUpload " . $this->Name . "/NotUploaded", false);
        $Tpl->setblockvar("FileUpload " . $this->Name . "/Uploaded", "");
      }

      $Tpl->Parse("FileUpload " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("FileUpload " . $this->Name, "");
    }
  }

  function SetValue($Value) {
    $this->Text = $Value;
    $this->Value = $Value;
    if(strlen($Value) 
      && !file_exists($this->TemporaryFolder . $Value) 
      && !file_exists($this->ProcessedFolder . $Value)) {
        $FileName = $this->GetFileName();
        $FieldName = $this->Caption;
        $this->Errors->addError("The file " . $FileName . " in field " . $FieldName . " was not found.");
    }
  }

  function SetText($Text) {
    $this->SetValue($Text);
  }

  function GetValue() {
    return $this->Value;
  }

  function GetText() {
    return $this->Text;
  }

  function GetFileName() {
    return substr($this->Value, 0, strrpos($this->Value, "."));
  }

  function GetFileSize() {
    $filesize = 0;
    if( file_exists($this->ProcessedFolder . $this->Value) ) {
      $filesize = filesize($this->ProcessedFolder . $this->Value);
    } else if ( file_exists($this->TemporaryFolder . $this->Value) ) {
      $filesize = filesize($this->TemporaryFolder . $this->Value);
    }
    return $filesize;
  }

}

//End clsFileUpload Class

//clsDatePicker Class @0-C553A971
Class clsDatePicker
{
  var $Name;
  var $DateFormat;
  var $Style;
  var $FormName;
  var $ControlName;
  var $Visible;
  var $Errors;

  var $CCSEvents = "";
  var $CCSEventResult;

  function clsDatePicker($Name, $FormName, $ControlName)
  {
    $this->Name        = $Name;
    $this->FormName    = $FormName;
    $this->ControlName = $ControlName;
    $this->Visible     = true;

    $this->Errors = new clsErrors;
  }

  function Show($RowNumber = "")
  {
    global $Tpl;
    if($this->Visible)
    {
      $ControlName = ($RowNumber === "") ? $this->ControlName : $this->ControlName . "_" . $RowNumber;
      $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
      $Tpl->SetVar("Name",        $this->FormName . "_" . $this->Name);
      $Tpl->SetVar("FormName",    $this->FormName);
      $Tpl->SetVar("DateControl", $ControlName);

      $Tpl->Parse("DatePicker " . $this->Name, false);
    }
    else
    {
      $Tpl->setblockvar("DatePicker " . $this->Name, "");
    }
  }

}

//End clsDatePicker Class

//clsErrors Class @0-32F63B82
class clsErrors
{
  var $Errors;
  var $ErrorsCount;
  var $ErrorDelimiter;

  function clsErrors()
  {
    $this->Errors = array();
    $this->ErrorsCount = 0;
    $this->ErrorDelimiter = "<br>";
  }

  function addError($Description)
  {
    if (strlen($Description))
    {
      $this->Errors[$this->ErrorsCount] = $Description; 
      $this->ErrorsCount++;
    }
  }

  function AddErrors($Errors)
  {
    for($i = 0; $i < $Errors->Count(); $i++)
      $this->addError($Errors[$i]);
  }

  function Clear()
  {
    $this->Errors = array();
    $this->ErrorsCount = 0;
  }

  function Count()
  {
    return $this->ErrorsCount;
  }

  function ToString()
  {

    if(sizeof($this->Errors) > 0)
      return join($this->ErrorDelimiter, $this->Errors) . $this->ErrorDelimiter;
    else
      return "";
  }

}
//End clsErrors Class


?>
