<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}?>

<p>I'm a bunch of settings</p>
<p>I'm looking for</p>
<ul>
	<?php
$keys = array_keys($settings);
var_dump($settings);
foreach ($keys as $setting) {
    $name = $settings[$setting]['display_name'];
    print("<li>{$name}</li>");
}
?>
</ul>