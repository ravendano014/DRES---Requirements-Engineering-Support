<?php
#############################################################################
## head.php - xDRE header frame                                            ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################
require_once("config.php");
require_once("lib/datasource.php");
require_once("lib/xmlutil.php");
require_once("lib/session.php");

session_start();
?>
<html>
<head>
<title></title>
<?php if(check_access()): ?>
<?php 
$dataSrc = new DataSource();
$xml = $dataSrc->list_all_folders();
?>
<script type="text/javascript" src="tree/mtmcode.js">
</script>

<script type="text/javascript">
<!--
/******************************************************************************
* User-configurable options.                                                  *
******************************************************************************/

// Menu table width, either a pixel-value (number) or a percentage value.
var MTMTableWidth = "100%";

// Name of the frame where the menu is to appear.
var MTMenuFrame = "tree";

// Variable for determining how a sub-menu gets a plus-sign.
// "Never" means it never gets a plus sign, "Always" means always,
// "Submenu" means when it contains another submenu.
var MTMSubsGetPlus = "Always";

// variable that defines whether the menu emulates the behaviour of
// Windows Explorer
var MTMEmulateWE = true;

// Directory of menu images/icons
var MTMenuImageDirectory = "tree-images/";

// Variables for controlling colors in the menu document.
// Regular BODY atttributes as in HTML documents.
var MTMBGColor = "#F4F8FB";
var MTMBackground = "";
var MTMTextColor = "#000000";

// color for all menu items
var MTMLinkColor = "#330099";

// Hover color, when the mouse is over a menu link
var MTMAhoverColor = "#990000";

// Foreground color for the tracking & clicked submenu item
var MTMTrackColor ="#000000";
var MTMSubExpandColor = "#666699";
var MTMSubClosedColor = "#666699";

// All options regarding the root text and it's icon
var MTMRootIcon = "menu_folder_closed.gif";
var MTMenuText = "<?=$dataSrc->get_project_name() ?>";
var MTMRootColor = "#000000";
var MTMRootFont = "Tahoma, Arial, Helvetica, sans-serif";
var MTMRootCSSize = "84%";
var MTMRootFontSize = "-1";

// Font for menu items.
var MTMenuFont = "Tahoma, Arial, Helvetica, sans-serif";
var MTMenuCSSize = "84%";
var MTMenuFontSize = "-1";

// Variables for style sheet usage
// 'true' means use a linked style sheet.
var MTMLinkedSS = false;
var MTMSSHREF = "";

// Additional style sheet properties if you're not using a linked style sheet.
// See the documentation for details on IDs, classes & elements used in the menu.
// Empty string if not used.
var MTMExtraCSS = "";

// Header & footer, these are plain HTML.
// Leave them to be "" if you're not using them

var MTMHeader = "";
var MTMFooter = "";

// Whether you want an open sub-menu to close automagically
// when another sub-menu is opened.  'true' means auto-close
var MTMSubsAutoClose = false;

// This variable controls how long it will take for the menu
// to appear if the tracking code in the content frame has
// failed to display the menu. Number if in tenths of a second
// (1/10) so 10 means "wait 1 second".
var MTMTimeOut = 15;

// Cookie usage.  First is use cookie (yes/no, true/false).
// Second is cookie name to use.
// Third is how many days we want the cookie to be stored.

var MTMUseCookies = true;
var MTMCookieName = "MTMCookie";
var MTMCookieDays = 3;

// Tool tips.  A true/false-value defining whether the support
// for tool tips should exist or not.
var MTMUseToolTips = true;

/******************************************************************************
* User-configurable list of icons.                                            *
******************************************************************************/

var MTMIconList = null;
MTMIconList = new IconList();
MTMIconList.addIcon(new MTMIcon("menu_folder_closed.gif", "folder", "any"));
MTMIconList.addIcon(new MTMIcon("menu_link_default.gif", "requirement", "any"));

/******************************************************************************
* User-configurable menu.                                                     *
******************************************************************************/

<?=xslt_transform($xml, load_file("xslt/jstree.xslt"), array()) ?>
//-->
</script>
<?php endif; ?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" bgcolor="#D4E1EC">
<table cellspacing="0" width="100%" height="100%">
 <tr>
  <td><a href="./" target="_top"><font style=" text-decoration:none;color: #000000; font-family: Verdana, Tahoma, Arial, Helvetica"><span style="font-size:40px; color:#355471"><b>DRES<b></span></font></a></td>
 </tr>
 <tr>
  <td><font style=" color: #000000; font-family: Arial, Tahoma, Verdana, Helvetica"><span style="font-size:10px; color:#355471"><b>D</b>istributed <b>R</b>equirements <b>E</b>ngineering <b>S</b>ystem v<?php echo VERSION ?></span></font></td>
 </tr>
</table>
</body>
</html>