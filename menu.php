<?php
require_once("lib/session.php");
require_once("lib/datasource.php");

$dataSrc = new DataSource();
?>
<html>
<head>
<title></title>
<link rel="stylesheet" href="css/site.css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" bgcolor="">
<table width="100%" height="100%" bgcolor="#D4E1EC">
	<tr>
		<td valign="middle" width="50%">
			Welcome, logged in as <b><?=$dataSrc->get_user_name() ?></b><br>
			Current project is <b><?=$dataSrc->get_project_name() ?></b><br>			
			<a href="login.php" target="_top">Relogin or switch projects</a>
		</td>
		<td valign="middle" align="right">
			<a href="main.php?page=home" target="main">Requirements</a>
			&nbsp;|&nbsp;
			<a href="main.php?page=reports" target="main">Reports</a>
			&nbsp;|&nbsp;
			<a href="main.php?page=search" target="main">Search</a>
			&nbsp;|&nbsp;
			<a href="admin/" target="_top">Administration</a>
		</td>
	</tr>
</table>
</body>
</html>