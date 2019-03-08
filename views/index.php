<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); } ?>

<p>I'm a bunch of settings</p>
<p>I'm looking for</p>
<ul>
	<?php
		$keys = array_keys($settings);
		foreach ($keys as $setting) { 
			print("<li>{$setting}</li>"); 
		} 
	?>
</ul>