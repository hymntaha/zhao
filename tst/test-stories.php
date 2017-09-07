<?php 

require_once dirname(__FILE__) . '/../config.php';

$stories_count = 0;
$stories_array = array();

$cursor = story::find(array('slug' => array('$exists' => false), 'status' => array('$ne' => 'bio')));

foreach ($cursor as $story) {
    $story = story::i($story);
    if ($story->slug === NULL) { 
      $stories_count++;
      $stories_array[] = $story->_id;
      echo 'ObjectId("' . $story->_id->{'$id'} . '")' . PHP_EOL;
    }
}

echo "Found $stories_count stories with no slug.".PHP_EOL;

