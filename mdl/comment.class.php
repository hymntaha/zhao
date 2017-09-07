<?php 

class comment extends kcol {

  protected $_types = array(
  /*
    'created' => 'date',
    'updated' => 'date'
  */
  );

  protected $_ols = array(
    'created_readable',
    'updated_readable',
    'created_diff',
    'updated_diff'
  );

  public function __get($name) {

    switch ($name) {

      case 'created_readable' :
      case 'updated_readable' :
        $field = substr($name, 0, strpos($name, '_'));
        return date('Y-m-d h:i:s', parent::__get($field));
        break;

      case 'created_diff' :
      case 'updated_diff' :
        return clock::duration(parent::__get(substr($name, 0, strpos($name, '_'))));
        break;

    }


    return parent::__get($name);

  }


}
