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
		
		function addDescription($rsID, $description){
			//TODO
		}
		
		function createUserDirectory($owner,$name){
			$UP_DIR = $_SERVER["DOCUMENT_ROOT"] . "/IoEsisto/v02/upload";
			if(file_exists("$UP_DIR/$owner")){
				/*DEBUG*/ echo "</br>la cartella $owner esiste</br>";
				if(file_exists("$UP_DIR/$owner/" . date("dmy"))){
					/*DEBUG*/ echo "</br>la cartella". date("dmy") . " esiste</br>";
					$path= "$UP_DIR/$owner/". date("dmy") . "/$name";
					return $path;
				}else{
					/*DEBUG*/ echo "</br>la cartella". date("dmy") . " NON esiste</br>";
					Mkdir("$UP_DIR/$owner/". date("dmy"),0777);
					$path= "$UP_DIR/$owner/". date("dmy") . "/$name";
					return $path;     
				}
			}else
				/*DEBUG*/ echo "</br>la cartella $owner NON esiste</br>";
				Mkdir("$UP_DIR/$owner",0777);
				Mkdir("$UP_DIR/$owner/". date("dmy"),0777);
				$path= "$UP_DIR/$owner/". date("dmy") . "/$name";
				return $path;     
		}
	
	}

?>