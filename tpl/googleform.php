<?php require_once 'tpl/header.php'; ?>

<style type="text/css">

.content-main {
  text-align: center;
  background-color: white;
  margin-top: -14px;
  padding-top: 14px;
 }

/* These styles need to be inline */

#googleform {
  width: <?= $formWidth ?>px;
  height: <?= $formHeight ?>px;
}

@media screen and (max-width : 671px) {

  .contain-main {
    margin-top: 0;
    padding-top: 0;
  }

  #googleform {
    width: 100%;
  }

}

</style>

<div class="content-main">

<iframe id="googleform" src="https://docs.google.com/a/bravoyourcity.com/spreadsheet/embeddedform?formkey=<?= $formKey ?>" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>

</div>

<script type="text/javascript">
$('#googleform').load(function(){
    window.scrollTo(0,0);
});
</script>

<?php  require_once 'tpl/footer.php'; ?>
