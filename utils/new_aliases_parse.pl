#!/usr/bin/perl
#
# $Id: new_aliases_parse.pl,v 1.3 2000/08/21 20:16:54 tperdue Exp $
#

require("include.pl");

@alias_dump = open_array_file("/home/dummy/dumps/alias_dump");
@sendmailcw = open_array_file("sendmail.cw");
@virtusertable = ();
$added_domains;

push @virtusertable, sprintf("%-24s%-10s", 'tim@sourceforge.net', 'tim@perdue.net' . "\n");

while ($ln = pop(@alias_dump)) {
	chop($ln);
	($username, $domainname, $userto) = split(":", $ln);

	if (!($added_domains =~ /.*$domainname.*/)) {
		push @sendmailcw, "$domainname\n";
		$added_domains = "$domainname,";
	}

	push @virtusertable, sprintf("%-24s%-10s", $username, $userto . "\n");
}

write_array_file("/etc/mail/virtusertable", @virtusertable);

write_array_file("/etc/sendmail.cw", @sendmailcw);

system("/etc/rc.d/init.d/sendmail restart >/dev/null 2>&1");

system("cd /etc/mail ; make >/dev/null 2>&1");
