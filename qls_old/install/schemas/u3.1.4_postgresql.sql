ALTER TABLE `{database_prefix}users` ADD `last_action` int4 DEFAULT '0' NOT NULL;

CREATE INDEX `users_idx7` ON `{database_prefix}users` (`last_action`);