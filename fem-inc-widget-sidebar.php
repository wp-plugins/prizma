<?php
defined('ABSPATH') or die('No script kiddies please!');

class Fem_Inc_Widget_Sidebar extends WP_Widget {

  private static $options;

  function Fem_Inc_Widget_Sidebar() {
    wp_enqueue_style('fem-inc-widget-admin-css', plugins_url('fem-inc-widget-admin.css', __FILE__));
    wp_register_script("fem-inc-widget", "http://cdn.prizma.tv/widget/prizma-widget.js");

    self::$options = get_option('fem-inc-widget-options');

    $widget_ops = array();
    $control_ops = array();
    $this->WP_Widget('fem-inc-widget', 'Prizma Widget', $widget_ops, $control_ops);
  }

  function includeScript($instance) {
    $partnerID = self::$options["partnerID"];
    if (!$partnerID) {
      return Text::get('noPartnerIDErrorMsg');
    }

    $data = array(
        "partnerID" => $partnerID,
        "cssFiles" => self::$options["cssFiles"],
        "layout" => $instance["layout"],
        "headerText" => $instance["headerText"],
        "width" => $instance["width"],
    );

    echo Fem_Inc_Widget::render($data);
  }

  function widget($args, $instance) {
    // used when the sidebar calls in the widget
    echo $args['before_widget'];
    echo $this->includeScript($instance);
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form($instance) {
    $headerText = !empty($instance['headerText']) ? $instance['headerText'] : self::$options["headerText"];
    $width = !empty($instance['width']) ? $instance['width'] : self::$options["width"];
    $layoutValue = !empty($instance['layout']) ? $instance['layout'] : self::$options["layout"];
    
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('headerText'); ?>"><?php _e('Widget Title:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('headerText'); ?>" name="<?php echo $this->get_field_name('headerText'); ?>" type="text" value="<?php echo esc_attr($headerText); ?>">
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:'); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>">
    </p>
    
    <?php
    
    foreach (Fem_Inc_Widget_Settings::$availableLayoutsSidebar as $key => $layout) {
      echo "<p class='fem-inc-widget-radio-group'>";
      printf('<label class="fem-inc-widget-layout-label sidebar ' . $key . '" for="' . $this->get_field_id($key) . '">%s</label>', $layout);
      printf('<input type="radio" id="' . $this->get_field_id($key) . '" name="' . $this->get_field_name('layout') . '" value="' . $key . '" %s />', ($key === $layoutValue) ? "checked" : "");
      echo "</p>";
    }
    
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update($new_instance, $old_instance) {
   $instance = array();
    $instance['headerText'] = (!empty($new_instance['headerText']) ) ? sanitize_text_field($new_instance['headerText']) : '';
    $instance['width'] = (!empty($new_instance['width']) ) ? sanitize_text_field($new_instance['width']) : '';

    if (isset($new_instance['layout']) && array_key_exists($new_instance['layout'], Fem_Inc_Widget_Settings::$availableLayoutsSidebar)) {
      $instance['layout'] = $new_instance['layout'];
    }
    else{
      $instance['layout'] = Fem_Inc_Widget_Settings::$availableLayoutsSidebar[0];
    }
    
    return $instance;
  }

}
?>
