<?php 

/***
 * Emulate a db traditional db sequence.
 */

class sequence extends kcol {

  public function __construct($name) {
    if (is_string($name)) {
      $sequence = self::col()->findOne(array('name' => $name));
      if ($sequence) {
        $this->_data = $sequence;
      } else {
        parent::__construct($name);
      }
    } else {
      parent::__construct($name);
    }
  }


  public function currVal() {
    return (int) $this->_data['currVal'];
  }

  public function nextVal() {

    $sequence = self::db()->command(
        array(
            'findandmodify' => 'sequence',
            'query'         => array('name' => $this->_data['name']),
            'update'        => array('$inc' => array('currVal' => 1)),
            'new'           => true
        )
    );

    $this->_data = $sequence['value'];
    return $this->currVal();
  }

}
