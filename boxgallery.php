<?php
/*

  Plugin Name: BoxGallery
   Plugin URI: http://github.com/ruandre/boxgallery
  Description: A shortcode for displaying multiple lightbox galleries per post or page, each represented by a single image.
      Version: 0.2
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



function boxgallery_shortcode($atts, $content = null) {

  // Unique number for each shortcode:
  static $i = 0; $i++;

  // Make shortcode attributes available as variables:
  extract(shortcode_atts(array('ids'   => false,
                               'thumb' => false,
                               'size'  => false), $atts));

  // Default image size:
  if (!$size) $size = 'medium';

  // Split $ids string into an array:
  if ($ids) $ids = explode(',', $ids);

  $images = array();

  // Make all ids integers and store in $images:
  if ($ids && is_array($ids) && (!empty($ids)))
    foreach ($ids as $id)
      $images[] = (int) $id;

  $set        = false;
  $boxgallery = false;

  // If we have attachment ids to work with:
  if ($images && !empty($images)):

    $first = array_shift($images); // Get first image and remove it.
    $src   = wp_get_attachment_image_src($first, $size);
    $href  = wp_get_attachment_image_src($first, 'large');
    $data  = " data-lightbox=\"set{$i}\"";

    if ($thumb) {
      // Let's remove the first image and link to the second instead:
      $first = array_shift($images);
      $href  = wp_get_attachment_image_src($first, 'large');
      $goto  = $href[0];
    }
    else {
      $goto = $href[0];
    }

    $first = "<a href=\"{$goto}\"{$data}><img src=\"{$src[0]}\" alt=\"\"></a>";

    // Create a set using all the remaining images:
    foreach ($images as $image) {
      $link = wp_get_attachment_image_src($image, 'large');
      $set .= "<a href=\"{$link[0]}\"{$data}></a>";
    }

    // Wrap in some handy markup:
    if ($set) $set = "<span class=\"boxgallery-set\">{$set}</span>";

    if ($first && $set)
      $boxgallery = "<span class=\"boxgallery\">{$first}{$set}</span>";

  endif; // $images

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