<?php require_once 'tpl/header.php'; ?>

<?php require_once 'tpl/_home.php'; ?>

<div class="outer-container">
  <div class="container">

  <div class="content_main">
  <?php require_once '_spinner.php'?>
  </div>

  <div class="clear"></div>

  </div>
</div>

<script type="text/javascript">
home.featuredMicroguides = <?= json_encode($features) ?>;

$(window).load(function() {
  home.i();
});

/* Manage featured box state */
if (location.hash != '' && location.hash != '#') {
  $('.container-home, .container-home-nav').hide();
}
$(window).on('search', function() {
  $('.container-home, .container-home-nav').hide();
});
$(window).on('nosearch', function() {
  $('.container-home, .container-home-nav').show();
});
</script>

<?php  require_once 'tpl/footer.php'; ?>

