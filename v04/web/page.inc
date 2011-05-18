<?php 

function writePageHeader($user, $ad) {
?>
<div id="header">
        
	<div id="widget">
		<div id="widgetLeft"></div>
		<div id="loginPlace">
		<?php if($user === false) {
			require_once 'page/UserPage.php';
			UserPage::showLoginForm();
		} else { ?>
			Benvenut<?php echo ($user->getGender() == "f" ? "a" : "o"); ?> 
			<b><?php echo $user->getNickname(); ?></b>
			<!-- temporaneo --> <a href="<?php echo FileManager::appendToRootPath("Logout");?>">Logout</a>
		<?php
		}?>
		</div>
	    <div id="widgetCenter">
		<img class="widgetImage" src="<?php echo FileManager::appendToRootPath("files/default/img/widgetClock.png");?>" />
		<img class="widgetImage" src="<?php echo FileManager::appendToRootPath("files/default/img/widgetMeteo.png");?>" />
		<div class="clear"></div>
	    </div>
	    <div id="search">
		<!-- TODO collegare a qualcosa -->
		<form name="search" action="" method="post">
		    <input class="text" type="text" name="search" value="cerca" onclick="document.search.search.value ='';">
		    <input id="magnify" type="submit" value="">
		</form>
	    </div>
	    <div id="widgetRight"></div>
	    <div class="clear"></div>
	</div>
	    x
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
		<a href="<?php echo FileManager::appendToRootPath(); ?>"><img src="<?php echo FileManager::appendToRootPath("files/default/img/logo.png"); ?>" alt="PublicHi!?"/></a>
	</div>
	    
	<div id="date">
		<?php echo date("l d F Y "); ?>
	</div>
	
	<div id="menuCategories">
	    <ul>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Cultura</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
			<li><a href="#">Cultura</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Finanza</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul  class="menuCategoriesSubLink clear">
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
			<li><a href="#">Finanza</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Motori</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			<li><a href="#">Motori</a></li>
			</ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Novit&#225</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
			<li><a href="#">Novit&#225</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Politica</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
			<li><a href="#">Politica</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Scienze</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
			<li><a href="#">Scienze</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Spettacoli</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
			<li><a href="#">Spettacoli</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Sport</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
			<li><a href="#">Sport</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Hobby</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
			<li><a href="#">Hobby</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Altro...</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
			<li><a href="#">Altro...</a></li>
		    </ul>
		</li>
		<li class="menuCategoriesLink">
		    <div><div class="menuCategoriesLinkTabLeft"></div><div><a href="#">Account</a></div><div class="menuCategoriesLinkTabRight"></div><div class="clear"></div></div>
		    <ul class="menuCategoriesSubLink clear">
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
			<li><a href="#">Account</a></li>
		    </ul>
		</li>
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
			You can find the code on <a href="http://github.com" target="_blank">github.com</a><br/>
			under the codename: <a href="http://github.com/Esisto/IoEsisto" target="_blank">IoEsisto</a>
		</p>
		<div class="clear"></div>
	</div>
</div>
<?php
}
?>