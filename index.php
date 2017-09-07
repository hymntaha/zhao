<?php

session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) && ($payload = summon::check())) {

  $user = new user($payload['user_id']);
  if ($user->exists() && isset($user->summon[$payload['hash']])) {
    $user->login();
  }

}

$app = new kctl($_SERVER['REQUEST_URI']);
$app->start();

?>

