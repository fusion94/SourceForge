#!/usr/bin/perl
#
# $Id: db_subcat.pl,v 1.18 2000/06/17 08:32:28 tperdue Exp $
#
use DBI;

require("include.pl");  # Include all the predefined functions

&db_connect;

# array vor category counts;
my @cat;

# gather all releases 
my $query = "SELECT group_id FROM groups WHERE is_public=1 AND (status='A')";
my $grp = $dbh->prepare($query);
$grp->execute();

# recursive sub for each node

sub incnode {
	local ($category) = @_;

	#increment this category
	@onecat[$category] = 1;
	
	#do the same for all parent categories only if not at root yet
	if ($category <= 3) {
		return;
	}

	my $query = "SELECT parent FROM category_link WHERE child=$category";
	my $catparent = $dbh->prepare($query);
	$catparent->execute();

	while (my ($parent) = $catparent->fetchrow()) {
		&incnode($parent);
	}
}

# for each release check all parents, all the way to the top
while(my ($group_id) = $grp->fetchrow()) {
	#clear onecat
	@onecat = ();

	# find all categories for group
	my $query = "SELECT category_id FROM group_category WHERE group_id=$group_id";
	my $grpcat = $dbh->prepare($query);
	$grpcat->execute();

	# for each category
	while (my ($category_id) = $grpcat->fetchrow()) {
		&incnode($category_id);
	}

	# add onecat entries to total cat
	for ($i=0;$i<@onecat;$i++) {	
		@cat[$i] += @onecat[$i];
	}
}

# output results, write to db

for ($i=0;$i<@cat;$i++) {
	if (@cat[$i]) {
		my $query = "UPDATE category SET sub_files=" . @cat[$i] . " WHERE category_id=$i";
		my $update = $dbh->prepare($query);
		$update->execute();
	}
}
