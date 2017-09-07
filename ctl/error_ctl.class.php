<?php 

class error_ctl {

  public function notfound() {

    $__body_class = "header-bare";
    header("HTTP/1.0 404 Not Found");
    //fastcgi version
    //header("Status: 404 Not Found");
    $img = search::random_noresult_image();
    require('tpl/error-notfound.php');

  }

}
