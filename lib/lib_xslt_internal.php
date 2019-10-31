<?php
// performs XSLT transformation on specified documents
function xslt_transform($data, $xslt, $params, $buffers = array())
{
	$xh = xslt_create();
	$arguments = array_merge(array("/_xml" => $data, "/_xsl" => $xslt), $buffers);
	$result = xslt_process($xh, "arg:/_xml", "arg:/_xsl", NULL, $arguments, $params);
	xslt_free($xh);

	return $result;
}

// performs XSLT transformation on specified files
function xslt_transform_files($xml_file, $xsl_file, $params, $buffers = array())
{
	$xh = xslt_create();
	$result = xslt_process($xh, "file://".realpath($xml_file), "file://".realpath($xsl_file), NULL, $buffers, $params);
	xslt_free($xh);

	return $result;
}
?>