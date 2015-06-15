<?php
defined('ABSPATH') or die('No script kiddies please!');

require_once 'fem-inc-widget-i18n.php';

class Fem_Inc_Widget_Settings {

  private static $options = array();

  // labeling for main article well
  public static $availableLayouts = array(
      "THUMBS" => "Grid",
      "SINGLE_ROW" => "Single row"
  );
  
  // alternate labeling for sidebar
  public static $availableLayoutsSidebar = array(
    "THUMBS" => "Text On Thumbnails",
    "SINGLE_ROW" => "Text Under Thumbnails"

  );

  public static function render() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    // Set class property
    self::$options = get_option('fem-inc-widget-options');
    self::setDefaultOptions();
    wp_enqueue_style('fem-inc-widget-admin-css', plugins_url('fem-inc-widget-admin.css', __FILE__));
    ?>
    <div class="wrap">
      <div class="logo-image"></div>
      <h2><?=Text::get('settingsHeader');?></h2>
      <p><?=Text::get('settingsDescription');?></p>
      <table class="dashboard-description">
        <tbody>
          <tr>
            <th scope="row">Your Prizma Dashboard<span class="fem-inc-widget-plugin-description">View engagement metrics and manage content settings.</span></th>
            <td><a href="https://dashboard.prizma.ai" class="dashboard-link">My Prizma Dashboard</a></td>
          </tr>
        </tbody>
      </table>
      <form method="post" action="options.php" class="fem-inc-widget-settings">
        <?php
        // This prints out all hidden setting fields
        settings_fields('fem-inc-widget');
        do_settings_sections('fem-inc-widget-settings');
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  static private function setDefaultOptions() {
    if (!isset(self::$options['width']) || "" === self::$options['width']) {
      self::$options['width'] = "100%";
    }
    if (!isset(self::$options['layout']) || "" === self::$options['layout']) {
      self::$options['layout'] = "THUMBS";
    }
    if (!isset(self::$options['autoplay'])) {
      self::$options['autoplay'] = "off";
    }
    if (!isset(self::$options['displayPages'])) {
      self::$options['displayPages'] = "on";
    }
    if (!isset(self::$options['displayPosts'])) {
      self::$options['displayPosts'] = "on";
    }
  }

  static private function getFieldTitle($field) {
    $description = Text::get($field . 'Description');
    if("layout" === $field){
      $description = sprintf($description, admin_url( 'widgets.php' ));
    }
    
    return Text::get($field . 'Title') . "<span class='fem-inc-widget-plugin-description'>" . $description . "</span>";
  }

  static public function registerSettings() {
    register_setting(
            'fem-inc-widget', // Option group
            'fem-inc-widget-options', // Option name
            array('Fem_Inc_Widget_Settings', 'sanitize') // Sanitize
    );

    add_settings_section(
            'fem_inc_main_section', // ID
            '', // Title
            null, // array('Fem_Inc_Widget_Settings', 'print_section_info'), // Callback
            'fem-inc-widget-settings' // Page
    );

    add_settings_field(
            'partnerID', // ID
            self::getFieldTitle('partnerID'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbPartnerID'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'cssFiles', // ID
            self::getFieldTitle('cssFiles'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbCssFiles'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'displayPages', // ID
            self::getFieldTitle('displayPages'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbDisplayPages'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'displayPosts', // ID
            self::getFieldTitle('displayPosts'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbDisplayPosts'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'headerText', // ID
            self::getFieldTitle('headerText'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbHeaderText'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'width', // ID
            self::getFieldTitle('width'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbWidth'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'autoplay', // ID
            self::getFieldTitle('autoplay'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbAutoplay'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'layout', // ID
            self::getFieldTitle('layout'), // Title 
            array('Fem_Inc_Widget_Settings', 'clbLayout'), // Callback
            'fem-inc-widget-settings', // Page
            'fem_inc_main_section' // Section           
    );
  }

  static public function sanitize($input) {
    $new_input = array();
    if (isset($input['partnerID'])) {
      $new_input['partnerID'] = sanitize_text_field($input['partnerID']);
    }
    if (isset($input['cssFiles'])) {
      $new_input['cssFiles'] = sanitize_text_field($input['cssFiles']);
    }
    if (isset($input['headerText'])) {
      $new_input['headerText'] = sanitize_text_field($input['headerText']);
    }
    if (isset($input['layout']) && array_key_exists($input['layout'], self::$availableLayouts)) {
      $new_input['layout'] = $input['layout'];
    }
    if (isset($input['width'])) {
      $new_input['width'] = sanitize_text_field($input['width']);
      if ("" === $new_input['width']) {
        $new_input['width'] = "100%";
      }
    }

    $new_input['autoplay'] = isset($input['autoplay']) ? "on" : "off";
    $new_input['displayPages'] = isset($input['displayPages']) ? "on" : "off";
    $new_input['displayPosts'] = isset($input['displayPosts']) ? "on" : "off";

    return $new_input;
  }
  static public function clbPartnerID() {
    self::clbInputText("partnerID");
  }

  static public function clbCssFiles() {
    self::clbInputText("cssFiles");
  }

  static public function clbHeaderText() {
    self::clbInputText("headerText");
  }

  static public function clbWidth() {
    self::clbInputText("width");
  }

  static public function clbAutoplay() {
    self::clbInputCheckbox("autoplay");
  }

  static public function clbDisplayPages() {
    self::clbInputCheckbox("displayPages", "pages");
  }

  static public function clbDisplayPosts() {
    self::clbInputCheckbox("displayPosts", "posts");
  }

  static public function clbLayout() {
    $name = "layout";

    foreach (self::$availableLayouts as $key => $layout) {
      echo "<div class='fem-inc-widget-radio-group'>";
      printf('<label class="fem-inc-widget-layout-label ' . $key . '" for="' . $key . '">%s</label>', $layout);
      printf('<input type="radio" id="' . $key . '" name="fem-inc-widget-options[' . $name . ']" value="' . $key . '" %s />', (isset(self::$options[$name]) && $key === self::$options[$name]) ? "checked" : "");
      echo "</div>";
    }
  }

//  

  static public function clbInputText($name) {
    printf('<input type="text" id="' . $name . '" name="fem-inc-widget-options[' . $name . ']" value="%s" />', isset(self::$options[$name]) ? esc_attr(self::$options[$name]) : '');
  }

  static public function clbInputCheckbox($name, $label = "") {
    printf('<input type="checkbox" id="' . $name . '" name="fem-inc-widget-options[' . $name . ']" %s /> %s', (isset(self::$options[$name]) && "on" === self::$options[$name]) ? "checked" : "", $label);
  }

}
?>
