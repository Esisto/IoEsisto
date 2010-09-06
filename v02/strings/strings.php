<?php

//
define("FEEDBACK_INITIAL_VALUE", 0);

//DB UTILITY STRINGS
define("LOGMANAGER", "LogManager");
define("DB_STATUS", "db_status");

//MAIL STRINGS
define("SPAM", "Spam");
define("TRASH", "Trash");
define("MAILBOX", "Mailbox");

//DB TABLES NAMES
function define_tables() {
	if(!defined("TABLE_CONTEST")) define("TABLE_CONTEST", "Contest");
	if(!defined("TABLE_CATEGORY")) define("TABLE_CATEGORY", "Category");
	if(!defined("TABLE_NATION")) define("TABLE_NATION", "Nation");
	if(!defined("TABLE_REGION")) define("TABLE_REGION", "Region");
	if(!defined("TABLE_PLACE")) define("TABLE_PLACE", "Place");
	if(!defined("TABLE_ROLE")) define("TABLE_ROLE", "Role");
	if(!defined("TABLE_USER")) define("TABLE_USER", "User");
	if(!defined("TABLE_POST")) define("TABLE_POST", "Post");
	if(!defined("TABLE_COMMENT")) define("TABLE_COMMENT", "Comment");
	if(!defined("TABLE_CONTACT")) define("TABLE_CONTACT", "Contact");
	if(!defined("TABLE_CONTACT_TYPE")) define("TABLE_CONTACT_TYPE", "ContactType");
	if(!defined("TABLE_CONTEST_SUBSCRIBER")) define("TABLE_CONTEST_SUBSCRIBER", "ContestSubscriber");
	if(!defined("TABLE_FEEDBACK")) define("TABLE_FEEDBACK", "Feedback");
	if(!defined("TABLE_FOLLOW")) define("TABLE_FOLLOW", "Follow");
	if(!defined("TABLE_LOG")) define("TABLE_LOG", "Log");
	if(!defined("TABLE_MAIL")) define("TABLE_MAIL", "Mail");
	if(!defined("TABLE_MAIL_DIRECTORY")) define("TABLE_MAIL_DIRECTORY", "MailDirectory");
	if(!defined("TABLE_MAIL_IN_DIRECTORY")) define("TABLE_MAIL_IN_DIRECTORY", "MailOfDirectory");
	if(!defined("TABLE_REPORT")) define("TABLE_REPORT", "Report");
	if(!defined("TABLE_RESOURCE")) define("TABLE_RESOURCE", "Resource");
	if(!defined("TABLE_SUB_CATEGORY")) define("TABLE_SUB_CATEGORY", "SubCategory");
	if(!defined("TABLE_TAG")) define("TABLE_TAG", "Tag");
	if(!defined("TABLE_VOTE")) define("TABLE_VOTE", "Vote");
}

//DB COLUMNS NAMES
function defineCategoryColumns() {
	if(!defined("CATEGORY_NAME")) define("CATEGORY_NAME", "cat_name");
}
function defineContestColumns() {
	if(!defined("CONTEST_ID")) define("CONTEST_ID", "ct_ID");
	if(!defined("CONTEST_TITLE")) define("CONTEST_TITLE", "ct_title");
	if(!defined("CONTEST_DESCRIPTION")) define("CONTEST_DESCRIPTION", "ct_description");
	if(!defined("CONTEST_TYPE_OF_SUBSCRIBER")) define("CONTEST_TYPE_OF_SUBSCRIBER", "ct_typeofsubscriber");
	if(!defined("CONTEST_RULES")) define("CONTEST_RULES", "ct_rules");
	if(!defined("CONTEST_PRIZES")) define("CONTEST_PRIZES", "ct_prizes");
	if(!defined("CONTEST_START")) define("CONTEST_START", "ct_start");
	if(!defined("CONTEST_END")) define("CONTEST_END", "ct_end");
}
function defineRoleColumns() {
	if(!defined("ROLE_NAME")) define("ROLE_NAME", "rl_name");
}
function defineUserColumns() {
	if(!defined("USER_ID")) define("USER_ID", "us_ID");
	if(!defined("USER_NICKNAME")) define("USER_NICKNAME", "us_nickname");
	if(!defined("USER_PASSWORD")) define("USER_PASSWORD", "us_password");
	if(!defined("USER_NAME")) define("USER_NAME", "us_name");
	if(!defined("USER_SURNAME")) define("USER_SURNAME", "us_surname");
	if(!defined("USER_BIRTHDAY")) define("USER_BIRTHDAY", "us_birthday");
	if(!defined("USER_E_MAIL")) define("USER_E_MAIL", "us_email");
	if(!defined("USER_GENDER")) define("USER_GENDER", "us_gender");
	if(!defined("USER_AVATAR")) define("USER_AVATAR", "us_avatar");
	if(!defined("USER_VISIBLE")) define("USER_VISIBLE", "us_visible");
	if(!defined("USER_VERIFIED")) define("USER_VERIFIED", "us_verified");
	if(!defined("USER_HOBBIES")) define("USER_HOBBIES", "us_hobbies");
	if(!defined("USER_JOB")) define("USER_JOB", "us_job");
	if(!defined("USER_BIRTHPLACE")) define("USER_BIRTHPLACE", "us_birthplace");
	if(!defined("USER_ROLE")) define("USER_ROLE", "us_role");
	if(!defined("USER_LIVINGPLACE")) define("USER_LIVINGPLACE", "us_livingplace");
	if(!defined("USER_CREATION_DATE")) define("USER_CREATION_DATE", "us_creationDate");
	if(!defined("USER_E_MAIL_UKEY")) define("USER_E_MAIL_UKEY", "email_UNIQUE");
	if(!defined("USER_NICKNAME_UKEY")) define("USER_NICKNAME_UKEY", "nickname_UNIQUE");
	if(!defined("USER_ROLE_FKEY")) define("USER_ROLE_FKEY", "fk_user_roles1");
	if(!defined("USER_RESOURCE_FKEY")) define("USER_RESOURCE_FKEY", "fk_user_resource1");
}
function definePostColumns() {
	if(!defined("POST_ID")) define("POST_ID", "ps_ID");
	if(!defined("POST_PERMALINK")) define("POST_PERMALINK", "ps_permalink");
	if(!defined("POST_TYPE")) define("POST_TYPE", "ps_type");
	if(!defined("POST_TITLE")) define("POST_TITLE", "ps_title");
	if(!defined("POST_TAGS")) define("POST_TAGS", "ps_tags");
	if(!defined("POST_SUBTITLE")) define("POST_SUBTITLE", "ps_subtitle");
	if(!defined("POST_HEADLINE")) define("POST_HEADLINE", "ps_headline");
	if(!defined("POST_CREATION_DATE")) define("POST_CREATION_DATE", "ps_creationDate");
	if(!defined("POST_MODIFICATION_DATE")) define("POST_MODIFICATION_DATE", "ps_modificationDate");
	if(!defined("POST_CONTENT")) define("POST_CONTENT", "ps_content");
	if(!defined("POST_CATEGORIES")) define("POST_CATEGORIES", "ps_categories");
	if(!defined("POST_VISIBLE")) define("POST_VISIBLE", "ps_visible");
	if(!defined("POST_AUTHOR")) define("POST_AUTHOR", "ps_author");
	if(!defined("POST_PLACE")) define("POST_PLACE", "ps_place");
	if(!defined("POST_USER_FKEY")) define("POST_USER_FKEY", "ps_tags");
	if(!defined("POST_PERMALINK_UKEY")) define("POST_PERMALINK_UKEY", "permalink_UNIQUE");
}
function defineCommentColumns() {
	if(!defined("COMMENT_ID")) define("COMMENT_ID", "cm_ID");
	if(!defined("COMMENT_CREATION_DATE")) define("COMMENT_CREATION_DATE", "cm_creationDate");
	if(!defined("COMMENT_COMMENT")) define("COMMENT_COMMENT", "cm_comment");
	if(!defined("COMMENT_AUTHOR")) define("COMMENT_AUTHOR", "cm_author");
	if(!defined("COMMENT_POST")) define("COMMENT_POST", "cm_post");
	if(!defined("COMMENT_USER_FKEY")) define("COMMENT_USER_FKEY", "fk_comment_user1");
	if(!defined("COMMENT_POST_FKEY")) define("COMMENT_POST_FKEY", "fk_comment_post1");
}
function defineContactTypeColumns() {
	if(!defined("CONTACT_TYPE_TYPE")) define("CONTACT_TYPE_TYPE", "ctt_type");
	if(!defined("CONTACT_TYPE_NAME")) define("CONTACT_TYPE_NAME", "ctt_name");
}
function defineContactColumns() {
	if(!defined("CONTACT_CONTACT")) define("CONTACT_CONTACT", "ct_contact");
	if(!defined("CONTACT_ID")) define("CONTACT_ID", "ct_ID");
	if(!defined("CONTACT_USER")) define("CONTACT_USER", "ct_user");
	if(!defined("CONTACT_NAME")) define("CONTACT_NAME", "ct_name");
	if(!defined("CONTACT_USER_FKEY")) define("CONTACT_USER_FKEY", "fk_contact_user");
	if(!defined("CONTACT_CONTACT_TYPE_FKEY")) define("CONTACT_CONTACT_TYPE_FKEY", "fk_contact_contactType");
}
function defineContestSubscriberColumns() {
	if(!defined("CONTEST_SUBSCRIBER_CONTEST")) define("CONTEST_SUBSCRIBER_CONTEST", "cs_contest");
	if(!defined("CONTEST_SUBSCRIBER_POST")) define("CONTEST_SUBSCRIBER_POST", "cs_post");
	if(!defined("CONTEST_SUBSCRIBER_PLACEMENT")) define("CONTEST_SUBSCRIBER_PLACEMENT", "cs_haswon");
	if(!defined("CONTEST_SUBSCRIBER_POST_FKEY")) define("CONTEST_SUBSCRIBER_POST_FKEY", "fk_contest_has_post_contest1");
	if(!defined("CONTEST_SUBSCRIBER_CONTEST_FKEY")) define("CONTEST_SUBSCRIBER_CONTEST_FKEY", "fk_contest_has_post_post1");
	if(!defined("CONTEST_SUBSCRIBER_UKEY")) define("CONTEST_SUBSCRIBER_UKEY", "contest_placement_UNIQUE");
}
function defineFeedbackColumns() {
	if(!defined("FEEDBACK_CREATOR")) define("FEEDBACK_CREATOR", "fb_feedbacker");
	if(!defined("FEEDBACK_SUBJECT")) define("FEEDBACK_SUBJECT", "fb_feedbacked");
	if(!defined("FEEDBACK_VALUE")) define("FEEDBACK_VALUE", "fb_feedback");
	if(!defined("FEEDBACK_CREATION_DATE")) define("FEEDBACK_CREATION_DATE", "fb_feedbackDate");
	if(!defined("FEEDBACK_USER_FKEY1")) define("FEEDBACK_USER_FKEY1", "fk_user_has_user_user3");
	if(!defined("FEEDBACK_USER_FKEY2")) define("FEEDBACK_USER_FKEY2", "fk_user_has_user_user4");
}
function defineFollowColumns() {
	if(!defined("FOLLOW_SUBJECT")) define("FOLLOW_SUBJECT", "fl_user");
	if(!defined("FOLLOW_FOLLOWER")) define("FOLLOW_FOLLOWER", "fl_follower");
	if(!defined("FOLLOW_SUBSCRIPTION_DATE")) define("FOLLOW_SUBSCRIPTION_DATE", "fl_subscriptionDate");
	if(!defined("FOLLOW_USER_FKEY1")) define("FOLLOW_USER_FKEY1", "fk_user_has_user_user1");
	if(!defined("FOLLOW_USER_FKEY2")) define("FOLLOW_USER_FKEY2", "fk_user_has_user_user2");
}
function defineLogColumns() {
	if(!defined("LOG_ID")) define("LOG_ID", "log_ID");
	if(!defined("LOG_TIMESTAMP")) define("LOG_TIMESTAMP", "log_timestamp");
	if(!defined("LOG_ACTION")) define("LOG_ACTION", "log_action");
	if(!defined("LOG_TABLE")) define("LOG_TABLE", "log_table");
	if(!defined("LOG_SUBJECT")) define("LOG_SUBJECT", "log_user");
	if(!defined("LOG_OBJECT")) define("LOG_OBJECT", "log_object");
	if(!defined("LOG_USER_FKEY")) define("LOG_USER_FKEY", "fk_log_user1");
}
function defineMailColumns() {
	if(!defined("MAIL_ID")) define("MAIL_ID", "ml_ID");
	if(!defined("MAIL_SUBJECT")) define("MAIL_SUBJECT", "ml_subject");
	if(!defined("MAIL_TEXT")) define("MAIL_TEXT", "ml_text");
	if(!defined("MAIL_FROM")) define("MAIL_FROM", "ml_from");
	if(!defined("MAIL_TO")) define("MAIL_TO", "ml_to");
	if(!defined("MAIL_REPLIES_TO")) define("MAIL_REPLIES_TO", "ml_repliesTo");
	if(!defined("MAIL_CREATION_DATE")) define("MAIL_CREATION_DATE", "ml_creationDate");
	if(!defined("MAIL_MAIL_FKEY")) define("MAIL_MAIL_FKEY", "fk_mail_mail1");
	if(!defined("MAIL_USER_FKEY")) define("MAIL_USER_FKEY", "fk_mail_user1");
}
function defineMailDirColumns() {
	if(!defined("MAIL_DIRECTORY_ID")) define("MAIL_DIRECTORY_ID", "md_ID");
	if(!defined("MAIL_DIRECTORY_NAME")) define("MAIL_DIRECTORY_NAME", "md_name");
	if(!defined("MAIL_DIRECTORY_OWNER")) define("MAIL_DIRECTORY_OWNER", "md_owner");
	if(!defined("MAIL_DIRECTORY_USER_FKEY")) define("MAIL_DIRECTORY_USER_FKEY", "fk_mailDirectory_user1");
}
function defineMailInDirColumns() {
	if(!defined("MAIL_IN_DIRECTORY_READ")) define("MAIL_IN_DIRECTORY_READ", "mod_read");
	if(!defined("MAIL_IN_DIRECTORY_DIRECTORY")) define("MAIL_IN_DIRECTORY_DIRECTORY", "mod_dir");
	if(!defined("MAIL_IN_DIRECTORY_MAIL")) define("MAIL_IN_DIRECTORY_MAIL", "mod_mail");
	if(!defined("MAIL_IN_DIRECTORY_MAIL_DIRECTORY_FKEY")) define("MAIL_IN_DIRECTORY_MAIL_DIRECTORY_FKEY", "fk_MailOfDirectory_MailDirectory1");
	if(!defined("MAIL_IN_DIRECTORY_MAIL_FKEY")) define("MAIL_IN_DIRECTORY_MAIL_FKEY", "fk_MailOfDirectory_mail1");
}
function defineResourceColumns() {
	if(!defined("RESOURCE_ID")) define("RESOURCE_ID", "rs_ID");
	if(!defined("RESOURCE_TYPE")) define("RESOURCE_TYPE", "rs_type");
	if(!defined("RESOURCE_PATH")) define("RESOURCE_PATH", "rs_path");
	if(!defined("RESOURCE_OWNER")) define("RESOURCE_OWNER", "rs_owner");
	if(!defined("RESOURCE_USER_FKEY")) define("RESOURCE_USER_FKEY", "fk_resource_user1");
}
function defineReportColumns() {
	if(!defined("REPORT_ID")) define("REPORT_ID", "rp_ID");
	if(!defined("REPORT_POST")) define("REPORT_POST", "rp_post");
	if(!defined("REPORT_USER")) define("REPORT_USER", "rp_user");
	if(!defined("REPORT_TEXT")) define("REPORT_TEXT", "rp_report");
	if(!defined("REPORT_USER_POST_UKEY")) define("REPORT_USER_POST_UKEY", "rp_postuser_UNIQUE");
	if(!defined("REPORT_USER_FKEY")) define("REPORT_USER_FKEY", "fk_post_has_user_user1");
	if(!defined("REPORT_POST_FKEY")) define("REPORT_POST_FKEY", "fk_post_has_user_post1");
}
function defineSubCategoryColumns() {
	if(!defined("SUB_CATEGORY_PARENT")) define("SUB_CATEGORY_PARENT", "sc_parent");
	if(!defined("SUB_CATEGORY_CATEGORY")) define("SUB_CATEGORY_CATEGORY", "sc_category");
	if(!defined("SUB_CATEGORY_CATEGORY_FKEY1")) define("SUB_CATEGORY_CATEGORY_FKEY1", "fk_category_has_category_category1");
	if(!defined("SUB_CATEGORY_CATEGORY_FKEY2")) define("SUB_CATEGORY_CATEGORY_FKEY2", "fk_category_has_category_category2");
}
function defineTagColumns() {
	if(!defined("TAG_NAME")) define("TAG_NAME", "tag_name");
}
function defineVoteColumns() {
	if(!defined("VOTE_CREATION_DATE")) define("VOTE_CREATION_DATE", "vt_creationDate");
	if(!defined("VOTE_VOTE")) define("VOTE_VOTE", "vt_vote");
	if(!defined("VOTE_AUTHOR")) define("VOTE_AUTHOR", "vt_author");
	if(!defined("VOTE_POST")) define("VOTE_POST", "vt_post");
	if(!defined("VOTE_USER_FKEY")) define("VOTE_USER_FKEY", "fk_vote_user1");
	if(!defined("VOTE_POST_FKEY")) define("VOTE_POST_FKEY", "fk_vote_post1");
}
?>