<?php
#############################################################################
## requirement_testcases.php - requirement test cases tab                  ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

$TestCasesForm = new XMLForm("forms/RequirementTestCases.xml", $PARAMS);
$TestCaseForm = new XMLForm("forms/RequirementTestCase.xml", $PARAMS);
$modified = false;
$requirement = "";
$doc = "";
$parent = "";
$node = "";
$testcase = "";

if ($CHECKOUT == $req_id)
{
	$requirement = @load_file($CHECKOUT_FILE);

	if ($HTTP_POST_VARS["testcase_delete"] && $HTTP_POST_VARS["req_testcases"] && $HTTP_POST_VARS["action"] == "req_testcases")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/test-cases/test-case[normalize-space(@name) = '".$HTTP_POST_VARS["req_testcases"]."']");
		$node->unlink();
		$modified = true;
	}

	if ($HTTP_POST_VARS["testcase_edit"] && $HTTP_POST_VARS["req_testcases"] && $HTTP_POST_VARS["action"] == "req_testcases")
	{
		if (!$doc) $doc = xmldoc($requirement);
		$node = get_xpath($doc, "/requirement/test-cases/test-case[normalize-space(@name) = '".$HTTP_POST_VARS["req_testcases"]."']");
		// $sample = $node->dump_node();
		$testcase = "<test-case name=\"".$node->get_attribute("name")."\">".get_content($node)."</test-case>";
	}

	if ($HTTP_POST_VARS["testcase_save"] && $HTTP_POST_VARS["testcase_name"] && $HTTP_POST_VARS["testcase_content"] && $HTTP_POST_VARS["action"] == "req_testcase")
	{
		if (!$doc) $doc = xmldoc($requirement);
		if ($HTTP_POST_VARS["old_testcase_name"])
		{
			$node = get_xpath($doc, "/requirement/test-cases/test-case[normalize-space(@name) = '".$HTTP_POST_VARS["old_testcase_name"]."']");
			$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["testcase_name"]));
			set_element_content($node, htmlspecialchars($HTTP_POST_VARS["testcase_content"]));
		}
		else
		{
			$parent = lookup_node($doc, "/requirement/test-cases");
			if ($parent)
			{
				$node = $doc->create_element("test-case");
				$node->set_attribute("name", htmlspecialchars($HTTP_POST_VARS["testcase_name"]));
				$node->set_content(htmlspecialchars($HTTP_POST_VARS["testcase_content"]));
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

echo $TestCasesForm->render($requirement);
echo $TestCaseForm->render($testcase);
?>