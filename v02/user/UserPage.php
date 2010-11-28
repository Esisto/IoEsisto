<?php
require_once("user/UserManager.php");
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");

class UserPage {

	static function showLoginForm($error = null) {
		if($error==null && count($_POST) > 0) {
			if(isset($_POST["username"]))
				$data["username"] = $_POST["username"];
			else
				$error[] = "non c'è lo username";
			if(isset($_POST["password"]))
				$data["password"] = $_POST["password"];
			else
				$error[] = "password non presente";

			if(count($error) > 0) {
				$s = FileManager::appendToRootPath("error?e=" . $error[0]);
			} else {
				$dataFiltered = Filter::filterArray($data);
				if(($logged = UserManager::login($data)) === true) {
					$s = FileManager::appendToRootPath("");
				} else {
					require_once 'errors/errors.php';
					$s = FileManager::appendToRootPath("error?e=" . $logged /*$errors[$logged]*/);
				}
			}
			if(!headers_sent())
				header("location: " . $s);
			else {
				?>
				<script type="text/javascript">
					location.href = "<?php echo $s; ?>";
				</script>
				<?php
			}
		} ?>
<form name="login" action="<?php echo FileManager::appendToRootPath("Login"); ?>" method="post">
		<?php //<!-- show error messages -->
		if($error != null) {
			foreach ($error as $valore) {
				echo $valore . "<br/>";
			}
		}?>
<p><label for="username">Username:</label> <input type="text" name="username" value="" /></p>
<p><label for="password">Password:</label> <input type="password" name="password" value="" /></p>
<p><input class="button" type="submit" value="Login"></p></form>
			<?php
	}

	static function showSignInForm($error = null) {
		if($error==null && count($_POST) > 0) {
			$data=array();
			$error=array();
			$privatekey = CAPTCHA_PRIVATE;

			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];
			else
				$error[] = "non c'è il nickname";
			if(isset($_POST["check_password"]) && isset($_POST["password"])){
				if($_POST["password"] == $_POST["check_password"])
					$data["password"] = $_POST["password"];
				else
					$error[] = "le password non corrispondono";
			} else {
				$error[] = "password non presente";
			}
			if(isset($_POST["email"]))
				$data["email"] = $_POST["email"];
			else
				$error[] = "non c'è l'email";

			/* check reCHAPTCHA response */
			require_once('recaptchalib.php');
			$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
				$error[] = "Il reCAPTCHA non è stato inserito correttamente. Prova di nuovo";

				if(count($error) > 0) {
					self::showSignInForm($error);
				} else {
					$dataFiltered= Filter::filterArray($data);
					$user = UserManager::createUser($dataFiltered);
					self::showProfile($user);
				}
			}
		} else {
			$POST_data = count($_POST) > 0; ?>
<script type="text/javascript">
	//change reCAPTCHA theme 
	var RecaptchaOptions = {theme : 'clean'};
</script>
<form name="signIn" action="" method="post"><!-- show error messages --> <?php
			if( $error != null) {
				foreach ($error as $valore) {
					echo "$valore<br>";
				}
			}
	?>
Nickname: <input type="text" name="nickname" value="<?php if($POST_data) echo $_POST["nickname"]; ?>" /><br>
Password: <input type="password" name="password" value="" /><br>
Check Password: <input type="password" name="check_password" value="" /><br>
Email: <input type="text" name="email" value="<?php if($POST_data) echo $_POST["email"]; ?>" /><br>
<!-- show reCAPTCHA --> <?php
require_once('recaptchalib.php');
$publickey = CAPTCHA_PUBLIC;
echo recaptcha_get_html($publickey);
?> <input type="submit" value="Sign In"></form>
<?php
		}
	}

	static function showProfile($user) {
		$my_profile = $user->getID() == Session::getUser()->getID();
		?>
<div class="userProfile" id="<?php echo $user->getID(); ?>">
<div class="user_avatar">Avatar: <?php echo Filter::decodeFilteredText($user->getAvatar()); ?></div>
<div class="user_nickname">Nickname: <?php echo Filter::decodeFilteredText($user->getNickname()); ?></div>
<div class="user_name">Name: <?php echo Filter::decodeFilteredText($user->getName()); ?></div>
<div class="user_surname">Surname: <?php echo Filter::decodeFilteredText($user->getSurname()); ?></div>
<div class="user_birthday">Birthday: <?php echo date('d-m-Y', $user->getBirthday()); ?></div>
<div class="user_birthplace">Birthplace: <?php echo Filter::decodeFilteredText($user->getBirthplace()); ?>
<!-- TODO: geolocate --></div>
<div class="user_email">Email: <?php echo Filter::decodeFilteredText($user->getEMail()); ?></div>
<div class="user_gender">Gender: <?php
	if($user->getGender() == "m")
		echo 'Male';
	else
		echo 'Female';
?></div>
<div class="user_hobbies">Hobbies: <?php echo $user->getHobbies(); ?></div>
<div class="user_job">Job: <?php echo Filter::decodeFilteredText($user->getJob()); ?></div>
<div class="user_livingPlace">Living Place: <?php echo Filter::decodeFilteredText($user->getLivingPlace()); ?>
<!-- TODO: geolocate --></div>
</div>
<?php
	}

	static function showEditProfileForm($user, $error = null) {
		if($error==null && count($_POST) > 0) {  /* information already insered  */
			$data=array();
			$error=array();
			/* check the value of the fields NON NULL on database */
			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];
			else
				$error[] = "non c'è il nickname";
			if(isset($_POST["current_password"])){
				if ($user->getPassword() == Filter::encodePassword($_POST["current_password"])){
					if (isset($_POST["check_password"]) && isset($_POST["new_password"])){
						if ($_POST["new_password"] == $_POST["check_password"])
							$data["password"] = $_POST["new_password"];
						else
							$error[] = "le password non corrispondono";
					}
				}else{
					$error[] = "password non corretta";
				}
			} else {
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
				if ($_POST["gender"] == "male")
					$data["gender"] = "m";
				else
					$data["gender"] = "f";
			}
			if(isset($_POST["nickname"]))
				$data["nickname"] = $_POST["nickname"];
			if(isset($_POST["job"]))
				$data["job"] = $_POST["job"];
			if(isset($_POST["birthplace"]))
				$data["birthplace"] = $_POST["birthplace"];
			if(isset($_POST["livingPlace"]))
				$data["livingPlace"] = $_POST["livingPlace"];
			if(isset($_POST["hobbies"]))
				$data["hobbies"] = $_POST["hobbies"];

			/* show error message or apply changes and show the profile page updated*/
			if(count($error) > 0) {
				self::showEditProfileForm($user, $error);
				return;
			} else {
				$dataFiltered= Filter::filterArray($data);
				UserManager::editUser($user, $dataFiltered);
				self::showProfile($user);
			}
		} else {
			/*show form with user's information
			 POST_data == true  -> user insered information in the form with error, the form will be reloaded with these information
			 POST_data == false -> first time user view the page, the form will be loaded with db information */
			$POST_data = count($_POST) > 0;
			?>
<form name="editProfile" action="" method="post"><!-- show error messages --> <?php
			if( $error != null) {
				foreach ($error as $valore) {
					echo "$valore<br>";
				}
			}?>
<div class="userProfile" id="<?php echo $user->getID(); ?>">
Avatar: <input type="text" name="avatar" value="<?php
			if(!$POST_data) echo Filter::decodeFilteredText($user->getAvatar());
			else echo $_POST["avatar"]; ?>" /> <br>
Nickname*: <input type="text" name="nickname" value="<?php
			if (!$POST_data) echo Filter::decodeFilteredText($user->getNickname());
			else echo $_POST["nickname"]; ?>" /> <br>
Current Password*: <input type="password" name="current_password" value="" /> <br>
New Password: <input type="password" name="new_password" value="" /> <br>
Check Password: <input type="password" name="check_password" value="" />
<br>
Name: <input type="text" name="name" value="<?php
			if (!$POST_data) echo Filter::decodeFilteredText($user->getName());
			else echo $_POST["name"]; ?>" /> <br>
Surname: <input type="text" name="surname" value="<?php
			if (!$POST_data) echo Filter::decodeFilteredText($user->getSurname());
			else echo $_POST["surname"]; ?>" /> <br>
email: <input type="text" name="email" value="<?php
			if (!$POST_data) echo Filter::decodeFilteredText($user->getEMail());
			else echo $_POST["email"]; ?>" /> <br>
Gender: <label for="male">Male</label><input type="radio" name="gender" value="male" <?php
			if ($user->getGender() == "m")
			echo 'checked="checked"'; ?> />
		<label for="female">Female</label>
		<input type="radio" name="gender" value="female" <?php
			if ($user->getGender() == "f")
			echo 'checked="checked"'; ?> /><br>
Job: <input type="text" name="job" value="<?php
			if (!$POST_data) echo Filter::decodeFilteredText($user->getJob());
			else echo $_POST["job"]; ?>" /> <br>
<p>Birthday: <br><?php
			if ($POST_data == false) {
				$birthday_year = date('Y', $user->getBirthday());
				$birthday_month = date('m', $user->getBirthday());
				$birthday_day = date('d', $user->getBirthday());
			} else {
				$birthday_year = $_POST["birthday_year"];
				$birthday_month = $_POST["birthday_month"];
				$birthday_day = $_POST["birthday_day"];
			}?>
Year: <input type="text" name="birthday_year" value="<?php echo $birthday_year ?>" /><br>
Month: <input type="text" name="birthday_month" value="<?php echo $birthday_month ?>" /><br>
Day: <input type="text" name="birthday_day" value="<?php echo $birthday_day ?>" /><br></p>
Birthplace: <input type="text" name="birthplace" value="<?php
		if (!$POST_data) echo Filter::decodeFilteredText($user->getBirthplace());
		else echo $_POST["birthplace"]; ?>" />
<br>
<!-- TODO: geolocate -->
Living Place: <input type="text" name="livingPlace" value="<?php
		if (!$POST_data) echo Filter::decodeFilteredText($user->getLivingPlace());
		else echo $_POST["livingPlace"]; ?>" /> <br>
<br>
<!-- TODO: geolocate-->
<?php
if (!$POST_data) 
	$hobbies = $user->getHobbies();
else
	$hobbies = $_POST["hobbies"];
?> Hobbies: <br><textarea cols="50" rows="4" name="hobbies"><?php echo $hobbies ?></textarea><br>
<input type="submit" value="Edit"></div>
</form>
<?php
		}
	}

	/**
	 * @deprecated
	 * Enter description here ...
	 */
	function addFollow() {
	}

	function addFeedback() {
		// TODO
	}

	/**
	 * @deprecated
	 * Enter description here ...
	 */
	function removeFeedback() {
	}

}
