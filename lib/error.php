<?php
#############################################################################
## error.php - common error handling routines                              ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

// displays error message using css styles if one passed
function show_error($error)
{
	if (!$error) return;

	echo '<table align="center" class="ErrorTable"><tr><td><blink>';
	if (is_array($error))
		foreach ($error as $err)
			echo "$err<br>";
	else
		echo "$error<br>";

	echo '</blink></td></tr></table>';
}

function redirect_error($error)
{
	header("Location: main.php?page=error&msg=".urlencode($error));
	exit;
}
?>