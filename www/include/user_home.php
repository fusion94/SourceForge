<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: user_home.php,v 1.28 2000/11/30 16:13:20 dbrogdon Exp $

/*

	Developer Info Page
	Written by dtype Oct 1999


	Assumes $user object for displayed user is present


*/

require ('vote_function.php');

$HTML->header(array('title'=>'Developer Profile'));

?>

<H3>Developer Profile</H3>
<P>
<TABLE width=100% cellpadding=2 cellspacing=2 border=0><TR valign=top>
<TD width=50%>

<?php echo $HTML->box1_top("Personal Information",true,false,false); ?>
<TR>
	<TD>User ID: </TD>
	<TD><B><?php print $user_id; ?></B> ( <A HREF="/people/viewprofile.php?user_id=<?php print $user_id; ?>"><B>Skills Profile</B></A> )</TD>
</TR>

<TR valign=top>
	<TD>Login Name: </TD>
	<TD><B><?php print $user->getUnixName(); ?></B></TD>
</TR>

<TR valign=top>
	<TD>Real Name: </TD>
	<TD><B><?php print $user->getRealName(); ?></B></TD>
</TR>

<TR valign=top>
	<TD>Email Addr: </TD>
	<TD>
	<B><A HREF="/sendmessage.php?touser=<?php print $user_id; 
		?>"><?php print $user->getUnixName(); ?> at <?php print $GLOBALS['sys_users_host']; ?></A></B>
	</TD>
</TR>

<TR>
	<TD>
	Site Member Since: 
	</TD>
	<TD><B><?php print date($sys_datefmt, $user->getAddDate()); ?></B>
	<?php

	echo $HTML->box1_middle('Peer Rating',false,false);

	echo vote_show_user_rating($user_id);

	echo $HTML->box1_middle('Diary And Notes');
 
	/*

		Get their diary information

	*/

	$res=db_query("SELECT count(*) from user_diary ".
		"WHERE user_id='". $user_id ."' AND is_public=1");
	echo 'Diary/Note Entries: '.db_result($res,0,0).'
	<P>
	<A HREF="/developer/diary.php?user='. $user_id .'">View Diary & Notes</A>
	<P>
	<A HREF="/developer/monitor.php?user='. $user_id .'">'. html_image("/images/ic/check.png",'15','13',array(),0) .'Monitor This Diary</A>';

	?>
</TD></TR>

<TR><TD COLSPAN=2>
	<H4>Project Info</H4>
	<P>
<?php
	// now get listing of groups for that user
	$res_cat = db_query("SELECT groups.group_name, "
	. "groups.unix_group_name, "
	. "groups.group_id, "
	. "user_group.admin_flags, "
	. "user_group.bug_flags FROM "
	. "groups,user_group WHERE user_group.user_id='$user_id' AND "
	// We don't need to block out foundries from displaying.
	//. "groups.group_id=user_group.group_id AND groups.is_public='1' AND groups.status='A' AND groups.type='1'");
	. "groups.group_id=user_group.group_id AND groups.is_public='1' AND groups.status='A'");

// see if there were any groups
if (db_numrows($res_cat) < 1) {
	?>
	<p>This developer is not a member of any projects.
	<?php
} else { // endif no groups
	print "<p>This developer is a member of the following groups:<BR>&nbsp;";
	while ($row_cat = db_fetch_array($res_cat)) {
		print ("<BR>" . "<A href=\"/projects/$row_cat[unix_group_name]/\">$row_cat[group_name]</A>\n");
	}
	print "</ul>";
} // end if groups

$HTML->box1_bottom(); ?>

</TD><TD>
If you are familiar with this user, please take a moment to rate him/her
on the following criteria. Keep in mind, that your rating will be visible to
the user and others.
<P>
The SourceForge Peer Rating system is based on concepts from 
<A HREF="http://www.advogato.com">Advogato.</A> The system has been re-implemented and expanded in a few ways.
	<CENTER>
        <?php echo vote_show_user_rate_box ($user_id); ?>
	</CENTER>
<P>
The Peer Rating box shows all rating averages
(and response levels) for each individual criteria. Due to the math and
processing required to do otherwise, these numbers incoporate responses from
both "trusted" and "non-trusted" users.
<ul>
<li> The "Sitewide Rank" field shows the user's rank compared to all ranked
SourceForge users. 
<li>The "Aggregate Score" shows an average, weighted overall score, based on
trusted-responses only. 
<li>The "Personal Importance" field shows the weight that users ratings of
other developers will be given (between 1 and 1.5) -- higher rated user's
responses are given more weight.  
</ul>
</TD></TR>
</TABLE>

<TABLE width=100% cellpadding=2 cellspacing=2 border=0><TR valign=top>
<TR><TD COLSPAN="2">

<?php 

if (user_isloggedin()) {

	?>
	&nbsp;
	<P>
	<H3>Send a Message to <?php echo $user->getRealName(); ?></H3>
	<P>
	<FORM ACTION="/sendmessage.php" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="touser" VALUE="<?php echo $user_id; ?>">

	<B>Your Email Address:</B><BR>
	<B><?php echo user_getname().'@'.$GLOBALS['sys_users_host']; ?></B>
	<INPUT TYPE="HIDDEN" NAME="email" VALUE="<?php echo user_getname().'@'.$GLOBALS['sys_users_host']; ?>">
	<P>
	<B>Your Name:</B><BR>
	<B><?php 

	$my_name=user_getrealname(user_getid());

	echo $my_name; ?></B>
	<INPUT TYPE="HIDDEN" NAME="name" VALUE="<?php echo $my_name; ?>">
	<P>
	<B>Subject:</B><BR>
	<INPUT TYPE="TEXT" NAME="subject" SIZE="30" MAXLENGTH="40" VALUE="">
	<P>
	<B>Message:</B><BR>
	<TEXTAREA NAME="body" ROWS="15" COLS="50" WRAP="HARD"></TEXTAREA>
	<P>
	<CENTER>
	<INPUT TYPE="SUBMIT" NAME="send_mail" VALUE="Send Message">
	</CENTER>
	</FORM>
	<?php

} else {

	echo '<H3>You Could Send a Message if you were logged in</H3>';

}

?>

</TD></TR>
</TABLE>

<?php

$HTML->footer(array());

?>
