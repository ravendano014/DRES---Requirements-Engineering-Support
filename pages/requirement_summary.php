<?php
#############################################################################
## requirement_summary.php - requirement summary tab                       ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################


$SummaryForm = new XMLForm("forms/RequirementSummary.xml", $PARAMS);
$summary_errors = array();

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["summary_save"] && $HTTP_POST_VARS["action"] == "req_summary")
	{
		$requirement = $SummaryForm->get_submit($requirement, &$summary_errors);
		if(sizeof($summary_errors) == 0)
			save_file($CHECKOUT_FILE, $requirement);
	}
}
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);

//dump_xml($requirement);
show_error($summary_errors);
echo $SummaryForm->render($requirement);
?>