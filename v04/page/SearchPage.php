<?php

class SearchPage {
	static function showDefaultSearchForm() {
	?>
	<div id="search">
		<form method="POST" >
			<input type="text" name="search" class="search_text" />
			<input type="submit" />
		</form>
	</div>
	<?php
	}
}
?>