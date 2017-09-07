<?php

/*
 * Runtime hooks.
 */

class hook {

  private static $actions = array();

  public static function add($event, $callback) {
    if (!isset(self::$actions[$event])) {
      self::$actions[$event] = array();
    }
    self::$actions[$event][] = $callback;
  }

  public static function run($event) {

    if (isset(self::$actions[$event])) {
      $args = array_slice(func_get_args(), 1);
      foreach (self::$actions[$event] as $callback) {
        call_user_func_array($callback, $args);
      }
    }

  }

}
