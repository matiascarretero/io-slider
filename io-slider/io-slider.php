<?php
/**
 * @package IO_Slider
 * @version 1.0.0
 */
/*
Plugin Name: IO Slider
Plugin URI: https://dev.carretero.com.ar/
Description: Slider or carousel for post, pages and custom post types, using Swiper. You can render default WordPress fields and PODS fields if available. Use [io_slider] shortcode or Elementor widget. 
Author: Matias Carretero
Version: 1.0.0
Author URI: https://dev.carretero.com.ar/
*/

define('IO_SLIDER_URL', plugin_dir_url( __FILE__ ));
require_once( plugin_dir_path( __FILE__ )."includes/classes/IO_Slider.php");

IO\IO_Slider::init();

// Declare global function io_slider()
function io_slider($atts = []) {
    return IO\IO_Slider::get_instance()->callback($atts);
}