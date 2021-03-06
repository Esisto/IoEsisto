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
	const TABLE_HISTORY = "History";
	const TABLE_LOG = "Log";
	const TABLE_MAIL = "Message";
	const TABLE_MAIL_DIRECTORY = "Directory";
	const TABLE_MAIL_IN_DIRECTORY = "MessageDirectory";
	const TABLE_POST = "Post";
	const TABLE_REPORT = "Report";
	const TABLE_RESOURCE = "Resource";
	const TABLE_CATEGORY_EDITOR = "CategoryEditor"; //TODO
	const TABLE_CATEGORY_MODERATOR = "CategoryModerator"; //TODO
	const TABLE_ROLE = "Role";
	const TABLE_SUB_CATEGORY = "SubCategory";
	const TABLE_SUSPENDED = "Suspended";
	const TABLE_TAG = "Tag";
	const TABLE_USER = "User";
	const TABLE_VOTE = "Vote";
	const TABLE_POST_RESOURCE = "PostRecource";
	
	//colonne comuni a più tabelle
	const EDITABLE = "editable";
	const REMOVABLE = "removable";
	const BLACK_CONTENT = "black";
	const RED_CONTENT = "red";
	const YELLOW_CONTENT = "yellow";
	const AUTO_BLACK_CONTENT = "auto_black";
	const PREVIOUS_VERSION = "previous_version";
	const ACCESS_COUNT = "access_count";
	
	// tabella Categoria
	const CATEGORY_NAME = "cat_name";
	const CATEGORY_CREATION_DATE = "cat_creationDate";
	const CATEGORY_AUTHOR = "cat_author";

	// tabella Commento
	const COMMENT_ID = "cm_ID";
	const COMMENT_CREATION_DATE = "cm_creationDate";
	const COMMENT_COMMENT = "cm_comment";
	const COMMENT_AUTHOR = "cm_author";
	const COMMENT_POST = "cm_post";
	
	// tabella Contatto
	const CONTACT_ID = "ct_ID";
	const CONTACT_CONTACT = "ct_contact";
	const CONTACT_NAME = "ct_name";
	const CONTACT_USER = "ct_user";
	
	// tabella Tipo Contatto
	const CONTACT_TYPE_NAME = "ctt_name";
	const CONTACT_TYPE_TYPE = "ctt_type";
	
	// tabella Contest
	const CONTEST_ID = "cs_ID";
	const CONTEST_DESCRIPTION = "cs_description";
	const CONTEST_TITLE = "cs_title";
	const CONTEST_AUHTOR = "cs_author";
	const CONTEST_TYPE_OF_SUBSCRIBER = "cs_typeofsubscriber";
	const CONTEST_RULES = "cs_rules";
	const CONTEST_PRIZES = "cs_prizes";
	const CONTEST_START = "cs_start";
	const CONTEST_END = "cs_end";
	
	// tabella Iscrizione a Contest
	const CONTEST_SUBSCRIBER_CONTEST = "css_contest";
	const CONTEST_SUBSCRIBER_POST = "css_post";
	const CONTEST_SUBSCRIBER_PLACEMENT = "css_placement";
	
	// tabella Feedback
	const FEEDBACK_CREATOR = "fb_feedbacker";
	const FEEDBACK_SUBJECT = "fb_feedbacked";
	const FEEDBACK_VALUE = "fb_feedback";
	const FEEDBACK_CREATION_DATE = "fb_feedbackDate";
	
	// tabella Follow
	const FOLLOW_SUBJECT = "fl_user";
	const FOLLOW_FOLLOWER = "fl_follower";
	const FOLLOW_SUBSCRIPTION_DATE = "fl_subscriptionDate";
	
	// tabella di Storico
	const HISTORY_ID = "hs_ID";
	const HISTORY_OBJECT = "hs_object";
	const HISTORY_DATE = "hs_date";
	const HISTORY_EDITOR = "hs_editor";
	const HISTORY_OPERATION = "hs_operation";
	
	// tabella Log
	const LOG_ID = "log_ID";
	const LOG_TIMESTAMP = "log_timestamp";
	const LOG_ACTION = "log_action";
	const LOG_TABLE = "log_table";
	const LOG_SUBJECT = "log_user";
	const LOG_OBJECT = "log_object";
	
	// tabella Mail
	const MAIL_ID = "msg_ID";
	const MAIL_SUBJECT = "msg_subject";
	const MAIL_TEXT = "msg_text";
	const MAIL_FROM = "msg_from";
	const MAIL_TO = "msg_to";
	const MAIL_REPLIES_TO = "msg_repliesTo";
	const MAIL_CREATION_DATE = "msg_creationDate";
	
	// tabella Cartella
	const MAIL_DIRECTORY_ID = "md_ID";
	const MAIL_DIRECTORY_NAME = "md_name";
	const MAIL_DIRECTORY_OWNER = "md_owner";
	
	// tabella Mail/Cartella
	const MAIL_IN_DIRECTORY_READ = "mod_read";
	const MAIL_IN_DIRECTORY_DIRECTORY = "mod_dir";
	const MAIL_IN_DIRECTORY_MAIL = "mod_mail";
	
	// tabella Post
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

	// tabella Report
	const REPORT_ID = "rp_ID";
	const REPORT_OBJECT_ID = "rp_subject";
	const REPORT_OBJECT_CLASS = "rp_subjectType";
	const REPORT_USER = "rp_user";
	const REPORT_CREATION_DATE = "rp_creationDate";
	const REPORT_TEXT = "rp_report";
	
	// tabella Risorsa
	const RESOURCE_ID = "rs_ID";
	const RESOURCE_TYPE = "rs_type";
	const RESOURCE_PATH = "rs_path";
	const RESOURCE_OWNER = "rs_owner";
	const RESOURCE_DESCRIPTION = "rs_description";
	const RESOURCE_CREATION_DATE = "rs_creationDate";
	const RESOURCE_TAGS = "rs_tags";
	const RESOURCE_MODIFICATION_DATE = "rs_modificationDate";
	
	// tabella Ruolo
	const ROLE_NAME = "rl_name";
	const READ = "read";
	const CREATE_NEWS = "create_news";
	const EDIT_NEWS = "edit_news";
	const DELETE_NEWS = "delete_news";
	const CREATE_PHOTOREP = "create_photorep";
	const EDIT_PHOTOREP = "edit_photorep";
	const DELETE_PHOTOREP = "delete_photorep";
	const CREATE_VIDEOREP = "create_videorep";
	const EDIT_VIDEOREP = "edit_videorep";
	const DELETE_VIDEOREP = "delete_videorep";
	const CHANGE_VISIBILITY = "change_visibility";
	const CREATE_LIST = "create_list";
	const EDIT_LIST = "edit_list";
	const DELETE_LIST = "delete_list";
	const COMMENT = "comment";
	const DELETE_COMMENT = "delete_comment";
	const VOTE = "vote";
	const FOLLOW = "follow";
	const STOP_FOLLOW = "stop_follow";
	const CREATE_FEEDBACK = "create_feedback";
	const DELETE_FEEDBACK = "delete_feedback";
 	const SEND_MESSAGE = "send_message";
	const CREATE_DIRECTORY = "create_directory";
	const EDIT_DIRECTORY = "edit_directory";
	const DELETE_DIRECTORY = "delete_directory";
	const MARK_AS_READ = "mark_as_read";
	const MOVE_MESSAGE = "move_message";
	const EMPTY_RECYCLE_BIN = "empty_recycle_bin";
	const CREATE_RESOURCE = "create_resource";
	const EDIT_RESOURCE = "edit_resource";
 	const DELETE_RESOURCE = "delete_resource";
	const EDIT_PROFILE = "edit_profile";
	const CREATE_CONTEST = "create_contest";
	const EDIT_CONTEST = "edit_contest";
	const DELETE_CONTEST = "delete_contest";
	const SUBSCRIBE = "subscribe";
	const UNSUBSCRIBE = "unsubscribe";
	const CREATE_USER = "create_user";
	const DELETE_USER = "delete_user";
	const BLOCK_USER = "block_user";
	const SUSPEND_USER = "suspend_user";
	const SIGNAL = "signal";
	const CREATE_CATEGORY = "create_category";
	const EDIT_CATEGORY = "edit_category";
	const DELETE_CATEGORY = "delete_category";
	const CREATE_TEMPLATE = "create_template";
	const EDIT_TEMPLATE = "edit_template";
	const DELETE_TEMPLATE = "delete_template";
	const ADVANCED_TPL_MANAGER = "adv_template_manager";
	const EDIT_OTHER_NEWS = "edit_other_news";
	const EDIT_OTHER_PHOTOREP = "edit_other_photorep";
	const EDIT_OTHER_VIDEOREP = "edit_other_videorep";
	const EDIT_OTHER_LIST = "edit_other_list";
	const EDIT_OTHER_PROFILE = "edit_other_profile";
	const EDIT_OTHER_RESOURCE = "edit_other_resource";
	const UNSUBSCRIBE_OTHER = "unsubscribe_other";
	const DELETE_OTHER_FEEDBACK = "delete_other_feedback";
	const HIDE_OTHER = "hide_other";
	const CREATE_OTHER_TEMPLATE = "create_other_template";
	const EDIT_OTHER_TEMPLATE = "edit_other_template";
	const DELETE_OTHER_TEMPLATE = "delete_other_template";
	const REQUEST_SUSPEND = "request_suspend";
	const REQUEST_BLOCK = "request_block";
	const VIEW_MOD_DECISION = "view_mod_decision";
	const VIEW_EDIT_DECISION = "view_edit_decision";
	const VIEW_HISTORY = "view_history";
	const VIEW_BLOCK_REQUEST = "view_block_request";
	const VIEW_SUSPEND_REQUEST = "view_suspend_request";
	
	// tabella Sottocategoria
	const SUB_CATEGORY_PARENT = "sc_parent";
	const SUB_CATEGORY_CATEGORY = "sc_category";
	
	// tabella Sospeso
	const SUSPENDED_ID = "sp_ID";
	const SUSPENDED_USER = "sp_user";
	const SUSPENDED_BY = "sp_by";
	const SUSPENDED_FORMER_ROLE = "sp_formerrole";
	const SUSPENDED_REASON = "sp_reason";
	const SUSPENDED_START = "sp_start";
	const SUSPENDED_END = "sp_end";
	
	// tabella Tag
	const TAG_NAME = "tag_name";
//	const TAG_ACCESS_COUNT = "tag_access_count"; //TODO implementare la cosa in TagManager
	
	// tabella Utente
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

	// tabella Voto
	const VOTE_CREATION_DATE = "vt_creationDate";
	const VOTE_VOTE = "vt_vote";
	const VOTE_AUTHOR = "vt_author";
	const VOTE_POST = "vt_post";
	
	//tabella PostResource
	const PR_POST_ID = "ps_ID";
	const PR_RESOURCE_ID = "rs_ID";
	
	static function getCreateQueries() {
		$s = "CREATE TABLE `" . self::TABLE_HISTORY . "` (
  `" . self::HISTORY_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::HISTORY_DATE . "` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::HISTORY_OBJECT . "` blob NOT NULL,
  `" . self::HISTORY_EDITOR . "` bigint(20) NOT NULL,
  `" . self::HISTORY_OPERATION . "` varchar(20) NOT NULL,
  PRIMARY KEY (`" . self::HISTORY_ID . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_ROLE . "` ( 
  `" . self::ROLE_NAME . "` varchar(50) NOT NULL,
  `" . self::READ . "` tinyint(1) NOT NULL,
  `" . self::CREATE_NEWS . "` tinyint(1) NOT NULL,
  `" . self::EDIT_NEWS . "` tinyint(1) NOT NULL,
  `" . self::DELETE_NEWS . "` tinyint(1) NOT NULL,
  `" . self::CREATE_PHOTOREP . "` tinyint(1) NOT NULL,
  `" . self::EDIT_PHOTOREP . "` tinyint(1) NOT NULL,
  `" . self::DELETE_PHOTOREP . "` tinyint(1) NOT NULL,
  `" . self::CREATE_VIDEOREP . "` tinyint(1) NOT NULL,
  `" . self::EDIT_VIDEOREP . "` tinyint(1) NOT NULL,
  `" . self::DELETE_VIDEOREP . "` tinyint(1) NOT NULL,
  `" . self::CHANGE_VISIBILITY . "` tinyint(1) NOT NULL,
  `" . self::CREATE_LIST . "` tinyint(1) NOT NULL,
  `" . self::EDIT_LIST . "` tinyint(1) NOT NULL,
  `" . self::DELETE_LIST . "` tinyint(1) NOT NULL,
  `" . self::COMMENT . "` tinyint(1) NOT NULL,
  `" . self::DELETE_COMMENT . "` tinyint(1) NOT NULL,
  `" . self::VOTE . "` tinyint(1) NOT NULL,
  `" . self::FOLLOW . "` tinyint(1) NOT NULL,
  `" . self::STOP_FOLLOW . "` tinyint(1) NOT NULL,
  `" . self::CREATE_FEEDBACK . "` tinyint(1) NOT NULL,
  `" . self::DELETE_FEEDBACK . "` tinyint(1) NOT NULL,
  `" . self::SEND_MESSAGE . "` tinyint(1) NOT NULL,
  `" . self::CREATE_DIRECTORY . "` tinyint(1) NOT NULL,
  `" . self::EDIT_DIRECTORY . "` tinyint(1) NOT NULL,
  `" . self::DELETE_DIRECTORY . "` tinyint(1) NOT NULL,
  `" . self::MARK_AS_READ . "` tinyint(1) NOT NULL,
  `" . self::MOVE_MESSAGE . "` tinyint(1) NOT NULL,
  `" . self::EMPTY_RECYCLE_BIN . "` tinyint(1) NOT NULL,
  `" . self::CREATE_RESOURCE . "` tinyint(1) NOT NULL,
  `" . self::EDIT_RESOURCE . "` tinyint(1) NOT NULL,
  `" . self::DELETE_RESOURCE . "` tinyint(1) NOT NULL,
  `" . self::EDIT_PROFILE . "` tinyint(1) NOT NULL,
  `" . self::CREATE_CONTEST . "` tinyint(1) NOT NULL,
  `" . self::EDIT_CONTEST . "` tinyint(1) NOT NULL,
  `" . self::DELETE_CONTEST . "` tinyint(1) NOT NULL,
  `" . self::SUBSCRIBE . "` tinyint(1) NOT NULL,
  `" . self::UNSUBSCRIBE . "` tinyint(1) NOT NULL,
  `" . self::CREATE_USER . "` tinyint(1) NOT NULL,
  `" . self::DELETE_USER . "` tinyint(1) NOT NULL,
  `" . self::BLOCK_USER . "` tinyint(1) NOT NULL,
  `" . self::SUSPEND_USER . "` tinyint(1) NOT NULL,
  `" . self::SIGNAL . "` tinyint(1) NOT NULL,
  `" . self::CREATE_CATEGORY . "` tinyint(1) NOT NULL,
  `" . self::EDIT_CATEGORY . "` tinyint(1) NOT NULL,
  `" . self::DELETE_CATEGORY . "` tinyint(1) NOT NULL,
  `" . self::CREATE_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::EDIT_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::DELETE_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::ADVANCED_TPL_MANAGER . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_NEWS . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_PHOTOREP . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_VIDEOREP . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_LIST . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_PROFILE . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_RESOURCE . "` tinyint(1) NOT NULL,
  `" . self::UNSUBSCRIBE_OTHER . "` tinyint(1) NOT NULL,
  `" . self::DELETE_OTHER_FEEDBACK . "` tinyint(1) NOT NULL,
  `" . self::HIDE_OTHER . "` tinyint(1) NOT NULL,
  `" . self::CREATE_OTHER_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::EDIT_OTHER_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::DELETE_OTHER_TEMPLATE . "` tinyint(1) NOT NULL,
  `" . self::REQUEST_SUSPEND . "` tinyint(1) NOT NULL,
  `" . self::REQUEST_BLOCK . "` tinyint(1) NOT NULL,
  `" . self::VIEW_MOD_DECISION . "` tinyint(1) NOT NULL,
  `" . self::VIEW_EDIT_DECISION . "` tinyint(1) NOT NULL,
  `" . self::VIEW_HISTORY . "` tinyint(1) NOT NULL,
  `" . self::VIEW_BLOCK_REQUEST . "` tinyint(1) NOT NULL,
  `" . self::VIEW_SUSPEND_REQUEST . "` tinyint(1) NOT NULL,
  PRIMARY KEY (`" . self::ROLE_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_USER . "` (
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
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  `" . self::AUTO_BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::RED_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::YELLOW_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::EDITABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::REMOVABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::PREVIOUS_VERSION . "` bigint(20) NULL,
  PRIMARY KEY (`" . self::USER_ID . "`),
  UNIQUE (`" . self::USER_NICKNAME . "`),
  UNIQUE (`" . self::USER_E_MAIL . "`),
  FOREIGN KEY (`" . self::USER_ROLE . "`) REFERENCES `" . self::TABLE_ROLE . "` (`" . self::ROLE_NAME . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::PREVIOUS_VERSION . "`) REFERENCES `" . self::TABLE_HISTORY . "` (`" . self::HISTORY_ID . "`) ON DELETE NO ACTION
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_CATEGORY . "` (
  `" . self::CATEGORY_NAME . "` varchar(50) NOT NULL,
  `" . self::CATEGORY_CREATION_DATE . "` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::CATEGORY_AUTHOR . "` bigint(20) NOT NULL,
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`" . self::CATEGORY_NAME . "`),
  FOREIGN KEY (`" . self::CATEGORY_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_CONTEST . "` (
  `" . self::CONTEST_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::CONTEST_TITLE . "` varchar(50) DEFAULT NULL,
  `" . self::CONTEST_DESCRIPTION . "` text DEFAULT NULL,
  `" . self::CONTEST_TYPE_OF_SUBSCRIBER . "` enum('post','photoreportage','videoreportage','news','collection','album','playlist','magazine') DEFAULT NULL,
  `" . self::CONTEST_RULES . "` text,
  `" . self::CONTEST_PRIZES . "` text DEFAULT NULL,
  `" . self::CONTEST_START . "` datetime NULL DEFAULT NULL,
  `" . self::CONTEST_END . "` datetime NULL DEFAULT NULL,
  `" . self::CONTEST_AUHTOR . "` bigint(20) NOT NULL,
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`" . self::CONTEST_ID . "`),
  FOREIGN KEY (`" . self::CONTEST_AUHTOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_SUSPENDED . "` (
  `" . self::SUSPENDED_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::SUSPENDED_USER . "` bigint(20) NOT NULL,
  `" . self::SUSPENDED_BY . "` bigint(20) NOT NULL,
  `" . self::SUSPENDED_FORMER_ROLE . "` varchar(20) NOT NULL,
  `" . self::SUSPENDED_REASON . "` blob,
  `" . self::SUSPENDED_START . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::SUSPENDED_END . "` datetime DEFAULT NULL,
  PRIMARY KEY (`" . self::SUSPENDED_ID . "`),
  FOREIGN KEY (`" . self::SUSPENDED_USER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::SUSPENDED_BY . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE `" . self::TABLE_POST . "` (
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
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  `" . self::AUTO_BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::RED_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::YELLOW_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::EDITABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::REMOVABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::PREVIOUS_VERSION . "` bigint(20) NULL,
  PRIMARY KEY (`" . self::POST_ID . "`),
  FOREIGN KEY (`" . self::POST_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::PREVIOUS_VERSION . "`) REFERENCES `" . self::TABLE_HISTORY . "` (`" . self::HISTORY_ID . "`) ON DELETE NO ACTION
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_COMMENT . "` (
  `" . self::COMMENT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::COMMENT_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::COMMENT_COMMENT . "` text NOT NULL,
  `" . self::COMMENT_AUTHOR . "` bigint(20) NULL,
  `" . self::COMMENT_POST . "` bigint(20) NOT NULL,
  `" . self::AUTO_BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::REMOVABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`" . self::COMMENT_ID . "`),
  FOREIGN KEY (`" . self::COMMENT_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::COMMENT_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_CONTACT_TYPE . "` (
  `" . self::CONTACT_TYPE_TYPE . "` enum('phone', 'address', 'email', 'website', 'IM') NOT NULL,
  `" . self::CONTACT_TYPE_NAME . "` varchar(20) NOT NULL,
  PRIMARY KEY (`" . self::CONTACT_TYPE_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_CONTACT . "` (
  `" . self::CONTACT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::CONTACT_CONTACT . "` varchar(100) NOT NULL,
  `" . self::CONTACT_USER . "` bigint(20) NOT NULL,
  `" . self::CONTACT_NAME . "` varchar(20) NULL DEFAULT 'other',
  PRIMARY KEY (`" . self::CONTACT_ID . "`),
  FOREIGN KEY (`" . self::CONTACT_USER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::CONTACT_NAME . "`) REFERENCES `" . self::TABLE_CONTACT_TYPE . "` (`" . self::CONTACT_TYPE_NAME . "`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_CONTEST_SUBSCRIBER . "` (
  `" . self::CONTEST_SUBSCRIBER_CONTEST . "` bigint(20) NOT NULL,
  `" . self::CONTEST_SUBSCRIBER_POST . "` bigint(20) NOT NULL,
  `" . self::CONTEST_SUBSCRIBER_PLACEMENT . "` tinyint(10) NULL,
  PRIMARY KEY (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`,`" . self::CONTEST_SUBSCRIBER_POST . "`),
  UNIQUE (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`, `" . self::CONTEST_SUBSCRIBER_PLACEMENT . "`),
  FOREIGN KEY (`" . self::CONTEST_SUBSCRIBER_CONTEST . "`) REFERENCES `" . self::TABLE_CONTEST . "` (`" . self::CONTEST_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::CONTEST_SUBSCRIBER_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_FEEDBACK . "` (
  `" . self::FEEDBACK_CREATOR . "` bigint(20) NOT NULL,
  `" . self::FEEDBACK_SUBJECT . "` bigint(20) NOT NULL,
  `" . self::FEEDBACK_VALUE . "` int(11) DEFAULT NULL,
  `" . self::FEEDBACK_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . self::FEEDBACK_CREATOR . "`,`" . self::FEEDBACK_SUBJECT . "`),
  FOREIGN KEY (`" . self::FEEDBACK_CREATOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::FEEDBACK_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_FOLLOW . "` (
  `" . self::FOLLOW_SUBJECT . "` bigint(20) NOT NULL,
  `" . self::FOLLOW_FOLLOWER . "` bigint(20) NOT NULL,
  `" . self::FOLLOW_SUBSCRIPTION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`" . self::FOLLOW_SUBJECT . "`,`" . self::FOLLOW_FOLLOWER . "`),
  FOREIGN KEY (`" . self::FOLLOW_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::FOLLOW_FOLLOWER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_LOG . "` (
  `" . self::LOG_ID . "` varchar(40) NOT NULL,
  `" . self::LOG_TIMESTAMP . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::LOG_ACTION . "` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `" . self::LOG_TABLE . "` varchar(20) NOT NULL,
  `" . self::LOG_SUBJECT . "` bigint(20) NULL,
  `" . self::LOG_OBJECT . "` varchar(40) NOT NULL,
  PRIMARY KEY (`" . self::LOG_ID . "`),
  FOREIGN KEY (`" . self::LOG_SUBJECT . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_MAIL . "` (
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

CREATE TABLE `" . self::TABLE_MAIL_DIRECTORY . "` (
  `" . self::MAIL_DIRECTORY_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::MAIL_DIRECTORY_NAME . "` varchar(50) NOT NULL,
  `" . self::MAIL_DIRECTORY_OWNER . "` bigint(20) NOT NULL,
  PRIMARY KEY (`" . self::MAIL_DIRECTORY_ID . "`),
  FOREIGN KEY (`" . self::MAIL_DIRECTORY_OWNER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_MAIL_IN_DIRECTORY . "` (
  `" . self::MAIL_IN_DIRECTORY_READ . "` tinyint(1) DEFAULT 0,
  `" . self::MAIL_IN_DIRECTORY_DIRECTORY . "` bigint(20) NOT NULL,
  `" . self::MAIL_IN_DIRECTORY_MAIL . "` bigint(20) NOT NULL,
  PRIMARY KEY (`mod_dir`,`mod_mail`),
  FOREIGN KEY (`" . self::MAIL_IN_DIRECTORY_DIRECTORY . "`) REFERENCES `" . self::TABLE_MAIL_DIRECTORY . "` (`" . self::MAIL_DIRECTORY_ID . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::MAIL_IN_DIRECTORY_MAIL . "`) REFERENCES `" . self::TABLE_MAIL . "` (`" . self::MAIL_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_REPORT . "` (
  `" . self::REPORT_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::REPORT_OBJECT_ID . "` bigint(20) NOT NULL,
  `" . self::REPORT_OBJECT_CLASS . "` varchar(20) NOT NULL,
  `" . self::REPORT_USER . "` bigint(20) NOT NULL,
  `" . self::REPORT_TEXT . "` text NOT NULL,
  PRIMARY KEY (`" . self::REPORT_ID . "`),
  UNIQUE (`" . self::REPORT_OBJECT_ID . "`, `" . self::REPORT_OBJECT_CLASS . "`, `" . self::REPORT_USER . "`),
  FOREIGN KEY (`" . self::REPORT_USER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_RESOURCE . "` (
  `" . self::RESOURCE_ID . "` bigint(20) NOT NULL AUTO_INCREMENT,
  `" . self::RESOURCE_TYPE . "` enum('video','photo') NOT NULL,
  `" . self::RESOURCE_PATH . "` varchar(255) NOT NULL,
  `" . self::RESOURCE_OWNER . "` bigint(20) NOT NULL,
  `" . self::RESOURCE_DESCRIPTION . "` text NULL,
  `" . self::RESOURCE_CREATION_DATE . "` timestamp NOT NULL,
  `" . self::RESOURCE_MODIFICATION_DATE . "` timestamp NOT NULL,
  `" . self::RESOURCE_TAGS . "` text NOT NULL,
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  `" . self::AUTO_BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::BLACK_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::RED_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::YELLOW_CONTENT . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::EDITABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::REMOVABLE . "` tinyint(1) NOT NULL DEFAULT 0,
  `" . self::PREVIOUS_VERSION . "` bigint(20) NULL,
  PRIMARY KEY (`" . self::RESOURCE_ID . "`),
  FOREIGN KEY (`" . self::RESOURCE_OWNER . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE NO ACTION,
  FOREIGN KEY (`" . self::PREVIOUS_VERSION . "`) REFERENCES `" . self::TABLE_HISTORY . "` (`" . self::HISTORY_ID . "`) ON DELETE NO ACTION
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE `" . self::TABLE_SUB_CATEGORY . "` (
  `" . self::SUB_CATEGORY_PARENT . "` varchar(50) NOT NULL,
  `" . self::SUB_CATEGORY_CATEGORY . "` varchar(50) NOT NULL,
  PRIMARY KEY (`" . self::SUB_CATEGORY_PARENT . "`,`" . self::SUB_CATEGORY_CATEGORY . "`),
  FOREIGN KEY (`" . self::SUB_CATEGORY_PARENT . "`) REFERENCES `" . self::TABLE_CATEGORY . "` (`" . self::CATEGORY_NAME . "`) ON DELETE CASCADE,
  FOREIGN KEY (`" . self::SUB_CATEGORY_CATEGORY . "`) REFERENCES `" . self::TABLE_CATEGORY . "` (`" . self::CATEGORY_NAME . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_TAG . "` (
  `" . self::TAG_NAME . "` varchar(50) NOT NULL,
  `" . self::ACCESS_COUNT . "` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`" . self::TAG_NAME . "`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_VOTE . "` (
  `" . self::VOTE_CREATION_DATE . "` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `" . self::VOTE_VOTE . "` int(10) unsigned NOT NULL,
  `" . self::VOTE_AUTHOR . "` bigint(20) NULL,
  `" . self::VOTE_POST . "` bigint(20) NOT NULL,
  FOREIGN KEY (`" . self::VOTE_AUTHOR . "`) REFERENCES `" . self::TABLE_USER . "` (`" . self::USER_ID . "`) ON DELETE SET NULL,
  FOREIGN KEY (`" . self::VOTE_POST . "`) REFERENCES `" . self::TABLE_POST . "` (`" . self::POST_ID . "`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `" . self::TABLE_POST_RESOURCE . "` (
  `" . self::PR_POST_ID . "` bigint(20) NOT NULL,
  `" . self::PR_RESOURCE_ID . "` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

		return $s;
	}
}
?>