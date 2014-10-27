<?php
/*

  Plugin Name: BoxGallery
   Plugin URI: http://github.com/ruandre/boxgallery
  Description: A shortcode for displaying multiple lightbox galleries per post or page, each represented by a single image.
      Version: 0.7
       Author: Ruandre Janse Van Rensburg
   Author URI: http://ruandre.com
      License: GNU General Public License v2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: boxgallery

  Lightbox by Lokesh Dhakar
  http://lokeshdhakar.com/projects/lightbox2/
  http://github.com/lokesh/lightbox2/

*/



// Bail if called directly:
if (!defined('WPINC')) die;



function boxgallery_shortcode($atts) {

  // Unique number for each shortcode:
  static $i = 0; $i++;

  // Make shortcode attributes available as variables:
  extract(shortcode_atts(array(
    'ids'   => false,
    'thumb' => false,
    'size'  => false
  ), $atts));

  // Default image size:
  if (!$size)
    $size = 'medium';

  // Split $ids string into an array:
  if ($ids)
    $ids = explode(',', $ids);

  $images = array();

  // Make all ids integers and store in $images:
  if ($ids && is_array($ids) && (!empty($ids)))
    foreach ($ids as $id)
      $images[] = (int) $id;

  $set        = false;
  $boxgallery = false;
  $data       = " data-lightbox=\"set{$i}\"";

  if ($images && is_array($images) && (!empty($images))) {
    if (count($images) > 1) {

      // Create thumbnail image from id:
      if ($thumb) {

        $src  = wp_get_attachment_image_src($thumb, $size);
        $src  = ($src && !empty($src)) ? $src[0] : false;
        $href = wp_get_attachment_image_src($images[0], 'large');
        $href = ($href && !empty($href)) ? $href[0] : false;

        $first = ($src && $href)
          ? "<a href=\"{$href}\"{$data}><img src=\"{$src}\" alt=\"\"></a>"
          : false;

        array_shift($images); // Remove first image.

      // No thumbnail id specified; use first image instead:
      } else {

        $first = array_shift($images); // Get first image and remove it.

        $src  = wp_get_attachment_image_src($first, $size);
        $src  = ($src && !empty($src)) ? $src[0] : false;
        $href = wp_get_attachment_image_src($first, 'large');
        $href = ($href && !empty($href)) ? $href[0] : false;

        $first = ($src && $href)
          ? "<a href=\"{$href}\"{$data}><img src=\"{$src}\" alt=\"\"></a>"
          : false;

      }

      // Create a set using the remaining images:
      foreach ($images as $image) {

        $link = wp_get_attachment_image_src($image, 'large');
        $link = ($link && !empty($link)) ? $link[0] : false;

        if ($link)
          $set .= "<a href=\"{$link}\"{$data}></a>";

      }

      // Wrap set in some handy markup:
      if ($set)
        $set = "<span class=\"boxgallery-set\">{$set}</span>";

      // Wrap both first image and set in some handy markup:
      if ($first && $set)
        $boxgallery = "<span class=\"boxgallery\">{$first}{$set}</span>";


    // Only one image? Let's make a single-image lightbox:
    } else {

      if ($thumb) {
        $src = wp_get_attachment_image_src($thumb, $size);
        $src = ($src && !empty($src)) ? $src[0] : false;
      } else {
        $src = wp_get_attachment_image_src($images[0], $size);
        $src = ($src && !empty($src)) ? $src[0] : false;
      }

      $href = wp_get_attachment_image_src($images[0], 'large');
      $href = ($href && !empty($href)) ? $href[0] : false;

      if (current_user_can('edit_posts'))
        $message = __("Invalid image ids; cannot display lightbox.", 'boxgallery');
      else
        $message = false;

      if ($src && $href)
        $boxgallery = "<a href=\"{$href}\"{$data}><img src=\"{$src}\" alt=\"\"></a>";
      else
        $boxgallery = $message;
    }

  } // if ($images)

  if ($boxgallery)
    return $boxgallery;

}
add_shortcode('boxgallery', 'boxgallery_shortcode');



function boxgallery_enqueues() {

  global $post; if (!$post) return;

  // Only load lightbox files when shortcode is present:
  if (has_shortcode($post->post_content, 'boxgallery')):

    wp_enqueue_style(
      'boxgallery_lightbox_css',
      plugins_url('css/lightbox.css', __FILE__)
    );

    wp_enqueue_script(
      'boxgallery_lightbox_js',
      plugins_url('js/lightbox.min.js', __FILE__),
      array('jquery')
    );

  endif;

}
add_action('wp_enqueue_scripts', 'boxgallery_enqueues');
