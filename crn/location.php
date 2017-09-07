<?php 

require_once '../config.php';

hpr('old addresses/locations left to convert:');


?>

<ul>

<?php 

foreach(story::find() as $story) {
  $story = story::i($story);
  if (!isset($story->location) && $story->status == 'accepted') {
?>

<li><a target="_new" href="/share/<?=$story->slug?>"><?=$story->title?></a></li>

<?php 
  }

}

?>

</ul>

