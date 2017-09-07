<?php require_once('header.php'); ?>

<div class="outer-container">
  <div class="container">

  <div><label class="label">Update SpeakerDeck Embed</label></div>

  <p>Current id: <?= $config->value ?></p>

  <p>Paste new embed code here:</p>

  <form>
    <textarea rows=5 cols=50 name="embedCode" id="embedCode"></textarea>
    <br /><input type=button id=test value="Test" />
    <input style="display: none;" type=button id=save value="Save" />
  </form>

  <div style="display: none;" class="feedback-pass">
    <p>
      Found id: <span class="found-id"></span>
    </p>
    <p>
      Check the embed code below to be sure it&apos;s correct, and then click Save.
    </p>
  </div>

  <div style="display: none; color: red;" class="feedback-fail">
    <p>
      Something appears to be wrong with the embed code.  Please try again.
    </p>
  </div>

  <div id="sl" class="slideshow"> </div>

  </div><!-- container -->
</div><!-- outer-container -->

<script type="text/javascript">

newPitchdeckEmbedId = null;

$('#test').on('click', function() {
    newPitchdeckEmbedId = null;
    var embedCode = $('#embedCode').val();
    var re = new RegExp('<script[^>]+data-id=[\'"]([0-9a-f]+)[\'"]');
    var matches = re.exec(embedCode);
    var id = null;
    if (matches) {
      newPitchdeckEmbedId = matches[1];
      $('.feedback-fail').hide();
      $('.found-id').html(newPitchdeckEmbedId);
      $('.feedback-pass').show();
      $('#save').show();
    } else {
      $('.feedback-fail').show();
      $('.feedback-pass').hide();
      $('#save').hide();
      $('#sl').hide();
    }

    if (newPitchdeckEmbedId) {
      $('#sl').show();
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = "//speakerdeck.com/assets/embed.js";
      ga.className = "speakerdeck-embed";
      ga.setAttribute('data-id', newPitchdeckEmbedId);
      ga.setAttribute('data-ratio', "1.33159947984395");
      document.getElementById('sl').appendChild(ga);

    }
});

$('#save').on('click', function() {
    $.post('/admin/dynamicConfig', { id: newPitchdeckEmbedId }, function(data, textStatus, jqXHR) {
        if (data.success) {
          alert('update successful!');
          document.location.href = '/pitchdeck/';
        } else {
          alert('Error: ' + data.errors);
        }
    }, 'json');
  }
);

</script>

<?php require_once('tpl/footer.php'); ?>