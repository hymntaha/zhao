<?php 

require_once '../config.php';

foreach (story::find() as $story) {
  $story = story::i($story);

  if (isset($story->photos[0]['src'])) {

    $photos = $story->photos;

    hpr($photos);
    foreach ($photos as $key=>$value) {
      unset($photos[$key]['src']);
    }
    hpr($photos);

    $story->photos = $photos;
    $story->save();

  }


}
