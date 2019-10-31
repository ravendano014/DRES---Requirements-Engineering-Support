<?php
#############################################################################
## requirement_details.php - requirement details tab                       ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$DetailsForm = new XMLForm("forms/RequirementDetails.xml", $PARAMS);

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["details_save"] && $HTTP_POST_VARS["action"] == "req_details")
	{
		$requirement = $DetailsForm->get_submit($requirement);
		save_file($CHECKOUT_FILE, $requirement);
	}
}
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);


echo $DetailsForm->render($requirement);
?>