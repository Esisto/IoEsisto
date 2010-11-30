<?php	
	class Resource {
		protected $ID;		//ID risorsa
		protected $path;	//percorso
		protected $type;	//tipo photo o video
		protected $description;	//descrizione
		protected $owner;	//proprietario
		
		function __construct($data) {
			
			if(isset($data["path"]))
				$this->setPath($data["path"]);
			if(isset($data["type"]))
				$this->setType($data["type"]);
			if(isset($data["description"]))
				$this->setDescription($data["description"]);
			if(isset($data["owner"]))
				$this->setOwner($data["owner"]);
			
		}
		
		function setPath($path){
			$this->path=$path;
			return $this;
		}
		
		function setType($type){
			$this->type=$type;
			return $this;
		}
		
		function setDescription($desc){
			$this->description=$desc;
			return $this;
		}
		
		function setOwner($owner){
			$this->owner=$owner;
			return $this;
		}
		
		function getID() {
			return $this->ID;
		}
		
		function getType() {
			return $this->type;
		}

		function getDescription() {
			return $this->description;
		}
		
		function getPath() {
			return $this->path;
		}
		
		function getOwner() {
			return $this->owner;
		}	
	
	}

?>