<?php
	require_once("common.php");
	define("HEIGHT","100");
	define("WIDTH","200");
	
	//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg
	
	class resourceManager {
		
		/**
		* Aggiunge una risorsa di tipo "photo" al sistema.
		* @fname: nome della foto da caricare
		* @owner: nickname del proprietario
		* @tmp_name: nome temporaneo attribuito dal server all'imamgine
		* @return: un oggetto resource di tipo photo completo di ogni informazione.
		*/
		function uploadPhoto($fname,$owner,$tmp_name,$mime){
			//salvo il file nella cartella uploads/owner/date
			$path=self::createUserDirectory($owner,$fname);
			if(@is_uploaded_file($tmp_name)){
			      @move_uploaded_file($tmp_name,$path)
			      or die("Impossibile spostare il file $fname con tmp_name: $tmp_name in: $path");
			      /*DEBUG*/echo "</br> L'upload del file $fname con tmp_name $tmp_name è avvenuto correttamente nella posizione: $path</br>";
			}
			 //converto immagini non in .jpg
			if($mime != "image/jpeg"){
				/*DEBUG*/echo "non è jpg";
				$path = self::convert2jpg($path);
				/*DEBUG*/echo "</br> file $fname convertito in jpg, nuovo path: $path</br>";
			}else
				/*DEBUG*/echo "è jpg</br>";
			//ridimensiono le immagini oversize
			list($width,$height) = getimagesize($path);
			if($width>WIDTH || $height>HEIGHT){
				/*DEBUG*/echo "è da ridimensionare";
				@self::resize($path) or die("errore durante il resize dell'immagine");
				/*DEBUG*/echo "</br> file $fname ridimensionato</br>";
			}else
				/*DEBUG*/echo "non è da ridimensionare</br>";
			//restituisce un oggetto resource
			$photo= new Resource($owner,$path,"photo");
			//TODO carica l'oggetto resource nel db
			return $photo;
		}
		
		function addDescription($rsID, $description){
			//TODO
		}
		
		/**
		* Ridimensiona una foto sovrascrivendo l'immagine sorgente
		* @source: il percorso dell'imamgine in formato .jpg
		* @return: true se è stata ridimensionata, false se ci sono stati errori
		*/
		function resize($source){
			$image = NULL;
			if (@imagetypes() & IMG_JPG)
				 $image = @imagecreatefromjpeg($source);
			if ($image == NULL)
			   return false;
			list($old_width,$old_height) = getimagesize($source);
			$new_height= HEIGHT;
			$new_width= WIDTH;
			/*DEBUG*/echo "</br> vecchia altezza: $old_height , vecchia larghezza: $old_width </br> nuova altezza: $new_height , nuova larghezza: $new_width </br>";
			$new_res = @imagecreatetruecolor($new_width, $new_height);
			if (!(@imagecopyresampled($new_res, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height)))
			   return false;
			imagejpeg($new_res, $source);
			imagedestroy($image);
			imagedestroy($new_res);
			return true;
		}
		
		/**
		* converte l'immagine $source in formato .jpg
		* @source: il percorso dell'imamgine
		* @return: il path aggiornato (stesso path ma estensione .jpg)
		*/
		function convert2jpg($source){
			$est = substr($source, -3);
			switch ($est) {
			   case "png":{
			      if (@imagetypes() & IMG_PNG)
				 $image = @imagecreatefrompng($source);
			   } break;
			   case "gif":{
			      if (@imagetypes() & IMG_GIF)
				 $image = @imagecreatefromgif($source);
			   } break;
			}
			//header('Content-type: image/jpeg');
			$path=explode(".$est",$source);
			imagejpeg($image,$path[0] . ".jpg");
			//elimino il file sorgente
			unlink($source);
			imagedestroy($image);
			return $path[0] . ".jpg";
		}
		
		/**
		* crea le cartelle necessarie per lo storage delle foto 
		* @owner: nickname del proprietario
		* @fname: nome dell'imamgine
		* @return: percorso per il savataggio delle immagini per l'utente richiedente
		*/
		function createUserDirectory($owner,$fname){
			$UP_DIR = $_SERVER["DOCUMENT_ROOT"] . "/IoEsisto/v02";
			if(file_exists("$UP_DIR/uploads/$owner")){
				/*DEBUG*/ echo "</br>la cartella $owner esiste</br>";
				if(file_exists("$UP_DIR/uploads/$owner/" . date("dmy"))){
					/*DEBUG*/ echo "</br>la cartella". date("dmy") . " esiste</br>";
					$path= self::generatePath($owner,$fname);
				}else{
					/*DEBUG*/ echo "</br>la cartella". date("dmy") . " NON esiste</br>";
					Mkdir("$UP_DIR/uploads/$owner/". date("dmy"),0777);
					$path= self::generatePath($owner,$fname);     
				}
			}else{
				/*DEBUG*/ echo "</br>la cartella $owner NON esiste</br>";
				Mkdir("$UP_DIR/uploads/$owner",0777);
				Mkdir("$UP_DIR/uploads/$owner/". date("dmy"),0777);
				$path= self::generatePath($owner,$fname);
			}
			return $path;     
		}
		
		/**
		*Genera un path relativo per il salvataggio dell'imamgine $fname
		*@owner: nickname del proprietario
		*@fname: nome dell'imamgine
		*@return: /uploads/nomeUtente/data/nomefile.jpg
		*Se il file esiste già genera n nome casuale aggiungendo la stringa "_X" prima dell'estensione
		*dove X è un indice che parte da 1 e viene incrementato ogni volta di 1 finchè non trova un nome libero
		*/
		function generatePath($owner,$fname){
			$UP_DIR = $_SERVER["DOCUMENT_ROOT"] . "/IoEsisto/v02";
			if(!file_exists("$UP_DIR/uploads/$owner/". date("dmy") . "/$fname")){
				$path= "uploads/$owner/". date("dmy") . "/$fname";
			}else{
				//esiste già un file con quel nome
				$i=1;
				$string=explode(".", $fname);
				//$string[0] = file name $string[1]= extension
				do{
					$editfname=$string[0]. "_" . $i . "." . $string[1];
					$i++;
				}while(file_exists("$UP_DIR/$owner/". date("dmy") . "/$editfname"));
				$path= "uploads/$owner/". date("dmy") . "/$editfname";
			}
			return $path;
		}
	}

?>