<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mod_bug.php,v 1.42 2000/01/13 18:36:34 precision Exp $

bug_header(array ('title'=>'Modify a Bug'));

$sql="SELECT * FROM bug WHERE bug_id='$bug_id' AND group_id='$group_id'";

$result=db_query($sql);

if (db_numrows($result) > 0) {

	echo "\n<H2>[ Bug #$bug_id ] ".stripslashes(db_result($result,0,"summary"))."</H2>";

	echo "<FORM ACTION=\"$PHP_SELF\" METHOD=\"POST\">\n".
		"<INPUT TYPE=\"HIDDEN\" NAME=\"func\" VALUE=\"postmodbug\">\n".
		"<INPUT TYPE=\"HIDDEN\" NAME=\"group_id\" VALUE=\"$group_id\">\n".
		"<INPUT TYPE=\"HIDDEN\" NAME=\"bug_id\" VALUE=\"$bug_id\">";

	echo	"\n<TABLE WIDTH=\"100%\">".
		"\n<TR><TD><B>Submitted By:</B><BR>".user_getname(db_result($result,0,"submitted_by"))."</TD><TD><B>Group:</B><BR>".group_getname($group_id)."</TD></TR>".

		"\n<TR><TD><B>Category:</B><BR>\n";
	/*
		List of categories for this bug.
	*/
	$sql="select bug_category_id,category_name from bug_category WHERE group_id='$group_id'";
	$result2=db_query($sql);
	build_select_box($result2,"category_id",db_result($result,0,"category_id")); 

	echo "</TD><TD><B>Priority:</B><BR>\n";

	/*
		Priority of this bug
	*/
	build_priority_select_box("priority",db_result($result,0,"priority"));

?>
	</TD></TR>

	<TR><TD><B>Group:</B><BR>
<?php
	/*
		List of possible bug_groups for this group
	*/
	$sql="select bug_group_id,group_name from bug_group WHERE group_id='$group_id'";

	$result3=db_query($sql);

	build_select_box($result3,"bug_group_id",db_result($result,0,"bug_group_id"));

?>
	</TD><TD><B>Resolution:</B><BR>
<?php
	/*
		List of possible bug_groups for this group
	*/
	$sql="select resolution_id,resolution_name from bug_resolution";

	$result4=db_query($sql);

	build_select_box($result4,"resolution_id",db_result($result,0,"resolution_id"));

?>
	</TD></TR>
	<TR><TD>
		<B>Assigned To:</B><BR>
		<?php

		/*
			List of people that can be assigned this bug
		*/
		$sql="SELECT user.user_id,user.user_name FROM user,user_group ".
			"WHERE user.user_id=user_group.user_id AND user_group.bug_flags IN (1,2) AND user_group.group_id='$group_id'";
		$result2=db_query($sql);
		build_select_box($result2,"assigned_to",db_result($result,0,"assigned_to"));
		?>
	</TD>
	<TD>
		<B>Status:</B><BR>
		<?php
		/*
			Status of this bug
		*/
		$sql="select * from bug_status";
		$result2=db_query($sql);
		build_select_box($result2,"status_id",db_result($result,0,'status_id'));
		?>
	</TD></TR>

	<TR><TD COLSPAN="2"><B>Summary:</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" SIZE="45" VALUE="<?php 
			echo stripslashes(db_result($result,0,'summary')); 
			?>" MAXLENGTH="60">
	</TD></TR>

	<TR><TD COLSPAN="2"><B>Add Comment:</B><BR>
		<TEXTAREA NAME="details" ROWS="7" COLS="60" WRAP="SOFT"></TEXTAREA>
		<P>
		<B>Original Submission:</B><BR>
		<?php
			echo nl2br(stripslashes(db_result($result,0,'details')));

			echo "<P>";

			echo show_bug_details($bug_id); 
		?>
	</TD></TR>

	<TR><TD VALIGN="TOP">
	<B>Dependent on Task:</B><BR>
	<?php 
	/*
		Dependent on Task........
	*/
	$sql="SELECT project_task.project_task_id,project_task.summary ".
		"FROM project_task,project_group_list WHERE project_task.group_project_id=project_group_list.group_project_id ".
		"AND project_task.status_id <> '3' AND project_group_list.group_id='$group_id' ORDER BY project_task_id DESC LIMIT 150";
	$result3=db_query($sql);

	/*
		Get the list of ids this is dependent on and convert to array
		to pass into multiple select box
	*/
	$result2=db_query("SELECT is_dependent_on_task_id FROM bug_task_dependencies WHERE bug_id='$bug_id'");

	build_multiple_select_box($result3,'dependent_on_task[]',result_column_to_array($result2));

	?>
	</TD><TD VALIGN="TOP">
	<B>Dependent on Bug:</B><BR>
	<?php
	/*
		Dependent on Bug........
	*/
	$sql="SELECT bug_id,summary ".
		"FROM bug WHERE group_id='$group_id' AND bug_id <> '$bug_id' ORDER BY bug_id DESC LIMIT 150";
	$result3=db_query($sql);

	/*
		Get the list of ids this is dependent on and convert to array
		to pass into multiple select box
	*/
	$result2=db_query("SELECT is_dependent_on_bug_id FROM bug_bug_dependencies WHERE bug_id='$bug_id'");

	build_multiple_select_box($result3,'dependent_on_bug[]',result_column_to_array($result2));

	?>
	</TD></TR>

        <TR><TD COLSPAN="2">
                <?php echo show_dependent_bugs($bug_id,$group_id); ?>
        </TD></TR>

	<TR><TD COLSPAN="2">
		<?php echo show_bughistory($bug_id); ?>
	</TD></TR>

	<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<INPUT TYPE="CHECKBOX" NAME="mail_followup" VALUE="y" Checked>Send Followup to Submittor
		<P>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Submit Changes">
		</FORM>
	</TD></TR>

	</TABLE>

<?php

} else {

	echo '
		<H1>Bug Not Found</H1>';
	echo db_error();
}

bug_footer(array());

?>
