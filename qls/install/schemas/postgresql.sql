CREATE SEQUENCE `{database_prefix}config_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}users_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}pages_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}groups_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}masks_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}invitations_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;
CREATE SEQUENCE `{database_prefix}password_requests_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache1;

CREATE TABLE `{database_prefix}config`(
	`id` int4 DEFAULT nextval('{database_prefix}config_id_seq'::text) NOT NULL,
	`name` varchar(255) DEFAULT '' NOT NULL,
	`value` VARCHAR(255) DEFAULT '' NOT NULL,
	CONSTRAINT `{database_prefix}config_pkey` PRIMARY KEY(`id`)
);

CREATE INDEX `config_name_idx` ON `{database_prefix}config` (`name`);

CREATE TABLE `{database_prefix}sessions`(
	`id` varchar(40) DEFAULT '' NOT NULL,
	`value` varchar(40) DEFAULT '' NOT NULL,
	`time` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}sessions_pkey` PRIMARY KEY(`id`)
);

CREATE INDEX `sessions_idx` ON `{database_prefix}sessions` (`value`,`time`);

CREATE TABLE `{database_prefix}users`(
	`id` int4 DEFAULT nextval('{database_prefix}users_id_seq'::text) NOT NULL,
	`username` varchar(255) DEFAULT '' NOT NULL,
	`password` varchar(40) DEFAULT '' NOT NULL,
	`code` varchar(40) DEFAULT '' NOT NULL,
	`active` char(3) DEFAULT '' NOT NULL,
	`last_login` int4 DEFAULT '0' NOT NULL,
	`last_session` varchar(40) DEFAULT '' NOT NULL,
	`blocked` char(3) DEFAULT 'no' NOT NULL,
	`tries` int2 DEFAULT '0' NOT NULL,
	`last_try` int4 DEFAULT '0' NOT NULL,
	`email` varchar(255) DEFAULT '' NOT NULL,
	`mask_id` int4 DEFAULT '0' NOT NULL,
	`group_id` int4 DEFAULT '2' NOT NULL,
	`activation_time` int4 DEFAULT '0' NOT NULL,
	`last_action` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}users_pkey` PRIMARY KEY(`id`)
);

CREATE INDEX `users_idx` ON `{database_prefix}users` (`username`);
CREATE INDEX `users_idx2` ON `{database_prefix}users` (`code`);
CREATE INDEX `users_idx3` ON `{database_prefix}users` (`last_login`);
CREATE INDEX `users_idx4` ON `{database_prefix}users` (`last_session`);
CREATE INDEX `users_idx5` ON `{database_prefix}users` (`last_try`);
CREATE INDEX `users_idx6` ON `{database_prefix}users` (`activation_time`);
CREATE INDEX `users_idx7` ON `{database_prefix}users` (`last_action`);

CREATE TABLE `{database_prefix}security_image`(
	`random_id` varchar(40) DEFAULT '' NOT NULL,
	`real_text` varchar(10) DEFAULT '' NOT NULL,
	`date` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}security_image_pkey` PRIMARY KEY(`random_id`)
);

CREATE INDEX `{security_image_idx` ON `{database_prefix}security_image` (`real_text`,`date`);

CREATE TABLE `{database_prefix}pages`(
	`id` int4 DEFAULT nextval('{database_prefix}pages_id_seq'::text) NOT NULL,
	`name` varchar(255) DEFAULT '' NOT NULL,
	`hits` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}pages_pkey` PRIMARY KEY(`id`),
);

CREATE INDEX `pages_idx` ON `{database_prefix}pages` (`name`);

CREATE TABLE `{database_prefix}groups`(
	`id` int4 DEFAULT nextval('{database_prefix}groups_id_seq'::text) NOT NULL,
	`name` varchar(255) DEFAULT '' NOT NULL,
	`mask_id` int4 DEFAULT '0' NOT NULL,
	`is_public` int2 DEFAULT '0' NOT NULL,
	`leader` int4 DEFAULT '0' NOT NULL,
	`expiration_date` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}groups_pkey` PRIMARY KEY(`id`),
);

CREATE INDEX `groups_idx` ON `{database_prefix}groups` (`name`);
CREATE INDEX `groups_idx2` ON `{database_prefix}groups` (`is_public`);
CREATE INDEX `groups_idx3` ON `{database_prefix}groups` (`expiration_date`);

CREATE TABLE `{database_prefix}masks`(
	`id` int4 DEFAULT nextval('{database_prefix}masks_id_seq'::text) NOT NULL,
	`name` varchar(255) DEFAULT '' NOT NULL,
	`auth_admin` int2 DEFAULT '0' NOT NULL,
	`auth_admin_phpinfo` int2 DEFAULT '0' NOT NULL,
	`auth_admin_configuration` int2 DEFAULT '0' NOT NULL,
	`auth_admin_add_user` int2 DEFAULT '0' NOT NULL,
	`auth_admin_user_list` int2 DEFAULT '0' NOT NULL,
	`auth_admin_remove_user` int2 DEFAULT '0' NOT NULL,
	`auth_admin_edit_user` int2 DEFAULT '0' NOT NULL,
	`auth_admin_add_page` int2 DEFAULT '0' NOT NULL,
	`auth_admin_page_list` int2 DEFAULT '0' NOT NULL,
	`auth_admin_remove_page` int2 DEFAULT '0' NOT NULL,
	`auth_admin_edit_page` int2 DEFAULT '0' NOT NULL,
	`auth_admin_page_stats` int2 DEFAULT '0' NOT NULL,
	`auth_admin_add_mask` int2 DEFAULT '0' NOT NULL,
	`auth_admin_list_masks` int2 DEFAULT '0' NOT NULL,
	`auth_admin_remove_mask` int2 DEFAULT '0' NOT NULL,
	`auth_admin_edit_mask` int2 DEFAULT '0' NOT NULL,
	`auth_admin_add_group` int2 DEFAULT '0' NOT NULL,
	`auth_admin_list_groups` int2 DEFAULT '0' NOT NULL,
	`auth_admin_remove_group` int2 DEFAULT '0' NOT NULL,
	`auth_admin_edit_group` int2 DEFAULT '0' NOT NULL,
	`auth_admin_activate_account` int2 DEFAULT '0' NOT NULL,
	`auth_admin_send_invite` int2 DEFAULT '0' NOT NULL,
	`auth_356a192b7913b04c54574d18c28d46e6395428ab` int2 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}masks_pkey` PRIMARY KEY(`id`),
);

CREATE INDEX `masks_idx` ON `{database_prefix}masks` (`name`);

CREATE TABLE `{database_prefix}invitations`(
	`id` int4 DEFAULT nextval('{database_prefix}invitations_id_seq'::text) NOT NULL,
	`used` int2 DEFAULT '0' NOT NULL,
	`code` varchar(40) DEFAULT '' NOT NULL,
	CONSTRAINT `{database_prefix}invitations_pkey` PRIMARY KEY(`id`)
);

CREATE INDEX `invitations_idx` ON `{database_prefix}invitations` (`code`);

CREATE TABLE `{database_prefix}password_requests`(
	`id` int4 DEFAULT nextval('{database_prefix}password_requests_id_seq'::text) NOT NULL,
	`user_id` int4 DEFAULT '0' NOT NULL,
	`code` varchar(40) DEFAULT '' NOT NULL,
	`used` int2 DEFAULT '0' NOT NULL,
	`date` int4 DEFAULT '0' NOT NULL,
	CONSTRAINT `{database_prefix}password_requests_pkey` PRIMARY KEY(`id`)
);

CREATE INDEX `password_requests_idx` ON `{database_prefix}password_requests` (`code`);
CREATE INDEX `password_requests_idx2` ON `{database_prefix}password_requests` (`date`);