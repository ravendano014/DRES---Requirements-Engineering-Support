<?php
#############################################################################
## xmlutil.php - common XML helper routines                                ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

require_once("lib/lib_xslt.php");

// loads whole file contents into string
function load_file($filename)
{
	$fh = fopen($filename, "r");
	@$size = filesize($filename);
	if (!$size) $size = 256 * 1024;
	$file = fread($fh, $size);
	fclose($fh);
	return $file;
}

// saves string contents into file
function save_file($filename, $content)
{
	$fh = fopen($filename, "w");
	fwrite($fh, $content);
	fclose($fh);
	return $file;
}

// retrieves XPath node text evaluation
function get_xpath_text($ctx, $query)
{
	if (get_class($ctx) == "DomDocument")
		$ctx = xpath_new_context($ctx);
	elseif (is_string($ctx))
	{
		$doc = xmldoc($ctx);
		$ctx = xpath_new_context($doc);
	}

	if (!$ctx)
		return "";
	else
	{
		$result=xpath_eval($ctx,$query);
		$node=$result->nodeset[0];
		if (!$node)
			return "";
		elseif ($node->type == XML_ELEMENT_NODE)
			return get_content($node);
		else
			return $node->value;
	}
}

// retrieves node using XPath
function get_xpath($ctx, $query)
{
	if (!$ctx)
		return;

	if (get_class($ctx) == "DomDocument")
		$ctx = xpath_new_context($ctx);
	elseif (is_string($ctx))
	{
		$doc = xmldoc($ctx);
		$ctx = xpath_new_context($doc);
	}

	if (get_class($ctx) != "XPathContext")
		return;

	$result = xpath_eval($ctx, $query);
	if (sizeof($result->nodeset) > 0)
		return $result->nodeset[0];
}

function get_element($parent, $name)
{
	if (!$parent) return false;
	$elements = $parent->get_elements_by_tagname($name);
	return $elements[0];
}

// retrieves node's concatenated text content
function get_content($node)
{
	if (!$node) return "";
	if ($node->has_child_nodes())
	{
		foreach($node->children() as $child)
			if ($child->type==XML_TEXT_NODE)
				$content=$child->content;

		return $content;
	}
	else
		return "";
}

// sets node's text content removing existing one
function set_element_content($node, $content)
{
	if ($node->has_child_nodes())
		foreach($node->children() as $child)
			$node->remove_child($child);
	$doc = $node->owner_document();
	$node->append_child($doc->create_text_node($content));
//	$node->set_content($content);
}

// alternate function to set XPath node value
function put_xpath2($doc, $path, $value, $op="replace")
{
	echo "$path -> $value<br>";
	$root = $doc->root();
	$elements = split("/", $path);
	foreach ($elements as $level => $element)
	{
		if ($element[0] == '@')
		{
			$node->set_attribute(substr($element, 1), $value);
			break;
		}
		elseif ($element)
		{
			if (!$root)
			{
				$doc->add_root($element);
				$root=$doc->root();
				$node=$root;
				continue;
			}
			elseif(!$node)
			{
				$node=$root;
				continue;
			}

			if ($node->has_child_nodes())
				foreach($node->children() as $child)
					if ($child->tagname==$element)
						break;
			if ($child->tagname!=$element)
				unset($child);

			if ($child)
			{
				$node=$child;
			}
			else
			{
				$child=domxml_node($element);
				$node->add_child($child);
				$node=$child;
			}

			if ($level==sizeof($elements)-1)
				$node->set_content($value);
		}
	}
}

// alternate function to set XPath node value
function put_xpath3($doc, $path, $value)
{
	$node = lookup_node($doc, $path, $value);

	if ($node)
		if ($node->node_type() == XML_ELEMENT_NODE)
		{
			if ($node->has_child_nodes())
			{
				$child = $node->first_child();
				$child->set_content(trim($value));
			}
		}
}

// function to set XPath node value
function put_xpath($doc, $path, $value)
{
//	echo "<b>$path -> $value</b><br>";
	$components = split("/", $path);

	$context="";
	foreach ($components as $level => $component)
		if ($component)
		{
			if (!$xpc && $doc->document_element())
				$xpc = xpath_new_context($doc);

			if ($xpc)
				$result = xpath_eval($xpc, "$context/$component");

			$parent = $node;
//			echo "<li>evaluating $context/$component<br>";
			if ($result && sizeof($result->nodeset) > 0)
			{
//				echo "<font color=red>node found</font><br>";
				$node = $result->nodeset[0];
			}
			else
			{
//				echo "<font color=red>creating $component at $context</font><br>";
				if ($component[0] == '@')
				{
//					echo "setting attribute on ";
//					print_r($parent);
					unset($node);
					$parent->set_attribute(substr($component, 1), $value);
					//$node = $parent->create_attribute(substr($component, 1),"");
					//$parent->add_child($node);
				}
				elseif (!$parent)
				{
//					echo "creating root<br>";
					$node = $doc->add_root($component);
					$node = $doc->document_element();
				}
				else
				{
//					echo "creating element<br>";
					$node = $doc->create_element($component);
					$parent->add_child($node);
					$node = $parent->last_child();
				}
			}

			$context .= "/$component";
		}

	if ($node)
		if ($node->node_type() == XML_ELEMENT_NODE)
			set_element_content($node, trim($value));
		else
			$parent->set_attribute(substr($component, 1), trim($value));

//	print_r($node);
//	echo "<br>";
}

// function to lookup XPath node or create it if not exists
function lookup_node($doc, $path, $value="")
{
	$components = split("/", $path);
	$xpc = xpath_new_context($doc);

	$result = xpath_eval($xpc, "$path");
	if (sizeof($result->nodeset) > 0)
		return $result->nodeset[0];
	
	$context="";
	foreach ($components as $level => $component)
		if ($component)
		{
			$result = xpath_eval($xpc, "$context/$component");

			$parent = $node;
			if (sizeof($result->nodeset) > 0)
			{
				$node = $result->nodeset[0];
			}
			else
			{
				if ($component[0] == '@')
				{
					unset($node);
					$parent->set_attribute(substr($component, 1), trim($value));
				}
				elseif (!$parent)
					$node = $doc->add_root($component);
				else
				{
					$node = $doc->create_element($component);
					$parent->add_child($node);
					$node = $parent->last_child();
				}
			}

			$context .= "/$component";
		}
	return $node;
}

// dumps XML document for debugging
function dump_xml($document, $name="")
{
	$result = @xslt_transform($document, load_file("xslt/xml.xslt"), array("name" => $name));
	if (!$result)
		echo "<pre>".htmlspecialchars($document)."</pre>";
	else
		echo $result;
}
?>