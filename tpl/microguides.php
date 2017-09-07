
<?php  require_once 'tpl/header.php'; ?>

<div class="outer-container">
  <div class="container">

<?php  require_once 'tpl/_microguides.php'; ?>

  <div class="clear"></div>

  </div>
</div>

<?php  require_once 'tpl/footer.php'; ?>

<script type="text/javascript">
/* Any JS that needs to get called to display all of the microguides */
$(window).load(function() {
  microguides.i();
});

</script>
