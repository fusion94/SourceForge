<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: add_bug.php,v 1.20 2000/01/13 18:36:34 precision Exp $

bug_header(array ('title'=>'Submit a Bug'));

	echo '<FORM ACTION="'.$PHP_SELF.'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="func" VALUE="postaddbug">
		<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
		<TABLE>
		<TR><TD VALIGN="TOP" COLSPAN="2"><B>Group:</B><BR>'.group_getname($group_id).'</TD></TR>
		<TR><TD VALIGN="TOP"><B>Category:</B><BR>';

	/*
		List of possible categories for this group
	*/
	$sql="select bug_category_id,category_name from bug_category WHERE group_id='$group_id'";

	$result=db_query($sql);

	build_select_box($result,'category_id');

	/*
		Priority List
	*/
	echo '</TD><TD><B>Bug Group:</B><BR>';

	/*
		List of possible bug_groups for this group
	*/
	$sql="select bug_group_id,group_name from bug_group WHERE group_id='$group_id'";

	$result=db_query($sql);

	build_select_box($result,'bug_group_id');

	?>
	</TD></TR>

	<TR><TD COLSPAN="2"><B>Summary:</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" SIZE="45" MAXLENGTH="60">
	</TD></TR>

	<TR><TD COLSPAN="2"><B>Details:</B><BR>
		<TEXTAREA NAME="details" ROWS="15" COLS="60" WRAP="SOFT"></TEXTAREA>
	</TD></TR>

	<TR><TD COLSPAN="2">
		<B><FONT COLOR="RED">Did you check to see if this has already been submitted?</FONT></B>
		<P>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT">
		<P>
	<?php 
	if (!user_isloggedin()) {
		echo '<B><FONT COLOR="RED">Please <A HREF="/account/login.php">log in,</A> so followups can be emailed to you.</FONT></B>';
	} 
	?>
		</FORM>
	</TD></TR>

	</TABLE>

<?php

bug_footer(array());

?>
