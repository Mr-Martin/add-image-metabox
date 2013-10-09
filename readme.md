---------------------------------------------
ADD IMG METABOX PLUGIN
---------------------------------------------
This is a revision of a plugin called "Multi Image Metabox" originally created by Willy Bahuaud (http://wordpress.org/plugins/multi-image-metabox/). Willy's plugin is awesome, but I felt it needed some improvements to fit my needs. Thats why I changed the code and added some functionality. I've also removed some functions that I felt was unnecessary.

What did I change/add?
- Added a functions to add unlimited new slides for each post type.
- Added a function to get the images in a nice array, to make it easier to use them.
- Added a title-, textarea- and link fields to each image slot.
- Made the delete button remove the slide you press delete on.
- I've also made the code structure more readable (imo).

---------------------------------------------
ADD IMG METABOX PLUGIN HOW TO
---------------------------------------------

1. Install and activate the plugin
2. Add the code below outside your loop.
3. Go to Appearance -> Add Image Metabox
4. Select which post type you want the option to be shown on
5. You're good to go!!


---------------------------------------------
THE CODE
---------------------------------------------

	<?php
		$imgs = aim_get_the_images();
		var_dump($imgs);
	?>


---------------------------------------------
PARAMETERS
---------------------------------------------

The function takes two params (img sizes), the first one is full size
and the second one is the thumbnails size. Standard is 'full' and 'thumbnail'
