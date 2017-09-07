<div class="message-share-steps-container">
<div class="message-share-steps">
  <div class="message-share-steps-lead">
    We saved a spot for you!  Sign in now and complete your story.
  </div>
  <div class="message-share-second">
  </div>
  <div class="message-share-steps-list-container">
    <ol class="message-share-steps-list">
      <li>
        <div class="message-share-step-number">Step 1 <?= isset($_SESSION['user']) ? '&#x2714;' : '' ?></div>
        <div><a class="user-sign-in" style="color:white; text-decoration: <?= isset($_SESSION['user']) ? 'none' : 'underline' ?>;">Create a new account or sign in</a> with an existing account.</div>
      </li>
      <li>
        <div class="message-share-step-number">Step 2</div>
        <div>Filling in all of the details makes your story useful to our readers!  Add your answers to any pre-filled fields, or fill in the empty ones.</div>
      </li>
      <li>
        <div class="message-share-step-number">Step 3</div>
        <div>Submit your story to Bravo Your City editors who will accept or decline your story. High photo quality is important!</div>
      </li>
    </ol>
  </div>
</div>
</div>
