<?php
#############################################################################
## requirement_screnarios.php - requirement scenarios tab                  ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$ScenariosForm = new XMLForm("forms/RequirementScenarios.xml", $PARAMS);
$ScenarioForm = new XMLForm("forms/RequirementScenario.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent = "";
$node = "";
$scenario = "";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["scenario_delete"] && $HTTP_POST_VARS["req_scenarios"] && $HTTP_POST_VARS["action"] == "req_scenarios")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/scenarios/scenario[normalize-space(@name) = '".$HTTP_POST_VARS["req_scenarios"]."']");
		$node->unlink();
		$modified = true;
	}

	if ($HTTP_POST_VARS["scenario_edit"] && $HTTP_POST_VARS["req_scenarios"] && $HTTP_POST_VARS["action"] == "req_scenarios")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/scenarios/scenario[normalize-space(@name) = '".$HTTP_POST_VARS["req_scenarios"]."']");
		// $sample = $node->dump_node();
		$scenario = "<scenario name=\"".$node->get_attribute("name")."\">".get_content($node)."</scenario>";
	}

	if ($HTTP_POST_VARS["scenario_save"] && $HTTP_POST_VARS["scenario_name"] && $HTTP_POST_VARS["scenario_content"] && $HTTP_POST_VARS["action"] == "req_scenario")
	{
		if (!$doc) $doc = xmldoc($requirement);
		if ($HTTP_POST_VARS["old_scenario_name"])
		{
			$node = get_xpath($doc, "/requirement/scenarios/scenario[normalize-space(@name) = '".$HTTP_POST_VARS["old_scenario_name"]."']");
			$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["scenario_name"]));
			set_element_content($node, htmlspecialchars($HTTP_POST_VARS["scenario_content"]));
		}
		else
		{
			$parent = lookup_node($doc, "/requirement/scenarios");
			if ($parent)
			{
				$node = $doc->create_element("scenario");
				$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["scenario_name"]));
				$node->set_content(htmlspecialchars($HTTP_POST_VARS["scenario_content"]));
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

echo $ScenariosForm->render($requirement);
echo $ScenarioForm->render($scenario);
?>