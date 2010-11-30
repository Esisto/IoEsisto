<?php
	require_once("post/resourceManager.php");

	//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg
	
	class resourceManager {
		
		/**
		* Aggiunge una risorsa di tipo "photo" al sistema.
		* @return: un oggetto resource di tipo photo completo di ogni informazione.
		*/
	
		function uploadPhoto($name,$type,$owner){
			//TODO genera il path 
			//restituisce un oggetto resource
			$data = array(  "path" => "percorso TODO",
					"type" => "photo",
					"description" => "",
					"owner" => $owner);
			$photo= new Resource($data);
			return $photo;
		}
	
	}

?>