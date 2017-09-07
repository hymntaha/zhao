<?php 

class useragent {

  /***
   * Returns true if user agent is, best guess, a mobile device, false otherwise.
   * Not definitive - use javascript/css when server-side isn't absolutely necessary.
   */
  public static function isMobile() {
    return (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobi') !== FALSE);
  }

}