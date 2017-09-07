
<div class="modal modal-auth">

  <div class="modal-close"></div>

  <div class="modal-inner modal-inner-1">
    <div class="header-image-container">
      <img class="header-image" src="/img/login-background-1.png" />
    </div>
    <div class="modal-inner-1-body">
      <div class="header-text">
       Share what it&apos;s like to walk and live in your city!
      </div>
      <div class="fb-button-container centered">
        <a href="<?php  require '_facebookconnect.php'; ?>"><span class="fb-button-sign-in" title="Sign in with Facebook"></span></a>
      </div>
      <div class="instruction">
        Sign in with <a class="auth-step-2">email</a> if you already have an account.
      </div>
      <hr />
      <div class="instruction">
        Are you a first-timer? <a class="auth-step-3">Register here!</a>
      </div>
    </div>
  </div><!-- modal-inner-1 -->

  <div class="modal-inner modal-inner-2">
    <div class="modal-inner-2-body">
      <div class="switch-modal">
        Are you a first-timer? <a class="auth-step-3">Register here!</a>
      </div>
      <div class="modal-form">
        <div class="instruction-prompt">
          Sign in:
        </div>
        <div>
          <input type="text" name="login_username" id="login_username" value="email" data-tip="email" class="input-text" data-help="specify your username or email address"/>
        </div>
        <div>
          <input type="text" name="login_password" id="login_password" value="password" data-tip="password" class="input-text input-password" data-help="specify your password" />
        </div>
        <div>
          <div class="forgot-password">
            <a href="/forgot"><span class="small-help">Forgot password?</span></a>
          </div>
          <div>
            <input type="checkbox" name="login_remember" id="login_remember" /> <span class="small-help">Remember me</span>
          </div>
        </div>
        <div class="clear"></div>
      
        <div class="baseline-controls">
          <div class="error-messages"></div>
          <div class="button-sign-in">
            <input type="button" name="login_submit" value="Sign in"  class="button button-login"/>
          </div>
        </div><!-- baseline-controls -->
      </div><!-- modal-form -->
    </div>
    <hr />
    <div class="modal-inner-2-body">
      <div class="fb-button-container">
        <a href="<?php  require '_facebookconnect.php'; ?>"><span class="fb-button-sign-in" title="Sign in with Facebook"></span></a>
      </div>
    </div>
  </div><!-- modal-inner-2 -->

  <div class="modal-inner modal-inner-3">
    <div class="modal-inner-3-body">
      <div class="switch-modal">
        Not your first time? <a class="auth-step-2">Sign in here.</a>
      </div>
      <div class="join-connect">
        <div class="instruction-prompt">
          Register to join Bravo Your City:
        </div>
        <div class="fb-button-container">
          <a href="<?php  require '_facebookconnect.php'; ?>"><span class="fb-button-connect" title="Connect with Facebook"></span></a>
        </div>
      </div><!-- join-connect -->
    </div><!-- modal-inner-3-body -->
    <hr />
    <div class="modal-inner-3-body">
    <div class="modal-form">
    <div class="instruction-prompt">
      Or use an email account:
    </div>
    <div>
      <input type="text" name="register_username" id="register_username" value="Full name" data-tip="Full name" class="input-text" data-help="Enter your full name" />
    </div>
    <div>
      <input type="text" name="register_email" id="register_email" value="email address" data-tip="email address" class="input-text" data-help="specify your email address" />
    </div>
    <div>
      <input type="text" name="register_password" id="register_password" value="password" data-tip="password"  class="input-text input-password" data-help="your password must be at least 6 characters and contain at least 1 number" />
    </div>
    <div>
      <input type="text" name="register_verify" id="register_verify" value="verify password" data-tip="verify password" class="input-text input-password" data-help="verify your password" />
    </div>

    <div class="baseline-controls">
      <div class="error-messages"></div>
      <div class="button-join">
        <input type="button" name="register_submit" id="register_submit" value="Join" class="button button-register" />
      </div>
    </div><!-- baseline-controls -->

    </div><!-- modal-form -->
    </div><!-- modal-inner-3-body -->
  </div><!-- modal-inner-3 -->

</div>
