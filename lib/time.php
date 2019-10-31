<?php
#############################################################################
## time.php - common date/time handling routines                           ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

// retrieves microsecond-exact timer value
function getmicrotime()
{
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
}

function get_current_date()
{
	return strftime("%Y-%m-%d %H:%M:%S");
}
?>