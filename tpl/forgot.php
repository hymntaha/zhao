<?php require_once('header.php'); ?>

<div class="outer-container">

  <div class="forgot">

    <div><label class="label">Reset your password</label></div>

    <div class="instruction">Enter your email address and we&apos;ll send you a link to reset your password.</div>

    <input 
      type="text" 
      name="email" 
      value="e-mail address" 
      data-tip="e-mail address"
      data-help="enter your current e-mail address"
      class="input-text"
    />

    <br />

    <div class="button-container">
      <input type="button" name="submit" value="Send" class="button forgot_button" /> 
    </div>

    <div class="clear"></div>

  </div>

</div><!-- outer-container -->

<script type="text/javascript">

$(window).load(function() {

  br.G_URL = '<?=G_URL?>';
  forgot.i();

});

</script>

<?php require_once('tpl/footer.php'); ?>
