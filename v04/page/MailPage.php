<?php
 
require_once("session.php");
require_once("manager/MailManager.php"); //TODO da elimenare la path con una define 

class MailPage {
	
	static function showEditDirectoryForm($directory, $error = array()) {
		$user = Session::getUser();
		if ( count($_POST) == 0 ) {
			if ( $user != false ) {						
				$directoryarray = MailManager::loadUsersDirectories($user->getID());
			?>
			<form name="editdirectory" action="" method="post">
				<fieldset>
					<legend>New Directory</legend>	
					Name*: <input type="text" name="name" /> 
					<input type="submit" name="create" value="create" /> <?php if (isset($error["name"]) ) echo $error["name"]; ?>				
				</fieldset>
				<fieldset>
					<legend>Delete Directory</legend>
					Check the directory that you wish delete<br />
					<script type="text/javascript">
						var nomiOriginali;						

						function clickRename(event)
						{
							var id =  parseInt(event.target.id);							
							document.getElementById( id + "name" ).removeAttribute('readOnly');
							document.getElementById( id + "confirm").disabled = false;
							document.getElementById( id + "cancel").disabled = false;
							event.target.disabled = true;
						}

						function clickCancel(event)
						{
							var id =  parseInt(event.target.id);
							document.getElementById( id + "name" ).setAttribute('readOnly','readOnly');
							document.getElementById( id + "confirm" ).disabled = true;
							document.getElementById( id + "rename" ).disabled = false;
							event.target.disabled = true;
						}
					</script>	
					<?php
						
						
					if ( is_array($directoryarray))
						foreach( $directoryarray as $directory ) {
							echo "<input type=\"checkbox\" name=\"exdirar[]\" value=\"" . $directory->getID() ."\" />
								<input type=\"text\" readonly=\"readonly\" id=\"" . $directory->getID() . "name\" name=\"" .  $directory->getName() ."\" value=\"" .  $directory->getName() ."\" />
								<button type=\"button\" id=\"" .  $directory->getID() . "rename\" onclick=\"clickRename(event);\" >rename</button>
								<input type=\"submit\" id=\"" . $directory->getID() . "confirm\" name=\"" .  $directory->getName() . "\" value=\"confirm\" disabled=\"disabled\" />
								<button id=\"" . $directory->getID() . "cancel\" disabled=\"disabled\"  onclick=\"clickCancel(event);\" />cancel</button><br />";
							}
					?>
					<input type="submit" name="delete" value="delete" />				
				</fieldset>
			</form>
			<?php
			}
		}
		else {
			$error = array();			

			if ( isset($_POST["create"]) && $_POST["create"] == "create" )
			{
				if ( isset($_POST["name"]) && $_POST["name"] !== "" ) {
					MailManager::createDirectory($_POST["name"], $user->getID() );
					return true;
				}
				else {
					$error["name"] = "it's obbligatory";
					$_POST = array();
					self::showEditDirectoryForm( $error, $directory );	
				}
			}

			if ( isset($_POST["delete"]) && $_POST["delete"] == "delete" )
			{
				if ( $user != false ) {
					//loadDirectory(,$user);
					MailManager::deleteDirectory();
				}
			}
		}
	}
	
	static function showMail($mail) {
		//TODO da implementare
	}

	static function showShortMail($mail) {
		//TODO da implementare
	}
	
	static function showMoveToForm($mail) {
		//TODO da implementare
	}
	
	/**
	 * Se mail == null ne crea una nuova, se mail ha valore quella nuova risponderà a mail.
	 * @param Mail $mail è la mail a cui rispondere.
	 */
	static function showNewForm($error = null, $mail = null) {
		$user = Session::getUser();
		if ( count($_POST) == 0 )
		{
			if ( $mail == null ) {
				?>
				<form name="newmail" action="" method="post">
					<fieldset>
						<legend>New Mail</legend>
						<?php 
							if ( $user != false )
								echo "From: <input type=\"text\" name=\"from\" readonly=\"readonly\" value=\"" . $user->getNickName() . "\" />";
							else 
								echo "From: <input type=\"text\" readonly=\"readonly\" />";

							if (isset($error["from"]) )echo $error["from"];
						?><br />
						To*: <input type="text" name="to" /> <?php if (isset($error["to"]) )echo $error["to"]; ?><br />
						Subject: <input type="text" name="subject" /><br />
						Text* <?php if (isset($error["text"]) ) echo $error["text"]; ?><br />
						<textarea name="text" rows="8" cols="50"></textarea><br />
						<input type="submit" name="send" value="Send" />				
					</fieldset>
				</form>
				<?php
			}
		}
		else
		{	
			$error = array();

			if( isset($_POST["from"]) && $_POST["from"] !== "" )
				$data["from"] = $user->getID();
			else
				$error["from"] = "it's obbligatory!";

			if( isset($_POST["to"]) && $_POST["to"] !== "" )
				$data["to"] = $_POST["to"];
			else
				$error["to"] = "it's obbligatory!";

			if( isset($_POST["subject"]) )
				$data["subject"] = $_POST["subject"];
			
			if( isset($_POST["text"]) && $_POST["text"] !== "" )
				$data["text"] = $_POST["text"];
			else
				$error["text"] = "it's obbligatory!";

			if ( count($error) > 0 )
			{
				$_POST = array();				
				self::showNewForm($error,$mail);
			}			
			else
			{
				MailManager::createMail($data);
				return true;
			}
		}
	}
}

?>
