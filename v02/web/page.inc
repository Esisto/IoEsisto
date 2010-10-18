<?php 

function writePageHeader($ad) {
?>
	<div id="headerBackground">
		<div id="header">
<?php 
	if($ad)
		writeAD("header");
?>
			<div id="operationsMenu">
				<div id="operations">
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="operationsMenuButton"><a href="#"> OPERATION </a></div>
					<div class="clear"></div>
				</div>	
			</div>
		</div>	
	</div>
	
	<div id="categoriesMenu">
		<div id="categories">
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>	
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
			<div class="categoriesMenuButton"><a href="#"> CATEGORY </a></div>
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
	echo $text;
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