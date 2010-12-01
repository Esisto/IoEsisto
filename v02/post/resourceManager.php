<?php
	require_once("common.php");
	
	//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg
	
	class resourceManager {
		
		/**
		* Aggiunge una risorsa di tipo "photo" al sistema.
		* @return: un oggetto resource di tipo photo completo di ogni informazione.
		*/
	
		function uploadPhoto($name,$owner,$tmp_name){
			//salvo il file nella cartella upload/owner/date
			$path=self::createUserDirectory($owner,$name);
			if(@is_uploaded_file($tmp_name)){
			      @move_uploaded_file($tmp_name,$path)
			      or die("Impossibile spostare il file $name con tmp_name: $tmp_name in: $path");
			      echo "</br> L'upload del file $name con tmp_name $tmp_name è avvenuto correttamente nella posizione: $path</br>";
			 }
			//restituisce un oggetto resource
			$photo= new Resource($owner,$path,"photo");
			//TODO carica l'oggetto resource nel db
			return $photo;
		}
		
		function createUserDirectory($owner,$name){
			$UP_DIR = $_SERVER["DOCUMENT_ROOT"] . "/IoEsisto/v02/upload";
			//IF già esiste NOMEUTENTE
				//IF già esiste DATAOGGI
					//return path
				//ELSE
					//crea DATAOGGI
					//return path
			//ELSE crea NOMEUTENTE/DATAOGGI
				$path= "$UP_DIR/ioesisto/011210/$name";
				return $path;
		}
	
	}

?>