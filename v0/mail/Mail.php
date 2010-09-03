<?php
require_once("strings/strings.php");

class MailDirectory {
	private $ID;
	private $name;
	private $owner;
	private $mails = array();
	
	function getID() {
		return $this->ID;
	}
	function getName() {
		return $this->name;
	}
	function getOwner() {
		return $this->owner;
	}
	function getMails() {
		return $this->mails;
	}
	
	function setName($name) {
		$this->name = $name;
		return $this;
	}
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setMails($mails) {
		$this->mails = $mails;
		return $this;
	}
	
	function __construct($name, $owner) {
		$this->name = $name;
		$this->owner = $owner;
	}
	
	function edit($name) {
		if($this->name == MAILBOX || $name == MAILBOX)
			return false; //non puoi cambiare nome alla mailbox o chiamare una cartella mailbox
		$this->setName($name);
		return $this->update();
	}
	
	function addMail($mail) {
		$this->mails[] = $mail;
		//echo $this; //DEBUG
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$res = $db->execute($s = Query::generateInsertStm($table,
												   array(MAIL_IN_DIRECTORY_DIRECTORY => $this->getID(),
														 MAIL_IN_DIRECTORY_MAIL => $mail->getID())),
						$table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("MailDirectory::addMail()");
		} else $db->display_connect_error("MailDirectory::addMail()");
		return false;
	}
	
	function moveMailTo($mail, $to) {
		$to->mails[] = $mail;
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$db->execute($s = Query::generateUpdateStm($table,
												   array(MAIL_IN_DIRECTORY_DIRECTORY => $to->getID()),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
			
			if($db->affected_rows() == 1) {
				$this->loadMails();
				return $this;
			} else $db->display_error("MailDirectory::moveMailTo()");
		} else $db->display_connect_error("MailDirectory::moveMailTo()");
		return false;
	}
	
	function removeMail($mail) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$db->execute($s = Query::generateDeleteStm($table,
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
			
			//echo "<p>" . $s . " - " . $db->affected_rows() . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				$this->loadMails();
				return $mail;
			} else $db->display_error("MailDirectory::removeMail()");
		} else $db->display_connect_error("MailDirectory::removeMail()");
		return false;
	}
	
	function setMailReadStatus($mail, $read) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$db->execute($s = Query::generateUpdateStm($table, array(MAIL_IN_DIRECTORY_READ => ($read ? 1 : 0)),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
				
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("MailDirectory::setMailReadStatus()");
		} else $db->display_connect_error("MailDirectory::setMailReadStatus()");
		return false;
	}
	
	/**
	 * @deprecated
	 */
	function getMailReadStatus($mail) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$db->execute($s = Query::generateSelectStm(array($table),
												   array(),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID())),
												   array()),
						$table->getName(), $this);
				
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				//echo "<p>" . $row[MAIL_IN_DIRECTORY_READ] . "</p>"; //DEBUG
				return $row[MAIL_IN_DIRECTORY_READ] > 0;
			} else $db->display_error("MailDirectory::getMailReadStatus()");
		} else $db->display_connect_error("MailDirectory::getMailReadStatus()");
		return false;		
	}
	
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
			$data = array(MAIL_DIRECTORY_NAME => $this->getName(),
						  MAIL_DIRECTORY_OWNER => $this->getOwner());
			
			$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $db->affected_rows(); //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				//echo "<br />" . $this; //DEBUG
				return $this;
			} else $db->display_error("MailDirectory::save()");
		} else $db->display_connect_error("MailDirectory::save()");
		return false;
	}
	
	function update() {
		$old = self::loadFromDatabase($this->getID());
		
		if($old->getName() != $this->getName()) {
			require_once("query.php");
			$db = new DBManager();
			if(!$db->connect_errno()) {
				define_tables(); defineMailDirColumns();
				$table = Query::getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
				$data[MAIL_DIRECTORY_NAME] = $this->getName();
				
				$rs = $db->execute($s = Query::generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_ID),Operator::$UGUALE,$this->getID()))),
								  $table->getName(), $this);
				if($db->affected_rows() == 1) {
					return $this;
				} else $db->display_error("MailDirectory::update()");
			} else $db->display_connect_error("MailDirectory::update()");
		}
		return false;
	}
	
	function delete() {
		if($this->getName() == MAILBOX)
			return false; //la mailbox non si può eliminare.
		
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
			//cerco la Mailbox dell'utente e sposto tutte le mail lì.
			$db->execute($s = Query::generateSelectStm(array($table),
												   array(),
												   array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_OWNER), Operator::$UGUALE, $this->owner),
														 new WhereConstraint($table->getColumn(MAIL_DIRECTORY_NAME), Operator::$UGUALE, MAILBOX)),
												   array()), $table->getName(), $this);
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$mailboxid = intval($row[MAIL_DIRECTORY_ID]);
				
				$table1 = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
				$db->execute($s = Query::generateUpdateStm($table1, array(MAIL_IN_DIRECTORY_DIRECTORY => intval($mailboxid)),
													   array(new WhereConstraint($table1->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()))),
							$table1->getName(), $this);
				
				if($db->affected_rows() == count($this->getMails())) {
					$rs = $db->execute($s = Query::generateDeleteStm($table,
																 array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_ID),Operator::$UGUALE,$this->getID()))),
									  $table->getName(), $this);
					//echo "<br />" . $db->affected_rows() . $s; //DEBUG
					if($db->affected_rows() == 1) {
						return $this;
					} else $db->display_error("MailDirectory::delete()");
				} else $db->display_error("MailDirectory::delete()");
			} else $db->display_error("MailDirectory::delete()");
		} else $db->display_connect_error("MailDirectory::delete()");
		return false;				
	}
	
	static function loadUsersDirectories($user) {
		defineMailDirColumns();
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_OWNER => $user));
		if($dirs !== false && count($dirs) > 0)
			return $dirs;
		return false;
	}
	
	static function loadDirectoryFromName($name, $user) {
		defineMailDirColumns();
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_NAME => $name, MAIL_DIRECTORY_OWNER => intval($user)));
		if($dirs !== false && count($dirs) == 1)
			return $dirs[0];
		return false;
	}
	
	static function loadFromDatabase($id) {
		defineMailDirColumns();
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_ID => $id));
		if($dirs !== false && count($dirs) == 1)
			return $dirs[0];
		return false;
	}
	
	private static function loadDirectoriesFrom($data) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
			$wheres = array();
			foreach($data as $comumnname => $d)
				$wheres[] = new WhereConstraint($table->getColumn($comumnname), Operator::$UGUALE, $d);
			$rs = $db->execute($s = Query::generateSelectStm(array($table), array(), $wheres, array()), $table->getName(), $data);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() > 0) {
				$dirs = array();
				while($row = $db->fetch_result()) {
					$d = new MailDirectory($row[MAIL_DIRECTORY_NAME], $row[MAIL_DIRECTORY_OWNER]);
					$d->setID(intval($row[MAIL_DIRECTORY_ID]))->loadMails();
					//echo "<p>" .$d ."</p>";
					$dirs[] = $d;
				}
				return $dirs;
			} else $db->display_error("MailDirectory::loadDirectoriesFrom()");
		} else $db->display_connect_error("MailDirectory::loadDirectoriesFrom()");
		return false;
	}
	
	function loadMails() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns(); defineMailInDirColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$table1 = Query::getDBSchema()->getTable(TABLE_MAIL);
			//echo "<p>" . $table . "</p>"; //DEBUG
			$rs = $db->execute($s =Query::generateSelectStm(array($table, $table1),
														 array(new JoinConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), $table1->getColumn(MAIL_ID))),
														 array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY),Operator::$UGUALE,$this->getID())),
														 array()),
							  $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			$mails = array();
			if($rs !== false && $db->num_rows() > 0) {
				while($row = $db->fetch_result()) {
					$m = new Mail(array("from" => $row[MAIL_FROM], "to" => $row[MAIL_TO],
									 "text" => $row[MAIL_TEXT], "subject" => $row[MAIL_SUBJECT],
									 "repliesTo" => $row[MAIL_REPLIES_TO]));
					$m->setID(intval($row[MAIL_ID]))->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[MAIL_CREATION_DATE])));
					$mails[] = $m;
					//echo "<p>" . serialize($q->hasNext()) ."</p>";
					//echo count($mails) . "-" . $db->num_rows() . "/"; //DEBUG
				}
				//TODO salvare il read status
				if($db->num_rows() == count($mails)) {
					return $this->setMails($mails);
				} else $db->display_error("MailDirectory::loadMails()");
			} else if($db->errno()) $db->display_error("MailDirectory::loadMails()");
		} else $db->display_connect_error("MailDirectory::loadMails()");
		return false;
	}
	
	function __toString() {
		$s = "DIRECTORY (ID = " . $this->getID() .
					  " | name = " . $this->getName() .
					  " | owner = " . $this->getOwner() .
					  " | mails = (";
		for($i=0; $i<count($this->getMails()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->mails[$i];
		}
		$s.= "))";
		return $s;
	}
}

class Mail {
	private $ID;
	private $creationDate;
	private $subject;
	private $text;
	private $from;
	private $to;
	private $repliesTo;
	
	function getID() {
		return $this->ID;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getSubject() {
		return $this->subject;
	}
	function getText() {
		return $this->text;
	}
	function getFrom() {
		return $this->from;
	}
	function getTo() {
		return $this->to;
	}
	function getRepliesTo() {
		return $this->repliesTo;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	
	function __construct($data) {
		if(isset($data["subject"]))
			$this->subject = $data["subject"];
		else
			$this->subject = NO_SUBJECT;
		if(isset($data["from"]))
			$this->from = $data["from"];
		if(isset($data["to"]))
			$this->to = $data["to"];
		if(isset($data["text"]))
			$this->text = $data["text"];
		if(isset($data["repliesTo"]))
			$this->repliesTo = $data["repliesTo"];
	}
	
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL);
			$data = array();
			if(isset($this->subject) && !is_null($this->getSubject()))
				$data[MAIL_SUBJECT] = $this->getSubject();
			if(isset($this->from) && !is_null($this->getFrom()))
				$data[MAIL_FROM] = intval($this->getFrom());
			if(isset($this->to) && !is_null($this->getTo()))
				$data[MAIL_TO] = $this->getTo();
			if(isset($this->text) && !is_null($this->getText()))
				$data[MAIL_TEXT] = $this->getText();
			if(isset($this->repliesTo) && !is_null($this->getRepliesTo()))
				$data[MAIL_REPLIES_TO] = intval($this->getRepliesTo());
			
			$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $db->affected_rows(); //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID(intval($db->last_inserted_id()));
				//echo "<br />" . serialize($this->ID); //DEBUG
				$rs = $db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,$this->getID())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[MAIL_CREATION_DATE])));
					//echo "<br />" . serialize($row[MAIL_CREATION_DATE]); //DEBUG
					
					//inserisce il messaggio nelle mailbox dei $to
					$toes = explode("|", $this->getTo());
					//echo serialize($toes); //DEBUG
					require_once("mail/MailManager.php");
					for($i=0; $i<count($toes); $i++) {
						$dir = MailManager::loadDirectoryFromName(MAILBOX, intval($toes[$i]));
						MailManager::addMailToDir($this, $dir);
					}
					//echo "<br />" . $this; //DEBUG
					return $this;
				} else $db->display_error("Mail::save()");
			} else $db->display_error("Mail::save()");
		} else $db->display_connect_error("Mail::save()");
		return false;
	}
	
	/**
	 * @deprecated
	 */
	function delete() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $db->affected_rows() . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Mail::delete()");
		} else $db->display_connect_error("Mail::delete()");
		return false;		
	}
	
	static function loadFromDatabase($id) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL);
			$db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,intval($id))),
														 array()),
							  $table->getName(), null);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$data = array("text" => $row[MAIL_TEXT],
							  "subject" => $row[MAIL_SUBJECT],
							  "from" => intval($row[MAIL_FROM]),
							  "to"=> $row[MAIL_TO],
							  "repliesTo" => $row[MAIL_REPLIES_TO]);
				
				$m = new Mail($data);
				$m->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[MAIL_CREATION_DATE])))->setID(intval($row[MAIL_ID]));
				//echo "<p>" .$m ."</p>";
				return $m;
			} else $db->display_error("Mail::loadFromDatabase()");
		} else $db->display_connect_error("Mail::loadFromDatabase()");
		return false;		
	}
	
	static function loadMailsFromUser($user) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineMailColumns();
			$table = Query::getDBSchema()->getTable(TABLE_MAIL);
			$db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(MAIL_FROM), Operator::$UGUALE, $user)),
														 array()),
							  $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
				$mails = array();
				while($row = $db->fetch_result()) {
					$data = array("text" => $row[MAIL_TEXT],
								  "subject" => $row[MAIL_SUBJECT],
								  "from" => intval($row[MAIL_FROM]),
								  "to"=> $row[MAIL_TO],
								  "repliesTo" => $row[MAIL_REPLIES_TO]);
					
					$m = new Mail($data);
					$m->setID(intval($row[MAIL_ID]))->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[MAIL_CREATION_DATE])));
					$mails[] = $m;
				}
				//echo "<p>" .$m ."</p>";
				return $mails;
			} else $db->display_error("Mail::loadMailsFromUser()");
		} else $db->display_connect_error("Mail::loadMailsFromUser()");
		return false;		
	}
	
	function __toString() {
		return "MAIL (ID = " . $this->getID() .
				   " | subject = " . $this->getSubject() .
				   " | from = " . $this->getFrom() .
				   " | to = " . $this->getTo() .
				   " | text = " . $this->getText() .
				   " | creationDate = " . date("d/m/Y G:i:s", $this->getCreationDate()) .
				   " | repliesTo = " . $this->getRepliesTo() .
				   ")";
	}
}

?>