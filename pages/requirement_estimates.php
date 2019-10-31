<?php
#############################################################################
## requirement_estimates.php - requirement estimates tab                   ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$StdEstimatesForm = new XMLForm("forms/RequirementStdEstimates.xml", $PARAMS);
$EstimatesForm = new XMLForm("forms/RequirementEstimates.xml", $PARAMS);
$EstimateForm = new XMLForm("forms/RequirementEstimate.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent = "";
$node = "";
$estimate = "";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["estimates_save"] && $HTTP_POST_VARS["action"] == "req_stdestimates")
	{
		$requirement = $StdEstimatesForm->get_submit($requirement);
		$modified = true;
	}

	if ($HTTP_POST_VARS["estimates_delete"] && $HTTP_POST_VARS["req_estimates"] && $HTTP_POST_VARS["action"] == "req_estimates")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/estimates/estimate[normalize-space(@name) = '".$HTTP_POST_VARS["req_estimates"]."']");
		if($node)
		{
			$node->unlink();
			$modified = true;
		}
	}

	if ($HTTP_POST_VARS["estimates_edit"] && $HTTP_POST_VARS["req_estimates"] && $HTTP_POST_VARS["action"] == "req_estimates")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/estimates/estimate[normalize-space(@name) = '".$HTTP_POST_VARS["req_estimates"]."']");
		if ($node)
			$estimate = "<estimate name=\"".$node->get_attribute("name")."\" value=\"".$node->get_attribute("value")."\"/>";
		// $sample = $node->dump_node();
	}

	if ($HTTP_POST_VARS["estimate_save"] && $HTTP_POST_VARS["estimate_name"] && $HTTP_POST_VARS["estimate_value"] && $HTTP_POST_VARS["action"] == "req_estimate")
	{
		if (!$doc) $doc = xmldoc($requirement);
		if ($HTTP_POST_VARS["old_estimate_name"])
		{
			$node = get_xpath($doc, "/requirement/estimates/estimate[normalize-space(@name) = '".$HTTP_POST_VARS["old_estimate_name"]."']");
			$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["estimate_name"]));
			$node->set_attribute("value", htmlspecialchars($HTTP_POST_VARS["estimate_value"]));
		}
		else
		{
			$parent = lookup_node($doc, "/requirement/estimates");
			if ($parent)
			{
				$node = $doc->create_element("estimate");
				$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["estimate_name"]));
				$node->set_attribute("value", htmlspecialchars($HTTP_POST_VARS["estimate_value"]));
				$parent->add_child($node);
			}
		}
		$modified = true;
	}

	if ($modified && $doc)
		$requirement = $doc->dumpmem();

	if ($modified)
		save_file($CHECKOUT_FILE, $requirement);

//	dump_xml($requirement);
}
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);

echo $StdEstimatesForm->render($requirement);
echo $EstimatesForm->render($requirement);
echo $EstimateForm->render($estimate);
?>