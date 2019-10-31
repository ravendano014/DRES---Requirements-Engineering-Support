<?php
//BindEvents Method @1-096E3C73
function BindEvents()
{
    global $Login;
    $Login->Button_DoLogin->CCSEvents["OnClick"] = "Login_Button_DoLogin_OnClick";
}
//End BindEvents Method

//Login_Button_DoLogin_OnClick @3-545F569A
function Login_Button_DoLogin_OnClick()
{
    $Login_Button_DoLogin_OnClick = true;
//End Login_Button_DoLogin_OnClick

//Login @4-319E0F84
    global $Login;
    if(!CCLoginUser($Login->login->Value, $Login->password->Value))
    {
        $Login->Errors->addError("Login or Password is incorrect.");
        $Login->password->SetValue("");
        $Login_Button_DoLogin_OnClick = false;
    }
    else
    {
        global $Redirect;
        $Redirect = CCGetParam("ret_link", $Redirect);
        $Login_Button_DoLogin_OnClick = true;
    }
//End Login

//Close Login_Button_DoLogin_OnClick @3-0EB5DCFE
    return $Login_Button_DoLogin_OnClick;
}
//End Close Login_Button_DoLogin_OnClick


?>
