<?php
#############################################################################
## requirement_definition.php - requirement definition tab                 ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$DefinitionForm = new XMLForm("forms/RequirementDefinition.xml", $PARAMS);
$SamplesForm = new XMLForm("forms/RequirementSamples.xml", $PARAMS);
$SampleForm = new XMLForm("forms/RequirementSample.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent = "";
$node = "";
$sample = "";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["definition_save"] && $HTTP_POST_VARS["action"] == "req_definition")
	{
		$requirement = $DefinitionForm->get_submit($requirement);
		$modified = true;
	}

	if ($HTTP_POST_VARS["sample_delete"] && $HTTP_POST_VARS["req_samples"] && $HTTP_POST_VARS["action"] == "req_samples")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/definition/samples/sample[normalize-space(@name) = '".$HTTP_POST_VARS["req_samples"]."']");
		$node->unlink();
		$modified = true;
	}

	if ($HTTP_POST_VARS["sample_edit"] && $HTTP_POST_VARS["req_samples"] && $HTTP_POST_VARS["action"] == "req_samples")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/definition/samples/sample[normalize-space(@name) = '".$HTTP_POST_VARS["req_samples"]."']");
		// $sample = $node->dump_node();
		$sample = "<sample name=\"".$node->get_attribute("name")."\">".get_content($node)."</sample>";
	}

	if ($HTTP_POST_VARS["sample_save"] && $HTTP_POST_VARS["sample_name"] && $HTTP_POST_VARS["sample_content"] && $HTTP_POST_VARS["action"] == "req_sample")
	{
		if (!$doc) $doc = xmldoc($requirement);
		if ($HTTP_POST_VARS["old_sample_name"])
		{
			$node = get_xpath($doc, "/requirement/definition/samples/sample[normalize-space(@name) = '".$HTTP_POST_VARS["old_sample_name"]."']");
			$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["sample_name"]));
			set_element_content($node, htmlspecialchars($HTTP_POST_VARS["sample_content"]));
		}
		else
		{
			$parent = lookup_node($doc, "/requirement/definition/samples");
			if ($parent)
			{
				$node = $doc->create_element("sample");
				$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["sample_name"]));
				$node->set_content(htmlspecialchars($HTTP_POST_VARS["sample_content"]));
				$parent->add_child($node);
			}
		}
		$modified = true;
	}

	if ($modified && $doc)
		$requirement = $doc->dumpmem();

	if ($modified)
		save_file($CHECKOUT_FILE, $requirement);
}
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);

//dump_xml($requirement);

echo $DefinitionForm->render($requirement);
echo $SamplesForm->render($requirement);
echo $SampleForm->render($sample);
?>