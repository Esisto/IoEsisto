<?php 

function writePageHeader($ad) {
?>
	<div id="headerBackground">
		<div id="header">
			<div id="login_place">
	<?php 
	if(Session::getUser() === false) {
		require_once 'user/UserPage.php';
		UserPage::showLoginForm();
	} else {
	?>
				<p>Benvenut<?php echo (Session::getUser()->getGender() == "f" ? "a" : "o"); ?> 
					<?php echo Session::getUser()->getNickname(); ?>
				</p>
				<p><a href="<?php echo FileManager::appendToRootPath("Logout"); ?>">Logout</a></p>
	<?php
	}
	?>
			</div>
<?php 
	if($ad)
		writeAD("header");
	if(Session::getUser() !== false) {
?>
			<div id="operationsMenu">
				<div id="operations">
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Post/New");?>">Nuova notizia</a></div>
					<div class="operationsMenuButton">
						<a href="<?php echo FileManager::appendToRootPath("Post/New?type=VideoReportage");?>">Nuovo Video</a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton lastButton"><a href="#"> OPERATION </a></div>
					<div class="clear"></div>
				</div>	
			</div>
<?php } ?>
		</div>	
	</div>
	
	<div id="categoriesMenu">
		<div id="categories">
			<div class="categoriesMenuButton"><a href="<?php echo FileManager::appendToRootPath('Category/Cultura'); ?>">Cultura</a></div>
			<div class="categoriesMenuButton"><a href="<?php echo FileManager::appendToRootPath('Category/Web'); ?>">Web</a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>	
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton lastButton"><a href="#"> CATEGORY </a></div>
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
	
}

function writePageFooter($class) {
?>
<div id="footer">
<?php 
	if($ad) {
		writeAD("footer");
	}
?>
</div>
<?php
}
?>