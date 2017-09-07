<?php 

require_once '../config.php';

$matches = array(
'out-and-about-nyc-washington-square-park' => 'clare-h',
'out-and-about-nyc-the-hamptons' => 'clare-h',
'first-prize-pies' => 'emiko-tsuchida',
'i-dream-a-city' => 'flaniererin',
//'bonanza-coffee-roasters' => '
'german-travel-i' => 'gary'
);

// check for null authors

foreach (story::find() as $story) {
  $story = story::i($story);
  if ($story->author == null) {
    if (isset($matches[$story->slug])) {
     hpr('match:' . $story->title);
     $user = user::i(user::findOne(array('slug' => $matches[$story->slug])));
     $story->author = $user->username;
     $story->authorSlug = $user->slug;
     $story->save();
    } else {
     hpr('no match:' . $story->title);
    }
  }
}
