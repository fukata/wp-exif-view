<?php
/*
Plugin Name: WPExifView
Plugin URI: http://fukata.org/dev/wp-plugin/wpexifview/
Description: Image EXIF information embedded within the body of the post.
Version: 1.4.1
Author: Tatsuya Fukata
Author URI: http://fukata.org
*/
require_once dirname(__FILE__).'/WPExifView.php';
$wpev = new WPExifView();

add_shortcode('exif', array($wpev, "doInsertExifData"));
add_action('admin_menu', array($wpev, "pluginMenu"));
?>
