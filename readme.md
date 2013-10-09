---------------------------------------------
ADD IMG METABOX PLUGIN HOW TO:
---------------------------------------------

1. Install and activate the plugin
2. Add the code below outside your loop.
3. Go to Appearance -> Add Image Metabox
4. Select which post type you want the option to be shown on
5. You're good to go!!


---------------------------------------------
THE CODE:
---------------------------------------------

<?php
	$imgs = aim_get_the_images();
	var_dump($imgs);
?>


---------------------------------------------
PARAMETERS:
---------------------------------------------

The function takes two params (img sizes), the first one is full size
and the second one is the thumbnails size. Standard is 'full' and 'thumbnail'