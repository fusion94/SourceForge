<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: detail.php,v 1.26 2000/01/13 18:36:36 precision Exp $

require ('pre.php');
require ('../snippet/snippet_utils.php');

/*

	Show a detail page for either a snippet or a package
	or a specific version of a package

*/

if ($type=='snippet') {
	/*


		View a snippet and show its versions
		Expand and show the code for the latest version


	*/

	snippet_header(array('title'=>'Snippet Library'));

	snippet_show_snippet_details($id);

	/*
		Get all the versions of this snippet
	*/
	$sql="SELECT user.user_name,snippet_version.snippet_version_id,snippet_version.version,snippet_version.date ".
		"FROM snippet_version,user ".
		"WHERE user.user_id=snippet_version.submitted_by AND snippet_id='$id' ".
		"ORDER BY snippet_version.snippet_version_id DESC";

	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '<H3>Error - no versions found</H3>';
	} else {
		echo '
			<H3>Versions Of This Snippet:</H3>
			<P>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Snippet ID</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Download Version</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Date Posted</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Author</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Delete</TD>
			</TR>';

		/*
			get the newest version of this snippet, so we can display its code
		*/
		$newest_version=db_result($result,0,'snippet_version_id');

		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 == 0) {
				$row_color=' BGCOLOR="#FFFFFF"';
			} else {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}

			echo '
				<TR'.$row_color.'><TD>'.db_result($result,$i,'snippet_version_id').
				'</TD><TD><A HREF="/snippet/download.php?type=snippet&id='.
				db_result($result,$i,'snippet_version_id').'"><B>'.
				db_result($result,$i,'version').'</B></A></TD><TD>'. 
				date($sys_datefmt,db_result($result,$i,'date')).'</TD><TD>'.
				db_result($result,$i,'user_name').'</TD><TD ALIGN="MIDDLE"><A HREF="/snippet/delete.php?type=snippet&snippet_version_id='.
				db_result($result,$i,'snippet_version_id').
				'"><IMG SRC="/images/ic/trash.png HEIGHT="16" WIDTH="16" BORDER=0"></A></TD></TR>';
		}
	}
	echo '</TABLE>';

	echo '
		<P>
		Download a raw-text version of this code by clicking on &quot;<B>Download Version</B>&quot;
		<P>';

	/*
		show the latest version of this snippet's code
	*/
	$result=db_query("SELECT code,version FROM snippet_version WHERE snippet_version_id='$newest_version'");	

	echo '
		<P>
		<HR>
		<P>
		<H2>Latest Snippet Version: '.db_result($result,0,'version').'</H2>
		<P>
		<PRE><FONT SIZE="-1">
'. stripslashes(db_result($result,0,'code')).'
		</FONT></PRE>
		<P>';
	/*
		Show a link so you can add a new version of this snippet
	*/
	echo '
	<H3><A HREF="/snippet/addversion.php?type=snippet&id='.$id.'"><FONT COLOR="RED">Submit a new version</FONT></A></H3>
	<P>
	You can submit a new version of this snippet if you have modified it 
	and you feel it is appropriate to share with others.';

	snippet_footer(array());

} else if ($type=='package') {
	/*


		View a package and show its versions
		Expand and show the snippets for the latest version


	*/

	snippet_header(array('title'=>'Snippet Library'));

	snippet_show_package_details($id);

	/*
		Get all the versions of this package
	*/
	$sql="SELECT user.user_name,snippet_package_version.snippet_package_version_id,".
		"snippet_package_version.version,snippet_package_version.date ".
		"FROM snippet_package_version,user ".
		"WHERE user.user_id=snippet_package_version.submitted_by AND snippet_package_id='$id' ".
		"ORDER BY snippet_package_version.snippet_package_version_id DESC";

	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '<H3>Error - no versions found</H3>';
	} else {
		echo '
			<H3>Versions Of This Package:</H3>
			<P>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Package Version</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Date Posted</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Author</TD>
			<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Edit/Del</TD>
			</TR>';

		/*
			determine the newest version of this package, 
			so we can display the snippets that it contains
		*/
		$newest_version=db_result($result,0,'snippet_package_version_id');

		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 == 0) {
				$row_color=' BGCOLOR="#FFFFFF"';
			} else {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}

			echo '
				<TR'.$row_color.'><TD><TD><A HREF="/snippet/detail.php?type=packagever&id='.
				db_result($result,$i,'snippet_package_version_id').'"><B>'.
				db_result($result,$i,'version').'</B></A></TD><TD>'.
				date($sys_datefmt,db_result($result,$i,'date')).'</TD><TD>'.
				db_result($result,$i,'user_name').
				'</TD><TD ALIGN="MIDDLE"><A HREF="/snippet/add_snippet_to_package.php?snippet_package_version_id='.
				db_result($result,$i,'snippet_package_version_id').
				'"><IMG SRC="/images/ic/pencil.png HEIGHT="25" WIDTH="20" BORDER=0"></A>
				&nbsp; &nbsp; &nbsp; <A HREF="/snippet/delete.php?type=package&snippet_package_version_id='.
				db_result($result,$i,'snippet_package_version_id').
				'"><IMG SRC="/images/ic/trash.png HEIGHT="16" WIDTH="16" BORDER=0"></A></TD></TR>';
		}
	}
	echo '</TABLE>';

	echo '
		<P>
		Download a raw-text version of this code by clicking on &quot;<B>Download Version</B>&quot;
		<P>';


	/*
		show the latest version of the package
		and its snippets
	*/

	echo '
		<P>
		<HR>
		<P>
		<H2>Latest Package Version: '.db_result($result,0,'version').'</H2>
		<P>
		<P>';
	snippet_show_package_snippets($newest_version);

	/*
		Show a form so you can add a new version of this package
	*/
	echo '
	<H3><A HREF="/snippet/addversion.php?type=package&id='.$id.'"><FONT COLOR="RED">Submit a new version</FONT></A></H3>
	<P>
	You can submit a new version of this package if you have modified it
	and you feel it is appropriate to share with others.';

	snippet_footer(array());

} else if ($type=='packagever') {
	/*
		Show a specific version of a package and its specific snippet versions
	*/
	
	snippet_header(array('title'=>'Snippet Library'));

	snippet_show_package_details($id);

	snippet_show_package_snippets($id);

	snippet_footer(array());

} else {

	exit_error('Error','Error - was the URL mangled?');

}

?>
