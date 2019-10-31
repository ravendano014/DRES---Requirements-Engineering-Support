<?php
#############################################################################
## login.php - DRES login dialog form                                      ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################
include_once("config.php");
include_once("lib/datasource.php");

session_start();

unset($username);
unset($password);
unset($project);
unset($message);
unset($Error);

$username = $HTTP_POST_VARS["username"];
$password = $HTTP_POST_VARS["password"];
$project  = $HTTP_POST_VARS["project"];
$message  = $HTTP_GET_VARS["message"];

session_register(SESSION_COOKIE);
session_register(PROJECT_COOKIE);
session_register(USER_COOKIE);

$dataSrc = new DataSource();

if ($HTTP_POST_VARS["submit"])
{
	if ($HTTP_SESSION_VARS[SESSION_COOKIE] = $dataSrc->log_in($username, $password, $project))
	{
		$HTTP_SESSION_VARS[USER_COOKIE] = $username;
		$HTTP_SESSION_VARS[PROJECT_COOKIE] = $project;
		header("Location: index.html");
		exit;
	}
	else
	{
		session_destroy();
		$HTTP_SESSION_VARS = array();
		$Error = "Failed to log in. Try again.";
	}
}

if (DISPLAY_USERS_COMBO)
	$users_list = $dataSrc->list_users();

if (DISPLAY_PROJECTS_COMBO)
	$projects_list = $dataSrc->list_projects();
?>
<html>
<head>
	<title>DRES - Distributed Requirements Engineering System</title>
	<link rel="stylesheet" href="css/site.css">
	<link rel="stylesheet" href="css/form.css">
	<link rel="stylesheet" href="css/grid.css">
</head>
<body>
	<table align="center" class="FormTable" width="400" height="100">
	 <tr align="center">
	  <td><a href="./" target="_top"><font style=" text-decoration:none;color: #000000; font-family: Verdana, Tahoma, Arial, Helvetica"><span style="font-size:40px; color:#355471"><b>DRES<b></span></font></a></td>
	 </tr>
	 <tr align="center">
	  <td><font style=" color: #000000; font-family: Arial, Tahoma, Verdana, Helvetica"><span style="font-size:10px; color:#355471"><b>D</b>istributed <b>R</b>equirements <b>E</b>ngineering <b>S</b>ystem v<?php echo VERSION ?></span></font></td>
	 </tr>
	</table>			
<br>
<?php if($message): ?>
	<table align="center" class="FormTable" width="400" height="100">
		<tr>
			<td align="center"><font color="red"><?=$message ?></font></td>
		</tr>
	</table>			
<?php endif; ?>
	<form action="login.php" method="post">
	<input type="hidden" name="submit" value="true">
	<table align="center" class="FormTable" width="400">
		<tr class="FormHeader">
			<td colspan="2" class="FormTitle">Please log in</td>
		</tr>
<?php if ($Error): ?>
		<tr>
			<td colspan="2" class="FormData" align="center"><font color="red"><?=$Error ?></font></td>
		</tr>
<?php endif; ?>
		<tr>
			<td class="FormLabel">Username</td>
			<td class="FormData">
<?php if(is_array($users_list)): ?>
				<select name="username" class="FormControl" style="width:100%">
<?php
foreach($users_list as $ulogin => $uname)
	echo "<option value=\"$ulogin\"".($ulogin == $username ? " selected" : "").">$uname</option>\n";
?>
				</select>
<?php else: ?>
				<input name="username" class="FormControl" type="text" size="20" value="<?=$username ?>" style="width:100%">
<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="FormLabel">Password</td>
			<td class="FormData"><input name="password" class="FormControl" type="password" size="20" style="width:100%"></td>
		</tr>
<?php if(is_array($projects_list)): ?>
		<tr>
			<td class="FormLabel">Project</td>
			<td class="FormData">
				<select name="project" class="FormControl" style="width:100%">
<?php
foreach($projects_list as $pid => $pname)
	echo "<option value=\"$pid\"".($pid == $project ? " selected" : "").">$pname</option>\n";
?>
				</select>
			</td>
		</tr>
<?php elseif(!DEFAULT_PROJECT): ?>
		<tr>
			<td class="FormLabel">Project</td>
			<td class="FormData">
				<input name="project" class="FormControl" type="text" size="20" value="<?=$project ?>" style="width:100%">
			</td>
		</tr>
<?php else: ?>
		<input type="hidden" name="project" value="<?=DEFAULT_PROJECT ?>">
<?php endif; ?>
		<tr class="FormFooter">
			<td colspan="2"><input class="FormButton" type="submit" name="login" value="Log in"></td>
		</tr>
<?php if (DATASOURCE == "mysql" && DISPLAY_REGISTRATION): ?>
		<tr>
			<td class="FormData" colspan="2">
				click <a href="admin/register.php">here</a> to register for new account
			</td>
		</tr>
<?php endif; ?>
	</table>
	</form>
</body>
</html>
