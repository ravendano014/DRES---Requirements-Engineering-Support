<?php
include_once("lib/error.php");

// performs XSLT transformation on specified documents
function xslt_transform($data, $xslt, $params, $buffers = array())
{
	save_file($xml_file = tempnam(TEMP_DIR, "DATA"), $data);
	save_file($xsl_file = tempnam(TEMP_DIR, "XSLT"), $xslt);
	$out_file = tempnam(TEMP_DIR, "OUTP");
	$par_string = serialize_parameters($params, $buffers);
	$output = array();
	//echo SABCMD_PATH.htmlspecialchars(" $xsl_file $xml_file $out_file $par_string")."<br>";
	exec(SABCMD_PATH." $xsl_file $xml_file $out_file $par_string", $output, $status);
	if ($status) handle_sabcmd_error($status, $output);
	@unlink($xml_file);
	@unlink($xsl_file);

	$result = load_file($out_file);
	@unlink($out_file);

	return $result;
}

// performs XSLT transformation on specified files
function xslt_transform_files($xml_file, $xsl_file, $params, $buffers = array())
{
	$out_file = tempnam(TEMP_DIR, "OUTP");
	$par_string = serialize_parameters($params, $buffers);
	$output = array();
	//echo SABCMD_PATH." $xsl_file $xml_file $out_file $par_string<br>";
	exec(SABCMD_PATH." $xsl_file $xml_file $out_file $par_string", $output, $status);
	if ($status) handle_sabcmd_error($status, $output);

	$result = load_file($out_file);
	@unlink($out_file);

	return $result;
}

// serializes parameters for command line tool execution
function serialize_parameters($params, $buffers = array())
{
	$par_string = "";
	foreach ($params as $pname => $pvalue)
		if (substr(php_uname(), 0, 7) == "Windows")
			$par_string .= " \"\$$pname=$pvalue\"";
		else
			$par_string .= " \"\\\$$pname=$pvalue\"";
	if (is_array($buffers))
	foreach ($buffers as $bname => $bvalue)
		$par_string .= " \"$bname=$bvalue\"";
	return $par_string;
}

function handle_sabcmd_error($status, $output)
{
	show_error("Failed executing Sablotron binary: ".SABCMD_PATH."<br>Status: $status<br><pre>".join("", $output)."</pre><br>");
}
?>