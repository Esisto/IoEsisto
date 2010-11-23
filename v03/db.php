<?php
require_once("strings/strings.php");

class DB {
	const TABLE_CATEGORY = "Category";
	const TABLE_COMMENT = "Comment";
	const TABLE_CONTACT = "Contact";
	const TABLE_CONTACT_TYPE = "ContactType";
	const TABLE_CONTEST = "Contest";
	const TABLE_CONTEST_SUBSCRIBER = "ContestSubscriber";
	const TABLE_FEEDBACK = "Feedback";
	const TABLE_FOLLOW = "Follow";
	const TABLE_LOG = "Log";
	const TABLE_MAIL = "Message";
	const TABLE_MAIL_DIRECTORY = "Directory";
	const TABLE_MAIL_IN_DIRECTORY = "MessageDirectory";
	const TABLE_POST = "Post";
	const TABLE_REPORT = "Report";
	const TABLE_RESOURCE = "Resource";
	const TABLE_ROLE = "Role"; //TODO creare le costanti delle colonne
	const TABLE_SUB_CATEGORY = "SubCategory";
	const TABLE_SUSPENDED = "Suspended"; //TODO creare le costanti delle colonne
	const TABLE_TAG = "Tag";
	const TABLE_USER = "User";
	const TABLE_VOTE = "Vote";
	
	const CATEGORY_NAME = "cat_name";
	const CATEGORY_CREATION_DATE = "cat_creationDate";
	const CATEGORY_AUTHOR = "cat_author";
	const CATEGORY_ACCESS_COUNT = "cat_access_count"; //TODO implementare la cosa in CategoryManager
	const CONTACT_ID = "ct_ID";
	const CONTACT_CONTACT = "ct_contact";
	const CONTACT_NAME = "ct_name";
	const CONTACT_USER = "ct_user";
	const CONTACT_TYPE_NAME = "ctt_name";
	const CONTACT_TYPE_TYPE = "ctt_type";
	const CONTEST_ID = "cs_ID";
	const CONTEST_DESCRIPTION = "cs_description";
	const CONTEST_TITLE = "cs_title";
	const CONTEST_TYPE_OF_SUBSCRIBER = "cs_typeofsubscriber";
	const CONTEST_RULES = "cs_rules";
	const CONTEST_PRIZES = "cs_prizes";
	const CONTEST_START = "cs_start";
	const CONTEST_END = "cs_end";
	const CONTEST_ACCESS_COUNT = "cs_access_count"; //TODO implementare la cosa in ContestManager
	const CONTEST_SUBSCRIBER_CONTEST = "css_contest";
	const CONTEST_SUBSCRIBER_POST = "css_post";
	const CONTEST_SUBSCRIBER_PLACEMENT = "css_placement";
	const FEEDBACK_CREATOR = "fb_feedbacker";
	const FEEDBACK_SUBJECT = "fb_feedbacked";
	const FEEDBACK_VALUE = "fb_feedback";
	const FEEDBACK_CREATION_DATE = "fb_feedbackDate";
	const FOLLOW_SUBJECT = "fl_user";
	const FOLLOW_FOLLOWER = "fl_follower";
	const FOLLOW_SUBSCRIPTION_DATE = "fl_subscriptionDate";
	const LOG_ID = "log_ID";
	const LOG_TIMESTAMP = "log_timestamp";
	const LOG_ACTION = "log_action";
	const LOG_TABLE = "log_table";
	const LOG_SUBJECT = "log_user";
	const LOG_OBJECT = "log_object";
	const MAIL_ID = "msg_ID";
	const MAIL_SUBJECT = "msg_subject";
	const MAIL_TEXT = "msg_text";
	const MAIL_FROM = "msg_from";
	const MAIL_TO = "msg_to";
	const MAIL_REPLIES_TO = "msg_repliesTo";
	const MAIL_CREATION_DATE = "msg_creationDate";
	const MAIL_DIRECTORY_ID = "md_ID";
	const MAIL_DIRECTORY_NAME = "md_name";
	const MAIL_DIRECTORY_OWNER = "md_owner";
	const MAIL_IN_DIRECTORY_READ = "mod_read";
	const MAIL_IN_DIRECTORY_DIRECTORY = "mod_dir";
	const MAIL_IN_DIRECTORY_MAIL = "mod_mail";
	const POST_ID = "ps_ID";
	const POST_PERMALINK = "ps_permalink";
	const POST_TYPE = "ps_type";
	const POST_TITLE = "ps_title";
	const POST_TAGS = "ps_tags";
	const POST_SUBTITLE = "ps_subtitle";
	const POST_HEADLINE = "ps_headline";
	const POST_CREATION_DATE = "ps_creationDate";
	const POST_MODIFICATION_DATE = "ps_modificationDate";
	const POST_CONTENT = "ps_content";
	const POST_CATEGORIES = "ps_categories";
	const POST_VISIBLE = "ps_visible";
	const POST_AUTHOR = "ps_author";
	const POST_PLACE = "ps_place";
	const POST_ACCESS_COUNT = "ps_access_count"; //TODO implementare le cosa in PostManager
	const REPORT_ID = "rp_ID";
	const REPORT_SUBJECT = "rp_subject";
	const REPORT_SUBJECT_TYPE = "rp_subjectType";
	const REPORT_USER = "rp_user";
	const REPORT_CREATION_DATE = "rp_creationDate";
	const REPORT_TEXT = "rp_report";
	const RESOURCE_ID = "rs_ID";
	const RESOURCE_TYPE = "rs_type";
	const RESOURCE_PATH = "rs_path";
	const RESOURCE_OWNER = "rs_owner";
	const RESOURCE_ACCESS_COUNT = "rs_access_count"; //TODO implementare la cosa in ResourceManager
	const ROLE_NAME = "rl_name";
	const SUB_CATEGORY_PARENT = "sc_parent";
	const SUB_CATEGORY_CATEGORY = "sc_category";
	const SUSPENDED_ID = "sp_ID";
	const SUSPENDED_USER = "sp_user";
	const TAG_NAME = "tag_name";
	const TAG_ACCESS_COUNT = "tag_access_count"; //TODO implementare la cosa in TagManager
	const USER_ID = "us_ID";
	const USER_NICKNAME = "us_nickname";
	const USER_PASSWORD = "us_password";
	const USER_NAME = "us_name";
	const USER_SURNAME = "us_surname";
	const USER_BIRTHDAY = "us_birthday";
	const USER_E_MAIL = "us_email";
	const USER_GENDER = "us_gender";
	const USER_AVATAR = "us_avatar";
	const USER_VISIBLE = "us_visible";
	const USER_VERIFIED = "us_verified";
	const USER_HOBBIES = "us_hobbies";
	const USER_JOB = "us_job";
	const USER_BIRTHPLACE = "us_birthplace";
	const USER_ROLE = "us_role";
	const USER_LIVINGPLACE = "us_livingplace";
	const USER_CREATION_DATE = "us_creationDate";
	const USER_ACCESS_COUNT = "us_access_count"; //TODO implementare la cosa in UserManager
	const VOTE_CREATION_DATE = "vt_creationDate";
	const VOTE_VOTE = "vt_vote";
	const VOTE_AUTHOR = "vt_author";
	const VOTE_POST = "vt_post";
	
	static function getCreateQueries() {
		$s = "CREATE TABLE IF NOT EXISTS `" . self::TABLE_CATEGORY . "` (
  `" . self::CATEGORY_NAME . "` varchar(50) NOT NULL,
  `" . self::CATEGORY_CREATION_DATE . "` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::CATEGORY_AUTHOR . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . self::CATEGORY_NAME . "`)
  FOREIGN KEY (`" . self::CATEGORY_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_CONTEST . "` (
  `" . self::CONTEST_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::CONTEST_TITLE . "` varchar(50) DEFAULT NULL,
  `" . self::CONTEST_DESCRIPTION . "` text DEFAULT NULL,
  `" . self::CONTEST_TYPE_OF_SUBSCRIBER . "` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `" . self::CONTEST_RULES . "` text,
  `" . self::CONTEST_PRIZES . "` text DEFAULT NULL,
  `" . self::CONTEST_START . "` datetime NULL DEFAULT NULL,
  `" . self::CONTEST_END . "` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`" . self::CONTEST_ID . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_ROLE . "` ( 
  `" . self::ROLE_NAME . "` varchar(50) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `create_news` tinyint(1) NOT NULL,
  `edit_news` tinyint(1) NOT NULL,
  `delete_news` tinyint(1) NOT NULL,
  `create_photorep` tinyint(1) NOT NULL,
  `edit_photorep` tinyint(1) NOT NULL,
  `delete_photorep` tinyint(1) NOT NULL,
  `create_videorep` tinyint(1) NOT NULL,
  `edit_videorep` tinyint(1) NOT NULL,
  `delete_videorep` tinyint(1) NOT NULL,
  `change_visibility` tinyint(1) NOT NULL,
  `create_list` tinyint(1) NOT NULL,
  `edit_list` tinyint(1) NOT NULL,
  `delete_list` tinyint(1) NOT NULL,
  `comment` tinyint(1) NOT NULL,
  `delete_comment` tinyint(1) NOT NULL,
  `vote` tinyint(1) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  `stop_follow` tinyint(1) NOT NULL,
  `create_feedback` tinyint(1) NOT NULL,
  `delete_feedback` tinyint(1) NOT NULL,
  `send_message` tinyint(1) NOT NULL,
  `create_directory` tinyint(1) NOT NULL,
  `edit_directory` tinyint(1) NOT NULL,
  `delete_directory` tinyint(1) NOT NULL,
  `mark_as_read` tinyint(1) NOT NULL,
  `move_message` tinyint(1) NOT NULL,
  `empty_recycle_bin` tinyint(1) NOT NULL,
  `create_resource` tinyint(1) NOT NULL,
  `edit_resource` tinyint(1) NOT NULL,
  `delete_resource` tinyint(1) NOT NULL,
  `edit_profile` tinyint(1) NOT NULL,
  `create_contest` tinyint(1) NOT NULL,
  `edit_contest` tinyint(1) NOT NULL,
  `delete_contest` tinyint(1) NOT NULL,
  `subscribe` tinyint(1) NOT NULL,
  `unsubscribe` tinyint(1) NOT NULL,
  `create_user` tinyint(1) NOT NULL,
  `delete_user` tinyint(1) NOT NULL,
  `block_user` tinyint(1) NOT NULL,
  `suspend_user` tinyint(1) NOT NULL,
  `signal` tinyint(1) NOT NULL,
  `create_category` tinyint(1) NOT NULL,
  `edit_category` tinyint(1) NOT NULL,
  `delete_category` tinyint(1) NOT NULL,
  `create_template` tinyint(1) NOT NULL,
  `edit_template` tinyint(1) NOT NULL,
  `delete_template` tinyint(1) NOT NULL,
  `adv_template_manager` tinyint(1) NOT NULL,
  `edit_other_news` tinyint(1) NOT NULL,
  `edit_other_photorep` tinyint(1) NOT NULL,
  `edit_other_videorep` tinyint(1) NOT NULL,
  `edit_other_list` tinyint(1) NOT NULL,
  `edit_other_profile` tinyint(1) NOT NULL,
  `edit_other_resource` tinyint(1) NOT NULL,
  `unsubscribe_other` tinyint(1) NOT NULL,
  `delete_other_feedback` tinyint(1) NOT NULL,
  `hide_other` tinyint(1) NOT NULL,
  `create_other_template` tinyint(1) NOT NULL,
  `edit_other_template` tinyint(1) NOT NULL,
  `delete_other_template` tinyint(1) NOT NULL,
  `request_suspend` tinyint(1) NOT NULL,
  `request_block` tinyint(1) NOT NULL,
  `view_mod_decision` tinyint(1) NOT NULL,
  `view_edit_decision` tinyint(1) NOT NULL,
  `view_history` tinyint(1) NOT NULL,
  `view_block_request` tinyint(1) NOT NULL,
  `view_suspend_request` tinyint(1) NOT NULL,
  PRIMARY KEY (`rl_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_USER . "` (
  `" . self::USER_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::USER_NICKNAME . "` varchar(100) NULL,
  `" . self::USER_E_MAIL . "` varchar(100) NOT NULL,
  `" . self::USER_PASSWORD . "` varchar(128) NOT NULL,
  `" . self::USER_NAME . "` varchar(100) NULL,
  `" . self::USER_SURNAME . "` varchar(100) NULL,
  `" . self::USER_GENDER . "` enum('m','f') DEFAULT NULL,
  `" . self::USER_BIRTHDAY . "` date NULL,
  `" . self::USER_BIRTHPLACE . "` varchar(255) NULL,
  `" . self::USER_LIVINGPLACE . "` varchar(255) NULL,
  `" . self::USER_AVATAR . "` varchar(255) DEFAULT NULL,
  `" . self::USER_HOBBIES . "` varchar(200) NULL,
  `" . self::USER_JOB . "` varchar(100) NULL,
  `" . self::USER_ROLE . "` varchar(50) NULL DEFAULT 'user',
  `" . self::USER_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::USER_VISIBLE . "` tinyint(1) DEFAULT 0,
  `" . self::USER_VERIFIED . "` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`" . self::USER_ID . "`),
  UNIQUE (`" . self::USER_NICKNAME . "`),
  UNIQUE (`" . self::USER_E_MAIL . "`),
  FOREIGN KEY (`" . self::USER_ROLE . "`) REFERENCES `" . self::TABLE_ROLE . "` (`" . self::ROLE_NAME . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_SUSPENDED . "` (
  `" . self::SUSPENDED_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::SUSPENDED_USER . "` bigint(20) NOT NULL,
  `sp_by` bigint(20) NOT NULL,
  `sp_fomerrole` varchar(20) NOT NULL,
  `sp_reason` blob,
  `sp_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sp_end` datetime DEFAULT NULL,
  PRIMARY KEY (`sp_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_POST . "` (
  `" . self::POST_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::POST_PERMALINK . "` text NOT NULL,
  `" . self::POST_TYPE . "` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `" . self::POST_TITLE . "` varchar(100) NOT NULL,
  `" . self::POST_SUBTITLE . "` varchar(100) NULL,
  `" . self::POST_HEADLINE . "` varchar(100) NULL,
  `" . self::POST_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::POST_MODIFICATION_DATE . "` datetime NULL,
  `" . self::POST_CONTENT . "` text NOT NULL,
  `" . self::POST_TAGS . "` text NULL,
  `" . self::POST_CATEGORIES . "` text NULL,
  `" . self::POST_VISIBLE . "` tinyint(1) DEFAULT 0,
  `" . self::POST_AUTHOR . "` bigint(20) NULL,
  `" . self::POST_PLACE . "` text NULL,
  PRIMARY KEY (`" . self::POST_ID . "`),
  FOREIGN KEY (`" . self::POST_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_COMMENT . "` (
  `" . self::COMMENT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::COMMENT_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::COMMENT_COMMENT . "` text NOT NULL,
  `" . self::COMMENT_AUTHOR . "` bigint(20) NULL,
  `" . self::COMMENT_POST . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . self::COMMENT_ID . "`),
  FOREIGN KEY (`" . self::COMMENT_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::COMMENT_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_CONTACT_TYPE . "` (
  `" . self::CONTACT_TYPE_TYPE . "` enum('phone', 'address', 'email', 'website', 'IM') NOT NULL,
  `" . self::CONTACT_TYPE_NAME . "` varchar(20) NOT NULL,
  PRIMARY KEY (`" . self::CONTACT_TYPE_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_CONTACT . "` (
  `" . self::CONTACT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::CONTACT_CONTACT . "` varchar(100) NOT NULL,
  `" . self::CONTACT_USER . "` bigint(20) NOT NULL,
  `" . self::CONTACT_NAME . "` varchar(20) NULL DEFAULT 'other',
  PRIMARY KEY (`" . self::CONTACT_ID . "`),
  FOREIGN KEY (`" . self::CONTACT_USER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::CONTACT_NAME . "`) REFERENCES `" . self::TABLE_CONTACT_TYPE . "` (`" . self::CONTACT_TYPE_NAME . "`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_CONTEST_SUBSCRIBER . "` (
  `" . self::CONTEST_SUBSCRIBER_CONTEST . "` bigint(20) NOT NULL,
  `" . self::CONTEST_SUBSCRIBER_POST . "` bigint(20) NOT NULL,
  `" . self::CONTEST_SUBSCRIBER_PLACEMENT . "` tinyint(10) NULL,
  PRIMARY KEY (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`,`" . self::CONTEST_SUBSCRIBER_POST . "`),
  UNIQUE (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`, `" . self::CONTEST_SUBSCRIBER_PLACEMENT . "`),
  FOREIGN KEY (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`) REFERENCES `" . self::TABLE_CONTEST . "` (`" . self::CONTEST_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::CONTEST_SUBSCRIBER_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_FEEDBACK . "` (
  `" . self::FEEDBACK_CREATOR . "` bigint(20) NOT NULL,
  `" . self::FEEDBACK_SUBJECT . "` bigint(20) NOT NULL,
  `" . self::FEEDBACK_VALUE . "` int(11) DEFAULT NULL,
  `" . self::FEEDBACK_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . self::FEEDBACK_CREATOR . "`,`" . self::FEEDBACK_SUBJECT . "`),
  FOREIGN KEY (`" . self::FEEDBACK_CREATOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::FEEDBACK_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_FOLLOW . "` (
  `" . self::FOLLOW_SUBJECT . "` bigint(20) NOT NULL,
  `" . self::FOLLOW_FOLLOWER . "` bigint(20) NOT NULL,
  `" . self::FOLLOW_SUBSCRIPTION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . self::FOLLOW_SUBJECT . "`,`" . self::FOLLOW_FOLLOWER . "`),
  FOREIGN KEY (`" . self::FOLLOW_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::FOLLOW_FOLLOWER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_LOG . "` (
  `" . self::LOG_ID . "` varchar(40) NOT NULL,
  `" . self::LOG_TIMESTAMP . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::LOG_ACTION . "` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `" . self::LOG_TABLE . "` varchar(20) NOT NULL,
  `" . self::LOG_SUBJECT . "` bigint(20) NULL,
  `" . self::LOG_OBJECT . "` varchar(40) NOT NULL,
  PRIMARY KEY (`" . self::LOG_ID . "`),
  FOREIGN KEY (`" . self::LOG_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_MAIL . "` (
  `" . self::MAIL_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::MAIL_SUBJECT . "` varchar(50) DEFAULT NULL,
  `" . self::MAIL_TEXT . "` text,
  `" . self::MAIL_FROM . "` bigint(20) NOT NULL,
  `" . self::MAIL_TO . "` varchar(255) NOT NULL,
  `" . self::MAIL_REPLIES_TO . "` bigint(20) NULL,
  `" . self::MAIL_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . self::MAIL_ID . "`),
  FOREIGN KEY (`" . self::MAIL_FROM . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::MAIL_REPLIES_TO . "`) REFERENCES `" . self::TABLE_MAIL . "` (`" . self::MAIL_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_MAIL_DIRECTORY . "` (
  `" . self::MAIL_DIRECTORY_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::MAIL_DIRECTORY_NAME . "` varchar(50) NOT NULL,
  `" . self::MAIL_DIRECTORY_OWNER . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . self::MAIL_DIRECTORY_ID . "`),
  FOREIGN KEY (`" . self::MAIL_DIRECTORY_OWNER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_MAIL_IN_DIRECTORY . "` (
  `" . self::MAIL_IN_DIRECTORY_READ . "` tinyint(1) DEFAULT 0,
  `" . self::MAIL_IN_DIRECTORY_DIRECTORY . "` bigint(20) NOT NULL,
  `" . self::MAIL_IN_DIRECTORY_MAIL . "` bigint(20) NOT NULL,
  PRIMARY KEY (`mod_dir`,`mod_mail`),
  FOREIGN KEY (`" . self::MAIL_IN_DIRECTORY_DIRECTORY . "`) REFERENCES `" . self::TABLE_MAIL_DIRECTORY . "` (`" . self::MAIL_DIRECTORY_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::MAIL_IN_DIRECTORY_MAIL . "`) REFERENCES `" . self::TABLE_MAIL . "` (`" . self::MAIL_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_REPORT . "` (
  `" . self::REPORT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::REPORT_SUBJECT . "` bigint(20) NOT NULL,
  `" . self::REPORT_SUBJECT_TYPE . "` enum('post', 'user', 'resource', 'comment') NOT NULL,
  `" . self::REPORT_USER . "` bigint(20) NOT NULL,
  `" . self::REPORT_TEXT . "` text NOT NULL,
  PRIMARY KEY (`" . self::REPORT_ID . "`),
  UNIQUE (`" . self::REPORT_POST . "`, `" . self::REPORT_USER . "`),
  FOREIGN KEY (`" . self::REPORT_USER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_RESOURCE . "` (
  `" . self::RESOURCE_ID . "` bigint(20) NOT NULL,
  `" . self::RESOURCE_TYPE . "` enum('video','photo') NOT NULL,
  `" . self::RESOURCE_PATH . "` varchar(255) NOT NULL,
  `" . self::RESOURCE_OWNER . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . self::RESOURCE_ID . "`),
  FOREIGN KEY (`" . self::RESOURCE_OWNER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_SUB_CATEGORY . "` (
  `" . self::SUB_CATEGORY_PARENT . "` varchar(50) NOT NULL,
  `" . self::SUB_CATEGORY_CATEGORY . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . self::SUB_CATEGORY_PARENT . "`,`" . self::SUB_CATEGORY_CATEGORY . "`),
  FOREIGN KEY (`" . self::SUB_CATEGORY_PARENT . "`) REFERENCES `" . self::TABLE_CATEGORY . "` (`" . self::CATEGORY_NAME . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::SUB_CATEGORY_CATEGORY . "`) REFERENCES `" . self::TABLE_CATEGORY . "` (`" . self::CATEGORY_NAME . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_TAG . "` (
  `" . self::TAG_NAME . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . self::TAG_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `" . self::TABLE_VOTE . "` (
  `" . self::VOTE_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::VOTE_VOTE . "` int(10) unsigned NOT NULL,
  `" . self::VOTE_AUTHOR . "` bigint(20) NULL,
  `" . VOTE_POST . "` bigint(20) NOT NULL,
  FOREIGN KEY (`" . self::VOTE_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE SET NULL,
  FOREIGN KEY (`" . self::VOTE_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

		return s;
	}
}
?>