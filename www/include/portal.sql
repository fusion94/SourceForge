#alter table groups add column type int not null default 1;

#create index idx_groups_type on groups(type);

#create table group_type (
#type_id int not null auto_increment primary key,
#name text
#);

#INSERT INTO group_type VALUES ('1','Project');
#INSERT INTO group_type VALUES ('2','Portal');

create table portal_projects (
portal_project_id int not null auto_increment primary key,
portal_id int not null,
group_id int not null,
rank int not null
);

create index idx_portal_project_group on portal_projects(group_id);

create table portal_news (
portal_news_id  int not null auto_increment primary key,
portal_id int not null,
news_id int not null
);

create index idx_portal_news_portal on portal_news(portal_id);

create table portal_links (
portal_link_id  int not null auto_increment primary key,
portal_id int not null,
url text not null,
title text not null,
description text not null
);

create index idx_portal_links_portal on portal_links(portal_id);

