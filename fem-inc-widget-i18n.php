<?php

class Text {

  private static $language = array(
      'settingsMenuLink' => "Prizma Widget",
      'settingsHeader' => "Manage Default Widget Settings",
      'settingsDescription' => 'Some settings can also be adjusted on indvidual posts and pages.',
      "partnerIDTitle" => "Partner ID",
      "partnerIDDescription" => "You can find your partner ID in the upper-right corner of <a href='https://dashboard.prizma.ai'>your dashboard</a>",
      "cssFilesTitle" => "CSS Files",
      "cssFilesDescription" => "Enter the URLs, separated by commas, of any stylesheets you would like applied to the widget.",
      "displayPagesTitle" => "Show the widget on the following types of pages",
      "displayPagesDescription" => "You can toggle the widget on individual pages, if necessary.",
      "displayPostsTitle" => "",
      "displayPostsDescription" => "",
      "headerTextTitle" => "Widget Title",
      "headerTextDescription" => 'This text appears above the widget. The default is "Recommended Videos."',
      "widthTitle" => "Width",
      "widthDescription" => "We recommend 100% unless you have a good reason not to.",
      "autoplayTitle" => 'Autoplay on click?',
      "autoplayDescription" => 'When a visitor clicks a thumbnail, should the video begin playing immediately?',
      "layoutTitle" => "Select layout",
      "layoutDescription" => "Select a layout for the main content area widget. To add a sidebar widget, go to <a href ='%s'>Appearance > Widgets</a>.",
      "noPartnerIDErrorMsg" => 'Please provide a valid partnerID. You can find your partnerID at <a href="https://dashboard.prizma.ai">https://dashboard.prizma.ai</a>. If you do not have a Prizma account, please visit <a href="http://prizma.ai/get-started">http://prizma.ai/get-started</a>.',
  );

  static public function get($key) {
    return isset(self::$language[$key]) ? self::$language[$key] : "";
  }

}
