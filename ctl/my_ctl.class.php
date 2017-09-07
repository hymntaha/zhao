<?php

class my_ctl {

  public function __construct() {

    // make sure user is logged in
    if (!isset($_SESSION['user'])) {
      header('Location: '.G_URL);
      return true;
    }

    // ensure directory URL ends with a /
    $request = parse_url($_SERVER['REQUEST_URI']);
    if ($request['path'] == '/my') {
      $request['path'] .= '/';
      $request['port'] = $_SERVER['SERVER_PORT'];
      $request['host'] = $_SERVER['HTTP_HOST'];
      $request['scheme'] = strpos($_SERVER['SERVER_PROTOCOL'],'HTTPS') === 0 ? 'https' : 'http';
      header('Location: ' . http_build_url($request));
      return true;
    }

  }

  public function index() {

    require_once 'tpl/my.php';

    return true;

  }

  public function account() {

    $__more_script = array('my');

    require_once 'tpl/my-account.php';

    return true;

  }

  public function accountUpdate() {

    $success = true;
    $errors = array();

    $user = user::i($_SESSION['user']);

    $username = $_POST['username'];
    $email = $_POST['email'];

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $success = false;
      $errors['email'] = 'Invalid email address. Please check that it\'s correct and try again.';
    }

    if (user::findOne(array('email' => $_POST['email'], 'slug' => array('$ne' => $user->slug))) != null) {
      $success = false;
      $errors['email'] = 'Another account exists with this email address.';
    }

    if ($success) {

      // AG: it'd be nice to have a generic update mechanism for session-dependent data
      $user->email = $email;
      $_SESSION['user']['email'] = $email;

      if ($username != $user->username) {
        $user->updateUsername($username); // saves to db and triggers several dependent updates
      } else {
        $user->save();
      }
    }

    echo json_encode(array(
                           'success' => $success,
                           'errors' => $errors,
                           ));

    return true;
  }

  public function stories($name) {

    $__more_script = array('my');
    $__more_style = array('my');
    $__body_class = "left-aligned";

    $validStatuses = array('pending', 'draft', 'rejected', 'accepted');
    if (in_array($name, $validStatuses)) {
      $statuses = array($name);
    } else {
      $statuses = $validStatuses;
    }

    require_once 'tpl/my-stories.php';

  }

  public function microguides($name) {
    $__body_class = "left-aligned";
    $__more_script = array('my');
    $__more_style = array('my', 'microguide');
    $validStatuses = array('pending', 'draft', 'rejected', 'accepted');
    $statuses = $validStatuses;
  
   require_once('tpl/my-microguides.php');
  }

}

if (!function_exists('http_build_url')) {
  /**
   * If pecl_http is not installed, provide a replacement.
   * No support for username/password.
   */
  function http_build_url($request) {
    $scheme = isset($request['scheme']) ? $request['scheme'] : 'http';
    $url = $scheme;
    $url .= '://';
    $url .= $request['host'];
    if (isset($request['port'])) {
      switch ($scheme) {
      case 'http':
        $url .= ($request['port'] == 80) ? '' : ':' . $request['port'];
        break;
      case 'https':
        $url .= ($request['port'] == 443) ? '' : ':' . $request['port'];
        break;
      default:
        $url .= ':' . $request['port'];
      }
    }
    $url .= isset($request['path']) ? $request['path'] : '/';
    $url .= isset($request['query']) ? '?' . $request['query'] : '';
    $url .= isset($request['fragment']) ? '#' . $request['fragment'] : '';
    return $url;
  }
}