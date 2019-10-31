<?php
#############################################################################
## requirement_custom.php - requirement custom attributes tab              ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$CustomsForm = new XMLForm("forms/RequirementCustoms.xml", $PARAMS);
$CustomForm = new XMLForm("forms/RequirementCustom.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent = "";
$node = "";
$custom = "";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["custom_delete"] && $HTTP_POST_VARS["req_customs"] && $HTTP_POST_VARS["action"] == "req_customs")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/custom-attributes/custom-attribute[normalize-space(@name) = '".$HTTP_POST_VARS["req_customs"]."']");
		if($node)
		{
			$node->unlink();
			$modified = true;
		}
	}

	if ($HTTP_POST_VARS["custom_edit"] && $HTTP_POST_VARS["req_customs"] && $HTTP_POST_VARS["action"] == "req_customs")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/custom-attributes/custom-attribute[normalize-space(@name) = '".$HTTP_POST_VARS["req_customs"]."']");
		if ($node)
			$custom = "<custom-attribute name=\"".$node->get_attribute("name")."\" value=\"".$node->get_attribute("value")."\"/>";
	}

	if ($HTTP_POST_VARS["custom_save"] && $HTTP_POST_VARS["custom_name"] && $HTTP_POST_VARS["custom_value"] && $HTTP_POST_VARS["action"] == "req_custom")
	{
		if (!$doc) $doc = xmldoc($requirement);
		if ($HTTP_POST_VARS["old_custom_name"])
		{
			$node = get_xpath($doc, "/requirement/custom-attributes/custom-attribute[normalize-space(@name) = '".$HTTP_POST_VARS["old_custom_name"]."']");
			$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["custom_name"]));
			$node->set_attribute("value", htmlspecialchars($HTTP_POST_VARS["custom_value"]));
		}
		else
		{
			$parent = lookup_node($doc, "/requirement/custom-attributes");
			if ($parent)
			{
				$node = $doc->create_element("custom-attribute");
				$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["custom_name"]));
				$node->set_attribute("value", htmlspecialchars($HTTP_POST_VARS["custom_value"]));
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

echo $CustomsForm->render($requirement);
echo $CustomForm->render($custom);
?>