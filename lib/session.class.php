<?php

class session {

  public static function usernameChanged($user) {
    if (isset($_SESSION['user']) && $_SESSION['user']['_id'] == $user->_id) {
      $_SESSION['user']['username'] = $user->username;
    }
  }

}