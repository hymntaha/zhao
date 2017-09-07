<?php 

class score extends kcol {

  protected $_types = array(
    'created' => 'date',
    'user' => 'id'
  );

  public function __get($name) {

    switch ($name) {

      case 'score_grouped':
        return number_format(parent::__get('score'));
        break;

      case 'created_readable' :
        return date('Y-m-d h:i:s', parent::__get('created'));
        break;

    }

    return parent::__get($name);

  }

}
