<?php 

function writePageHeader($user, $ad, $nologin) {
?>
<div id="header">
        
	<div id="widget">
		<div class="left" id="widgetLeft"></div>
		<div class="left" id="widgetCenter">
			<div class="left" id="widgetCenterLeft">
			<?php
			if(!$nologin){
				if($user === false) {
					require_once 'page/UserPage.php';
					UserPage::showLoginForm();
				} else { ?>
					<span class="welcome">Benvenut<?php echo ($user->getGender() == "f" ? "a" : "o"); ?> <b><?php echo $user->getNickname(); ?></b></span>
					<span class="welcome"><a href="<?php echo FileManager::appendToRootPath("Logout");?>">Logout</a></span>
				<?php }
			}
			?>
			</div>
			<div class="right" id="WidgetCenterRight">
				<?php
				//require_once 'page/SearchPage.php';
				//SearchPage::showDefaultSearchForm();
				?>
				<div id="wsearch">
					<form class="right" action="" method="post">
						<p><input type="text" name="search" value="cerca"/></p>
						<p><input type="submit" value=""/></p>
					</form>
				</div>
				<div id="meteo"><img class="right widgetImage" src="<?php echo FileManager::appendToRootPath("files/default/img/widgetMeteo.png");?>" alt="widgetMeteo" title="widgetMeteo"/></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="left" id="widgetRight"></div>
		<div class="clear"></div>
	</div>
	
	<ul id="menuCity">
	    <li><a href="#"> Roma </a></li>
	    <li><a href="#"> Milano </a></li>
	    <li><a href="#"> Torino </a></li>
	    <li><a href="#"> Napoli </a></li>
	    <li><a href="#"> Bologna </a></li>
	    <li><a href="#"> Firenze </a></li>
	    <li><a href="#"> Padova </a></li>
	    <li><a href="#"> Venezia </a></li>
	    <li><a href="#"> Verona </a></li>
	    <li><a href="#"> Bari </a></li>
	    <li><a href="#"> Genova </a></li>
	    <li><a href="#"> Como </a></li>
	    <li><a href="#"> Bergamo </a></li>
	    <li><a href="#"> Brescia </a></li>
	    <li><a href="#"> Varese </a></li>
	</ul>
			
	<div id="logo">
		<a href="<?php echo FileManager::appendToRootPath(); ?>"><img src="<?php echo FileManager::appendToRootPath("files/default/img/logo.png"); ?>" alt="PublicHi!?" title="PublicHi!?"/></a>
	</div>
	    
	<div id="date">
		<?php echo date("l d F Y "); ?>
	</div>
		
	<div id="menuCategories">
	    <ul>
		<?php
		require_once("manager/CategoryManager.php");
		$cat = CategoryManager::loadAllCategories();
		foreach ($cat as $category){?>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="<?php echo FileManager::appendToRootPath("Category/".$category->getName()); ?>"><?php echo $category->getName(); ?></a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<?php foreach ($category->getChildren() as $child)
			echo "<li><a href='" . FileManager::appendToRootPath("Category/".$child->getName()) . "'>" . $child->getName() . "</a></li>";
			?>
		    </ul>
		</li>
		<?php } ?>
	
		<?php if($user){ ?>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Account</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="<?php echo FileManager::appendToRootPath("Post/New");?>">Pubblica</a></li>
			<li><a href="<?php echo FileManager::appendToRootPath("Edit");?>">Modifica</a></li>
			<li><a href="<?php echo FileManager::appendToRootPath("Mailbox");?>">Posta</a></li>
			<li><a href="<?php echo FileManager::appendToRootPath("Profile");?>">Profilo</a></li>
			<li><a href="<?php echo FileManager::appendToRootPath("Favourites");?>">Preferiti</a></li>
			<li><a href="<?php echo FileManager::appendToRootPath("Logout");?>">Logout</a></li>
		    </ul>
		</li>
		<?php } else { ?>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="<?php echo FileManager::appendToRootPath("Signin");?>"">Registrati</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		</li>
		<?php } ?>
	    </ul>
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
<div id="footer">
	<div id="references">
		<div class ="left">Powered by CLAD-Team</div>
		<div class="right">
		Queries made: 
	<?php
	$first = true;
	foreach(Session::getQueryCounter() as $type => $count) {
		if($first) $first = false;
		else echo ", ";
		echo $count . " " . $type;
	} ?>
		</div>
	</div>
	
	<div id="footerAdvertise" <?php echo (!is_null($class) ? 'class="' . $class . '"' : ''); ?>>
	<?php 
		if($ad) {
			writeAD("footerAdvertise");
	}
	?>	
	</div>

	<div id="copyright">
		<div class="left"></div>
		<div id="copyrightMenu">
			<ul>
				<li><a href="<?php echo FileManager::appendToRootPath("Copyright"); ?>">Copyright</a></li>
				<li><a href="<?php echo FileManager::appendToRootPath("Conditions"); ?>">Termini e Condizioni</a></li>
				<li><a href="<?php echo FileManager::appendToRootPath("Contacts"); ?>">Contatti</a></li>
				<li><a href="<?php echo FileManager::appendToRootPath("Privacy"); ?>">Privacy</a></li>
				<li><a href="<?php echo FileManager::appendToRootPath("Rules"); ?>">Regole Guida</a></li>
			</ul>
		</div>
		<div class="right"></div>
	</div>
</div>
<?php
}
?>