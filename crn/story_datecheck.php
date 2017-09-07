<?php 

// check for missing dates and fill the old broken ones in

require_once '../config.php';

foreach (story::find() as $story) {

  $story = story::i($story);

  if ($story->status == 'accepted') {

    if ($story->created == null) {
      hpr($story->title . ' ( by ' . $story->author . ' !created) status:'.$story->status);
      /*
      $story->created = strtotime('-2 days ago');
      $story->updated = strtotime('-2 days ago');
      $story->save();
      */
    }
    
    if ($story->created == null) {
      hpr($story->title . ' ( by ' . $story->author . ' !updated) status:'.$story->status);
    }


  }

}
