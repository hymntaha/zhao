
<?php foreach ($comments as $array): ?>
<?php $comment = comment::i($array); ?>
<?=$comment->author?>: 
	<?=$comment->text?>
	
	
<?php endforeach?>
