<?php
#############################################################################
## xmlforms.php - XMLForms engine core classes                             ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

require ("lib/xmlutil.php");

// represents abstract XML-bound control
class XMLBound
{
	var $definition;	// definition filename for the control
	var $parameters;	// input parameters for the control
	var $renderer;		// control renderer stylesheet

	// performs render operation, first it transforms definition document using renderer stylesheet
	// to receive data stylesheet, that it is applied to the data document to get xml-bound html content
	function render($data, $buffers = array())
	{
		if (!isset($this->definition) || !isset($this->renderer))
			return false;

		/* UNSTABLE
		$xsl=load_file($this->renderer);
		$xml=load_file($this->definition);
		$arguments = array("/_xml" => $xml, "/_xsl" => $xsl);
		$stylesheet = xslt_process($xh, "file:".$this->definition, "file:".$this->renderer, NULL, NULL, $this->parameters);
		*/

		// process definition document through renderer to obtain stylesheet
		$stylesheet = xslt_transform_files($this->definition, $this->renderer, $this->parameters);
		//dump_xml($stylesheet);

		// check no document given
		if (!$data)
			$data = "<doc/>";

		// render document using stylesheet if generated
		if (trim($stylesheet))
		{
			/* UNSTABLE
			$arguments = array("/_xml" => $data, "/_xsl" => $stylesheet);
			$render = xslt_process($xh, "arg:/_xml", "arg:/_xsl", NULL, $arguments, $this->parameters);
			*/
			$render = xslt_transform($data, $stylesheet, $this->parameters, $buffers);
		}

		return $render;
	}

	// renders the control to the file, saving it in the cache directory with tempoary filename
	function render_to_file($data, $force=false)
	{
		$output = $this->render($data);
		$hash = md5($output);
		$filename = "cache/$hash";

		if (!file_exists($filename) || $force)
		{
			$fh = fopen($filename,"w");
			fwrite($fh, $output);
			fclose($fh);
		}

		return $filename;
	}
}

// represents XML-bound edit/submit form
class XMLForm extends XMLBound
{
	function XMLForm($def, $params)
	{
		$this->definition = $def;
		$this->parameters = $params?$params:array();
		$this->renderer = "xslt/form.xslt";
	}

	// retrieves form's submitted data reconstructed into bound document
	function get_submit($document="", $errors="")
	{
		global $HTTP_POST_VARS;

		if ($document)
			$doc = xmldoc($document);
		else
			$doc = domxml_new_xmldoc("1.0");

//		$formdoc = xmldocfile($this->definition);
		$formdoc = xmldoc(load_file($this->definition));
		$formctx = xpath_new_context($formdoc);

		$result = xpath_eval($formctx, "//field[@binding and (@display = 'edit' or not(@display)) and @control != 'label' and @control != 'readonly']");
		foreach ($result->nodeset as $node)
		{
			$value = $HTTP_POST_VARS[$node->get_attribute("name")];
			if ($node->get_attribute("required") == "true" && !$value)
				$errors[] = $node->get_attribute("label")." field is required";
			if ($node->get_attribute("pattern") && !ereg($node->get_attribute("pattern"), $value))
				$errors[] = $node->get_attribute("label")." does not match pattern";
			put_xpath($doc, $node->get_attribute("binding"), $value);
		}
		/*
		$root = $formdoc->root();
		foreach ($root->children() as $node)
			if ($node->tagname == "field")
				put_xpath($doc, $node->get_attribute("binding"), $HTTP_POST_VARS[$node->get_attribute("name")]);
		*/
		return $doc->dumpmem();
	}

	// retrieves form's submitted data reconstructed into bound document
	function get_defaults($document="", $collection)
	{
		if ($document)
			$doc = xmldoc($document);
		else
			$doc = domxml_new_xmldoc("1.0");
		$formdoc = xmldoc(load_file($this->definition));
		$formctx = xpath_new_context($formdoc);

		$result = xpath_eval($formctx, "//field[@binding and (@display = 'edit' or not(@display)) and @control != 'label' and @control != 'readonly']");
		foreach ($result->nodeset as $node)
		{
			$value = $collection[$node->get_attribute("name")];
			if ($value)
				put_xpath($doc, $node->get_attribute("binding"), $value);
		}

		return $doc->dumpmem();
	}
}

// represents XML-bound paging/sorting grid control
class XMLGrid extends XMLBound
{
	function XMLGrid($def, $params)
	{
		global $HTTP_GET_VARS;

		$this->definition = $def;
		$this->parameters = $params?$params:array();
		$this->renderer = "xslt/grid.xslt";

		$prefix = $this->parameters["GridName"];

		if (!$this->parameters["OrderColumn"] && $HTTP_GET_VARS[$prefix."_sort"]) $this->parameters["OrderColumn"] = $HTTP_GET_VARS[$prefix."_sort"];
		if (!$this->parameters["OrderDirection"] && $HTTP_GET_VARS[$prefix."_order"]) $this->parameters["OrderDirection"] = $HTTP_GET_VARS[$prefix."_order"];
		if (!$this->parameters["CurrentPage"] && $HTTP_GET_VARS[$prefix."_page"]) $this->parameters["CurrentPage"] = $HTTP_GET_VARS[$prefix."_page"];
	}
}

// represents plain XML-bound view control
class XMLView extends XMLBound
{
	function XMLView($def, $params)
	{
		$this->definition = $def;
		$this->parameters = $params?$params:array();
		$this->renderer = "xslt/view.xslt";
	}
}
?>