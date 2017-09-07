<?php 

class static_ctl {

  function __call($name, $args) {

    $__more_style = array('static');

    $staticPages = array(
                         'how-it-works',
                         'faq',
                         'about',
                         'fifty',
                         'terms',
                         'privacy',
                         'team',
                         'payments',
                         );

    if (in_array($name, $staticPages)) {
      require_once "tpl/$name.php";
    } else {
      header("HTTP/1.0 404 Not Found");
    }

  }

}
