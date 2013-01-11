<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.2 2000/01/13 18:36:34 precision Exp $

      ////////////////////////////////////
      //                                //  
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                //
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     //
      //                                //
      //////////////////////////////////// 

// include common configuration, database connection
//
$root = "../..";
require("$root/pre.php3");
include("common.inc");

// query for all menus
//
if (!($dbResult = mysql_query("SELECT * FROM menus WHERE active=1 ORDER BY name, date_created", $GLOBALS[DB]))) {
	ErrorPage("Database query failed;<br>" . mysql_errno() . ": " . mysql_error());
} else {
?>

    <center>
      <h4>Edit Menus</h4>
    </center>

    <table cellpadding=2 cellspacing=0 border=1 align='center'>
    <tr bgcolor='#314a9c'>
      <th><font color='#FFFFFF'>Menu Name</font></th>
      <th><font color='#FFFFFF'>Description</font></th>
      <th><font color='#FFFFFF'>Actions</font></th>
    </tr>

<?php
	// output existing menu rows
	// 
	while($dbRow = mysql_fetch_array($dbResult)) {
		(($nCount++ % 2) == 0) ? $strColor = "#DDDDDD" : $strColor = "#FFFFFF";
		printf("<tr bgcolor='%s'>", $strColor);
		printf("<td>%s</td>\n", $dbRow["name"]);
		printf("<td>%s</td>\n", $dbRow["description"]);
		printf("<td><a href='edit_menu.php3?f_mid=%s'>Edit</a> - <a href='delete.php3?f_mid=%s'>Delete</a></td>\n", $dbRow["mid"], $dbRow["mid"]);
		printf("</td></tr>\n");

		// create array for copy menu
		//
		$aMenuList[$dbRow["mid"]] = $dbRow["name"];
	}
?>

    </table>

    <center>
      <h4>Create A Menu</h4>
    </center>

    <form action='create.php3' method='POST'>
    <table cellpadding=2 cellspacing=0 border=0 align='center'>
    <tr>
      <td><b>Name:</b></td>
      <td><input type='text' name='f_strName' size=20 maxlength=32></td>
    </tr>
    <tr>
      <td><b>Description:</b></td>
      <td><input type='text' name='f_strDesc' size=20 maxlength=128></td>
    </tr>
    <tr>
      <td align='center' colspan='2'><input type='submit' value='Go!'><input type='reset' value='Clear'></td>
    </tr>
    </table>
    </form>

    <center>
      <h4>Copy an Existing Menu</h4>
    </center>

    <form action='copy.php3' method='POST'>
    <table cellpadding=2 cellspacing=0 border=0 align='center'>
    <tr>
      <td><b>Original Menu Name:</b></td>
      <td>
        <select name='f_nSourceMID'>
<?php

	while (list($key, $val) = each($aMenuList)) {
		printf("<option value='%s'>%s\n", $key, $val);
	}

?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>New Menu Name:</b></td>
      <td><input type='text' name='f_strDestName' size=20 maxlength=128></td>
    </tr>
    <tr>
      <td><b>New Menu Description:</b></td>
      <td><input type='text' name='f_strDesc' size=20 maxlength=128></td>
    </tr>
    <tr>
      <td align='center' colspan='2'><input type='submit' value='Go!'><input type='reset' value='Clear'></td>
    </tr>
    </table>
    </form>

<?php
} // else

require("$root/post.php3");
?>

