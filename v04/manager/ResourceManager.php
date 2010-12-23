<?php
	require_once("dataobject/Resource.php");
	require_once("settings.php");
	define("HEIGHT","100");
	define("WIDTH","200");
	
	define("UP_DIR",$_SERVER["DOCUMENT_ROOT"]."/".ROOT_LINK);

	
	//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg
	
	class ResourceManager {
		
		/**
		* Aggiunge una risorsa di tipo "photo" al sistema.
		* @param fname: nome della foto da caricare
		* @param owner: nickname del proprietario
		* @param tmp_name: nome temporaneo attribuito dal server all'imamgine
		* @return: un oggetto resource di tipo photo completo di ogni informazione.
		*/
		function uploadPhoto($fname,$owner,$tmp_name,$mime){
			//salvo il file nella cartella uploads/owner/date
			$path=self::createUserDirectory($owner,$fname);
			if(@is_uploaded_file($tmp_name)){
			      @move_uploaded_file($tmp_name,$path)
			      or die("Impossibile spostare il file $fname con tmp_name: $tmp_name in: $path");
			}
			
			//converto immagini non in .jpg
			$path = self::convert2jpg($path,$mime,$owner);
			//ridimensiono le immagini oversize
			self::resize($path) or die("errore durante il resize dell'immagine");
			
			//restituisce un oggetto resource
			$photo= new Resource($owner,$path,Resource::PHOTO);
			//TODO carica l'oggetto resource nel db
			return $photo;
		}
		
		function addDescription($rsID, $description){
			//TODO
		}
		
		/**
		* Controlla se l'immagine è da ridimensioanre e ridimensiona la foto sovrascrivendo l'immagine sorgente
		* @param source: il percorso dell'imamgine in formato .jpg
		* @eturn:true se è stata ridimensionata, false se ci sono stati errori
		*/
		function resize($source){
			list($width,$height) = getimagesize($source);
			if($width>WIDTH || $height>HEIGHT){
				$image = NULL;
				if (@imagetypes() & IMG_JPG)
					 $image = @imagecreatefromjpeg($source);
				if ($image == NULL)
				   return false;
				$new_height= HEIGHT;
				$new_width= WIDTH;
				$new_res = @imagecreatetruecolor($new_width, $new_height);
				if (!(@imagecopyresampled($new_res, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height)))
				   return false;
				imagejpeg($new_res, $source);
				imagedestroy($image);
				imagedestroy($new_res);
			return true;
			}
		}
		
		/**
		* Controlla se l'immagine è da convertire e converte l'immagine $source in formato .jpg
		* @param source: il percorso dell'imamgine
		* @param mime: il mime type dell'immagine
		* @param owner: il proprietario (serve per controllare se esistono immagini con stesso nome)
		* @return: il path aggiornato (stesso path ma estensione .jpg)
		*/
		function convert2jpg($source,$mime,$owner){
			if($mime != "image/jpeg"){
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
				$string=explode(".$est",$source); //$string[0] è path senza estensione
				$parts=explode("/",$string[0]); //parts[3] è il nome del file senza estensione
				$path=self::generatePath($owner,$parts[3] . ".jpg"); //se nomeimmagine.jpg esiste genera un'altro nome e path contiene il nuovo percorso
				imagejpeg($image,$path);
				//elimino il file sorgente
				unlink($source);
				imagedestroy($image);
			}else
				$path=$source;
			return $path;
		}
		
		/**
		* crea le cartelle necessarie per lo storage delle foto 
		* @param owner: nickname del proprietario
		* @param fname: nome dell'imamgine
		* @return: percorso per il savataggio delle immagini per l'utente richiedente
		*/
		function createUserDirectory($owner,$fname){
			if(file_exists(UP_DIR."uploads/$owner")){
				if(file_exists(UP_DIR."uploads/$owner/" . date("dmy"))){
					$path= self::generatePath($owner,$fname);
				}else{
					Mkdir(UP_DIR."uploads/$owner/". date("dmy"),0777);
					$path= self::generatePath($owner,$fname);     
				}
			}else{
				Mkdir(UP_DIR."uploads/$owner",0777);
				Mkdir(UP_DIR."uploads/$owner/". date("dmy"),0777);
				$path= self::generatePath($owner,$fname);
			}
			return $path;     
		}
		
		/**
		*Genera un path relativo per il salvataggio dell'imamgine $fname
		*@param owner: nickname del proprietario
		*@param fname: nome dell'imamgine
		*@return: /uploads/nomeUtente/data/nomefile.jpg
		*Se il file esiste già genera n nome casuale aggiungendo la stringa "_X" prima dell'estensione
		*dove X è un indice che parte da 1 e viene incrementato ogni volta di 1 finchè non trova un nome libero
		*/
		function generatePath($owner,$fname){
			if(!file_exists(UP_DIR."uploads/$owner/". date("dmy") . "/$fname")){
				$path= "uploads/$owner/". date("dmy") . "/$fname";
			}else{
				//esiste già un file con quel nome
				$i=1;
				$string=explode(".", $fname); //$string[0] = file name, $string[1]= extension
				do{
					$editfname=$string[0]. "_" . $i . "." . $string[1];
					$i++;
				}while(file_exists(UP_DIR."$owner/". date("dmy") . "/$editfname"));
				$path= "uploads/$owner/". date("dmy") . "/$editfname";
			}
			return $path;
		}
	}

?>