
<?php foreach ($comments as $array): ?>
<?php $comment = comment::i($array); ?>
<div class="comment">
  <div><?=$comment->author?><span><?=$comment->created_diff?> ago</span></div>
  <div><?=$comment->text?></div>
</div>
<?php endforeach?>
