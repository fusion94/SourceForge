<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mod_task.php,v 1.23 2000/01/18 05:27:53 tperdue Exp $

pm_header(array('title'=>'Modify A Task'));

$sql="SELECT * FROM project_task ".
	"WHERE project_task_id='$project_task_id' AND group_project_id='$group_project_id'";

$result=db_query($sql);

?>
<H2>Modify A Task In <?php echo  get_task_group_name($group_project_id); ?></H2>

<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="func" VALUE="postmodtask">
<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
<INPUT TYPE="HIDDEN" NAME="group_project_id" VALUE="<?php echo $group_project_id; ?>">
<INPUT TYPE="HIDDEN" NAME="project_task_id" VALUE="<?php echo $project_task_id; ?>">

<TABLE BORDER="0" WIDTH="100%">
	<TR>
		<TD><B>Percent Complete:</B>
		<BR>
		<?php echo ShowPercentCompleteBox('percent_complete',db_result($result,0,'percent_complete')); ?>
		</TD>

		<TD><B>Priority:</B>
		<BR>
		<?php echo build_priority_select_box('priority',db_result($result,0,'priority')); ?>
		</TD>
	</TR>

  	<TR>
		<TD COLSPAN="2"><B>Task Summary:</B>
		<BR>
		<INPUT TYPE="text" name="summary" size="40" MAXLENGTH="65" VALUE="<?php echo stripslashes(db_result($result,0,'summary')); ?>">
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
		<B>Original Comment:</B>
		<P>
		<?php echo nl2br(stripslashes(db_result($result,0,'details'))); ?>
		<P>
		<B>Add A Comment:</B>
		<BR>
		<TEXTAREA NAME="details" ROWS="5" COLS="40" WRAP="SOFT"></TEXTAREA>
		</TD>
	</TR>

	<TR>
    		<TD COLSPAN="2"><B>Start Date:</B>
		<BR>
		<?php
		ShowMonthListSelectBox('start_month',date('m', db_result($result,0,'start_date')));
		ShowDaySelectBox('start_day',date('d', db_result($result,0,'start_date')));
		ShowYearSelectBox('start_year',date('Y', db_result($result,0,'start_date')));
		?>
		<BR><a href="calendar.php">View Calendar</a>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2"><B>End Date:</B>
		<BR>
		<?php
		ShowMonthListSelectBox('end_month',date('m', db_result($result,0,'end_date')));
		ShowDaySelectBox('end_day',date('d', db_result($result,0,'end_date')));
		ShowYearSelectBox('end_year',date('Y', db_result($result,0,'end_date')));
		?>
		</TD>
	</TR>

	<TR>
		<TD>
		<B>Assigned To:</B>
		<BR>
		<?php
		/*
			List of possible users that this one could be assigned to
		*/

		$sql="SELECT user.user_id,user.user_name ".
			"FROM user,user_group WHERE user.user_id=user_group.user_id ".
			"AND user_group.group_id='$group_id' AND user_group.project_flags IN (1,2)";
		$result3=db_query($sql);
		/*
			Get the list of ids this is assigned to and convert to array
			to pass into multiple select box
		*/

		$result2=db_query("SELECT assigned_to_id FROM project_assigned_to WHERE project_task_id='$project_task_id'");
//		echo db_numrows($result2);

		build_multiple_select_box($result3,'assigned_to[]',result_column_to_array($result2));
		?>
		</TD>

		<TD>
		<B>Dependent On Task:</B>
		<BR>
		<?php
		/*
			List of possible tasks that this one could depend on
		*/

		$sql="SELECT project_task_id,summary ".
			"FROM project_task ".
			"WHERE group_project_id='$group_project_id' ".
			"AND status_id <> '3' ".
			"AND project_task_id <> '$project_task_id' ORDER BY project_task_id DESC LIMIT 100";
		$result3=db_query($sql);

		/*
			Get the list of ids this is dependent on and convert to array
			to pass into multiple select box
		*/
		$result2=db_query("SELECT is_dependent_on_task_id FROM project_dependencies WHERE project_task_id='$project_task_id'");
//		echo db_numrows($result2);

		build_multiple_select_box($result3,'dependent_on[]',result_column_to_array($result2));
		?>
		</TD>
	</TR>

	<TR>
		<TD>
		<B>Hours:</B>
		<BR>
		<INPUT TYPE="text" name="hours" size="5" VALUE="<?php echo db_result($result,0,'hours'); ?>">
		</TD>

		<TD>
		<B>Status:</B>
		<BR>
		<?php
		build_select_box(db_query('SELECT * FROM project_status'),'status_id',db_result($result,0,'status_id'));
		?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
			<?php echo show_dependent_tasks ($project_task_id,$group_id,$group_project_id); ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
			<?php echo show_dependent_bugs ($project_task_id,$group_id,$group_project_id); ?>
		</TD>
	</TR>
 
	<TR>
		<TD COLSPAN="2">
			<?php echo show_task_details ($project_task_id); ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
			<?php echo show_task_history ($project_task_id); ?>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
		<INPUT TYPE="submit" value="Submit" name="submit">
		</TD>
		</form>
	</TR>

</table>
<?php

pm_footer(array());

?>
