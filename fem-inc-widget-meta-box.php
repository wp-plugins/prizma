<?php

defined('ABSPATH') or die('No script kiddies please!');

class Fem_Inc_Widget_Meta_Box {

  private static $options;

  public static function render($post) {
    self::$options = get_option('fem-inc-widget-options');

    wp_enqueue_style('fem-inc-widget-admin-css', plugins_url('fem-inc-widget-admin.css', __FILE__));

    wp_nonce_field('fem-inc-widget-meta-box', 'fem-inc-widget-meta-box-nonce');
    if ('page' == get_post_type($post)) {
      self::displayInputCheckbox($post->ID, 'displayPages', 'Enable');
    } else {
      self::displayInputCheckbox($post->ID, 'displayPosts', 'Enable');
    }
    self::displayInputText($post->ID, 'headerText', 'Widget Title');
    self::displayInputText($post->ID, 'width', 'Width');
    self::displayLayoutOptions($post->ID, 'layout', 'Layout');
  }

  static public function displayInputCheckbox($id, $name, $label = "") {
    $value = self::getMetaValue($id, $name);
    $fullName = self::getFullName($name);

    printf('<p><input type="checkbox" id="' . $fullName . '" name="' . $fullName . '" %s /> %s</p>', (isset($value) && "on" === $value) ? "checked" : "", $label);
  }

  static public function displayInputText($id, $name, $label = "") {
    $value = self::getMetaValue($id, $name);
    $fullName = self::getFullName($name);

    printf('<p><label for="' . $fullName . '">%s</label><input type="text" id="' . $fullName . '" name="' . $fullName . '" value="%s" /></p>', $label, isset($value) ? esc_attr($value) : '');
  }

  static public function displayLayoutOptions($id, $name, $label = "") {
    $value = self::getMetaValue($id, $name);
    $fullName = self::getFullName($name);

    foreach (Fem_Inc_Widget_Settings::$availableLayouts as $key => $layout) {
      echo "<div class='fem-inc-widget-radio-group'>";
      printf('<label class="fem-inc-widget-layout-label ' . $key . '" for="' . $key . '">%s</label>', $layout);
      printf('<input type="radio" id="' . $key . '" name="' . $fullName . '" value="' . $key . '" %s />', (isset($value) && $key === $value) ? "checked" : "");
      echo "</div>";
    }
  }

  private static function getFullName($name) {
    return "fem-inc-widget-meta-" . $name;
  }

  private static function getMetaValue($id, $name) {
    $default = self::$options[$name];
    $meta = get_post_meta($id, self::getFullName($name), true);

    if ("" === $meta) {
      return $default;
    }

    return $meta;
  }

  public static function save($post_id) {

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */
    // Check if our nonce is set.
    if (!isset($_POST['fem-inc-widget-meta-box-nonce'])) {
      return $post_id;
    }

    // Verify that the nonce is valid.
    $nonce = $_POST['fem-inc-widget-meta-box-nonce'];
    if (!wp_verify_nonce($nonce, 'fem-inc-widget-meta-box')) {
      return $post_id;
    }


    // If this is an autosave, our form has not been submitted,
    //     so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Check the user's permissions.
    if ('page' == $_POST['post_type'] && !current_user_can('edit_page', $post_id)) {
      return $post_id;
    } else if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

    if ('page' == $_POST['post_type']) {
      self::updateRecord($post_id, self::getFullName('displayPages'), true);
    } else {
      self::updateRecord($post_id, self::getFullName('displayPosts'), true);
    }
    self::updateRecord($post_id, self::getFullName('layout'));
    self::updateRecord($post_id, self::getFullName('headerText'));
    self::updateRecord($post_id, self::getFullName('width'));
    self::updateRecord($post_id, self::getFullName('autoplay'), true);
  }

  private static function updateRecord($post_id, $name, $isCheckbox = false) {

    if(true === $isCheckbox && !isset($_POST[$name])){
      $_POST[$name] = "off";
    }
    
    $ret = sanitize_text_field($_POST[$name]);
    update_post_meta($post_id, $name, $ret);

    return $ret;
  }

}
?>