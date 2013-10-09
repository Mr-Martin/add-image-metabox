<?php
/*
Plugin Name: Add Image Metabox
Plugin URI: http://040.se
Description: Adds a image upload metabox
Version: 0.1
Author: Martin Nilsson
Author URI: http://040.se

This is a revision of a plugin called "Multi Image Metabox" originally created by Willy Bahuaud (http://wordpress.org/plugins/multi-image-metabox/). Willy's plugin is awesome, but I felt it needed some improvements to fit my needs. Thats why I changed the code and added some functionality. I've also removed some functions that I felt was unnecessary.

What did I change/add?
- Added a functions to add unlimited new slides for each post type.
- Added a function to get the images in a nice array, to make it easier to use them.
- Added a title-, textarea- and link fields to each image slot.
- Made the delete button remove the slide you press delete on.
- I've also made the code structure more readable (imo).

LICENSE:
This plugin is licensed under GPLv2, you can read about the license here: (http://wordpress.org/about/gpl/).
*/



/**
 *
 * Load the function file
 *
 **/
require_once('options/add-img-options.php');



/**
*
* Javascripts & CSS
*
* Loading all the css and js that i needed here
*
**/
function aim_add_css() {
  if(is_admin()) {
    wp_enqueue_style('add-img-mb-css', plugins_url('/add-img.css',__FILE__));
  }
}

function aim_add_js() {
  if(is_admin()) {
    wp_enqueue_script('add-img-mb-draggable-js', plugins_url('/add-img-draggable.js',__FILE__), array('jquery'));
    wp_enqueue_script('add-img-mb-js', plugins_url('/add-img.js',__FILE__), array('jquery'));
    wp_enqueue_script('jquery-ui-draggable');
  }
}

add_action('admin_init', 'aim_add_css');
add_action('admin_init', 'aim_add_js');



/**
*
* aim_list_my_image_slots()
*
* This function defines how many imageboxes there should be on
* each page. The function checks if there is any option for more
* than the standard 3 imageboxes. If there is, it will use this
* amount instead.
*
**/
function aim_list_my_image_slots() {
  global $post;
  $slideAmount = get_post_meta($post->ID, 'slide-amount', true);

  if($slideAmount) {
    $imageArray = array();
    $imageTextArray = array();
    for ($i=1; $i <= $slideAmount; $i++) { 
      $imageArray['image'.$i] = '_image' . $i;
      $imageTitleArray['image'.$i] = '_image' . $i;
      $imageTextArray['image'.$i] = '_image' . $i;
      $imageLinkArray['image'.$i] = '_image' . $i;
    }
  } else {
    $imageArray = array(
      'image1' => '_image1',
      'image2' => '_image2',
      'image3' => '_image3',
    );

    $imageTitleArray = array(
      'title_image1' => 'title_image1',
      'title_image2' => 'title_image2',
      'title_image3' => 'title_image3',
    );

    $imageTextArray = array(
      'text_image1' => 'text_image1',
      'text_image2' => 'text_image2',
      'text_image3' => 'text_image3',
    );

    $imageLinkArray = array(
      'link_image1' => 'link_image1',
      'link_image2' => 'link_image2',
      'link_image3' => 'link_image3',
    );
  }

  $slots = array(
    'imgs' => $imageArray,
    'titles' => $imageTitleArray,
    'texts' => $imageTextArray,
    'links' => $imageLinkArray
  );

  return $slots;
}


/**
*
* INITIALIZE
*
* This is where the plugin begin, it adds the meta box to every page
* and load all the necessary stuff.
*
**/
add_action('add_meta_boxes', 'aim_metabox');
function aim_metabox() {
  $cpts = get_option('add_image_metabox_settings');

  if($cpts) {
    foreach($cpts as $cpt) {
      add_meta_box(
        'add_img_metabox',
        __('Add images'),
        'aim_markup',
        $cpt,
        'normal',
        'core'
      );
    }
  }
}


/**
*
* MARKUP
*
* This functions handles all the markup
*
**/
function aim_markup($post) {
  $image_slots = aim_list_my_image_slots();
  $slideAmount = get_post_meta($post->ID, 'slide-amount', true);

  if($slideAmount) {
    $value = $slideAmount;
  } else {
    $value = '3';
  }

  wp_nonce_field( 'image-slide-save_'.$post->ID, 'image-slide-nonce');
  echo '<input type="hidden" name="slide-amount" class="slide-amount" value="'.$value.'" />';

  echo '<div id="droppable">';
  $z = 1;
  foreach($image_slots['imgs'] as $k=>$i) {
    $meta = get_post_meta($post->ID,$i,true);
    $title = get_post_meta($post->ID,'title_'.$i,true);
    $text = get_post_meta($post->ID,'text_'.$i,true);
    $link = get_post_meta($post->ID,'link_'.$i,true);
    $img = ($meta) ? '<img src="'.wp_get_attachment_thumb_url($meta).'" alt="">' : '';

    echo '<div class="image-entry">';
    echo '<input type="hidden" name="'.$k.'" id="'.$k.'" class="id_img" data-num="'.$z.'" value="'.$meta.'">';
    echo '<div class="img-preview" data-num="'.$z.'">'.$img.'</div>';

    echo '<p>'.__('Title').'</p>';
    echo '<input type="text" name="title_'.$k.'" id="title_'.$k.'" class="id_title" data-num="'.$z.'" value="'.$title.'">';
    echo '<p>'.__('Description').'</p>';
    echo '<textarea name="text_'.$k.'" id="text_'.$k.'" class="id_text" data-num="'.$z.'">'.$text.'</textarea>';
    echo '<p>'.__('Link').'</p>';
    echo '<input type="text" name="link_'.$k.'" id="link_'.$k.'" class="id_link" data-num="'.$z.'" value="'.$link.'">';

    echo '<a class="get-image button-primary" data-num="'.$z.'">'.__('Add image').'</a>';
    echo '<a class="remove-slide button-secondary" data-num="'.$z.'">'.__('Delete').'</a>';
    echo '</div>';
    $z++;
  }
  echo '<div class="add-more-slides" data-action="add">+</div>';
  echo '</div>';

}

/**
*
* SAVE METABOX 
*
**/
add_action('save_post', 'aim_save_details'); 
function aim_save_details($post_ID) { 
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_ID;
  }
   
  update_post_meta($post_ID, 'slide-amount', $_POST['slide-amount']);

  $image_slots = aim_list_my_image_slots();
  foreach($image_slots['imgs'] as $k => $i) {
    if(isset($_POST[$k])) {
      check_admin_referer('image-slide-save_'.$_POST['post_ID'], 'image-slide-nonce');
      update_post_meta($post_ID, $i, esc_html($_POST[$k]));
      update_post_meta($post_ID,'title_'.$i, esc_html($_POST['title_'.$k]));
      update_post_meta($post_ID,'text_'.$i, esc_html($_POST['text_'.$k]));
      update_post_meta($post_ID,'link_'.$i, esc_html($_POST['link_'.$k]));
    }
  }
}


/**
*
* aim_get_post_slide_images()
*
* This function push each image in an array with both the image and its ID.
* Then it use that array to create the final array that contains the attributes
* of the image and the thumbnail (src, width, height and resized).
*
**/
function aim_get_post_slide_images($large = null, $small = null) {
  global $post;
  $the_id = $post->ID;
  $image_slots = aim_list_my_image_slots();

  $imgsWithIds = array();
  foreach ($image_slots['imgs'] as $img => $id) {
    if($i = get_post_meta($the_id,$id,true)) {
      $imgsWithIds[$img] = $i;
    }
  }

  $imgAndThumb = array();
  foreach($imgsWithIds as $k => $id) {
    $imgAndThumb[$k] = array(
      wp_get_attachment_image_src($id, $small),
      wp_get_attachment_image_src($id, $large),
      get_post_meta($the_id, 'title_'.$image_slots['titles'][$k], true),
      get_post_meta($the_id, 'text_'.$image_slots['texts'][$k], true),
      get_post_meta($the_id, 'link_'.$image_slots['links'][$k], true)
    );
  }
  return $imgAndThumb;
}


/**
*
* aim_get_the_images()
*
* This is the main function you should use in your loop. The function makes
* the imgAndThumb array look better and also makes it easier to use.
*
* You can also define what size you want for the full size img (param 1)
* and the thumbnails (param 2).
*
* Standard sizes are 'full' and 'thumbnail'
*
**/
function aim_get_the_images($imgSize = 'full', $thumbSize = 'thumbnail') {
  $props = aim_get_post_slide_images($imgSize, $thumbSize);
  $result = array();
  $keys = array(
    'full_src',
    'full_width',
    'full_height',
    'resized',
    'thumb_src',
    'thumb_width',
    'thumb_height',
    'resized',
    'title',
    'text',
    'link'
  );

  $i = 0;
  foreach ($props as $name => $arr) {
    $result[$i] = array_combine(
      $keys, array_merge(
        $props[$name][1],
        $props[$name][0],
        (array)$props[$name][2],
        (array)$props[$name][3],
        (array)$props[$name][4])
      );
    $i++;
  }

  return $result;
}