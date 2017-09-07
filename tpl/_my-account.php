


  <div class="content_main">

    <div class="share"> 

      <div class="share_header">update your account</div>

<div style="margin-top: 2rem;">

      <label>name:</label>
      <input type="text" name="username" id="username" value="<?= $_SESSION['user']['username'] ?>" class="input-text" data-tip="<?= $_SESSION['user']['username'] ?>" data-help="Enter your Full Name" />

      <div class="clear"></div>

      <label>email:</label>
      <input type="text" name="email" id="email" value="<?= $_SESSION['user']['email'] ?>" class="input-text" data-tip="<?= $_SESSION['user']['email'] ?>" data-help="Enter your email address" />


      <input type="button" class="button submit" name="save" value="Save" />
      <input type="button" class="button plainbutton cancel" name="cancel" value="Cancel" />

</div>

    </div><!-- share -->

  </div><!-- content_main -->

<script>
my.account.i();
</script>
