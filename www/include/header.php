<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: header.php,v 1.2 2000/01/13 18:36:35 precision Exp $


/*
	Set up the database connectivity
*/

require('/etc/local.inc');
require($DOCUMENT_ROOT.'/include/utils.php3');

$dbhost = "$sys_dbhost";
$dbname = "$sys_dbname";
$dbuser = "$sys_dbuser";
$dbpasswd = "$sys_dbpasswd";
$server = "$sys_server";

require($DOCUMENT_ROOT.'/include/mysql.php3');

db_connect("");


/*
	Forum header/footer
*/

function forum_header($string) {

?>

<html>
<head>
<title>VA Linux Systems:  <?php echo $string; ?></title>
<style>
<!--
BODY, TD, TR, P, TH, TABLE {font-family: arial, helvetica, sans-serif}
H1, H2, H3, H4, H5, H6 {font-family: arial, helvetica, sans-serif}
PRE, TT {font-family: courier, sans-serif; font-size: 1em }
-->
</STYLE>
<basefont face="arial,helvetica,sanserif">
</head>

<BODY BGCOLOR="#80B0C0">

<TABLE>
	<TR>
		<TD>
			<font face="arial,helvetica,sans-serif">
<?php


}

function forum_footer() {

?>

			</font>
		</td>
	</tr>
</table>
</body>
</html>
<?php

}

/*
	Bug header/footer
*/

function bug_header($string) {

?>

<html>
<head>
<title>VA Linux Systems:  <?php echo $string; ?></title>
<style>
<!--
BODY, TD, TR, P, TH, TABLE {font-family: arial, helvetica, sans-serif}
H1, H2, H3, H4, H5, H6 {font-family: arial, helvetica, sans-serif}
PRE, TT {font-family: courier, sans-serif; font-size: 1em }
-->
</STYLE>
<basefont face="arial,helvetica,sanserif">
</head>

<BODY BGCOLOR="#80B0C0">

<TABLE>
        <TR>
                <TD>
                        <font face="arial,helvetica,sans-serif">
<?php


}

function bug_footer() {

?>

                        </font>
                </td>
        </tr>
</table>
</body>
</html>
<?php

}


?>
