<?php require_once('header.php'); ?>

<div class="outer-container">

<div class="fb-container">

  <div class="salutation">
    Welcome <?= $fb_user['first_name'] ?>!
  </div>

  <div class="fb-link-prompt">
   You&apos;re on your way to discovering and sharing local stories around the world.
  </div>

  <div class="fb-link-prompt">
   One last thing: do you have an existing email and password?
  </div>

  <div class="fb-link-about">
    If you do, link your accounts below. (You&apos;ll only have to do this once.)
  </div>

  <div class="form-wrapper">

    <div class="top-line-wrapper">

      <div class="fb-profile-image-wrapper">
        <img class="fb-profile-image" src="http://graph.facebook.com/<?= $fb_user['username'] ?>/picture" />
        <div class="fb-icon"></div>
      </div>

      <div class="fb-link-arrows">
        <div class="fb-link-arrows-img"></div>
      </div>

      <div class="fb-input-wrapper">
        <input type="text" name="fb_useremail" id="fb_useremail" value="email address" 
          class="input-text fb-connecting" data-tip="email address" data-help="Your BYC email address" />
        <input type="text" name="fb_password" id="fb_password" value="password" 
        class="input-text input-password fb-connecting" data-tip="password" data-help="Your BYC password" />
        <input type="hidden" name="fb_code" id="fb_code" value="<?=$_REQUEST['code']?>" />
        <div class="forgot-link"><a href="/forgot/">Forgot your password?</a></div>
        <div class="byc-icon"></div>
      </div>

    </div><!-- top-line-wrapper -->

    <div class="button-wrapper">
      <input type="button" id="fb_submit_link_accounts" value="Yes, link accounts" class="button fb-register-button fb-connecting fb_submit_link_accounts inactive" /> 
      <input type="button" id="fb_submit_link_skip" value="Skip this screen" class="button fb-register-button fb_submit_link_skip" />
    </div>

  </div><!-- form-wrapper -->

  <div class="fb-register">
    <input type="hidden" name="fb_username" id="fb_username" value="<?= htmlentities($fb_user['name']) ?>" 
      class="input-text" data-tip="Full Name" data-help="Enter your full name" />

    <input type="hidden" name="fb_email" id="fb_email" value="<?= htmlentities($fb_user['email']) ?>" 
      class="input-text" data-tip="email address" data-help="Specify an email address" />

    <input type="hidden" name="fb_code" id="fb_code" value="<?=$_REQUEST['code']?>" />
    <div class="clear"></div>
  </div>


</div>

</div><!-- outer-container -->

<script type="text/javascript">

$(window).load(function() {

  br.G_URL = '<?=G_URL?>';
  fb_register.i();

});

</script>

<?php require_once('tpl/footer.php'); ?>
