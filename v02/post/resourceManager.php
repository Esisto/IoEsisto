<?php
	require_once("common.php");

	//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg
	
	class resourceManager {
		
		/**
		* Aggiunge una risorsa di tipo "photo" al sistema.
		* @return: un oggetto resource di tipo photo completo di ogni informazione.
		*/
	
		function uploadPhoto($name,$owner){
			//TODO genera il path 
			//restituisce un oggetto resource
			$photo= new Resource($owner,"TODO","photo");
			return $photo;
		}
	
	}

?>