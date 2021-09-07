drop table if exists roles;
create table roles
(
  role_id bigint not null primary key auto_increment,
  role varchar(255) not null,
  description varchar(255) not null,
  system_role int not null
);
insert into roles select null, 'Super Users', 'Super Users Role', 1;
create index ix_roles_1 on roles(role);

drop table if exists users;
create table users
(
  user_id bigint not null primary key auto_increment,
  user_name varchar(255) not null,
  password mediumtext not null,
  role_id bigint not null,
  first_name varchar(255) not null,
  last_name varchar(255) not null,
  adr_1 varchar(255),
  adr_2 varchar(255),
  adr_3 varchar(255),
  city varchar(255),
  state varchar(255),
  country varchar(255),
  zip_code varchar(20),
  work_phone varchar(30),
  personal_phone varchar(30),
  email_address varchar(255),
  active int not null,
  system_user int not null
);
insert into users select null, 'root',
                    'aE9lMVVvTkp5VkJjNWRMKzFSV3lQSWJMdGVPN092dG1KMmwvdlA0Mkc4UUtwVmgvaGRnTjVIV2JGdDlKcjBFcw==',
                    1, 'Super', 'User', null, null, null, null, null, null, null, null, null,
                    'tmays@edgecommunications.com', 1, 1;
create index ix_users_1 on users(user_name);
create index ix_users_2 on users(active);

drop table if exists modules;
create table modules
(
  module_id bigint not null primary key auto_increment,
  module varchar(255) not null
);
insert into modules select null, 'Administration';
insert into modules select null, 'Reports';
insert into modules select null, 'Tools';
create index ix_modules_1 on users(module);

drop table if exists submodules;
create table submodules
(
  submodule_id bigint not null primary key auto_increment,
  module_id bigint not null,
  submodule varchar(255) not null,
  description varchar(255) not null,
  tag varchar(255) not null,
  url mediumtext,
  help_url mediumtext,
  css_class varchar(255),
  single_instance int
);
create index ix_submodules_1 on submodules (module_id);
create index ix_submodules_2 on submodules (submodule);
insert into submodules select null, 1, 'Roles', 'Allows a user to manage system roles', 'manageroles', 'administration/roles', 'core/help/show_help/administration/roles', 'roles_window', 1;
insert into submodules select null, 1, 'Users', 'Allows a user to manage users', 'manageusers', 'administration/users','core/help/show_help/administration/users', 'users_window', 1;
insert into submodules select null, 2, 'Role Users', 'Allows a user to view user profiles associated with a roles', 'roleuserss', 'reports/role_users', 'core/help/show_help/reports/role_users', 'role_users_window', 1;
insert into submodules select null, 1, 'Permissions', 'Allows a user to manage permissions', 'managepermissions', 'administration/permissions', 'core/help/show_help/administration/permissions','permissions_window', 1;
insert into submodules select null, 1, 'Change Password', 'Allows a user to manage passwords', 'changepassword', 'administration/change_password', 'core/help/show_help/administration/change_password', 'change_password_window', 1;
insert into submodules select null, 3, 'Audit Log', 'Allows a user to view Audit Logs', 'auditlog', 'tools/audit', 'core/help/show_help/tools/audit', 'audit_log_window', 0;
insert into submodules select null, 1, 'Can Add Roles', 'Allows a user to add a new role', 'canaddrole', null, null, null, null;
insert into submodules select null, 1, 'Can Edit Roles', 'Allows a user to edit a role', 'caneditrole', null, null, null, null;
insert into submodules select null, 1, 'Can Delete Roles', 'Allows a user to delete a role', 'candeleterole', null, null, null, null;
insert into submodules select null, 1, 'Can Add Users', 'Allows a user to add new user profiles', 'canadduser', null, null, null, null;
insert into submodules select null, 1, 'Can Edit Users', 'Allows a user to edit other user profiles', 'canedituser', null, null, null, null;
insert into submodules select null, 1, 'Can Delete Users', 'Allows a user to delete a user profile', 'candeleteuser', null, null, null, null;
insert into submodules select null, 1, 'Can Change User Passwords', 'Allows a user to change other user passwords', 'canchangeuserpasswords', null, null, null, null;

drop table if exists permissions;
create table permissions
(
  role_id bigint not null,
  submodule_id bigint not null,
  primary key (role_id, submodule_id)
);
insert into permissions select 1, 1;
insert into permissions select 1, 2;
insert into permissions select 1, 3;
insert into permissions select 1, 4;
insert into permissions select 1, 5;
insert into permissions select 1, 6;
insert into permissions select 1, 7;
insert into permissions select 1, 8;
insert into permissions select 1, 9;
insert into permissions select 1, 10;
insert into permissions select 1, 11;
insert into permissions select 1, 12;
insert into permissions select 1, 13;

drop table if exists favorites;
create table favorites
(
  favorite_id bigint not null primary key auto_increment,
  user_id bigint not null,
  url mediumtext not null,
  title mediumtext not null,
  help_url mediumtext not null,
  css_class varchar(255) not null,
  single_instance int not null
);
create index ix_favorites_1 on favorites (user_id);

drop table if exists desktops;
create table desktops
(
  desktop_id bigint not null primary key auto_increment,
  user_id bigint not null,
  url mediumtext not null,
  title mediumtext not null,
  help_url mediumtext not null,
  css_class varchar(255) not null,
  single_instance int not null,
  original_top int not null,
  original_left int not null,
  original_width int not null,
  original_height int not null,
  maximized tinyint not null,
  actual_top int not null,
  actual_left int not null,
  actual_width int not null,
  actual_height int not null,
  z_index varchar(20) not null,
  minimized tinyint not null
);
create index ix_desktops_1 on desktops (user_id);

create table audit_log
(
  audit_log_id bigint not null primary key auto_increment,
  user_id bigint not null,
  module varchar(255) not null,
  trx_date bigint not null
);
create index ix_audit_log_1 on audit_log (user_id);
create index ix_audit_log_2 on audit_log (module);
create index ix_audit_log_3 on audit_log (trx_date);
create index ix_audit_log_4 on audit_log (user_id, module, trx_date);

create table audit_log_data
(
  audit_log_id bigint not null,
  old mediumtext,
  new mediumtext
);
create index ix_audit_log_data_1 on audit_log_data (audit_log_id);

drop user 'mdissk'@'localhost';
create user 'mdissk'@'localhost' identified by 'mdissk';
grant all privileges on mdissk.* to 'mdissk'@'localhost';
flush privileges;