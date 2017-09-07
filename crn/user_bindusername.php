<?php 

require_once '../config.php';


// finds stories bound to titles and changes them to usernames

foreach (user::find() as $user) {

  $user = user::i($user);
  if (isset($user->title)) {
    hpr($user->title . ' ('.$user->username.')' . ' ['. ($user->username == $user->title).']');
    if ($user->username != $user->title) {
      hpr(story::find(array('author' => $user->title))->count());
      foreach(story::find(array('author' => $user->title)) as $story) {
        $story = story::i($story);
        $story->author = $user->username;
        $story->save();
      }
    }

  }

}

