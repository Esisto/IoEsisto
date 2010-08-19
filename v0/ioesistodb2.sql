CREATE TABLE IF NOT EXISTS `Category` (
  `cat_name` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Contest` (
  `ct_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ct_title` varchar(50) DEFAULT NULL,
  `ct_description` varchar(255) DEFAULT NULL,
  `ct_typeofsubscriber` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `ct_rules` text,
  `ct_prizes` varchar(100) DEFAULT NULL,
  `ct_start` datetime NULL DEFAULT NULL,
  `ct_end` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`ct_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Nation` (
  `nat_name` varchar(100) NOT NULL,
  PRIMARY KEY (`nat_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Region` (
  `rg_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `rg_name` varchar(100) NOT NULL,
  `rg_nation` varchar(100) NOT NULL,
  PRIMARY KEY (`rg_ID`),
  KEY `fk_region_nation1` (`rg_nation`),
  FOREIGN KEY (`rg_nation`) REFERENCES `Nation` (`nat_name`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `Place` (
  `pl_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `pl_name` varchar(100) NOT NULL,
  `pl_region` bigint(20) NOT NULL,
  PRIMARY KEY (`pl_ID`),
  KEY `fk_place_region1` (`pl_region`),
  FOREIGN KEY (`pl_region`) REFERENCES `Region` (`rg_ID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `Role` (
  `rl_name` varchar(50) NOT NULL,
  PRIMARY KEY (`rl_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `User` (
  `us_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `us_nickname` varchar(100) NOT NULL,
  `us_password` varchar(128) NOT NULL,
  `us_name` varchar(100) NOT NULL,
  `us_surname` varchar(100) NOT NULL,
  `us_birthday` date NULL,
  `us_email` varchar(100) NOT NULL,
  `us_gender` enum('m','f') DEFAULT NULL,
  `us_avatar` varchar(255) DEFAULT NULL,
  `us_visible` tinyint(1) DEFAULT 0,
  `us_verificated` tinyint(1) DEFAULT 0,
  `us_hobbies` varchar(200) NULL,
  `us_job` varchar(100) NULL,
  `us_birthplace` bigint(20) NULL,
  `us_role` varchar(50) NOT NULL,
  `us_livingplace` bigint(20) NULL,
  PRIMARY KEY (`us_ID`),
  UNIQUE KEY `nickname_UNIQUE` (`us_nickname`),
  UNIQUE KEY `email_UNIQUE` (`us_email`),
  KEY `fk_user_place1` (`us_birthplace`),
  KEY `fk_user_place2` (`us_livingplace`),
  KEY `fk_user_roles1` (`us_role`),
  FOREIGN KEY (`us_birthplace`) REFERENCES `Place` (`pl_ID`) ON DELETE NO ACTION,
  FOREIGN KEY (`us_role`) REFERENCES `Role` (`rl_name`) ON DELETE NO ACTION,
  FOREIGN KEY (`us_livingplace`) REFERENCES `Place` (`pl_ID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `Post` (
  `ps_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ps_type` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `ps_title` varchar(100) NOT NULL,
  `ps_subtitle` varchar(100) NULL,
  `ps_headline` varchar(100) NULL,
  `ps_creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ps_modificationDate` datetime NULL,
  `ps_content` text NOT NULL,
  `ps_visible` tinyint(1) DEFAULT 0,
  `ps_author` bigint(20) NOT NULL,
  `ps_place` bigint(20) NULL,
  PRIMARY KEY (`ps_ID`),
  KEY `fk_post_user1` (`ps_author`),
  KEY `fk_post_place1` (`ps_place`),
  FOREIGN KEY (`ps_author`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`ps_place`) REFERENCES `Place` (`pl_ID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `Comment` (
  `cm_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `cm_creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cm_comment` text NOT NULL,
  `cm_author` bigint(20) NOT NULL,
  `cm_post` bigint(20) NOT NULL,
  PRIMARY KEY (`cm_ID`),
  KEY `fk_comment_user1` (`cm_author`),
  KEY `fk_comment_post1` (`cm_post`),
  FOREIGN KEY (`cm_author`) REFERENCES `User` (`us_ID`) ON DELETE NO ACTION,
  FOREIGN KEY (`cm_post`) REFERENCES `Post` (`ps_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Contact` (
  `ct_contact` varchar(100) NOT NULL,
  `ct_user` bigint(20) NOT NULL,
  `ct_type` varchar(50) NOT NULL,
  PRIMARY KEY (`ct_contact`,`ct_user`),
  KEY `fk_contact_user` (`ct_user`),
  FOREIGN KEY (`ct_user`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `ContestSubscriber` (
  `cs_contest` bigint(20) NOT NULL,
  `cs_post` bigint(20) NOT NULL,
  `cs_haswon` tinyint(1) NOT NULL,
  PRIMARY KEY (`cs_contest`,`cs_post`),
  KEY `fk_contest_has_post_contest1` (`cs_contest`),
  KEY `fk_contest_has_post_post1` (`cs_post`),
  FOREIGN KEY (`cs_contest`) REFERENCES `Contest` (`ct_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`cs_post`) REFERENCES `Post` (`ps_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Feedback` (
  `fb_feedbacker` bigint(20) NOT NULL,
  `fb_feedbacked` bigint(20) NOT NULL,
  `fb_feedback` int(11) DEFAULT NULL,
  `fb_feedbackDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fb_feedbacker`,`fb_feedbacked`),
  KEY `fk_user_has_user_user3` (`fb_feedbacker`),
  KEY `fk_user_has_user_user4` (`fb_feedbacked`),
  FOREIGN KEY (`fb_feedbacker`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`fb_feedbacked`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Follow` (
  `fl_user` bigint(20) NOT NULL,
  `fl_follower` bigint(20) NOT NULL,
  `fl_subscriptionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fl_user`,`fl_follower`),
  KEY `fk_user_has_user_user1` (`fl_user`),
  KEY `fk_user_has_user_user2` (`fl_follower`),
  FOREIGN KEY (`fl_user`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`fl_follower`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Log` (
  `log_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_action` varchar(255) NOT NULL,
  `log_user` bigint(20) NOT NULL,
  `log_object` blob NOT NULL,
  PRIMARY KEY (`log_ID`),
  KEY `fk_log_user1` (`log_user`),
  FOREIGN KEY (`log_user`) REFERENCES `User` (`us_ID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Mail` (
  `ml_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ml_date` timestamp NULL DEFAULT NULL,
  `ml_object` varchar(50) DEFAULT NULL,
  `ml_text` text,
  `ml_from` bigint(20) NOT NULL,
  `ml_to` varchar(255) NOT NULL,
  `ml_repliesTo` bigint(20) NULL,
  `ml_creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ml_ID`),
  KEY `fk_mail_user1` (`ml_from`),
  KEY `fk_mail_mail1` (`ml_repliesto`),
  FOREIGN KEY (`ml_from`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`ml_repliesto`) REFERENCES `Mail` (`ml_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MailDirectory` (
  `md_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `md_name` varchar(50) NOT NULL,
  `md_owner` bigint(20) NOT NULL,
  PRIMARY KEY (`md_ID`),
  KEY `fk_mailDirectory_user1` (`md_owner`),
  FOREIGN KEY (`md_owner`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MailOfDirectory` (
  `md_read` tinyint(1) DEFAULT 0,
  `md_dir` bigint(20) NOT NULL,
  `md_mail` bigint(20) NOT NULL,
  PRIMARY KEY (`md_dir`,`md_mail`),
  KEY `fk_MailOfDirectory_MailDirectory1` (`md_dir`),
  KEY `fk_MailOfDirectory_mail1` (`md_mail`),
  FOREIGN KEY (`md_dir`) REFERENCES `MailDirectory` (`md_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`md_mail`) REFERENCES `Mail` (`ml_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Report` (
  `rp_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `rp_post` bigint(20) NOT NULL,
  `rp_user` bigint(20) NOT NULL,
  `rp_report` text NOT NULL,
  PRIMARY KEY (`rp_ID`),
  KEY `fk_post_has_user_post1` (`rp_post`),
  KEY `fk_post_has_user_user1` (`rp_user`),
  FOREIGN KEY (`rp_post`) REFERENCES `Post` (`ps_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`rp_user`) REFERENCES `User` (`us_ID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `Resource` (
  `rs_ID` bigint(20) NOT NULL,
  `rs_type` enum('video','photo') NOT NULL,
  `rs_path` varchar(255) NOT NULL,
  `rs_owner` bigint(20) NOT NULL,
  PRIMARY KEY (`rs_ID`),
  KEY `fk_resource_user1` (`rs_owner`),
  FOREIGN KEY (`rs_owner`) REFERENCES `User` (`us_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `SubCategory` (
  `sc_parent` varchar(50) NOT NULL,
  `sc_category` varchar(50) NOT NULL,
  PRIMARY KEY (`sc_parent`,`sc_category`),
  KEY `fk_category_has_category_category1` (`sc_parent`),
  KEY `fk_category_has_category_category2` (`sc_category`),
  FOREIGN KEY (`sc_parent`) REFERENCES `Category` (`cat_name`) ON DELETE CASCADE,
  FOREIGN KEY (`sc_category`) REFERENCES `Category` (`cat_name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Tag` (
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Vote` (
  `vt_creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vt_vote` int(10) unsigned NOT NULL,
  `vt_author` bigint(20) NOT NULL,
  `vt_post` bigint(20) NOT NULL,
  KEY `fk_vote_user1` (`vt_author`),
  KEY `fk_vote_post1` (`vt_post`),
  FOREIGN KEY (`vt_author`) REFERENCES `User` (`us_ID`) ON DELETE NO ACTION,
  FOREIGN KEY (`vt_post`) REFERENCES `Post` (`ps_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin DEFAULT CHARSET=utf8 COLLATE=utf8_bin;