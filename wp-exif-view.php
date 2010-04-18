<?php
/*
Plugin Name: WPExifView
Plugin URI: 
Description: 記事本文にEXIF情報を埋め込みます。
Version: 1.1.0
Author: Tatsuya Fukata
Author URI: http://fukata.org
*/
require_once dirname(__FILE__).'/WPExifView.php';
$wpev = new WPExifView();

add_shortcode('exif', array($wpev, "doInsertExifData"));
add_action('admin_menu', array($wpev, "pluginMenu"));
?>