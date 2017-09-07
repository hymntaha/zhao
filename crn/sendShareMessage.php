<?php

require_once(dirname(__FILE__) . '/../config.php');

$cursor = user::find();
//$cursor = user::find(array('email' => new MongoRegex("/aegrumet/i")));
$sent = 0;
foreach ($cursor as $id => $user) {
  $result = message::sendMakeMicroguides($id);
  if ($result) {
    echo "Sent share instruction to $id\n";
    $sent++;
  }
}

echo "Sent $sent total messages.\n";

