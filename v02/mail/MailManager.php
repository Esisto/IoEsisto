<?php
require_once("mail/Mail.php");
require_once("session.php");

class MailManager {
	
	static function createMail($data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		$mail = new Mail($data);
		return $mail->save();
	}
	
	static function createDirectory($name, $owner) {
		require_once("common.php");
		$name = Filter::filterText($name);
		
		$dir = new MailDirectory($name, $owner);
		return $dir->save();
	}
	
	static function editDirectory($dir, $newname) {
		return $dir->edit($newname);
	}
	
	static function deleteDirectory($dir) {
		return $dir->delete();
	}
	
	static function answerMail($mail, $data) {
		$data["repliesTo"] = $mail->getID();
		return self::createMail($data);
	}
	
	static function setReadStatus($mail, $dir, $readStatus) {
		return $dir->setMailReadStatus($mail, $readStatus);
	}
	
	static function getReadStatus($mail, $dir) {
		return $dir->getMailReadStatus($mail);
	}

	/**
	 * Sposta $mail da $olddir a $newdir.
	 *
	 * @param $mail: un oggetto Mail.
	 * @param $olddir: un oggetto MailDirectory. Se null, aggiunge $mail a $newdir.
	 * @param $newdir: un oggetto MailDirectory.
	 */
	static function addMailToDir($mail, $newdir) {
		return self::moveMailToDir($mail, null, $newdir);
	}
	
	/**
	 * Sposta $mail da $olddir a $newdir.
	 *
	 * @param $mail: un oggetto Mail.
	 * @param $olddir: un oggetto MailDirectory. Se null, aggiunge $mail a $newdir.
	 * @param $newdir: un oggetto MailDirectory.
	 */
	static function moveMailToDir($mail, $olddir, $newdir) {
		if($olddir != null)
			return $olddir->moveMailTo($mail, $newdir);
		else
			return $newdir->addMail($mail);
	}
	
	/**
	 * @deprecated use moveToTrashInstead
	 * Cancella $mail dalle directory di $user
	 * 
	 * @param $mail: un oggetto Mail.
	 * @param $user: uno user id.
	 */
	static function deleteMail($mail, $user) {
		$dir = self::loadDirectoryFromName(TRASH, $user);
		return $dir->removeMail($mail);
	}
	
	/**
	 * Sposta $mail da $olddir nella cartella cartella spam dello stesso owner di $olddir.
	 * 
	 * @param $mail: un oggetto Mail.
	 * @param $olddir: un oggetto MailDirectory.
	 */
	static function moveToSpam($mail, $olddir) {
		$spam = self::loadDirectoryFromName(SPAM, $olddir->getOwner());
		self::moveMailToDir($mail, $olddir, $spam);
	}
	
	/**
	 *
	 * @param $mail: un oggetto Mail.
	 * @param $olddir: un oggetto MailDirectory.
	 */
	static function moveToTrash($mail, $olddir) {
		$trash = self::loadDirectoryFromName(TRASH, $olddir->getOwner());
		self::moveMailToDir($mail, $olddir, $trash);
	}
	
	/**
	 *
	 * @param $user: uno user id.
	 */
	static function getMailSent($user) {
		return Mail::loadMailsFromUser($user);
	}
	
	/**
	 *
	 * @param $user: uno user id.
	 */
	static function loadUsersDirectories($user) {
		return MailDirectory::loadUsersDirectories($user);
	}
	
	/**
	 *
	 * @param $dirname: il nome di una MailDirectory.
	 * @param $user: uno user id.
	 */
	static function loadDirectoryFromName($dirname, $user) {
		return MailDirectory::loadDirectoryFromName($dirname, $user);
	}
	
	/**
	 *
	 * @param $id: l'id di una Maildirectory.
	 */
	static function loadDirectory($id) {
		return MailDirectory::loadFromDatabase($id);
	}
	
	/**
	 *
	 * @param $id: l'id di una Mail
	 */
	static function loadMail($id) {
		return Mail::loadFromDatabase($id);
	}
	
	static function searchMail() {}
}


?>