<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: add_task.php,v 1.58 2000/01/13 18:36:35 precision Exp $

pm_header(array('title'=>'Add a New Task'));

?>
<H2>Add A Task To <?php echo  get_task_group_name($group_project_id); ?></H2>

<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="func" VALUE="postaddtask">
<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
<INPUT TYPE="HIDDEN" NAME="group_project_id" VALUE="<?php echo $group_project_id; ?>">

<TABLE BORDER="0" WIDTH="100%">
	<TR>
		<TD>
			<B>Percent Complete:</B>
			<BR>
			<?php echo ShowPercentCompleteBox(); ?>
		</TD>
		<TD>
			<B>Priority:</B>
			<BR>
			<?php echo build_priority_select_box(); ?>
		</td>
	</TR>

  	<TR>
		<TD COLSPAN="2"><B>Task Summary:</B>
		<BR>
		<INPUT TYPE="text" name="summary" size="40" MAXLENGTH="65">
		</td>
	</TR>
	<TR>
		<TD COLSPAN="2"><B>Task Details:</B>
		<BR>
		<TEXTAREA NAME="details" ROWS="5" COLS="40" WRAP="SOFT"></TEXTAREA></td>
	</TR>
	<TR>
    		<TD COLSPAN="2"><B>Start Date:</B>
		<BR>
		<?php
		ShowMonthListSelectBox('start_month',date('m', time()));
		ShowDaySelectBox('start_day',date('d', time()));
		ShowYearSelectBox('start_year',date('Y', time()));
		?>
			<BR><a href="calendar.php">View Calendar</a>
		 </td>

	</TR>
	<TR>
		<TD COLSPAN="2"><B>End Date:</B>
		<BR>
		<?php
		ShowMonthListSelectBox('end_month',date('m', time()));
		ShowDaySelectBox('end_day',date('d', time()));
		ShowYearSelectBox('end_year',date('Y', time()));
		?>
		</td>

	</TR>
	<TR>
		<TD>
		<B>Assigned To:</B>
		<BR>
		<?php
		$sql="SELECT user.user_id,user.user_name ".
			"FROM user,user_group WHERE user.user_id=user_group.user_id ".
			"AND user_group.group_id='$group_id' AND user_group.project_flags IN (1,2)";
		$result=db_query($sql);
		build_multiple_select_box($result,'assigned_to[]',array());
		?>
		</td>
		<TD>
		<B>Dependent On Task:</B>
		<BR>
		<?php
		$sql="SELECT project_task_id,summary ".
			"FROM project_task WHERE group_project_id='$group_project_id' AND status_id <> '3' ORDER BY project_task_id DESC LIMIT 200";
		$result=db_query($sql);
		build_multiple_select_box($result,'dependent_on[]',array());
		?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><B>Hours:</B>
		<BR>
		<INPUT TYPE="text" name="hours" size="5">
		</td>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<INPUT TYPE="submit" value="Submit" name="submit">
		</td>
		</form>
	</TR>
</TABLE>
<?php

pm_footer(array());

?>
