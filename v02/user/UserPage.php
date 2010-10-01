<?php
require_once("user/UserManager.php");
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");

class UserPage {
	
	function showLoginForm($error = null){
		if($error==null && count($_POST) > 0){
			if(isset($_POST["username"]))
				$data["username"] = $_POST["username"];
			else
				$error[] = "non c'è lo username";
			if(isset($_POST["password"]))
				$data["password"] = $_POST["password"];
			else
				$error[] = "password non presente";
			
			if(count($error) > 0) {
				self::showLoginForm($error);
			}else{
				$dataFiltered= Filter::filterArray($data);
				UserManager::login($data["username"], $data["password"]);
			}
		} else { ?>
			<form name="login" action="" method="post">
				<!-- show error messages -->
				<?php if( $error != null)
				foreach ($error as $valore) {
					echo "$valore<br>";
				}?>
				Username: <input type="text" name="username" value="" />
				Password: <input type="password" name="password" value="" />
				<input type="submit" value="Login">
			</form>
			<?php
		}
	}

	function showSignInForm($error = null){
		if($error==null && count($_POST) > 0){ 
			$data=array();
			$error=array();

			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];
			else
				$error[] = "non c'è il nickname";
			if(isset($_POST["check_password"]) && isset($_POST["password"])){
				if ($_POST["password"] == $_POST["check_password"])
					$data["password"] = $_POST["password"];
				else
					$error[] = "le password non corrispondono";
			}else{
				$error[] = "password non presente";
			}
			if(isset($_POST["email"]))
				$data["email"] = $_POST["email"];
			else
				$error[] = "non c'è l'email";
			
			if(count($error) > 0) {
				self::showSignInForm($error);
			}else{
				$dataFiltered= Filter::filterArray($data);
				$user = UserManager::createUser($data);
				self::showProfile($user);
			}				
		} else { 
			$POST_data = count($_POST) > 0; ?>
			<form name="signIn" action="" method="post">
				<!-- show error messages -->
				<?php if( $error != null)
				foreach ($error as $valore) {
					echo "$valore<br>";
				}?>
				Nickname: <input type="text" name="nickname" value="
				<?php
					if(!$POST_data) echo $_POST["nickname"];
				?>" /><br>
				Password: <input type="password" name="password" value="" /><br>
				Check Password: <input type="password" name="check_password" value="" /><br>
				email: <input type="text" name="email" value="
				<?php
					if(!$POST_data) echo $_POST["email"];
				?>" /><br>
				<input type="submit" value="Sign In">
			</form>
			<?php
		}
	}


	function showProfile($user){
	?>
	<div class="userProfile" id="<?php echo $user->getID(); ?>">
		<div class="user_avatar"><?php echo Filter::decodeFilteredText($user->getAvatar()); ?></div>
		<div class="user_nickname">Nickname: <?php echo Filter::decodeFilteredText($user->getNickname()); ?></div>
		<div class="user_password">Password: <?php echo Filter::decodeFilteredText($user->getPassword()); ?></div>
		<div class="user_name">Name: <?php echo Filter::decodeFilteredText($user->getName()); ?></div>
		<div class="user_surname">Surname: <?php echo Filter::decodeFilteredText($user->getAvatar()); ?></div>
		<div class="user_birthday">
			Birthday: 
			<?php $data = date('d-m-Y', $user->getBirthday()); 
			echo $data; ?>				
		</div>
		<div class="user_birthplace">
			Birthplace: <?php echo Filter::decodeFilteredText($user->getBirthplace()); ?>
			<!-- TODO: geolocate -->
		</div>
		<div class="user_email">email: <?php echo Filter::decodeFilteredText($user->getEMail()); ?></div>
		<div class="user_gender">
			Gender: <?php
			if($user->getGender() == "m")
				echo 'Male';
			else
				echo 'Female';
			?>
		</div>
		<div class="user_hobbies">
			<?php
			$hobbies = explode(",", $user->getHobbies());
			echo $hobbies;
			?>
		</div>
		<div class="user_job">Job: <?php echo Filter::decodeFilteredText($user->getJob()); ?></div>
		<div class="user_livingPlace">
			Living Place: <?php echo Filter::decodeFilteredText($user->getLivingPlace()); ?>
			<!-- TODO: geolocate -->
		</div>
	</div>
	<?php
	}
	
	function showEditProfileForm($user, $error = null){
		if($error==null && count($_POST) > 0){  /* information already insered  */
			$data=array();
			$error=array();
			/* check the value of the fields NON NULL on database */
			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];
			else
				$error[] = "non c'è il nickname";
			if(isset($_POST["current_password"]) && isset($_POST["check_password"]) && isset($_POST["new_password"])){
				// if current_password = password del db
				if ($_POST["new_password"] == $_POST["check_password"])
					$data["password"] = $_POST["password"];
				else
					$error[] = "le password non corrispondono";
			}else{
				$error[] = "password non presente";
			}
			if(isset($_POST["email"]))
				$data["email"] = $_POST["email"];
			else
				$error[] = "non c'è l'email";
				
			/* check other fields */	
			if(isset($_POST["birthday_year"]) && isset($_POST["birthday_month"]) && isset($_POST["birthday_day"])){
				$birthday_timestamp= mktime(0,0,0,$_POST["birthday_month"],$_POST["birthday_day"],$_POST["birthday_year"]);
				$data["birthday"] = $birthday_timestamp;
			} else {
				$error[] = "inserisci una data completa di giorno, mese e anno";
			}
			if(isset($_POST["avatar"]))
				$data["avatar"] = $_POST["avatar"];
			if(isset($_POST["name"]))
				$data["name"] = $_POST["name"];
			if(isset($_POST["surname"]))
				$data["surname"] = $_POST["surname"];
			if(isset($_POST["gender"])){
				if ($_POST["gender"] == "Male")
					$data["gender"] = $_POST["m"];
				else
					$data["gender"] = $_POST["f"];
			}
			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];	
			if(isset($_POST["job"]))
				$data["job"] = $_POST["job"];	
			if(isset($_POST["birthplace"]))
				$data["birthplace"] = $_POST["birthplace"];
			if(isset($_POST["living_place"]))
				$data["living_place"] = $_POST["living_place"];
			if(isset($_POST["hobbies"]))
				$data["hobbies"] = $_POST["hobbies"];	
				
			/* show error message or apply changes and show the profile page updated*/
			if(count($error) > 0) {
				self::showEditProfileForm($user, $error);
			}else{
				$dataFiltered= Filter::filterArray($data);
				UserManager::editUser($user, $dataFiltered);
				self::showProfile($user);
			}
		}else {
			/*show form with user's information
			POST_data == true  -> user insered information in the form with error, the form will be reloaded with these information 
			POST_data == false -> first time user view the page, the form will be loaded with db information */				
			$POST_data = count($_POST) > 0;
			?>
			<form name="editProfile" action="" method="post">
				<!-- show error messages -->
				<?php if( $error != null)
				foreach ($error as $valore) {
					echo "$valore<br>";
				}?>
				<div class="userProfile" id="<?php echo $user->getID(); ?>"> 
					Avatar: <input type="text" name="avatar" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getAvatar());
						else echo $_POST["avatar"];
					?>"/> <br>
					Nickname: <input type="text" name="nickname" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getNickname());
						else echo $_POST["nickname"];
					?>"/> <br>
					Current Password: <input type="password" name="current_password" value="" /> <br>
					New Password: <input type="password" name="new_password" value="" /> <br>
					Check Password: <input type="password" name="check_password" value=""/> <br>
					Name: <input type="text" name="name" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getName());
						else echo $_POST["name"];
					?>"/> <br>
					Surname: <input type="text" name="surname" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getSurname());
						else echo $_POST["surname"];
					?>"/> <br>
					email: <input type="text" name="email" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getEMail());
						else echo $_POST["email"];
					?>"/> <br>
					
					Gender: <label for="Male">Male</label><input type="radio" name="gender" value="Male" 
					<?php
					if ($user->getGender() == "m")
						echo 'checked="checked"';	
					?> /><label for="Female">Female</label><input type="radio" name="gender" value="Female" 
					<?php
					if ($user->getGender() == "f")
						echo 'checked="checked"';
					?> /><br>
					Job: <input type="text" name="job" value="
					<?php
						if (!$POST_data) echo Filter::decodeFilteredText($user->getJob());
						else echo $_POST["job"];
					?>"/> <br>
					Birthday:
					<?php if ($POST_data == false) {
						$birthday_year = date('Y', $user->getBirthday());
						$birthday_month = date('m', $user->getBirthday());
						$birthday_day = date('d', $user->getBirthday());
					}else{
						$birthday_year = $_POST["birthday_year"];
						$birthday_month = $_POST["birthday_month"];
						$birthday_day = $_POST["birthday_day"];
					}?>
					<input type="text" name="birthday_year" value="<?php echo $birthday_year ?>"/>
					<input type="text" name="birthday_month" value="<?php echo $birthday_month ?>"/>
					<input type="text" name="birthday_day" value="<?php echo $birthday_day ?>"/> <br>
					Birthplace: <input type="text" name="birthpalce" value="<?php echo Filter::decodeFilteredText($user->getBirthplace()); ?>" /> <br> <!-- TODO: geolocate -->
					Living Place: <input type="text" name="living_palce" value="<?php echo Filter::decodeFilteredText($user->getLivingPlace()); ?>" /> <br> <!-- TODO: geolocate-->
					<?php if (!$POST_data) 
						$hobbies = explode(",", $user->getHobbies());
					else
						$hobbies = $_POST["hobbies"];
					?>
					Hobbies: <input type="text" name="hobbies" value="<?php echo $hobbies ?>" /> <br>
					<input type="submit" value="Edit">
				</div>
			</form>
			<?php
		}
	}
	

	function addFollow(){
		// TODO	
	}

	function addFeedback(){
		// TODO	
	}

	function removeFeedback(){
		// TODO	
	}
	
}