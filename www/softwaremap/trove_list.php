<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: trove_list.php,v 1.148 2000/12/14 22:08:02 tperdue Exp $

require "pre.php";    
require "vars.php";
require "trove.php";

$HTML->header(array('title'=>'Software Map'));
echo'
	<FONT face="arial, helvetica" size="5"><B>Software Map</B></FONT>
	<HR NoShade>
';

// assign default. 18 is 'topic'
if (!$form_cat) $form_cat = 18;
$form_cat = intval($form_cat);

// get info about current folder
$res_trove_cat = db_query('SELECT * FROM trove_cat WHERE trove_cat_id='.$form_cat);
if (db_numrows($res_trove_cat) < 1) {
	echo db_error();
	exit_error('Invalid Trove Category','That Trove category does not exist.');
}
$row_trove_cat = db_fetch_array($res_trove_cat);

// #####################################
// this section limits search and requeries if there are discrim elements

unset ($discrim_url);
unset ($discrim_desc);

if ($discrim) {
	unset ($discrim_queryalias);
	unset ($discrim_queryand);
	unset ($discrim_url_b);

	// commas are ANDs
	$expl_discrim = explode(',',$discrim);

	// need one link for each "get out of this limit" links
	$discrim_url = '&discrim=';

	$lims=sizeof($expl_discrim);
	if ($lims > 2) {
		$lims=2;
	}

	// one per argument	
	for ($i=0;$i<$lims;$i++) {
		// make sure these are all ints, no url trickery
		$expl_discrim[$i] = intval($expl_discrim[$i]);

		// need one aliased table for everything
		$discrim_queryalias .= ',trove_group_link trove_group_link_'.$i.' ';
		
		// need additional AND entries for aliased tables
		$discrim_queryand .= 'AND trove_group_link_'.$i.'.trove_cat_id='
			.$expl_discrim[$i].' AND trove_group_link_'.$i.'.group_id='
			.'trove_agg.group_id ';

		// must build query string for all urls
		if ($i==0) {
			$discrim_url .= $expl_discrim[$i];
		} else {
			$discrim_url .= ','.$expl_discrim[$i];
		}
		// must also do this for EACH "get out of this limit" links
		// convoluted logic to build urls for these, but works quickly
		for ($j=0;$j<sizeof($expl_discrim);$j++) {
			if ($i!=$j) {
				if (!$discrim_url_b[$j]) {
					$discrim_url_b[$j] = '&discrim='.$expl_discrim[$i];
				} else {
					$discrim_url_b[$j] .= ','.$expl_discrim[$i];
				}
			}
		}

	}

	// build text for top of page on what viewier is seeing
	$discrim_desc = '<FONT size="-1">
<FONT color="#FF0000">
Now limiting view to projects in the following categories:
</FONT>';
	
	for ($i=0;$i<sizeof($expl_discrim);$i++) {
		$discrim_desc .= '<BR> &nbsp; &nbsp; &nbsp; '
			.trove_getfullpath($expl_discrim[$i])
			.' <A href="/softwaremap/trove_list.php?form_cat='.$form_cat
			.$discrim_url_b[$i].'">[Remove This Filter]'
			.'</A>';
	}
	$discrim_desc .= "<HR></FONT>\n";
} 

// #######################################

print '<P>'.$discrim_desc;

// ######## two column table for key on right
// first print all parent cats and current cat
print '<TABLE width=100% border="0" cellspacing="0" cellpadding="0">
<TR valign="top"><TD><FONT face="arial, helvetica" size="3">';
$folders = explode(" :: ",$row_trove_cat['fullpath']);
$folders_ids = explode(" :: ",$row_trove_cat['fullpath_ids']);
$folders_len = count($folders);
for ($i=0;$i<$folders_len;$i++) {
	for ($sp=0;$sp<($i*2);$sp++) {
		print " &nbsp; ";
	}
	echo html_image("/images/ic/ofolder15.png",'15','13',array());
	print "&nbsp; ";
	// no anchor for current cat
	if ($folders_ids[$i] != $form_cat) {
		print '<A href="/softwaremap/trove_list.php?form_cat='
			.$folders_ids[$i].$discrim_url.'">';
	} else {
		print '<B>';
	}
	print $folders[$i];
	if ($folders_ids[$i] != $form_cat) {
		print '</A>';
	} else {
		print '</B>';
	}
	print "<BR>\n";
}

// print subcategories
$res_sub = db_query('SELECT trove_cat.trove_cat_id AS trove_cat_id,'
	.'trove_cat.fullname AS fullname,'
	.'trove_treesums.subprojects AS subprojects FROM trove_cat '
	.'LEFT JOIN trove_treesums USING (trove_cat_id) '
	.'WHERE (trove_treesums.limit_1=0 '
	.'OR trove_treesums.limit_1 IS NULL) AND ' // need no discriminators
	.'trove_cat.parent='.$form_cat.' ORDER BY fullname');
echo db_error();
while ($row_sub = db_fetch_array($res_sub)) {
	for ($sp=0;$sp<($folders_len*2);$sp++) {
		print " &nbsp; ";
	}
	print ('<a href="trove_list.php?form_cat='.$row_sub['trove_cat_id'].$discrim_url.'">');
	echo html_image("/images/ic/cfolder15.png",'15','13',array());
	print ('&nbsp; '.$row_sub['fullname'].'</a> <I>('
		.($row_sub['subprojects']?$row_sub['subprojects']:'0')
		.' projects)</I><BR>');
}
// ########### right column: root level
print '</TD><TD><FONT face="arial, helvetica" size="3">';
// here we print list of root level categories, and use open folder for current
$res_rootcat = db_query('SELECT trove_cat_id,fullname FROM trove_cat WHERE '
	.'parent=0 ORDER BY fullname');
echo db_error();
print 'Browse by:';
while ($row_rootcat = db_fetch_array($res_rootcat)) {
	// print open folder if current, otherwise closed
	// also make anchor if not current
	print ('<BR>');
	if (($row_rootcat['trove_cat_id'] == $row_trove_cat['root_parent'])
		|| ($row_rootcat['trove_cat_id'] == $row_trove_cat['trove_cat_id'])) {
		echo html_image('/images/ic/ofolder15.png','15','13',array());
		print ('&nbsp; <B>'.$row_rootcat['fullname']."</B>\n");
	} else {
		print ('<A href="/softwaremap/trove_list.php?form_cat='
			.$row_rootcat['trove_cat_id'].$discrim_url.'">');
		echo html_image('/images/ic/cfolder15.png','15','13',array());
		print ('&nbsp; '.$row_rootcat['fullname']."\n");
		print ('</A>');
	}
}
print '</TD></TR></TABLE>';
?>
<HR noshade>
<?php
// one listing for each project

$query_projlist = "SELECT * 
	FROM trove_agg
	$discrim_queryalias
	WHERE trove_agg.trove_cat_id='$form_cat'
	$discrim_queryand";

/*

//old query
$query_projlist = "SELECT groups.group_id, "
	. "groups.group_name, "
	. "groups.unix_group_name, "
	. "groups.status, "
	. "groups.register_time, "
	. "groups.short_description, "
	. "project_metric.percentile, "
	. "project_metric.ranking "
	. "FROM groups "
	. "LEFT JOIN project_metric USING (group_id) "
	. ", trove_group_link "
	. $discrim_queryalias
	. "WHERE trove_group_link.group_id=groups.group_id AND "
	. "(groups.is_public=1) AND "
	. "(groups.type=1) AND "
	. "(groups.status='A') AND "
	. "trove_group_link.trove_cat_id=$form_cat "
	. $discrim_queryand
//	. "GROUP BY groups.group_id "
	. "ORDER BY groups.group_name ";
*/
/*

//nightly aggregation query
CREATE TABLE trove_agg AS
SELECT tgl.trove_cat_id, g.group_id, g.group_name, g.unix_group_name, g.status, g.register_time, g.short_description, 
        project_metric.percentile, project_metric.ranking 
        FROM groups g
        LEFT JOIN project_metric USING (group_id) , 
        trove_group_link tgl 
        WHERE 
        tgl.group_id=g.group_id 
        AND (g.is_public=1) 
        AND (g.type=1) 
        AND (g.status='A') 
	ORDER BY g.group_name;

CREATE INDEX troveagg_trovecatid ON trove_agg(trove_cat_id);

SELECT * 
	FROM trove_agg ,
	trove_group_link trove_group_link_0 ,
	trove_group_link trove_group_link_1 
	WHERE trove_agg.trove_cat_id='20' 
	AND trove_group_link_0.trove_cat_id=7 
	AND trove_group_link_0.group_id=trove_agg.group_id 
	AND trove_group_link_1.trove_cat_id=226 
	AND trove_group_link_1.group_id=trove_agg.group_id LIMIT 300 OFFSET 0

SELECT g.group_id, g.group_name, g.unix_group_name, g.status, g.register_time, g.short_description, 
	project_metric.percentile, project_metric.ranking 
	FROM groups 
	LEFT JOIN project_metric USING (group_id) , 
	trove_group_link ,
	trove_group_link trove_group_link_0 ,
	trove_group_link trove_group_link_1 
	WHERE 
	trove_group_link.group_id=groups.group_id 
	AND (groups.is_public=1) 
	AND (groups.type=1) 
	AND (groups.status='A') 
	AND trove_group_link.trove_cat_id=7 
	AND trove_group_link_0.trove_cat_id=7 
	AND trove_group_link_0.group_id=groups.group_id 
	AND trove_group_link_1.trove_cat_id=233 
	AND trove_group_link_1.group_id=groups.group_id 
	ORDER BY groups.group_name 
	LIMIT 300 OFFSET 0

*/

$res_grp = db_query($query_projlist,$TROVE_HARDQUERYLIMIT);
echo db_error();
$querytotalcount = db_numrows($res_grp);
	
// #################################################################
// limit/offset display

// no funny stuff with get vars
$page = intval($page);
if (!$page) {
	$page = 1;
}

// store this as a var so it can be printed later as well
$html_limit = '<SPAN><CENTER><FONT size="-1">';
if ($querytotalcount == $TROVE_HARDQUERYLIMIT)
	$html_limit .= 'More than ';
$html_limit .= '<B>'.$querytotalcount.'</B> projects in result set.';

// only display pages stuff if there is more to display
if ($querytotalcount > $TROVE_BROWSELIMIT) {
	$html_limit .= ' Displaying '.$TROVE_BROWSELIMIT.' per page.<BR>';

	// display all the numbers
	for ($i=1;$i<=ceil($querytotalcount/$TROVE_BROWSELIMIT);$i++) {
		$html_limit .= ' ';
		if ($page != $i) {
			$html_limit .= '<A href="/softwaremap/trove_list.php?form_cat='.$form_cat;
			$html_limit .= $discrim_url.'&page='.$i;
			$html_limit .= '">';
		} else $html_limit .= '<B>';
		$html_limit .= '&lt;'.$i.'&gt;';
		if ($page != $i) {
			$html_limit .= '</A>';
		} else $html_limit .= '</B>';
		$html_limit .= ' ';
	}
}

$html_limit .= '</FONT></CENTER></SPAN>';

print $html_limit."<HR>\n";

// #################################################################
// print actual project listings
// note that the for loop starts at 1, not 0
for ($i_proj=1;$i_proj<=$querytotalcount;$i_proj++) { 
	$row_grp = db_fetch_array($res_grp);

	// check to see if row is in page range
	if (($i_proj > (($page-1)*$TROVE_BROWSELIMIT)) && ($i_proj <= ($page*$TROVE_BROWSELIMIT))) {
		$viewthisrow = 1;
	} else {
		$viewthisrow = 0;
	}	

	if ($row_grp && $viewthisrow) {
		print '<TABLE border="0" cellpadding="0" width="100%"><TR valign="top"><TD colspan="2"><FONT face="arial, helvetica" size="3">';
		print "$i_proj. <a href=\"/projects/". strtolower($row_grp['unix_group_name']) ."/\"><B>"
			.htmlspecialchars($row_grp['group_name'])."</B></a> ";
		if ($row_grp['short_description']) {
			print "- " . htmlspecialchars($row_grp['short_description']);
		}

		print '<BR>&nbsp;';
		// extra description
		print '</TD></TR><TR valign="top"><TD><FONT face="arial, helvetica" size="3">';
		// list all trove categories
		trove_getcatlisting($row_grp['group_id'],1,0);

		print '</TD>'."\n".'<TD align="right"><FONT face="arial, helvetica" size="3">'; // now the right side of the display
		print 'Activity Percentile: <B>'.$row_grp['percentile'].'</B>';
		print '<BR>Activity Ranking: <B>'.$row_grp['ranking'].'</B>';
		print '<BR>Register Date: <B>'.date($sys_datefmt,$row_grp['register_time']).'</B>';
		print '</TD></TR>';
/*
                if ($row_grp['jobs_count']) {
                	print '<tr><td colspan="2" align="center">'
                              .'<a href="/people/?group_id='.$row_grp['group_id'].'">[This project needs help]</a></td></td>';
                }
*/
                print '</TABLE>';
		print '<HR>';
	} // end if for row and range chacking
}

// print bottom navigation if there are more projects to display
if ($querytotalcount > $TROVE_BROWSELIMIT) {
	print $html_limit;
}

// print '<P><FONT size="-1">This listing was produced by the following query: '
//	.$query_projlist.'</FONT>';

$HTML->footer(array());

?>
