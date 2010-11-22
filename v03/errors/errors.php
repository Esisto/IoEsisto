<?php
/* Questo file contiene i codici e la descrizione degli errori possibili nel sistema
 * I manager useranno questi codici per ritornare gli errori alle page e... 
 * le page useranno le descrizioni per mostrare all'utente la presenza di un errore */

static $errors = array(
			// UserManager
			UserManager::UM_NoUserError => "L'utente non è stato trovato.", //FIXME: DEBUG modificare in "L'utente o la password sono errati."
			UserManager::UM_NoPasswordError => "La password è errata.", //FIXME: DEBUG modificare in "L'utente o la password sono errati."
			UserManager::UM_NoSessionError => "Non è stato possibile creare una sessione valida, controlla Opzioni Internet e abilita i cookie.",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			"" => "",
			);

function showError($error) {
	echo "<p class='error'>" . $error . "</p>";
}
?>