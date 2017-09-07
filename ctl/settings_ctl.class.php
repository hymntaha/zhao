<?php

class settings_ctl {

  public function __call($name, $args) {

    header('Location: ' . str_replace('/settings', '/my', $_SERVER['REQUEST_URI']));
    return true;

  }

}