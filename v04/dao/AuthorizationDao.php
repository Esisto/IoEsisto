<?php
require_once ('Dao.php');
require_once("db.php");
require_once("query.php");
require_once("dataobject/Contact.php");

class AuthorizationDao extends Dao {
    
    function  __construct( ){
        parent::__construct();
    }
    
    /*
     *@ Override
     */
    function load($rl_name) {

        parent::load($rl_name);
    
        $result = array ();
    
    	$query = "SELECT * FROM " . DB::TABLE_ROLE . " WHERE " . DB::ROLE_NAME . " = '" . $rl_name . "'"; // query usando le const

	$this->db->execute($query); //eseguo la query con l'oggetto DBManager che hanno tutti i DAO
    	if($this->db->num_rows() != 1) //controllo la quantitˆ di risultati
			throw new Exception("L'oggetto cercato non  stato trovato. Riprovare.");
			
		$res = $this->db->fetch_result(); //recupero il primo (e unico) risultato.
		//TRUCCO!!! il risultato  giˆ in un array associativo dove l'indice  il nome della colonna...
		//posso ritornarlo direttamente cos“...

		//ma se voglio fare qualche cosa in pi, posso fare cos“:
		foreach($res as $index => $value)
			$result[$index] = $value > 0;	//metto nell'array dei valori booleani
			
		return $result;
        
    }
    
    
    function loadPermit($rl_name, $permit){
		
	//parent::load($rl_name);
	//parent::loadPermit($rl_name, $permit);
	
	$result = array ();
    
    	//$query = "SELECT '" . $permit . "'  FROM " . DB::TABLE_ROLE . " WHERE " . DB::ROLE_NAME . " = '" . $rl_name . "'"; // query usando le const
	$query = "SELECT `". $permit . "` FROM `Role` WHERE `rl_name` = 'admin'";

	$this->db->execute($query); //eseguo la query con l'oggetto DBManager che hanno tutti i DAO
    	if($this->db->num_rows() != 1) //controllo la quantitˆ di risultati
			throw new Exception("L'oggetto cercato non  stato trovato. Riprovare.");
			
		$res = $this->db->fetch_result(); 
		
		//ma se voglio fare qualche cosa in pi, posso fare cos“:
		foreach($res as $index => $value)
			$result[$index] = $value > 0;	//metto nell'array dei valori booleani
			
		return $result;
			
    }
    
    function check($rl_name){
	
	$result = $this-> load($rl_name);
	
	foreach( $result as $index => $value){
	    if( $value == true)
		echo $index . "<input type='checkbox' checked='checked'/> <br/>";
	    else
		echo $index . "<input type='checkbox' /> <br/>";
	}
    }
    
}

?>
