<?php 

class forgot_ctl {

  public function index() {

    $__body_class = "header-bare";
    $__more_style = array('fb_register');
    $__more_script = array('forgot');
    require_once 'tpl/forgot.php';

  }

  public function submit() {

    define('KDEBUG_JSON', true);

    $error = false;
    $success = true;

    if (
      !isset($_REQUEST['email']) || 
      empty($_REQUEST['email']) || 
      $_REQUEST['email'] == 'email address' ||
      !filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) ||
      user::findOne(array('email' => $_REQUEST['email'])) == null
    ) {

      $error = 'e-mail address invalid or not found';
      $success = false;

    }

    if ($success == true) {

      $user = user::i(user::findOne(array('email' => $_REQUEST['email'])));

      $hash = sha1(microtime(true));
      $user->forgot_hash = $hash;
      $user->forgot_expires = new MongoDate(time() + ( 60 * 60 * 24 ));
      $user->save();

      $body = '

Please click this link to reset your password:

'.G_URL.'forgot/reset/'.$hash.'

';

    $headers = 
'From: noreply@bravoyourcity.com' . "\r\n" .
'Reply-To: noreply@bravoyourcity.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();


    email::mail($_REQUEST['email'], 'Bravo Your City! password reset', $body);


    }

    echo json_encode(array('success' =>$success, 'error' => $error));

  }

  public function reset($hash) {


    if (isset($_REQUEST['params']) && $json = json_decode($_REQUEST['params'], true)) {

      define('KDEBUG_JSON', true);

      $success = true;
      $error = false;

      $user = user::i(user::findOne(array('forgot_hash' => $json['hash'])));

      if (!$user->exists()) {
        $error = 'Invalid hash';
        $success = false;
      } elseif (user::verify('password', $json['password']) !== true) {
        $error = user::verify('password', $json['password']);
        $success = false;
      } elseif ($json['password'] != $json['confirm']) {
        $error = 'Your passwords do not match';
        $success = false;
      }

      if ($success) {
        $user->password = crypt($json['password']);
        unset($user->forgot_hash);
        unset($user->forgot_expires);
        $user->save();
      }

      echo json_encode(array('success' =>$success, 'error' => $error));

      return true;
    }

    $error = false;
    $reset = false;

    if (
      isset($hash) && strlen($hash) == 40 &&
      ($user = user::i(user::findOne(array('forgot_hash' => $hash)))) &&
      $user->exists()
      ) {

      $reset = true;

    } else {

      $error = 'Invalid hash';

    }

    $__body_class = "header-bare";
    $__more_style = array('fb_register');
    $__more_script = array('forgot');

    require_once 'tpl/reset.php';

  }

}
