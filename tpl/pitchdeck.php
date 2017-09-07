<?php   require_once 'header_raw.php'; ?>

<style type="text/css">
body {
  background-color:  #242424
}  
.header {
  display: none;
}
.slideshow-container {
  text-align: center;
}
.slideshow {
  display: inline-block;
}
.home-link {
  color:white;
  font-size: 16px;
  margin-bottom: 5px;
  margin-top: 5px;
}
.home-link a {
  color: white;
  text-decoration: none;
} 
</style>

<div class="slideshow-container">

<div class="home-link">
  <a href="http://www.bravoyourcity.com/">Bravo Your City is live. Check us out!</a>
</div>

<div id="sl" class="slideshow">

</div>
</div>

</body>
</html>

<script type="text/javascript">
var newWidth = parseInt($(window).height()*1.33159947984395) - 150;
$('.slideshow').css({width: newWidth + "px"});

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = "//speakerdeck.com/assets/embed.js";
  ga.className = "speakerdeck-embed";
  ga.setAttribute('data-id', "<?= $pitchdeckEmbedId ?>");
  ga.setAttribute('data-slide', "<?= $pitchdeckSlideNumber ?>");
  ga.setAttribute('data-ratio', "1.33159947984395");
  document.getElementById('sl').appendChild(ga);

  var tryCount = 0;
  var interval = null;

  interval = setInterval(
    function() {
      if (++tryCount > 20 ) {
        clearInterval(interval);
        return;
      }
      var ifr = $('.speakerdeck-iframe');
      if (ifr.length) {
        ifr.focus();
        clearInterval(interval);
      }
    },100);

  $('body').on('click', function() { document.location.href = '/'; });

})();

</script>
