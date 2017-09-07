<div class="fade"></div>

<?php 
  require_once "tpl/_modal_auth.php";
  require_once "tpl/_microguide_modal.php";
  require_once "tpl/_tooltips.php";
  require_once "tpl/_menu.php";

?>

<div class="loader">
  <ul></ul>

    <div class="meter">
      <span style="width: 25%"><span></span></span>
    </div>

</div>

<div id="fb-root"></div>

<script type="text/javascript">

$(window).load(function() {

  br.G_URL = '<?=G_URL?>';
  user.i(<?=isset($_SESSION['user']) ? 'true' : 'false'?>);

  bravo.i();
  search.i();
  header.i();
  modal.i();
  
  <? if (isset($story) && get_called_class() == 'story_ctl'): ?>
  display.type = ['story'];
  display.needsRedirect = true;
  display.sort = 'random';
  search.stories = true;
  story.i();
  
  <? elseif (get_called_class() == 'index_ctl'): ?>
  
  /* If we're not on the home page */
  if (location.hash != '' && location.hash != '#') {
    display.type = ['microguide', 'story'];
    search.stories = true;
  } else {
    display.type = ['story'];
    search.stories = true;
  }
  
  <? else: ?>
  
  display.type = ['story', 'microguide'];
  search.stories = true;
  
  <? endif ?>
  
  <?php if (isset($bio) && $bio == true && get_called_class() == 'story_ctl'): ?>
  display.bio = true;
  <?php elseif (get_called_class() == 'microguide_ctl'):?>
  
  if (location.pathname.match(/^\/*microguide\/*$/)) { // If we're at /microguide
    display.type = ['microguide'];
    display.needsRedirect = true;
    search.stories = false;
  } else { // We're at a microguide TOC 
    display.type = ['story'];
    display.needsRedirect = true;
    search.stories = false;
  }
  
  <? endif ?>
  
  <?php if (isset($status)):?>
  display.status = '<?=$status?>';
  <?php endif?>

  <?php if (get_called_class() == 'share_ctl'):?>
    display.needsRedirect = true; 
    <?php if (isset($story)):?> 
    share.id = '<?=$story->_id?>';
    share.tags = <?=json_encode($story->tags)?>;
    <?php endif?>

    <?php if (isset($files)):?>
    <?php foreach ($files as $key=>$value):?>
    share.img.data[<?=$key?>] = {
      data: '',
      width: <?=$value->file['width']?>,
      height: <?=$value->file['height']?>,
      type: '<?=$value->file['type']?>'
    };
    <?php endforeach?>
    <?php endif?>
		
  share.i();
  <?php endif?>

  <? if (get_called_class() == 'static_ctl' || get_called_class() == 'forms_ctl' || get_called_class() == 'admin_ctl' || get_called_class() == 'my_ctl'): ?>
    display.needsRedirect = true;
    display.type = ['microguide'];
  <? endif ?>

  <?php  if (get_called_class() == 'story_ctl' || get_called_class() == 'index_ctl' || get_called_class() == 'static_ctl' || (isset($__withStoriesScroll) && $__withStoriesScroll)): ?>
  // TODO: figure out if this is quite right
  display.i();
  <?php endif?>

});

window.fbAsyncInit = function() {
  FB.init({
    appId      : '<?=FB_APPID?>',
    status     : true, 
    cookie     : true,
    xfbml      : true,
    oauth      : true
  });
};

(function(d){
   var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   d.getElementsByTagName('head')[0].appendChild(js);
 }(document));

</script>
</body>
</html>
