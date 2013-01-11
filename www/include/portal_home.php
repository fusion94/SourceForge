<?php

require($DOCUMENT_ROOT.'/news/news_utils.php');

site_header(array('title'=>group_getname($group_id).' - Portal','group'=>$group_id));

echo db_result($res_grp,0,'short_description');

echo html_tabs('home',$group_id);

/*

	News that was selected for display by the portal
	News items are chosen froma list of news in subprojects

*/

echo '<TABLE WIDTH="100%" BORDER="0">

	<TR><TD COLSPAN=2>
		<H2>Project News</H2>
		<P>';

echo news_portal_latest($group_id);

echo '</TD></TR>

	<TR><TD WIDTH="50%" VALIGN="TOP">';

html_box1_top('Discussion Forums');

/*

	Message Forums

*/

$sql="SELECT * FROM forum_group_list WHERE group_id='$group_id' AND is_public='1';";

$result = db_query ($sql);

$rows = db_numrows($result);

if (!$result || $rows < 1) {

	echo '<H1>No forums found for '.group_getname($group_id).'</H1>';

} else {

	/*
		Put the result set (list of forums for this group) into a column with folders
	*/

	for ($j = 0; $j < $rows; $j++) {
		echo '
			<A HREF="/forum/forum.php?forum_id='. db_result($result, $j, 'group_forum_id') .'">'.
			'<IMG SRC="/images/ic/cfolder15.png" HEIGHT=13 WIDTH=15 BORDER=0> &nbsp;'.
			db_result($result, $j, 'forum_name').'</A> ';
		//message count
		echo '('.db_result(db_query("SELECT count(*) FROM forum WHERE group_forum_id='".db_result($result, $j, 'group_forum_id')."'"),0,0).' msgs)';
		echo "<BR>\n";
		echo db_result($result,$j,'description').'<P>';
	}

}

html_box1_bottom();

echo '</TD>

<TD WIDTH="50%" VALIGN="TOP">';

/*
	The project's news
*/

html_box1_top('Announcements');

echo news_show_latest($group_id,5,false);

html_box1_bottom();
/*

	Links

*/

echo '</TD></TR>

<TR><TD COLSPAN=2>';

html_box1_top('Links &amp; Resources');

$sql="SELECT * FROM portal_links WHERE portal_id='$group_id'";
$result=db_query($sql);
$rows=db_numrows($result);

if ($rows < 1) {

	echo '<B>Links:</B>';

} else {

	$split_at_row=intval(($rows/2)+1);

	for ($i=0; $i<$rows; $i++) {
		if ($i==$split_at_row) {
			echo '</TD><TD VALIGN="TOP">';
		}
		echo '<A HREF="'. db_result($result,$i,'link_url') .'">'. db_result($result,$i,'link_title') .'</A><BR>';
	}

}

html_box1_bottom();

echo '</TD></TR></TABLE>';

site_footer(array());

?>
