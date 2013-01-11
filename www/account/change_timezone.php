<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: change_timezone.php,v 1.4 2000/06/30 19:03:34 tperdue Exp $

require ('pre.php');
require ('account.php');
require ('timezones.php');

if (!user_isloggedin()) {
	exit_not_logged_in();
}

if ($submit) {	
	if (!$timezone) {
		$feedback .= ' Nothing Updated ';
	} else {
		// if we got this far, it must be good
		db_query("UPDATE user SET timezone='$timezone' WHERE user_id=" . user_getid());
		session_redirect("/account/");
	}
}

site_header(array('title'=>"Change RealName"));

?>
<H3>Timezone Change</h3>
<P>
Now, no matter where you live, you can see all dates and times throughout SourceForge 
as if it were in your neighborhood.
<P>
<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
<?php

echo '<H4>'.$feedback.'</H4>';

echo util_build_select_box_from_arrays ($TZs,$TZs,'timezone',user_get_timezone());

?>
<input type="submit" name="submit" value="Update">
</form>

<?php

site_footer(array());

?>
