<?php
    class User{
        protected $ID;
        protected $nickname;
        protected $name;
        protected $surname;
        protected $hobby;
        protected $job;
        protected $leavingPlace;
        protected $feedback;
        protected $mail;
        protected $contacts;
        protected $password;
        protected $follow;
        protected $creationDate;
        protected $visible;
        protected $verificated;
        protected $signals;
        
        static $contactsTypes = array("PHONE" => "phone",
                                      "ADDRESS" => "address",
                                      "MOBILE" => "mobile");
        
        function setNickname($nickname) {
			$this->nickname = $nickname;
	}
	function setName($name) {
			$this->name = $name;
	}
	function setSurname($surname) {
			$this->surname = $surname;
	}
	function setHobby($hobby) {
			$this->hobby = $hobby;
	}
	function setJob($job) {
			$this->job = $job;
	}
	function setLeavingPlace($leavingPlace) {
			$this->leavingPlace= $leavingPlace;
	}
	function setFeedback($feedback) {
			$this->feedback = $feedback;
	}
	function setMail($mail) {
			$this->mail = $mail;
	}
        function setCreationDate($crationDate) {
			$this->creationDate = $crationDate;
	}
        function setPassword($password) {
			$this->password = $password;
	}
        function setVisible($visible) {
			$this->visible = $visible;
	}
        function setVerificated($verificated) {
			$this->verificated = $verificated;
	}
        function setSignals($signals) {
			$this->signals = $signals;
	}

        function __construct($nickname, $name, $surname, $hobby, $job, $leavingPlace, $feedback, $mail, $password, $creationDate, $visible, $verificated, $signals){
	    $this->setNickname($nickname);
            $this->setName($name);
            $this->setSurname($hobby);
            $this->setJob($job);
            $this->setLeavingPlace($leavingPlace);
            $this->setFeedback($feedback);
            $this->setMail($mail);
            $this->setPassword($password);
            $this->setCreationDate($creationDate);
            $this->setVisible($visible);
            $this->setVerificated($verificated);
            $this->setSignals($signals);
        }
    
	function setID ($ID){
	    $this->ID=$ID;
	}
        function addFollow($utente){
	    if(isset($this->follow)){
		if(array_search($utente,$this->follow)==false)
		    $this->follow[]=$utente;
	    }else
		$this->follow[]=$utente;
        }
        
        function removeFollow($utente){
	    if(array_search($utente,$this->follow))
		unset($this->follow[array_search($utente,$this->follow)]);  
        }
        
        function positiveFeedback(){
            if (isset($this->feedback))
                $this->feedback++;
            else
                $this->feedback=1;
        }
        
        function negativeFeedback(){
             if (isset($this->feedback) && $this->feedback > 0)
                $this->feedback--;
            else
                $this->feedback=0;
        }
        
        function addContact($contnet, $type){
            if (isset($this::$contactsTypes[$type])){
                    $this->contacts[$contnet]=$type;
            } 
        }
        
        function removeContact($contnent){
	    unset( $this->contacts[$contnent] );  
        }
        
        function addSignal(){
	    if (isset($this->signals))
                $this->signals++;
            else
                $this->signals=1;
        }
	
	function login(){
	    //TODO
	}

    }

?>
