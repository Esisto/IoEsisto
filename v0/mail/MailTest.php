<?php
require_once("mail/MailManager.php");
require_once("mail/Mail.php");

class MailTest {
	var $author_id;
	var $mail_data;
	var $mail_data2;
	var $dir_name;
	var $dir_name2;
	var $fake_mailbox_dir_id;
	var $fake_spam_dir_id;
	var $fake_trash_dir_id;
	
	function __construct() {
		$this->author_id = 1;
		$this->mail_data = array("from" => $this->author_id, "to" => "$this->author_id",
								 "text" => "TESTO!");
		$this->mail_data_all = array("subject" => "OGGETTO", "from" => $this->author_id, "to" => "$this->author_id",
								 "text" => "TESTO!");
		$this->mail_data2 = array("subject" => "oggetto", "from" => $this->author_id, "to" => "$this->author_id",
								 "text" => "testo!");
		$this->dir_name = "CARTELLA1";
		$this->dir_name2 = "cartella2";
		$this->fake_spam_dir_id = 3;
		$this->fake_mailbox_dir_id = 2;
		$this->fake_trash_dir_id = 3;
	}
	
	function testMail() {
		$data = $this->mail_data_all;
		$mail = MailManager::createMail($data);
		
		//echo "<p>" . $mail . "</p>"; //DEBUG
		if($mail === false)
			return "<br />Mail test NOT PASSED: not created";
		if(isset($data["subject"]))
			if($mail->getSubject() != $data["subject"])
				return "<br />Mail test NOT PASSED: subject";
		if(isset($data["from"]))
			if($mail->getFrom() != $data["from"])
				return "<br />Mail test NOT PASSED: from";
		if(isset($data["to"]))
			if($mail->getTo() != $data["to"])
				return "<br />Mail test NOT PASSED: to";
		if(isset($data["text"]))
			if($mail->getText() != $data["text"])
				return "<br />Mail test NOT PASSED: text";
		if(isset($data["repliesTo"]))
			if($mail->getRepliesTo() != $data["repliesTo"])
				return "<br />Mail test NOT PASSED: repliesTo";
		
		$mail2 = MailManager::loadMail($mail->getID());
		echo "<p>" . $mail . "<br />" . $mail2 . "</p>"; //DEBUG
		if($mail === false)
			return "<br />Mail test NOT PASSED: not saved";
		if($mail->getSubject() != $mail2->getSubject())
			return "<br />Mail test NOT PASSED: not saved subject";
		if($mail->getFrom() != $mail2->getFrom())
			return "<br />Mail test NOT PASSED: not saved from";
		if($mail->getTo() != $mail2->getTo())
			return "<br />Mail test NOT PASSED: not saved to";
		if($mail->getText() != $mail2->getText())
			return "<br />Mail test NOT PASSED: not saved text";
		if($mail->getRepliesTo() != $mail2->getRepliesTo())
			return "<br />Mail test NOT PASSED: not saved repliesTo";	
		if($mail->getCreationDate() != $mail2->getCreationDate())
			return "<br />Mail test NOT PASSED: not saved creationDate";
		if($mail->getID() != $mail2->getID())
			return "<br />Mail test NOT PASSED: not saved ID";	
		return "<br />Mail test passed";
	}
	
	function testDirectory() {
		$data = $this->mail_data;
		$mail = MailManager::createMail($data);
		$dir = MailManager::createDirectory($this->dir_name, $this->author_id);
		
		//echo "<p>" . $mail . "<br />" . $dir . "</p>"; //DEBUG
		if($dir == null || $dir === false)
			return "<br />Directory test NOT PASSED: not created";
		if($dir->getOwner() != $this->author_id)
				return "<br />Directory test NOT PASSED: owner";
		if($dir->getName() != $this->dir_name)
				return "<br />Directory test NOT PASSED: name";
			
		$dir2 = MailManager::loadDirectory($dir->getID());
		//echo "<p>" . $dir . "<br />" . $dir2 . "</p>"; //DEBUG
		if($dir2 === false)
			return "<br />Directory test NOT PASSED: not saved";
		if($dir->getOwner() != $dir2->getOwner())
				return "<br />Directory test NOT PASSED: not saved owner";
		if($dir->getName() != $dir2->getName())
				return "<br />Directory test NOT PASSED: not saved name";
		
		$dir = MailManager::addMailToDir($mail, $dir);
		$dir2 = MailManager::loadDirectory($dir->getID());
		echo "<p>" . $dir . "<br />" . $dir2 . "</p>"; //DEBUG
		if($dir2 === false)
			return "<br />Directory test NOT PASSED: not updated";
		if($dir->getMails() != $dir2->getMails())
				return "<br />Directory test NOT PASSED: not updated mails";
			
		$dir = MailManager::editDirectory($dir, $this->dir_name2);
		$dir2 = MailManager::loadDirectory($dir->getID());
		echo "<p>" . $dir . "<br />" . $dir2 . "</p>"; //DEBUG
		if($dir2 === false)
			return "<br />Directory test NOT PASSED: not updated";
		if($dir->getName() != $dir2->getName())
				return "<br />Directory test NOT PASSED: not updated name";
			
		return "<br />Directory test passed";
	}
	
	function testDeleteMailFromDirectory() {
		require_once("common.php");
		$data = Filter::filterArray($this->mail_data);
		$mail = MailManager::createMail($data);
		//echo "<hr style='height:3px;background-color:blue;' />";
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		//echo "<hr style='height:3px;background-color:blue;' />";
		
		$oldmailboxcount = count($dir->getMails());
		$dir2 = MailManager::loadDirectoryFromName(TRASH, $dir->getOwner());
		$oldtrashcount = count($dir2->getMails());
		//echo "<p>" . $mail . "<br />" . $dir . "</p>"; //DEBUG
		if($mail == null || $mail === false)
			return "<br />Mail test NOT PASSED: not created";
		
		MailManager::moveToTrash($mail, $dir);
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		$mail2 = MailManager::loadMail($mail->getID());
		$dir2 = MailManager::loadDirectoryFromName(TRASH, $dir->getOwner());
		if($mail === false)
			return "<br />Mail test NOT PASSED: deleted";
		//echo "<p>" . $dir . "<br />" . $dir2 . "</p>"; //DEBUG
		if($mail != $mail2)
			return "<br />Mail test NOT PASSED: mail duplicated";
		if(count($dir->getMails()) == $oldmailboxcount)
			return "<br />Mail test NOT PASSED: mailbox not updated";
		if(count($dir2->getMails()) == $oldtrashcount)
			return "<br />Mail test NOT PASSED: trash not updated";
		
		return "<br />Mail deleting test passed";		
	}
	
	function testDeleteDirectory() {
		$data = $this->mail_data;
		$mail = MailManager::createMail($data);
		$dir = MailManager::createDirectory($this->dir_name, $this->author_id);
		$mailbox = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		$oldmailboxcount = count($dir->getMails());
		
		//echo "<p>" . $mail . "<br />" . $dir . "</p>"; //DEBUG
		if($dir == null || $dir === false)
			return "<br />Directory test NOT PASSED: not created";
		
		$dir2 = MailManager::deleteDirectory($dir);
		$dir = MailManager::loadDirectory($dir2->getID());
		$mailbox2 = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		//echo "<p>" . $mailbox . "<br />" . $mailbox2 . "</p>"; //DEBUG
		if($dir !== false)
			return "<br />Directory test NOT PASSED: not deleted";
		if(count($mailbox2->getMails()) == $oldmailboxcount)
			return "<br />Directory test NOT PASSED: not moved to Mailbox";
		
		return "<br />Directory deleting test passed";
	}
	
	function testSetReadStatus() {
		$data = $this->mail_data;
		$mail = MailManager::createMail($data);
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		$oldreadstatus = MailManager::getReadStatus($mail, $dir);
		
		MailManager::setReadStatus($mail, $dir, true);
		$dir2 = MailManager::loadDirectory($dir->getID());
		$newstatus = MailManager::getReadStatus($mail, $dir2);
		//echo "<p>" . serialize($oldreadstatus) . "<br />" . serialize($newstatus) . "</p>"; //DEBUG
		if($dir2 === false)
			return "<br />Status test NOT PASSED: not updated";
		if($newstatus == $oldreadstatus)
				return "<br />Status test NOT PASSED: not updated status";
				
		return "<br />Status test passed";		
	}
	
	function testAnswerMail() {
		$data = $this->mail_data;
		$mail = MailManager::createMail($data);
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		//MailManager::addMailToDir($mail, $dir);
		
		$data = $this->mail_data2;
		$data["repliesTo"] = 1;
		$mail2 = MailManager::answerMail($mail, $data);
		//MailManager::addMailToDir($mail2, $dir);
		
		$dir2 = MailManager::loadDirectory($dir->getID());
		//echo "<p>" . serialize($oldreadstatus) . "<br />" . serialize($newstatus) . "</p>"; //DEBUG
		if($mail2->getRepliesTo() != $mail->getID())
				return "<br />Answer test NOT PASSED: not answered";
				
		return "<br />Answer test passed";		
	}
	
	function testSendMail() {
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		$oldmailboxcount = count($dir->getMails());
		
		$data = $this->mail_data;
		$mail = MailManager::createMail($data);
		
		$dir = MailManager::loadDirectoryFromName(MAILBOX, $this->author_id);
		$newmailboxcount = count($dir->getMails());
		
		//echo "<p>" . $oldmailboxcount . "<br />" . $newmailboxcount . "</p>"; //DEBUG
		if($oldmailboxcount == $newmailboxcount)
			return "<br />Send test NOT PASSED: not sent";
				
		return "<br />Send test passed";		
	}
}
?>