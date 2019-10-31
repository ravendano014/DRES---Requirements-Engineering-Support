<?php
#############################################################################
## requirement_keywords.php - requirement keywords tab                     ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$KeywordsForm = new XMLForm("forms/RequirementKeywords.xml", $PARAMS);
$KeywordForm = new XMLForm("forms/RequirementKeyword.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent ="";
$node ="";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["keyword_delete"] && $HTTP_POST_VARS["req_keywords"] && $HTTP_POST_VARS["action"] == "req_keywords")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = lookup_node($doc, "/requirement/keywords/keyword[normalize-space(text()) = '".$HTTP_POST_VARS["req_keywords"]."']");
		$node->unlink();
		$modified = true;
	}

	if ($HTTP_POST_VARS["keyword_add"] && $HTTP_POST_VARS["req_keyword"] && $HTTP_POST_VARS["action"] == "req_keyword")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$parent = lookup_node($doc, "/requirement/keywords");
		if ($parent)
		{
			$node = $doc->create_element("keyword");
			$parent->add_child($node);
			$node = $parent->last_child();
			set_element_content($node, htmlspecialchars($HTTP_POST_VARS["req_keyword"]));
			$modified = true;
		}
	}

	if ($modified && $doc)
		$requirement = $doc->dumpmem();

	if ($modified)
		save_file($CHECKOUT_FILE, $requirement);

//	dump_xml($requirement);
}
elseif($req_id)
	$requirement = $DataSrc->get_requirement($req_id);

echo $KeywordsForm->render($requirement);
echo $KeywordForm->render("");
?>