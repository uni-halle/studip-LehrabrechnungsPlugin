CREATE TABLE IF NOT EXISTS `mlu_lvvo_plugin` (
  `seminar_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `sws_user` float NOT NULL,
  `last_changed_user_id` varchar(32) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`seminar_id`,`user_id`)
) ENGINE=MyISAM;
