<?php

/**
 * Plugin Name: Prizma for WordPress
 * Plugin URI: http://prizma.ai
 * Description: Maximize, measure, and monetize video engagement on your site. Grow your business by distributing premium Prizma Syndication Network content. 
 * Version: 1.2
 * Author: FEM, Inc.
 * Author URI: http://prizma.ai
 * License: MIT
 */
defined('ABSPATH') or die('No script kiddies please!');

require_once 'fem-inc-widget-sidebar.php';
require_once 'fem-inc-widget-settings.php';
require_once 'fem-inc-widget-meta-box.php';

class Fem_Inc_Widget {

  private static $options;

  static public function init() {
    self::$options = get_option('fem-inc-widget-options');

    self::addHooks();
  }

  static private function addHooks() {
    add_action('widgets_init', array('Fem_Inc_Widget', 'registerWidget'));
    add_filter('the_content', array('Fem_Inc_Widget', 'displayBelowPost'));
    add_action('admin_menu', array('Fem_Inc_Widget', 'displaySettingsMenu'));
    add_action('add_meta_boxes', array('Fem_Inc_Widget', 'displayMetaBox'));
    add_action('admin_init', array('Fem_Inc_Widget_Settings', 'registerSettings'));
    add_action('save_post', array('Fem_Inc_Widget_Meta_Box', 'save'));
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Fem_Inc_Widget', 'displaySettingsLinkOnPluginPage'));
  }

  static public function registerWidget() {
    register_widget('Fem_Inc_Widget_Sidebar');
  }

  static public function render($data = array()) {
    wp_enqueue_script('fem-inc-widget', "http://cdn.prizma.tv/widget/prizma-widget.js");
    wp_enqueue_style('fem-inc-widget-css', plugins_url('fem-inc-widget.css', __FILE__));

    $container_id = "fem-widget-container-" . uniqid("fem");

    if ("" !== $data['cssFiles']) {
      $data['cssFiles'] = preg_replace("#\\\\/#", '/', json_encode(explode(' ', $data['cssFiles'])));
    }
    if (isset($data['width']) && "100%" === trim($data['width'])) {
      unset($data['width']);
    }

    $params = "";
    foreach ($data as $key => $val) {
      if ('cssFiles' === $key) {
        if($val){
          $params .= "{$key}: $val,\n";
        }
        continue;
      }

      $params .= "{$key}: '$val',\n";
    }


    $ret = "
      <div class='fem-inc-widget-container' id='{$container_id}'></div>
      <script>
      var prizmaOptions = prizmaOptions || [];
      prizmaOptions.push({
        {$params}
        id: '{$container_id}'
//        url: 'http://fem-inc.com',
      });
    </script>";

    return $ret;
  }

  private static function getPropValue($name) {
    return get_post_meta(get_the_ID(), "fem-inc-widget-meta-" . $name, true);
  }

  static public function displayBelowPost($content) {
    if ('page' == get_post_type(get_the_ID())) {
      $isEnabledProp = 'displayPages';
    } else {
      $isEnabledProp = 'displayPosts';
    }

      // $partnerID is mandatory
    $partnerID = self::$options["partnerID"];
    if ("off" !== self::getPropValue($isEnabledProp) && $partnerID) {
      $data = array(
          "partnerID" => $partnerID,
          "cssFiles" => self::$options["cssFiles"],
          "layout" => self::getPropValue("layout"),
          "headerText" => self::getPropValue("headerText"),
          "width" => self::getPropValue("width"),
          "autoplay" => self::getPropValue("autoplay"),
      );
      
      $content .= self::render($data);
    }

    return $content;
  }

  static public function displaySettingsMenu() {
    add_options_page('Prizma for WordPress Settings', Text::get('settingsMenuLink'), 'manage_options', 'fem-inc-widget-settings', array("Fem_Inc_Widget_Settings", "render"));
  }

  static public function displayMetaBox() {
    add_meta_box("fem-inc-post-meta-box", "Prizma options", array("Fem_Inc_Widget_Meta_Box", "render"), "post", "side", "high", null);
    add_meta_box("fem-inc-page-meta-box", "Prizma options", array("Fem_Inc_Widget_Meta_Box", "render"), "page", "side", "high", null);
  }

  function displaySettingsLinkOnPluginPage($links) {
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=fem-inc-widget-settings')) . '">Settings</a>';
    return $links;
  }

}

Fem_Inc_Widget::init();
?>
