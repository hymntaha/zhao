
<?php  require_once 'tpl/header.php'; ?>

<div class="outer-container">

<?php //NB: we pushed the container class into story.php to accomodate the slider layout ?>
<?php  require_once 'tpl/_story.php'; ?>

</div>
<?php if ($bio == false):?>
<div class="outer-container">
  <div class="container">
    <?php require_once 'tpl/_related_microguides.php';?>
  </div>
</div>
<div class="outer-container message-abut">
   <?php require_once('tpl/_message-make-your-microguide.php'); ?>
</div>
<div class="outer-container">
	<div class="container">
		<?php require_once 'tpl/_featured_microguides.php';?>
	</div>
</div>
<?php else: ?>
<?php if (count($usersMicroguides) > 0): ?>
<div class="users-microguides outer-container">
  <div class="container">
    <?php require_once 'tpl/_users_microguides.php';?>
  </div>
</div>
<?php endif ?>
<?php endif ?>
<div class="outer-container">
  <div class="container">
		<div class="local-stories-header"><hr/></div>

    <div class="content_main">
      <?php require_once '_spinner.php'?>
    </div>

  </div>
</div>

<div class="zoom zoomhidden">
  <li>
	  <div class = "zoom-arrows">
	  	<div class = "zoom-left-nav"><div class = "left-nav-arrow"></div></div>
	    <div class = "zoom-right-nav"><div class = "right-nav-arrow"></div></div>
	  </div>
    <img src="" />
    <div class="clear"></div>
    <label></label>
  </li>
</div>

<?php if ($bio): ?>
<style type="text/css">
.users-microguides .header hr:after {
  content: "<?= strtoupper($story->author) ?>'S MICROGUIDES"
}
.local-stories-header hr:after {
  content: "<?= strtoupper($story->author) ?>'S STORIES"
}
</style>
<?php endif ?>

<?php  require_once 'tpl/footer.php'; ?>
