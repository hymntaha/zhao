<?php 

class db {

  /***
   * Converts a list of string keys to a list of MongoId objects.
   */
  public static function idList($stringList) {
    $idList = array();
    for ($i = 0, $max = count($stringList); $i < $max; $i++) {
      $idList[] = new MongoId($stringList[$i]);
    }
    return $idList;
  }

}