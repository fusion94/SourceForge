<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: requirements.php,v 1.13 2000/01/13 18:36:36 precision Exp $

require "pre.php";    // Initial db and session library, opens session
session_require(array(isloggedin=>1));
site_header(array(title=>"Project Requirements"));
?>

<H2>Step 1: SourceForge Services & Requirements</H2>

<P>We are now offering a full suite of services for SourceForge
projects. If you haven't already, please be sure to browse
the most recent revision of the 
<B><A href="/docs/site/services.php">SourceForge Services</A></B>.

<P><B>Use of Project Account</B>
<P>The space given to you on this server is given for the expressed
purpose of Open Source development or, in the case of web sites,
the advancement of Open Source. We will terminate the account if
it is used for other reasons. If you have a question regarding use
of your account, please ask it and we will provide prompt feedback.
<P>You will be providing us with a summary of your project later
in the signup process.

<P><B>Creative Freedom</B>
<P>It is our intent to allow you creative freedom on your project.
This is not a totally free licence, though.
For our legal protection and yours we must monitor sites
for inappropriate material. Please know, however that we too are
Open Source developers that value our freedom and we will 
stay out of your way as much as possible.

<P>Details about these restrictions will be presented in the Terms
of Service Agreement. 

<P><B>Advertisements</B>
<P>You may not place any revenue-generating advertisements on
a site hosted at SourceForge.

<P><B>SourceForge Link</B>
<P>If you host a web site at SourceForge,
you must place one of our approved graphic images on your site
with a link back to SourceForge. The graphic may either link
to the main SourceForge site or to your project page on 
SourceForge. We will placement up to you, and will not alter
your site in any way.

<P>There are many small and non-intrusive image choices.

<P><B>Open Source/Rights to Code</B>
<P>You will be presented with a choice of <A href="http://www.opensource.org">Open Source</A>
approved <A href="http://www.opensource.org/licenses/">licenses</A>
 for your project. You will still own the code,
but all of these licenses will also allow us to 
make your code available to the general public. Although you may
choose to stop hosting your project with us, the nature of these 
licenses will allow us to continue to make your code available.

<P>If you wish to use another license that is not currently
approved by the Open Source Initiative, let us know and we will
handle these requests on a case-by-case basis.

<P>It is our intent to provide a <I>permanent</I> home for all
versions of your code. We do reserve the right, however, to terminate
your project if there is due cause. Details will be presented in
the Terms of Service Agreement.

<p>&nbsp;
<BR><H3 align=center><a href="tos.php">Step 2: Terms of Service Agreement</a></H3>
</p>

<?php
site_footer(array());
site_cleanup(array());
?>

