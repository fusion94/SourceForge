<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.8 2000/08/31 22:58:45 msnelham Exp $ 
require('pre.php');
require('project_stats_utils.php');

site_project_header(array('title'=>"Project statistics ".$groupname,'group'=>$group_id,'toptab'=>'home'));

if ( !$group_id ) {
	exit_error("Invalid Group","That group could not be found.");
}
   //if the project isn't active, require you to be a member of the super-admin group
//if ( !(db_result($res_grp,0,'status') == 'A') ) {
//	session_require( array('group'=>1) );
//}

//
// BEGIN PAGE CONTENT CODE
//

echo "\n\n";

if ( !$span ) {
	$span = 14;
}

if ( !$view ) { 
	$view = "daily";
}

print '<DIV ALIGN="CENTER">';
print '<font size="+1"><b>Usage Statistics </b></font><BR>';
print '<IMG SRC="stats_graph.png?group_id='.$group_id.'&span='.$span.'&view='.$view.'">';
print '</DIV>';

if ( $view == 'daily' ) {

	print '<P>';
	stats_project_daily( $group_id, $span );

} elseif ( $view == 'weekly' ) {

	print '<P>';
	stats_project_weekly( $group_id, $span );

} elseif ( $view == 'monthly' ) {

	print '<P>';
	stats_project_monthly( $group_id, $span );

} else {

	   // default stats display, DAILY
	print '<P>';
	stats_project_daily( $group_id, $span );

}

print '<BR><P>';
stats_site_agregate( $group_id );

?>

<DIV ALIGN="center">
<FORM action="index.php" method="get">
View the Last <SELECT NAME="span">
<OPTION VALUE="4" <?php if ($span == 4) {echo 'SELECTED';} ?>>4</OPTION>
<OPTION VALUE="7" <?php if ($span == 7 || !isset($span) ) {echo 'SELECTED';} ?>>7</OPTION>
<OPTION VALUE="12" <?php if ($span == 12) {echo 'SELECTED';} ?>>12</OPTION>
<OPTION VALUE="14" <?php if ($span == 14) {echo 'SELECTED';} ?>>14</OPTION>
<OPTION VALUE="30" <?php if ($span == 30) {echo 'SELECTED';} ?>>30</OPTION>
<OPTION VALUE="52" <?php if ($span == 52) {echo 'SELECTED';} ?>>52</OPTION>
</SELECT>
&nbsp;
<SELECT NAME="view">
<OPTION VALUE="monthly" <?php if ($view == "monthly") {echo 'SELECTED';} ?>>Months</OPTION>
<OPTION VALUE="weekly" <?php if ($view == "weekly") {echo 'SELECTED';} ?>>Weeks</OPTION>
<OPTION VALUE="daily" <?php if ($view == "daily" || !isset($view) ) {echo 'SELECTED';} ?>>Days</OPTION>
</SELECT>
&nbsp; 
<INPUT type="submit" value="Change Stats View">
<INPUT type="hidden" name="group_id" value="<?php echo $group_id; ?>">
</FORM>
</DIV>


<?php
//
// END PAGE CONTENT CODE
//

site_project_footer( array() );
?>
