<?php

/**
 *
 * Add the plugin option page to admin menu
 *
 **/
add_action('admin_menu', 'add_image_metabox_admin_page');
function add_image_metabox_admin_page() {
  add_theme_page('Add image Settings', 'Add image metabox', 'administrator', 'add_image_metabox_settings', 'add_image_metabox_admin_page_markup');
}


/**
 *
 * Register the settings
 *
 * This will save the option in the wp_options table as 'add_image_metabox_settings'
 * The third parameter is a function that will validate your input values
 *
 **/
add_action('admin_init', 'add_image_metabox_register_settings');
function add_image_metabox_register_settings() {
  register_setting('add_image_metabox_settings', 'add_image_metabox_settings', 'add_image_metabox_settings_validate');
}


/**
 *
 * Register stylesheet
 *
 **/
add_action('admin_init', 'add_image_metabox_stylesheet');
function add_image_metabox_stylesheet() {
	wp_enqueue_style( 'add_img_stylesheet', plugin_dir_url( __FILE__ ) . 'add-img-style.css', false, 'screen');
}


/**
 *
 * Validate inputs
 *
 * This function validates your input values.
 *
 * $args will contain the values posted in your settings form, you can validate
 * them as no spaces allowed, no special chars allowed or validate emails etc.
 *
 **/
function add_image_metabox_settings_validate($args) {
	// Enter validation here

  // Make sure you return the args
  return $args;
}


/**
 *
 * Admin notices
 *
 * Display the validation errors and update messages
 *
 **/
if(is_admin() && $_GET['page'] == 'add_image_metabox_settings') {
	add_action('admin_notices', 'add_image_metabox_admin_notices');
	function add_image_metabox_admin_notices() {
	  settings_errors();
	}
}


/**
 *
 * add_image_metabox_admin_page_markup
 *
 * This function handles the markup for your plugin settings page
 *
 **/
function add_image_metabox_admin_page_markup() { ?>
  <div class="wrap">

	  <?php screen_icon('themes'); ?>
		<h2>Add image metabox</h2>
		<p>This is a option page for the plugin: <strong>Add image metabox</strong>.</p>

	  <form action="options.php" method="post"><?php
	    settings_fields( 'add_image_metabox_settings' );
	    do_settings_sections( __FILE__ );

	    //get the older values, wont work the first time
	    $options = get_option( 'add_image_metabox_settings' ); ?>

	    <div class="add-img-mb-box">
	    	<div class="sidebar-name"><h3>Where do you want to display the options?</h3></div>
	    	<p>Here you can customize how the plugin should handle things and where to display the image metaboxes.</p>

	    
		    <div class="postbox">
					<div class="sidebar-name"><h3>Select your post types</h3></div>
					<div class="inside">
						<input name="add_image_metabox_settings[add_image_metabox_show_on_pages]" type="checkbox" name="pages" value="page" <?php echo (isset($options['add_image_metabox_show_on_pages']) && $options['add_image_metabox_show_on_pages'] != '') ? 'checked="checked"' : ''; ?>/> Pages<br>
						<input name="add_image_metabox_settings[add_image_metabox_show_on_posts]" type="checkbox" name="posts" value="post" <?php echo (isset($options['add_image_metabox_show_on_posts']) && $options['add_image_metabox_show_on_posts'] != '') ? 'checked="checked"' : ''; ?>/> Posts<br>


						<?php
							// Loop through every custom post type and add a input field
							$args = array(
							  'public'   => true,
							  '_builtin' => false
							);

							$types_obj = get_post_types($args, 'objects');
							$types = get_post_types($args);
							$html  = array();

							foreach($types as $type) {
								$name = $types_obj[$type]->labels->singular_name;

								$html[$type] = '<input name="add_image_metabox_settings[add_image_metabox_show_on_'.$type.']" type="checkbox" name="'.$type.'" value="'.$type.'" ';
								$html[$type] .= (isset($options['add_image_metabox_show_on_'.$type.'']) && $options['add_image_metabox_show_on_'.$type.''] != '') ? 'checked="checked"' : '';
								$html[$type] .= '/> '.$name.'<br>';
							}

							foreach($html as $input) {
								echo $input;
							}
						?>
					</div>
				</div>
			</div>

	    <input class="button button-primary button-large" type="submit" value="Save Changes" />
	  </form>
	</div>
<?php } ?>