<?php
#############################################################################
## url.php - URL handling routines                                         ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

// rebuilds page URL using current parements except specified ones
function getQueryString($except)
{
	global $HTTP_GET_VARS;

	$qs = "";
	foreach ($HTTP_GET_VARS as $var=>$val)
		if (!in_array($var, $except))
			$qs .= ($qs ? "&" : "").$var."=".urlencode($val);
	return $qs;
}
?>