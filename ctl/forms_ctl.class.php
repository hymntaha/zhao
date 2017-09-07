<?php 

class forms_ctl {

  private static $_forms = array(
    'publisher' => array(
      'formWidth' => 760,
      'formHeight' => 1500,
      'formKey' => 'dElEeEVrNlg3YlJId3lDMjY2VDBSUmc6MQ',
    ),
    'production' => array(
      'formWidth'=> 760,
      'formHeight' => 1500,
      'formKey' => 'dGJQUkRTel9zRjNrVGk4QXR5ZFI5Z2c6MQ',
    )
  );

  public function __call($name, $args) {
    if (isset(self::$_forms[$name])) {
      extract(self::$_forms[$name]);
      require_once('tpl/googleform.php');
    } else {
      header("HTTP/1.0 404 Not Found");
      //fastcgi version
      //header("Status: 404 Not Found");
    }
  }
}
