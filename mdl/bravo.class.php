<?php 

class bravo extends kcol {

  protected $_types = array(
    'created' => 'date'
  );

  public function getUserBravos($user_id, $stories=false) {

    $bravos = array();

    $criteria = array();
    $criteria['$and'][] =  array( 'user_id' => $user_id);
    if ($stories) {
      $criteria['$and'][] =  array( 'story_id' => array('$in' => $stories));
    }

    foreach (self::find($criteria) as $key=>$value) {
      $bravos[$value['story_id']->{'$id'}] = $value;
    }

    return $bravos;

  }

  public function save() {
    if ($this->weight === NULL) {
      $this->weight = 1;
    }
    parent::save();
  }

}
