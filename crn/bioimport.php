<?php 

require_once '../config.php';

foreach (user::find() as $user) {

  $user = user::i($user);

  if (isset($user->photos)) {

    $story = new story();
    $story->status = 'bio';

    $story->author = $user->username;
    $story->authorSlug = $user->slug;
    $story->created = $user->created;
    $story->updated = $user->updated;

    foreach (
      array(
        'photos','captions','text','tags','address','city',
        'hood','state','country','phone','url') as $opt) {
      if (isset($user->$opt)) {
        $story->$opt = $user->$opt;
        unset($user->$opt);

      }
    }

    $user->save();
    $story->save();
    hpr('imported bio from '.$user->username);
  }

}
