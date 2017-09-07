<?php   require_once('header.php'); ?>

<?php if ($error == false): ?>

<div class="outer-container">

  <div class="forgot">

    <label class="label">enter a new password</label>

    <br /><br />
    <input 
      type="text" 
      name="password" 
      value="password" 
      data-tip="password"
      data-help="enter a new password"
      class="input-text input-password input-pass1"
    />

    <br />

    <input 
      type="text" 
      name="confirm" 
      value="confirm" 
      data-tip="confirm"
      data-help="confirm your new password"
      class="input-text input-password input-pass2"
    />

    <input type="button" name="submit" value="submit" class="button reset_button" /> 

    <div class="clear"></div>

  </div>

</div><!-- outer-container -->

<?php endif?>

<script type="text/javascript">

$(window).load(function() {

  br.G_URL = '<?=G_URL?>';
  <?php if (isset($reset) && $reset == true): ?>
  forgot.hash = '<?=$hash?>';
  <?php endif?>
  forgot.i();

  <?php if ($error != false): ?>
  loader.create('invalid and/or expired hash', {timeout: 30000});
  <?php endif?>

});

</script>

<?php require_once('tpl/footer.php'); ?>
