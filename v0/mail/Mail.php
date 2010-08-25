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
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$q->execute($s = $q->generateInsertStm($table,
												   array(MAIL_IN_DIRECTORY_DIRECTORY => $this->getID(),
														 MAIL_IN_DIRECTORY_MAIL => $mail->getID())),
						$table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($q->affected_rows() == 1) {
				return $this;
			}
		}
		return false;
	}
	
	function moveMailTo($mail, $to) {
		$to->mails[] = $mail;
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$q->execute($s = $q->generateUpdateStm($table,
												   array(MAIL_IN_DIRECTORY_DIRECTORY => $to->getID()),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
			
			if($q->affected_rows() == 1) {
				$this->loadMails();
				return $this;
			}
		}
		return false;
	}
	
	function removeMail($mail) {
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$q->execute($s = $q->generateDeleteStm($table,
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
			
			//echo "<p>" . $s . " - " . $q->affected_rows() . "</p>"; //DEBUG
			if($q->affected_rows() == 1) {
				return $mail;
			}
		}
		$this->loadMails();
		return false;
	}
	
	function setMailReadStatus($mail, $read) {
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$q->execute($s = $q->generateUpdateStm($table, array(MAIL_IN_DIRECTORY_READ => ($read ? 1 : 0)),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID()))),
						$table->getName(), $this);
				
			if($q->affected_rows() == 1) {
				return $this;
			}
		}
		return false;
	}
	
	function getMailReadStatus($mail) {
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
			$q->execute($s = $q->generateSelectStm(array($table),
												   array(),
												   array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()),
														 new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_MAIL), Operator::$UGUALE, $mail->getID())),
												   array()),
						$table->getName(), $this);
				
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($q->num_rows() == 1) {
				$row = $q->next();
				//echo "<p>" . $row[MAIL_IN_DIRECTORY_READ] . "</p>"; //DEBUG
				return $row[MAIL_IN_DIRECTORY_READ] > 0;
			}
		}
		return false;		
	}
	
	function save() {
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable(TABLE_MAIL_DIRECTORY);
			$data = array(MAIL_DIRECTORY_NAME => $this->getName(),
						  MAIL_DIRECTORY_OWNER => $this->getOwner());
			
			$rs = $q->execute($s = $q->generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $q->affected_rows(); //DEBUG
			$this->setID($q->last_inserted_id());
			//echo "<br />" . $this; //DEBUG
			return $this;
		}
		return false;
	}
	
	function update() {
		$old = self::loadFromDatabase($this->getID());
		
		if($row[MAIL_DIRECTORY_NAME] != $this->getName()) {
			require_once("query.php");
			$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$table = $q->getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
				$data[MAIL_DIRECTORY_NAME] = $this->getName();
				
				$rs = $q->execute($s = $q->generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_ID),Operator::$UGUALE,$this->getID()))),
								  $table->getName(), $this);
				if($q->affected_rows() == 1) {
					return $this;
				}
			}	
		}
		return false;
	}
	
	function delete() {
		if($this->getName() == MAILBOX)
			return false; //la mailbox non si può eliminare.
		
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable(TABLE_MAIL_DIRECTORY);
			//cerco la Mailbox dell'utente e sposto tutte le mail lì.
			$q->execute($s = $q->generateSelectStm(array($table),
												   array(),
												   array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_OWNER), Operator::$UGUALE, $this->owner),
														 new WhereConstraint($table->getColumn(MAIL_DIRECTORY_NAME), Operator::$UGUALE, MAILBOX)),
												   array()), $table->getName(), $this);
			if($q->num_rows() == 1) {
				while($q->hasNext()) {
					$row = $q->next();
					$mailboxid = $row[MAIL_DIRECTORY_ID];
				}
				
				$table1 = $dbs->getTable(TABLE_MAIL_IN_DIRECTORY);
				$q->execute($s = $q->generateUpdateStm($table1, array(MAIL_IN_DIRECTORY_DIRECTORY => $mailboxid),
													   array(new WhereConstraint($table1->getColumn(MAIL_IN_DIRECTORY_DIRECTORY), Operator::$UGUALE, $this->getID()))),
							$table1->getName(), $this);
				
				if($q->affected_rows() == count($this->getMails())) {
					$rs = $q->execute($s = $q->generateDeleteStm($table,
																 array(new WhereConstraint($table->getColumn(MAIL_DIRECTORY_ID),Operator::$UGUALE,$this->getID()))),
									  $table->getName(), $this);
					//echo "<br />" . $q->affected_rows() . $s; //DEBUG
					if($q->affected_rows() == 1) {
						return $this;
					}
				}
			}
		}
		return false;				
	}
	
	static function loadUsersDirectories($user) {
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_OWNER => $user));
		if($dirs !== false && count($dirs) > 0)
			return $dirs;
		return false;
	}
	
	static function loadDirectoryFromName($name, $user) {
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_NAME => $name, MAIL_DIRECTORY_OWNER => $user));
		if($dirs !== false && count($dirs) == 1)
			return $dirs[0];
		return false;
	}
	
	static function loadFromDatabase($id) {
		$dirs = self::loadDirectoriesFrom(array(MAIL_DIRECTORY_ID => $id));
		if($dirs !== false && count($dirs) == 1)
			return $dirs[0];
		return false;
	}
	
	private static function loadDirectoriesFrom($data) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable(TABLE_MAIL_DIRECTORY);
		$wheres = array();
		foreach($data as $comumnname => $d)
			$wheres[] = new WhereConstraint($table->getColumn($comumnname), Operator::$UGUALE, $d);
		$rs = $q->execute($s = $q->generateSelectStm(array($table), array(), $wheres, array()), $table->getName(), $data);
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . $q->num_rows() . "</p>"; //DEBUG
		if($rs !== false && $q->num_rows() > 0) {
			$dirs = array();
			while($q->hasNext()) {
				$row = $q->next();
				$d = new MailDirectory($row[MAIL_DIRECTORY_NAME], $row[MAIL_DIRECTORY_OWNER]);
				$d->setID(intval($row[MAIL_DIRECTORY_ID]))->loadMails();
				$d->loadMails();
				//echo "<p>" .$d ."</p>";
				$dirs[] = $d;
			}
			return $dirs;
		} else {
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}
	}
	
	function loadMails() {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable(TABLE_MAIL_IN_DIRECTORY);
		//echo "<p>" . $table . "</p>"; //DEBUG
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn(MAIL_IN_DIRECTORY_DIRECTORY),Operator::$UGUALE,$this->getID())),
													 array()),
						  $table->getName(), $this);
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . $q->num_rows() . "</p>"; //DEBUG
		$mails = array();
		if($rs !== false && $q->num_rows() > 0) {
			while($q->hasNext()) {
				$row = $q->next();
				$m = Mail::loadFromDatabase($row[MAIL_IN_DIRECTORY_MAIL]);
				if($m !== false) {
					$mails[] = $m;
				}
				//echo "<p>" .$m ."</p>";
			}
			if($q->num_rows() == count($mails))
				return $this->setMails($mails);
		} else {
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}
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
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable(TABLE_MAIL);
			$data = array();
			if(isset($this->subject) && !is_null($this->getSubject()))
				$data[MAIL_SUBJECT] = $this->getSubject();
			if(isset($this->from) && !is_null($this->getFrom()))
				$data[MAIL_FROM] = $this->getFrom();
			if(isset($this->to) && !is_null($this->getTo()))
				$data[MAIL_TO] = $this->getTo();
			if(isset($this->text) && !is_null($this->getText()))
				$data[MAIL_TEXT] = $this->getText();
			if(isset($this->repliesTo) && !is_null($this->getRepliesTo()))
				$data[MAIL_REPLIES_TO] = $this->getRepliesTo();
			
			$rs = $q->execute($s = $q->generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $q->affected_rows(); //DEBUG
			$this->setID($q->last_inserted_id());
			//echo "<br />" . serialize($this->ID); //DEBUG
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,$this->getID())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			while($q->hasNext()) {
				$row = $q->next();
				$this->setCreationDate(time($row[MAIL_CREATION_DATE]));
				//echo "<br />" . serialize($row[MAIL_CREATION_DATE]); //DEBUG
				
				//inserisce il messaggio nelle mailbox dei $to
				$toes = explode("|", $this->getTo());
				//echo serialize($toes); //DEBUG
				require_once("mail/MailManager.php");
				for($i=0; $i<count($toes); $i++) {
					$dir = MailManager::loadDirectoryFromName(MAILBOX, $toes[$i]);
					MailManager::addMailToDir($this, $dir);
				}
				
				break;
			}
			//echo "<br />" . $this; //DEBUG
			return $this;
		}
		return false;
	}
	
	/**
	 * @deprecated
	 */
	function delete() {
		require_once("query.php");
		$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable(TABLE_MAIL);
			$rs = $q->execute($s = $q->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $q->affected_rows() . $s; //DEBUG
			if($q->affected_rows() == 1) {
				return $this;
			}
		}
		return false;		
	}
	
	static function loadFromDatabase($id) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable(TABLE_MAIL);
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn(MAIL_ID),Operator::$UGUALE,$id)),
													 array()),
						  $table->getName(), null);
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . $q->num_rows() . "</p>"; //DEBUG
		if($rs !== false && $q->num_rows() == 1) {
			// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
			while($q->hasNext()) {
				$row = $q->next();
				$data = array("text" => $row[MAIL_TEXT],
							  "subject" => $row[MAIL_SUBJECT],
							  "from" => intval($row[MAIL_FROM]),
							  "to"=> $row[MAIL_TO],
							  "repliesTo" => $row[MAIL_REPLIES_TO]);
				
				$m = new Mail($data);
				$m->setCreationDate(time($row[MAIL_CREATION_DATE]))->setID(intval($row[MAIL_ID]));
				break;
			}
			//echo "<p>" .$m ."</p>";
			return $m;
		} else {
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}		
	}
	
	static function loadMailsFromUser($user) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable(TABLE_MAIL);
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn(MAIL_FROM), Operator::$UGUALE, $user)),
													 array()),
						  $table->getName(), $this);
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . $q->num_rows() . "</p>"; //DEBUG
		if($rs !== false && $q->num_rows() == 1) {
			// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
			$mails = array();
			while($q->hasNext()) {
				$row = $q->next();
				$data = array("text" => $row[MAIL_TEXT],
							  "subject" => $row[MAIL_SUBJECT],
							  "from" => intval($row[MAIL_FROM]),
							  "to"=> $row[MAIL_TO],
							  "repliesTo" => $row[MAIL_REPLIES_TO]);
				
				$m = new Mail($data);
				$m->setCreationDate(time($row[MAIL_CREATION_DATE]))->setID(intval($row[MAIL_ID]));
				$mails[] = $m;
			}
			//echo "<p>" .$m ."</p>";
			return $mails;
		} else {
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}		
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