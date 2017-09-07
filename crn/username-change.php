<?php 

/***
 * Change a user's displayed name.
 * 
 * Call me like this:
 *
 *   php story_migrate_author.php slug "User Name"
 */

require_once dirname(__FILE__) . '/../config.php';

if (count($_SERVER['argv']) < 3) die('Usage: ' . $_SERVER['argv'][0] . ' <slug> <username>' . PHP_EOL);

$slug = $_SERVER['argv'][1];
$username = $_SERVER['argv'][2];


$user = user::findOne(array('slug' => $slug));
if ($user === NULL) {
  print "Could not find user: '$slug'.  Giving up." . PHP_EOL;
  die;
}

print "Updating username...";

$user = user::i($user);
$user->username = $username;
$user->save();

print "done." . PHP_EOL;

print "Updating stories...";

story::col()->update(
       array("authorSlug" => $slug),
       array('$set' => array('author' => $username)),
       array("multiple" => true)
);

print "done." . PHP_EOL;

$info = story::db()->command(array('getlasterror' => 1));

if (isset($info['n'])) {
  print "--> {$info['n']} records updated." . PHP_EOL;
}

print "Updating microguides...";

microguide::col()->update(
       array("authorSlug" => $slug),
       array('$set' => array('author' => $username)),
       array("multiple" => true)
);

print "done." . PHP_EOL;

$info = microguide::db()->command(array('getlasterror' => 1));

if (isset($info['n'])) {
  print "--> {$info['n']} records updated." . PHP_EOL;
}

