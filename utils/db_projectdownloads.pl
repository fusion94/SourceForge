#!/usr/bin/perl
#
# $Id: db_projectdownloads.pl,v 1.4 1999/12/21 04:21:27 dtype Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

&db_connect;

# gather all releases 
my $query = "SELECT group_id,file_downloads FROM groups";
my $rel = $dbh->prepare($query);
$rel->execute();

# for each release
while(my ($group_id,$oldcount) = $rel->fetchrow()) {
	my $query = "SELECT SUM(downloads) FROM filerelease WHERE group_id=$group_id";
	my $count = $dbh->prepare($query);
	$count->execute();
	($row_count) = $count->fetchrow();
	if (!$row_count) {
		$row_count = 0;
	}

	print "ID $group_id: $row_count downloads\n";
	
# only update if count is different
	if ($row_count != $oldcount) { 
		$query = "UPDATE groups SET file_downloads=$row_count "
			." WHERE group_id=$group_id";
		my $update = $dbh->prepare($query);
		$update->execute();
	}
}
