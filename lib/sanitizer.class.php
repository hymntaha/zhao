<?php 

/* de-xss */

class sanitizer {

  private $excludedKeys = array();

  public function __construct() {

  }

  public function excludeKey($key) {
    $this->excludedKeys[] = $key;
  }

  private function cleanKey(&$item, $key) {
    if (in_array($key, $this->excludedKeys, true)) {
      return;
    }
    $item = strip_tags($item);
  }

  public function cleanArray(&$arr) {
    array_walk_recursive($arr, 'sanitizer::cleanKey');
  }

  public function cleanRequest() {
    $this->cleanArray($_GET);
    $this->cleanArray($_POST);
    $this->cleanArray($_REQUEST);
  }

}
