<?php 

/**
 * Fix bad image dimensions
 *
 * Examples:
 *
 * php test-photo-sizes.php --limit=100 --dry-run
 * php test-photo-sizes.php --limit=10 --slug=sort-1
 */ 

$shortopts  = "";
$shortopts .= "l::"; // limit
$shortopts .= "s::"; // slug
$shortopts .= "n";   // dry-run

$longopts  = array(
                   "limit::",
                   "slug::",
                   "dry-run",
                   );
$options = getopt($shortopts, $longopts);

//defaults
$limit = 100;
$slug = null;
$dry_run = false;

//parse args
foreach ($options as $k => $v) {

  switch ($k) {
  case 'l':
  case 'limit':
    if ($v && (int)$v) {
    $limit = (int)$v;
    }
    break;
  case 's':
  case 'slug':
    if ($v) {
      $slug = $v;
    }
    break;
  case 'n':
  case 'dry-run':
    $dry_run = true;

  }

}

// GO!

require_once(dirname(__FILE__) . '/../config.php');

if ($slug) {
  $cursor = story::find(array('slug'=>$slug));
} else {
  $cursor = story::find()->sort(array('created'=>-1))->limit($limit);
}

$seen = 0;
$passed = 0;
$failed = 0;
$fixed = 0;

foreach ($cursor as $story) {
  $story = story::i($story);
  print 'Testing ' . $story->slug . '...';
  $new_photos = array();
  $pass = true;
  foreach ($story->photos as $photo) {

    $seen++;

    $file = story::grid()->get($photo['id']);
    if (!is_object($file)) {
      print "no file found" . PHP_EOL;
      continue;
    }

    $img = imagecreatefromstring($file->getBytes());
    $width = imagesx($img);
    $height = imagesy($img);

    print PHP_EOL . "  photo id  " . $photo['id'] . ': ';
    print "  measured x|y: $width|$height";
    print "  db x|y: {$photo['width']}|{$photo['height']}";

    if ($width == $photo['width'] && $height == $photo['height']) {
      $passed++;
      print '   [PASS]';
    } else {
      $failed++;
      print '   [FAIL]';
      $pass = false;
      $photo['width'] = $width;
      $photo['height'] = $height;
    }

    $new_photos[] = $photo;
  }

  if (!$pass && !$dry_run) {
    $fixed++;
    print " ...fixing in db.";
    $story->photos = $new_photos;
    $story->save();
  }

    print PHP_EOL;
}

print "seen : $seen" . PHP_EOL;
print "pass : $passed" . PHP_EOL;
print "fail : $failed" . PHP_EOL;
print "fixed: $fixed" . PHP_EOL;