
<?php  require_once 'tpl/header.php'; ?>

<div class="outer-container">
  <div class="container">

<?php  require_once 'tpl/_microguide.php'; ?>

  <div class="clear"></div>

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

<div class="local-stories-header"><hr></div>

<div class="outer-container">
  <div class="container">

    <div class="content_main">
      <?php require_once '_spinner.php'?>
    </div>

  </div><!-- container -->
</div><!-- outer-container -->

<?php  require_once 'tpl/footer.php'; ?>

<script type="text/javascript">
/* This is where we'd want to put in the url encoding and appropriate modal opening eh? */
$(window).load(function() {
  microguide.slug = <?= json_encode($microguide->slug) ?>;
  microguide.i();

<?php if($storySlug !== NULL):?>
  modal.modalOpen = false;
  modal.originalUrl = "<?= G_URL . "microguide/" . $microguide->slug?>";

  modal.microguideModalSlug = "<?= $microguide->slug?>";
  modal.microguideModalTitle = "<?= $microguide->title?>";
  modal.openMicroguideModal(<?= $storyIndex ?>);
<?php endif;?>
});

</script>
