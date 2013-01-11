<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: deleted.php,v 1.7 2000/08/31 06:07:52 gherteg Exp $

require "pre.php";    
$HTML->header(array(title=>"Deleted Account"));
?>

<P><B>Deleted Account</B>

<P>Your account has been deleted. If you have questions regarding your deletion,
please email <A HREF="mailto:staff@<?php echo $GLOBALS['sys_default_domain']; ?>">staff@<?php echo $GLOBALS['sys_default_domain']; ?></A>.
Inquiries through other channels will be directed to this address.

<?php
$HTML->footer(array());

?>
