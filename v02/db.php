<?php
require_once("strings/strings.php");
defineCategoryColumns();
defineCommentColumns();
defineContactColumns();
defineContactTypeColumns();
defineContestColumns();
defineContestSubscriberColumns();
defineFeedbackColumns();
defineFollowColumns();
defineLogColumns();
defineMailColumns();
defineMailDirColumns();
defineMailInDirColumns();
definePostColumns();
defineReportColumns();
defineResourceColumns();
defineRoleColumns();
defineSubCategoryColumns();
defineTagColumns();
defineUserColumns();
defineVoteColumns();
define_tables();

$s = "CREATE TABLE IF NOT EXISTS `" . TABLE_CATEGORY . "` (
  `" . CATEGORY_NAME . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . CATEGORY_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_CONTEST . "` (
  `" . CONTEST_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . CONTEST_TITLE . "` varchar(50) DEFAULT NULL,
  `" . CONTEST_DESCRIPTION . "` text DEFAULT NULL,
  `" . CONTEST_TYPE_OF_SUBSCRIBER . "` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `" . CONTEST_RULES . "` text,
  `" . CONTEST_PRIZES . "` text DEFAULT NULL,
  `" . CONTEST_START . "` datetime NULL DEFAULT NULL,
  `" . CONTEST_END . "` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`" . CONTEST_ID . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_ROLE . "` (
  `" . ROLE_NAME . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . ROLE_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_USER . "` (
  `" . USER_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . USER_NICKNAME . "` varchar(100) NULL,
  `" . USER_E_MAIL . "` varchar(100) NOT NULL,
  `" . USER_PASSWORD . "` varchar(128) NOT NULL,
  `" . USER_NAME . "` varchar(100) NULL,
  `" . USER_SURNAME . "` varchar(100) NULL,
  `" . USER_GENDER . "` enum('m','f') DEFAULT NULL,
  `" . USER_BIRTHDAY . "` date NULL,
  `" . USER_BIRTHPLACE . "` varchar(255) NULL,
  `" . USER_LIVINGPLACE . "` varchar(255) NULL,
  `" . USER_AVATAR . "` varchar(255) DEFAULT NULL,
  `" . USER_HOBBIES . "` varchar(200) NULL,
  `" . USER_JOB . "` varchar(100) NULL,
  `" . USER_ROLE . "` varchar(50) NULL DEFAULT 'user',
  `" . USER_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . USER_VISIBLE . "` tinyint(1) DEFAULT 0,
  `" . USER_VERIFIED . "` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`" . USER_ID . "`),
  UNIQUE KEY `" . USER_NICKNAME_UKEY . "` (`" . USER_NICKNAME . "`),
  UNIQUE KEY `" . USER_E_MAIL_UKEY . "` (`" . USER_E_MAIL . "`),
  KEY `" . USER_ROLE_FKEY . "` (`" . USER_ROLE . "`),
  FOREIGN KEY (`" . USER_ROLE . "`) REFERENCES `" . TABLE_ROLE . "` (`" . ROLE_NAME . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . TABLE_POST . "` (
  `" . POST_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . POST_PERMALINK . "` text NOT NULL,
  `" . POST_TYPE . "` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `" . POST_TITLE . "` varchar(100) NOT NULL,
  `" . POST_SUBTITLE . "` varchar(100) NULL,
  `" . POST_HEADLINE . "` varchar(100) NULL,
  `" . POST_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . POST_MODIFICATION_DATE . "` datetime NULL,
  `" . POST_CONTENT . "` text NOT NULL,
  `" . POST_TAGS . "` text NULL,
  `" . POST_CATEGORIES . "` text NULL,
  `" . POST_VISIBLE . "` tinyint(1) DEFAULT 0,
  `" . POST_AUTHOR . "` bigint(20) NULL,
  `" . POST_PLACE . "` text NULL,
  PRIMARY KEY (`" . POST_ID . "`),
  KEY `" . POST_USER_FKEY . "` (`" . POST_AUTHOR . "`),
  FOREIGN KEY (`" . POST_AUTHOR . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . TABLE_COMMENT . "` (
  `" . COMMENT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . COMMENT_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . COMMENT_COMMENT . "` text NOT NULL,
  `" . COMMENT_AUTHOR . "` bigint(20) NULL,
  `" . COMMENT_POST . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . COMMENT_ID . "`),
  KEY `" . COMMENT_USER_FKEY . "` (`" . COMMENT_AUTHOR . "`),
  KEY `" . COMMENT_POST_FKEY . "` (`" . COMMENT_POST . "`),
  FOREIGN KEY (`" . COMMENT_AUTHOR . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL,
  FOREIGN KEY (`" . COMMENT_POST . "`) REFERENCES `" . TABLE_POST . "` (`" . POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_CONTACT_TYPE . "` (
  `" . CONTACT_TYPE_TYPE . "` enum('phone', 'address', 'email', 'website', 'IM') NOT NULL,
  `" . CONTACT_TYPE_NAME . "` varchar(20) NOT NULL,
  PRIMARY KEY (`" . CONTACT_TYPE_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_CONTACT . "` (
  `" . CONTACT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . CONTACT_CONTACT . "` varchar(100) NOT NULL,
  `" . CONTACT_USER . "` bigint(20) NOT NULL,
  `" . CONTACT_NAME . "` varchar(20) NULL DEFAULT 'other',
  PRIMARY KEY (`" . CONTACT_ID . "`),
  KEY `" . CONTACT_CONTACT_TYPE_FKEY . "` (`" . CONTACT_NAME . "`),
  KEY `" . CONTACT_USER_FKEY . "` (`" . CONTACT_USER . "`),
  FOREIGN KEY (`" . CONTACT_USER . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . CONTACT_NAME . "`) REFERENCES `" . TABLE_CONTACT_TYPE . "` (`" . CONTACT_TYPE_NAME . "`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_CONTEST_SUBSCRIBER . "` (
  `" . CONTEST_SUBSCRIBER_CONTEST . "` bigint(20) NOT NULL,
  `" . CONTEST_SUBSCRIBER_POST . "` bigint(20) NOT NULL,
  `" . CONTEST_SUBSCRIBER_PLACEMENT . "` tinyint(10) NOT NULL,
  PRIMARY KEY (`" . CONTEST_SUBSCRIBER_CONTEST . "`,`" . CONTEST_SUBSCRIBER_POST . "`),
  KEY `" . CONTEST_SUBSCRIBER_CONTEST_FKEY . "` (`" . CONTEST_SUBSCRIBER_CONTEST . "`),
  KEY `" . CONTEST_SUBSCRIBER_POST_FKEY . "` (`" . CONTEST_SUBSCRIBER_POST . "`),
  UNIQUE KEY `" . CONTEST_SUBSCRIBER_UKEY . "` (`" . CONTEST_SUBSCRIBER_CONTEST . "`, `" . CONTEST_SUBSCRIBER_PLACEMENT . "`),
  FOREIGN KEY (`" . CONTEST_SUBSCRIBER_CONTEST . "`) REFERENCES `" . TABLE_CONTEST . "` (`" . CONTEST_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . CONTEST_SUBSCRIBER_POST . "`) REFERENCES `" . TABLE_POST . "` (`" . POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_FEEDBACK . "` (
  `" . FEEDBACK_CREATOR . "` bigint(20) NOT NULL,
  `" . FEEDBACK_SUBJECT . "` bigint(20) NOT NULL,
  `" . FEEDBACK_VALUE . "` int(11) DEFAULT NULL,
  `" . FEEDBACK_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . FEEDBACK_CREATOR . "`,`" . FEEDBACK_SUBJECT . "`),
  KEY `" . FEEDBACK_USER_FKEY1 . "` (`" . FEEDBACK_CREATOR . "`),
  KEY `" . FEEDBACK_USER_FKEY2 . "` (`" . FEEDBACK_SUBJECT . "`),
  FOREIGN KEY (`" . FEEDBACK_CREATOR . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . FEEDBACK_SUBJECT . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_FOLLOW . "` (
  `" . FOLLOW_SUBJECT . "` bigint(20) NOT NULL,
  `" . FOLLOW_FOLLOWER . "` bigint(20) NOT NULL,
  `" . FOLLOW_SUBSCRIPTION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . FOLLOW_SUBJECT . "`,`" . FOLLOW_FOLLOWER . "`),
  KEY `" . FOLLOW_USER_FKEY1 . "` (`" . FOLLOW_SUBJECT . "`),
  KEY `" . FOLLOW_USER_FKEY2 . "` (`" . FOLLOW_FOLLOWER . "`),
  FOREIGN KEY (`" . FOLLOW_SUBJECT . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . FOLLOW_FOLLOWER . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_LOG . "` (
  `" . LOG_ID . "` varchar(40) NOT NULL,
  `" . LOG_TIMESTAMP . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . LOG_ACTION . "` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `" . LOG_TABLE . "` varchar(20) NOT NULL,
  `" . LOG_SUBJECT . "` bigint(20) NULL,
  `" . LOG_OBJECT . "` varchar(40) NOT NULL,
  PRIMARY KEY (`" . LOG_ID . "`),
  KEY `" . LOG_USER_FKEY . "` (`" . LOG_SUBJECT . "`),
  FOREIGN KEY (`" . LOG_SUBJECT . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `AccessLog` (
  `alog_type` varchar(10) NOT NULL,
  `alog_id` bigint(20) NOT NULL,
  `alog_count` bigint(20) NOT NULL DEFAULT 1,
  PRIMARY KEY (`alog_type`, `alog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_MAIL . "` (
  `" . MAIL_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . MAIL_SUBJECT . "` varchar(50) DEFAULT NULL,
  `" . MAIL_TEXT . "` text,
  `" . MAIL_FROM . "` bigint(20) NOT NULL,
  `" . MAIL_TO . "` varchar(255) NOT NULL,
  `" . MAIL_REPLIES_TO . "` bigint(20) NULL,
  `" . MAIL_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . MAIL_ID . "`),
  KEY `" . MAIL_USER_FKEY . "` (`" . MAIL_FROM . "`),
  KEY `" . MAIL_MAIL_FKEY . "` (`" . MAIL_REPLIES_TO . "`),
  FOREIGN KEY (`" . MAIL_FROM . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . MAIL_REPLIES_TO . "`) REFERENCES `" . TABLE_MAIL . "` (`" . MAIL_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . TABLE_MAIL_DIRECTORY . "` (
  `" . MAIL_DIRECTORY_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . MAIL_DIRECTORY_NAME . "` varchar(50) NOT NULL,
  `" . MAIL_DIRECTORY_OWNER . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . MAIL_DIRECTORY_ID . "`),
  KEY `" . MAIL_DIRECTORY_USER_FKEY . "` (`" . MAIL_DIRECTORY_OWNER . "`),
  FOREIGN KEY (`" . MAIL_DIRECTORY_OWNER . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . TABLE_MAIL_IN_DIRECTORY . "` (
  `" . MAIL_IN_DIRECTORY_READ . "` tinyint(1) DEFAULT 0,
  `" . MAIL_IN_DIRECTORY_DIRECTORY . "` bigint(20) NOT NULL,
  `" . MAIL_IN_DIRECTORY_MAIL . "` bigint(20) NOT NULL,
  PRIMARY KEY (`mod_dir`,`mod_mail`),
  KEY `" . MAIL_IN_DIRECTORY_MAIL_DIRECTORY_FKEY . "` (`" . MAIL_IN_DIRECTORY_DIRECTORY . "`),
  KEY `" . MAIL_IN_DIRECTORY_MAIL_FKEY . "` (`" . MAIL_IN_DIRECTORY_MAIL . "`),
  FOREIGN KEY (`" . MAIL_IN_DIRECTORY_DIRECTORY . "`) REFERENCES `" . TABLE_MAIL_DIRECTORY . "` (`" . MAIL_DIRECTORY_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . MAIL_IN_DIRECTORY_MAIL . "`) REFERENCES `" . TABLE_MAIL . "` (`" . MAIL_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_REPORT . "` (
  `" . REPORT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . REPORT_POST . "` bigint(20) NOT NULL,
  `" . REPORT_USER . "` bigint(20) NULL,
  `rp_report` text NOT NULL,
  PRIMARY KEY (`" . REPORT_ID . "`),
  UNIQUE KEY `" . REPORT_USER_POST_UKEY . "` (`" . REPORT_POST . "`, `" . REPORT_USER . "`),
  KEY `" . REPORT_POST_FKEY . "` (`" . REPORT_POST . "`),
  KEY `" . REPORT_USER_FKEY . "` (`" . REPORT_USER . "`),
  FOREIGN KEY (`" . REPORT_POST . "`) REFERENCES `" . TABLE_POST . "` (`" . POST_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . REPORT_USER . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . TABLE_RESOURCE . "` (
  `" . RESOURCE_ID . "` bigint(20) NOT NULL,
  `" . RESOURCE_TYPE . "` enum('video','photo') NOT NULL,
  `" . RESOURCE_PATH . "` varchar(255) NOT NULL,
  `" . RESOURCE_OWNER . "` bigint(20) NULL,
  PRIMARY KEY (`" . RESOURCE_ID . "`),
  KEY `" . RESOURCE_USER_FKEY . "` (`" . RESOURCE_OWNER . "`),
  FOREIGN KEY (`" . RESOURCE_OWNER . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_SUB_CATEGORY . "` (
  `" . SUB_CATEGORY_PARENT . "` varchar(50) NOT NULL,
  `" . SUB_CATEGORY_CATEGORY . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . SUB_CATEGORY_PARENT . "`,`" . SUB_CATEGORY_CATEGORY . "`),
  KEY `" . SUB_CATEGORY_CATEGORY_FKEY1 . "` (`" . SUB_CATEGORY_PARENT . "`),
  KEY `" . SUB_CATEGORY_CATEGORY_FKEY2 . "` (`" . SUB_CATEGORY_CATEGORY . "`),
  FOREIGN KEY (`" . SUB_CATEGORY_PARENT . "`) REFERENCES `" . TABLE_CATEGORY . "` (`" . CATEGORY_NAME . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . SUB_CATEGORY_CATEGORY . "`) REFERENCES `" . TABLE_CATEGORY . "` (`" . CATEGORY_NAME . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_TAG . "` (
  `" . TAG_NAME . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . TAG_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . TABLE_VOTE . "` (
  `" . VOTE_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . VOTE_VOTE . "` int(10) unsigned NOT NULL,
  `" . VOTE_AUTHOR . "` bigint(20) NULL,
  `" . VOTE_POST . "` bigint(20) NOT NULL,
  KEY `" . VOTE_USER_FKEY . "` (`" . VOTE_AUTHOR . "`),
  KEY `" . VOTE_POST_FKEY . "` (`" . VOTE_POST . "`),
  FOREIGN KEY (`" . VOTE_AUTHOR . "`) REFERENCES `" . TABLE_USER . "` (`" . USER_ID . "`) ON DELETE SET NULL,
  FOREIGN KEY (`" . VOTE_POST . "`) REFERENCES `" . TABLE_POST . "` (`" . POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

//avatar non può essere in dipendenza da una risorsa perché crea un ciclio di dipendenze…
//una soluzione è creare una tabella Avatar… da discutere.
//ALTER TABLE `" . TABLE_USER . "` ADD CONSTRAINT
//  KEY `" . USER_RESOURCE_FKEY . "` (`" . USER_AVATAR . "`),
//  FOREIGN KEY (`" . USER_AVATAR . "`) REFERENCES `" . TABLE_RESOURCE . "` (`" . RESOURCE_ID . "`) ON DELETE NO ACTION";
?>