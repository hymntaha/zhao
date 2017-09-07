<?php 

require_once '../config.php';


foreach (story::find(array('status' => 'working')) as $story) {
  $story = story::i($story);
  hpr($story->status . ':' . $story->author . ':' . $story->title);
  $story->status = 'pending';
  $story->save();
}
