<?php 

class user_ctl {

  public function __construct() {
  }

  public function index() {
    require_once 'tpl/user.php';
  }

  public function logout() {

    if (isset($_SESSION['user'])) {

      $user = new user($_SESSION['user']['_id']);
      $user->summon = summon::remove($user->summon);
      $user->save();
    }

    unset($_SESSION['user']);
    if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 0) {
      header('Location: ' . G_URL);
    }

    return true;
  }

  public function _facebook_verify($code) {

    /* first verify our code */
    if (!isset($_REQUEST['code']) || empty($_REQUEST['code'])) {
      return false;
    }

    $verify = fb::access($_REQUEST['code'], G_URL.'user/facebook?redirect='.urlencode($_REQUEST['redirect']));

    if (!isset($verify['access_token'])) {
      return false;
    }

    $fb = new fb($verify['access_token']);
    $me = $fb->api('/me');
    $me['access_token'] = $verify['access_token'];

    return $me;

  }

  // login or register via facebook
  public function facebook() {

    // Uncomment to debug. Re-comment before committing!
    // $debug_fb = true;

    if (!$fb_user = $this->_facebook_verify($_REQUEST['code'])) {
      if ($debug_fb && isset($_SESSION['fb_user'])) {
        $fb_user = $_SESSION['fb_user'];
      } else {
        header('Location: /');
        return false;
      }
    }

    $user = user::i(user::findOne(array('fb_uid' => $fb_user['id'])));

    if (!$user->exists()) {
      $_SESSION['fb_user'] = $fb_user;
      $__more_style = array('fb_register');
      $__more_script = array('fb_register');
      $__body_class = "header-bare";
      require_once 'tpl/register_facebook.php';
    } else {

      if ($user->fb_access_token != $fb_user['access_token']) {
        $user->fb_access_token = $fb_user['access_token'];
        $user->save();
      }

      $user->login();
      header('Location: '.$_REQUEST['redirect']);
    }

    return true;

  }

  // connect an existing account to facebook
  public function facebook_json_connect() {

    define('KDEBUG_JSON', true);
    $errors = array();
    $success = true;

    if (!isset($_SESSION['fb_user']) || !isset($_SESSION['fb_user']['id']) || !isset($_SESSION['fb_user']['access_token'])) {
      echo json_encode(array('success' => false, 'errors' => array('error with saved facebook user')));
      return false;
    }

    if (!isset($_REQUEST['useremail']) || empty($_REQUEST['useremail']) || $_REQUEST['useremail'] == 'username / email') {
      $errors['fb_useremail'] = 'You must specify username or email';
      $success = false;
    }
    
    // more dupe code
    if (!isset($_REQUEST['password']) || empty($_REQUEST['password']) || $_REQUEST['password'] == 'password') {
      $errors['fb_password'] = 'You must specify a password';
      $success = false;
    }


    if ($success) {

      if (!$user = user::auth($_REQUEST['useremail'], $_REQUEST['password'])) {
        $errors['fb_useremail'] = 'Unrecognized username/email or password';
        $success = false;
      } else {
        $fb_user = $_SESSION['fb_user'];
        unset($_SESSION['fb_user']);
        $user->fb_uid = (string) $fb_user['id'];
        $user->fb_access_token = $fb_user['access_token'];
        $user->save();
        $user->login();
      }

    }

    // This gets displayed on the next page load.
    if ($success) {
      $_SESSION['alert'] = 'Success! You can login with Facebook from now on.';
    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;

  }

  // register your facebook account
  public function facebook_json_register() {

    define('KDEBUG_JSON', true);
    $errors = array();
    $success = true;

    if (!isset($_SESSION['fb_user']) || !isset($_SESSION['fb_user']['id']) || !isset($_SESSION['fb_user']['access_token'])) {
      echo json_encode(array('success' => false, 'errors' => array('error with saved facebook user')));
      return false;
    }

    $slug = user::generateUniqueUsername($_REQUEST['username']);
    /* dupe code needs to move to the user model */
    if (!isset($_REQUEST['username']) || empty($_REQUEST['username']) || $_REQUEST['username'] == 'username') {
      $errors['fb_username'] = 'You must specify a username';
      $success = false;
    } elseif (user::findOne(array('slug' => $slug))) {
      $errors['fb_submit_link_skip'] = 'Sorry, it looks like another user has the same name.  Please paste this message in an email to support@bravoyourcity.com.';
      $success = false;
    }

    if (!isset($_REQUEST['email']) || empty($_REQUEST['email']) || $_REQUEST['email'] == 'email address') {
      $errors['fb_email'] = 'Something\'s wrong with your facebook email address. Please verify your facebook email and try again.';
      $success = false;
    } elseif (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['fb_email'] = 'Something\'s wrong with your facebook email address. Please verify your facebook email and try again.';
      $success = false;
    } elseif (user::findOne(array('email' => $_REQUEST['email'])) != null) {
      $errors['fb_submit_link_skip'] = 'An account with your facebook email address already exists.  Please try linking accounts.';
      $success = false;
    }

    if ($success) {

      // create the user
      $fb_user = $_SESSION['fb_user'];
      unset($_SESSION['fb_user']);
      $user = new user();
      $user->fb_uid = (string) $fb_user['id'];
      $user->email = $_REQUEST['email'];
      $user->username = $_REQUEST['username'];
      $user->slug = $slug;
      $user->public = 1;
      $user->created = time();
      $user->updated = time();
      $user->fb_access_token = $fb_user['access_token'];
      $user->save();
      $user->login();

    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;

  }

  // standard login
  public function login() {

    define('KDEBUG_JSON', true);

    $errors = array();
    $success = true;

    if (!isset($_REQUEST['username']) || empty($_REQUEST['username']) || $_REQUEST['username'] == 'username') {
      $errors['login_username'] = 'You must specify a username';
      $success = false;
    } 

    if (isset($_REQUEST['remember']) && $_REQUEST['remember'] == 'checked') {
      $rememberMe = true;
    } else {
      $rememberMe = false;
    }

    if ($success) {

      if (!$user = user::auth($_REQUEST['username'], $_REQUEST['password'])) {
        $errors['login_password'] = 'It looks like your email or password isn\'t valid.  Check again, or <a href="/forgot">reset your password</a>.';
        $success = false;
      } else {
        $user->login($rememberMe);
      }

    }

    echo json_encode(array('success' => $success, 'errors' => $errors));

    return true;

  }

  // registration validation / completion
  public function register() {

    define('KDEBUG_JSON', true);

    $errors = array();
    $success = true;

    $slug = user::generateUniqueUsername($_REQUEST['username']);
    if (!isset($_REQUEST['username']) || empty($_REQUEST['username']) || $_REQUEST['username'] == 'Full name') {
      $errors['register_username'] = 'You must specify your Full Name';
      $success = false;
    } elseif (user::findOne(array('slug' => $slug))) {
      $errors['register_username'] = 'This username already exists';
      $success = false;
    }

    if (!isset($_REQUEST['email']) || empty($_REQUEST['email']) || $_REQUEST['email'] == 'email address') {
      $errors['register_email'] = 'You must specify an e-mail address';
      $success = false;
    } elseif (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['register_email'] = 'Invalid e-mail address';
      $success = false;
    } elseif (user::findOne(array('email' => $_REQUEST['email'])) != null) {
      $errors['register_email'] = 'This e-mail address already exists';
      $success = false;
    }


    if (!isset($_REQUEST['password']) || empty($_REQUEST['password']) || $_REQUEST['password'] == 'password') {
      $errors['register_password'] = 'You must specify a password';
      $success = false;
    } elseif (user::verify('password', $_REQUEST['password']) !== true) {
      $errors['register_password'] = user::verify('password', $_REQUEST['password']);
      $success = false;
    }

    if ($_REQUEST['password'] != $_REQUEST['verify']) {
      $errors['register_verify'] = 'Your passwords do not match';
      $success = false;
    }

    if ($success) {

      // create our new user
      $user = new user();
      $user->email = $_REQUEST['email'];
      $user->username = $_REQUEST['username'];
      $user->slug = $slug;

      $user->public = 1;
      $user->created = time();
      $user->updated = time();
      $user->password = crypt($_REQUEST['password']);
      $user->save();
      $user->login();

    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;

  }

  public function profilebar() {

    define('KDEBUG_JSON', true);

    if (!isset($_SESSION['user'])) {
      echo json_encode(array('success' => false, 'error' => 'not logged in'));
      return true;
    }

    $stories = array();
    foreach (story::find(array('author' => $_SESSION['user']['username'])) as $story) {
      $stories[$story['status']][] = story::i($story);
    }

    ob_start();
    require_once 'tpl/_profilebar.php';
    $html = ob_get_clean();

    echo json_encode(array('success' => true, 'html' => $html));
    return true;

  }
  
  public function fbShare() {

    $errors = array();
    $success = true;
        
    if (isset($_SESSION['user']['fb_uid'])) {

      define('KDEBUG_JSON', true);

      $fb = new fb($_SESSION['user']['fb_access_token']);

      if (isset($_REQUEST['slug'])) {
        if (isset($_REQUEST['story_num'])) {
          $story = $microguide->stories[$_REQUEST['story_num']];
        } else {
          $story = $microguide->stories[0];
        }

        $slug = $_REQUEST['slug'];
        $microguide = microguide::i(microguide::findOne(array('slug' => $slug)));

        if ($microguide->storyCount > 0) {

          $story = $microguide->stories[0];

          $params = array('link'=>G_URL.'microguide/'.$slug,
                          'name'=>$microguide->title,
                          'caption'=>$story->title,
                          'description'=>$story->text_short,
                          'picture'=>$story->photos[0]['path']['800x800']
                          );

          $response = $fb->api(
                               '/me/feed',
                               $params,
                               'post'
                               );


          echo json_encode(array('success'=>true, 'errors'=>$errors, 'response'=>$response));
          return true;

        } else {
          $errors[] = "Could not find microguide";
          $success = false;

        }

      } else {
        $errors[] = "Microguide slug not set";
        $success = false;

      }
    } else {
      $errors[] = "User not logged in with Facebook";
      $success = false;
      echo json_encode(array('success'=>$success, 'errors'=>$errors, 'loggedIn'=>false));
      return true;

    }

    echo json_encode(array('success'=>$success, 'errors'=>$errors));
  }
  
  public function closeFacebookShare() {
        require_once 'tpl/_close_facebook_share.php';
  }

  public function search($arg) {

    $query = urldecode($arg);
    $search = array('$or' => array(
                                  array( 'username' => new MongoRegex("/$query/i") ),
                                  array( 'slug' => new MongoRegex("/$query/i") ),
                                  array( 'email' => new MongoRegex("/$query/i") ),
                                  )
                    );

    $cursor = user::find($search)->limit(15);
    $results = array();
    foreach ($cursor as $id => $user) {
      $results[] = array(
                         'username' => $user['username'],
                         'slug' => $user['slug'],
                         );
    }
    echo json_encode($results);
    return;
  }

  public function __call($name, $args) {

    hpr("section $name not written yet");

  }

  public function __destruct() {

  }

}
