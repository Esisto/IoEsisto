<?php 

function writePageHeader($user, $ad) {
?>
	<div id="headerBackground">
		<div id="header">
			<div id="logo">
				<a href="<?php echo FileManager::appendToRootPath(); ?>">
					<img src="<?php echo FileManager::appendToRootPath("files/default/img/logo.png"); ?>"/>
				</a>
			</div>
			<div id="login_place">
	<?php 
	if($user === false) {
		require_once 'user/UserPage.php';
		UserPage::showLoginForm();
	} else {
	?>
				<p>Benvenut<?php echo ($user->getGender() == "f" ? "a" : "o"); ?> 
					<b><?php echo $user->getNickname(); ?></b>
				</p>
	<?php
	}
	?>
			</div>
<?php
	if($ad)
		writeAD("header");
	if($user !== false) {
?>
			<div id="operationsMenu">
				<div id="operations">
					<div class="operationsMenuButton firstButton">
						<a href="<?php echo FileManager::appendToRootPath("Post/New");?>">Pubblica</a></div>
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Edit");?>">Modifica</a></div>
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Mail");?>">Posta</a></div>
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Profile"); ?>">Profilo</a></div>
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Favourites");?>">Preferiti</a></div>
					<div class="operationsMenuButton lastButton">
						<a href="<?php echo FileManager::appendToRootPath("Logout");?>">Logout</a></div>
					<div class="clear"></div>
				</div>	
			</div>
<?php } ?>
		</div>	
	</div>
	
	<div id="categoriesMenu">
		<div id="categories">
			<div class="categoriesMenuButton firstButton"><a href="<?php echo FileManager::appendToRootPath('Category/Cultura'); ?>">Cultura</a></div>
			<div class="categoriesMenuButton"><a href="<?php echo FileManager::appendToRootPath('Category/Web'); ?>">Web</a></div>
			<div class="categoriesMenuButton"><a href="#">Cronaca</a></div>
			<div class="categoriesMenuButton"><a href="#">Sport</a></div>
			<div class="categoriesMenuButton"><a href="#">Finanza</a></div>	
			<div class="categoriesMenuButton"><a href="#">Meteo</a></div>
			<div class="categoriesMenuButton"><a href="#">Foto Rep.</a></div>
			<div class="categoriesMenuButton"><a href="#">Video</a></div>
			<div class="categoriesMenuButton lastButton"><a href="#">Cerca</a></div>
			<div class="clear"></div>
		</div>
	</div>
<?php
}

function opendiv($class, $id) {
?>
<div<?php
	if(!is_null($class)) {
	?> class="<?php echo $class; ?>"<?php
	}
	if(!is_null($id)) {
	?> id="<?php echo $id; ?>"<?php	
	}
	?>>
<?php 
}

function closediv() {
?>
</div>
<?php
}

function writePlainText($text) {
	echo "<p>" . $text . "</p>";
}

function writeAD($class) {
	//return; //TODO test and remove it!
?>
<div class="ad ad_<?php echo $class; ?>"><?php
	//$p = PartnerManager::searchPartnerForClass($class); ?>
	<a href="<?php //echo $p->getExternalLink(); ?>" target="_blank">
		<img class="banner_<?php echo $class; ?>" src="<?php //echo $p->getBannerForClass($class); ?>" alt="<?php //echo $p->getBannerDescription(); ?>"/>
	</a>
</div>	
<?php
}

function writePageFooter($class, $ad) {
?>
<div id="footerBackground">
	<div id="references">
		<p>Powered by CLAD-Team
			<em>Queries made: 
				<?php
	$first = true;
	foreach(Session::getQueryCounter() as $type => $count) {
		if($first) $first = false;
		else echo ", ";
		echo $count . " " . $type;
	} ?>
			</em>
		</p>
	</div>
	<div id="footer" <?php echo (!is_null($class) ? 'class="' . $class . '"' : ''); ?>>
<?php 
	if($ad) {
		writeAD("footer");
	}
?>
		<p id="license">
			This site is under GNU-License<br/>
			You can find the code on <a href="http::/github.com" target="_blank">github.com</a><br/>
			under the codename: <a href="http://github.com/Esisto/IoEsisto" target="_blank">IoEsisto</a>
		</p>
		<div class="clear"></div>
	</div>
</div>
<?php
}
?>