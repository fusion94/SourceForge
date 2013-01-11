<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: download.php,v 1.11 2000/01/13 18:36:36 precision Exp $

require ('pre.php');

$sql="SELECT * FROM snippet_version WHERE snippet_version_id='$id'";
$result=db_query($sql);

if ($result && db_numrows($result) > 0) {
	header('Content-Type: text/plain');
	echo util_unconvert_htmlspecialchars(stripslashes(db_result($result,0,'code')));
} else {
	echo 'Error';
}

?>
