<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: alive.php,v 1.8 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

print "<PRE>\n";
print "On webserver ";
system("hostname",$res[2]);
print "\n################################### Pinging web1...\n";
system("ping -c 1 web1",$res[0]);
print "\n################################### Pinging web2...\n";
system("ping -c 1 web2",$res[1]);
print "\n################################### Pinging underworld...\n";
system("ping -c 1 underworld",$res[3]);
print "\n################################### Pinging geocrawler...\n";
system("ping -c 1 geocrawler",$res[4]);

phpinfo();

print "</PRE>";

?>
