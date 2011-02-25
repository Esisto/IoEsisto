<?php
require_once("dataobject/Resource.php");
require_once("settings.php");
require_once("dao/ResourceDao.php");

//Developer Key: AI39si4EINGln7_eb54GuuotT8Nnc3UyNzth0jRkuiiDYFW40nHp6xCzESfc7dRuIYG7fatRX5boymYR49CLQnDshZGIKYQkUg

class ResourceManager {
	
	/**
	* Aggiunge una risorsa di tipo "photo" al sistema.
	* @param fname: nome della foto da caricare
	* @param owner: nickname del proprietario
	* @param tmp_name: nome temporaneo attribuito dal server all'imamgine
	* @return: un oggetto resource di tipo photo completo di ogni informazione.
	*/
	function uploadPhoto($fname,$owner,$ownerID,$tmp_name,$mime){
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
		
		$p = self::createResource($ownerID,$path,'photo');
		return $p;
	}
	
	/**
	* Tronca a 50 caratteri il nome di un file mantenendone l'estensione e filtra alcuni caratteri
	* @param fname: nome da troncare
	* @return: il nome troncato
	*/
	static function editFileName($fname){
		if(strlen($fname) > 50){
			$string=explode(".", $fname); //$string[0] = file name, $string[1]= extension
			$fname = substr($fname,0,49) . "." . $string[1];	
		}
		echo "<br>" . $fname;
		$fname = str_replace('?','',$fname);
		$fname = str_replace('#','',$fname);
		$fname = str_replace(' ','_',$fname);
		echo "<br>" . $fname;
		return trim($fname);
	}
	
	static function createResource($ownerID, $path, $type) {
		if($type=='photo')
			$resource = new Resource($ownerID,$path,Resource::PHOTO);
		else if($type=='video')
			$resource = new Resource($ownerID,$path,Resource::VIDEO);
		else
			throw new Exception("L'oggetto da creare non ha un tipo valido");
		$resourcedao = new ResourceDao();
		$r = $resourcedao->save($resource);
		return $r;
	}
	
	static function editResource($resourceID, $description=null, $tags=null, $user) {
		$resource = self::loadResource($resourceID);
		if($description != null)
			$resource->setDescription($description);
		if($tags != null)
			$resource->setTags($tags);
		$resourcedao = new ResourceDao();
		return $resourcedao->update($resource,$user);
	}
	
	static function deleteResource($resource) {
		$resourcedao = new ResourceDao();
		return $resourcedao->delete($resource); 
	}
	
	static function loadResource($id) {
		$resourcedao = new ResourceDao();
		return $resourcedao->load($id);
	}
	
	static function resourceExists($resource) {
		$resourcedao = new ResourceDao();
		return $resourcedao->exists($resource);
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
		}
		return true;
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
	*Se il file esiste già genera n nome casuale aggiungendo un numero in fondo al nome del file
	*/
	function generatePath($owner,$fname){
		if(!file_exists(UP_DIR."uploads/$owner/". date("dmy") . "/$fname")){
			$path= "uploads/$owner/". date("dmy") . "/$fname";
			return $path;
		}else{
			$i=1;
			do{
				$string=explode(".", $fname); //$string[0] = file name, $string[1]= extension
				$editedfname=$string[0]. $i . "." . $string[1];
				$i++;
			}while(file_exists(UP_DIR."uploads/$owner/". date("dmy") . "/$editedfname"));
			$path="uploads/$owner/". date("dmy") . "/$editedfname";
			return $path;
		}
	}
}

?>