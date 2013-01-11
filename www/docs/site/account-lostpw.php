<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: account-lostpw.php,v 1.4 2000/01/13 18:36:35 precision Exp $

require "pre.php";    
site_header(array(title=>"Your account email address"));
?>

<P><B>If you lose your password...</B>

<P>If you lose your password simply visit the login page and click
"Lost Your Password?". 
A confirmation hash will be emailed to the address we have on file for you.
Load the URL in the email to reset your password.

<P><A href="/docs/site/">[Return to Site Documentation]</A>

<?php
site_footer(array());
site_cleanup(array());
?>
