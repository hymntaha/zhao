<?php 

require_once dirname(__FILE__).'/../config.php';

if (count($_SERVER['argv']) < 2) die("Usage: " . $_SERVER['argv'][0] . " guide1 guide2 guide3 ..." . PHP_EOL);

foreach (array_slice($_SERVER['argv'],1) as $name) {
  
  $fname = dirname(realpath(__FILE__)) . "/../dat/microguides/$name";
  if (strpos($fname, '.json') === FALSE) {
    $fname .= '.json';
  }

  if (!file_exists($fname)) {
    echo "Skipping non-existent file $fname" . PHP_EOL;
    continue;
  }

  $json = json_decode(file_get_contents($fname));

  if (!$json) {
    echo "Json for $name didn't parse.  Skipping." . PHP_EOL;
    continue;
  }
  if (empty($json->slug)) {
    $slug = pathinfo($fname, PATHINFO_FILENAME);
    echo "Json for $name has no slug.  Using filename $slug." . PHP_EOL;
    $json->slug = $slug;
  } else {
    $slug = $json->slug;
  }

  $microguide = microguide::findOne(array('slug'=>$json->slug));
  if (!$microguide) {
    echo "Inserting new microguide for slug " . $json->slug . PHP_EOL;
    $microguide = new microguide();
  } else {
    echo "Updating existing microguide for slug " . $json->slug . PHP_EOL;
    $microguide = microguide::i($microguide);
  }
  foreach (array('title','slug','author','authorSlug') as $field) {
    $microguide->$field = $json->$field;
  }
  $microguide->created = time();
  $microguide->updated = false;
  $storyIds = array();
  foreach ($json->storyIds as $storyId) {
    $path = parse_url($storyId,PHP_URL_PATH);
    $slug = basename($path);
    $story = story::findOne(array('slug'=>$slug));
    if (!$story) {
      echo "Couldn't find story for slug '$slug'.  Skipping." . PHP_EOL;
      continue;
    }
    $storyIds[] = $story['_id'];
  }
  $microguide->storyIds = $storyIds;

  if (!$microguide->issueNumber) {
    $sequence = new sequence('microguide-issue-number');
    $microguide->issueNumber = $sequence->nextVal();
    echo "Assigned issue number " . $microguide->issueNumber . " to microguide." . PHP_EOL;
  }

  $microguide->save();
}
