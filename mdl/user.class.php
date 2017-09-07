<?php 

class user extends kcol {

  protected $_types = array(
  /*
    'created' => 'date',
    'updated' => 'date'
  */
  );

  protected $_ols = array(
    'created_readable',
    'created_diff',
    'updated_readable'
  );

  public function __get($name) {

    switch ($name) {

      case 'created_readable' :
      case 'updated_readable' :
        $field = substr($name, 0, strpos($name, '_'));
        return date('Y-m-d h:i:s', parent::__get($field));
        break;

      case 'created_diff' :
        return clock::duration(parent::__get('created'));
        break;



    }

    return parent::__get($name);

  }

  static public function slugify($text) { 

    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
      return false;
    }

    return $text;

  }

  static public function generateUniqueUsername($name) {
    $attempts = 0;
    $slug = user::slugify($name);
    $candidateUsername = $slug;
    while ($attempts < 11 && user::findOne(array('slug' => $candidateUsername))) {
      $attempts++;
      $candidateUsername = $slug . '-' . $attempts;
    }

    return $candidateUsername;
  }

  static public function verify($field, $value) {

    switch ($field) {

      case 'password' :

        if (strlen($value) < 6) {
          return 'Your passord must be 6 or more characters';
        }
        
        if (!preg_match('/[0-9]/', $value)) {
          return 'Your password must contain at least one number';
        }

        return true;
        break;

    }

  }

  static public function auth($useremail, $password) {

    if (filter_var($useremail, FILTER_VALIDATE_EMAIL)) {
      $user = self::i(self::findOne(array('email' => $useremail)));
    } else {
      $user = self::i(self::findOne(array('username' => $useremail)));
    }

    if (!$user->exists()) {
      return false;
    }

    if (crypt($password, $user->password) != $user->password) {
      return false;
    }

    return $user;

  }

  public static function isAdmin() {

    if (
      !isset($_SESSION['user']) || 
      !isset($_SESSION['user']['role']) || 
      $_SESSION['user']['role'] != 'admin') {
      return false;
    }

    return true;

  }
  
  public static function isSlug($slug) {

    if ($_SESSION['user']['slug'] == $slug) {
      return true;
    } else {
      return false;
    }
    
  }

  public function save() {

    $newUser = ($this->_id === NULL);

    parent::save();

    if ($newUser) {
      hook::run('user_new', $this);
    }

  }

  // This must be called explicitly to trigger dependent updates.
  public function updateUsername($username) {

    $this->username = $username;
    $this->save();
    hook::run('user_username_changed', $this);

  }

  public function login($rememberMe=true) {

    // refresh our user session data
    $_SESSION['user'] = $this->data();

    // set our new hash and store it
    if ($rememberMe) {
      $this->summon = summon::set($this->id(true), $this->summon);
    } else {
      $this->summon = summon::remove($this->summon);
    }

    parent::save();

  }

}
