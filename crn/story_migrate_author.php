<?php 

/***
 * Migrate ownership of all stories owned by one username to another.
 * 
 * Call me like this:
 *
 *   php story_migrate_author.php rfung8 "Randy F"
 */

require_once dirname(__FILE__) . '/../config.php';

if (count($_SERVER['argv']) < 3) die('Usage: ' . $_SERVER['argv'][0] . ' <old_author_name> <new_author_name>' . PHP_EOL);

$old_username = $_SERVER['argv'][1];
$new_username = $_SERVER['argv'][2];

foreach (array($old_username, $new_username) as $test_username) {
  $test_user = user::findOne(array('username' => $test_username));
  if ($test_user === NULL) {
    print "Warning: could not find username: $test_username" . PHP_EOL;
  }
}

$search = array(
    'author' => $old_username
);

$count = 0;

foreach (story::find($search) as $story) {
  $story = story::i($story);
  $story->author = $new_username;
  unset($story->username);
  $story->save();
  $count++;
}

print "Updated $count stories" . PHP_EOL;
