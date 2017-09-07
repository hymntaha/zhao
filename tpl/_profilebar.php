<label>bio</label>
<ul>
  <li class="edit"><a href="/story/bio/<?=$_SESSION['user']['slug']?>">View</a></li>
  <li class="edit menubottom"><a href="/share/bio">Edit</a></li>
</ul>


<?php if  (isset($stories['draft'])):?>
<label>drafts</label>
<ul>
<?php foreach ($stories['draft'] as $story):?>
<?php require 'tpl/_profilebar_story.php';?>
<?php endforeach?>
</ul>
<?php endif?>

<label>pending stories</label>

<?php if  (isset($stories['pending'])):?>
<ul>
<?php foreach ($stories['pending'] as $story):?>
<?php require 'tpl/_profilebar_story.php';?>
<?php endforeach?>
</ul>
<?php else:?>

  <div class="none">no pending stories</div>
  <ul>
    <li><a href="/share/">publish one!</a></li>
  </ul>

<?php endif?>

<label>published stories</label>

<?php if  (isset($stories['accepted'])):?>
<ul>
<?php foreach ($stories['accepted'] as $story):?>
 <!-- <li><a href="/story/<?=$story->slug?>"><?=$story->title?></a></li>-->
  <?php require 'tpl/_profilebar_story.php';?>
<?php endforeach?>
</ul>
<?php else:?>
  <div class="none">no published stories</div>
<?php endif?>


<?php if  (isset($stories['rejected'])):?>
<label>rejected stories</label>
<ul>
<?php foreach ($stories['rejected'] as $story):?>
<?php require 'tpl/_profilebar_story.php';?>
<?php endforeach?>
</ul>
<?php endif?>


