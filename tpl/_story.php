
  <div class="container">

    <div class="story">
      
      <div class="section">
        <?php  if ($bio == false):?>
        <h1 class="title"><a href = "<?php 
        	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        	$parts = explode($actual_link, "#");
        	echo rtrim($parts[0], '#');
        	?>"><?=$story->title?></a></h1>
        <?php else:?>
        <h1 class="title">About <?=$story->author?></h1>
        <?php endif?>
        <hr class="separator" />

        <span class="author" title="created <?=$story->created_readable?>">by
          <a data-slug="<?=$story->authorSlug?>"><?=$story->author?></a>
          <span class="date"><?=$story->created_diff?> ago</span>
        </span>
        <?php if ($story->status == 'accepted'){ $bravoSize = "large"; $backgroundGrey = true; require_once('_bravo_button.php'); } ?>
        <? if ($bio && user::isSlug($story->authorSlug)): ?>
        <div class="edit-bio button">
          Edit Bio
        </div>
        <? endif ?>
      </div>


      <div class="section info">
        
        <? if ($displayEbookMicroguides): ?>
        <div class="available-on">
          <span class="available-on-text info-title">Now Available On:</span>
          <? foreach ($ebookMicroguide->availableOn as $store => $link): ?>
          <a href="<?=$link?>" target="_blank"><icon class="ebook <?=$store?> dark"></icon></a>
          <? endforeach ?>
        <hr class="separator" />
        </div>
        <? endif?>
        
        <div class="subsection subsection-info">

          <div class="info-title">Info</div>
          <hr class="separator"/>
          <div class="clear"></div>

          <div class="phone"><?=isset($story->phone) && strlen($story->phone > 1) ? $story->phone : ''?></div>
          <div class="url">
            <?php if (isset($story->url)):?>
            <a target="_new" href="<?=$story->url_format?>"><?=$story->url?></a>
            <?php endif?>
          </div>

          <?php if (isset($story->location)): ?>

          <div class="address">
            <a target="_new" href="https://maps.google.com/maps?q=<?=$story->location['formatted']?>">
              <?=$story->location['formatted']?>
            </a>
          </div>

          <?php else:?>

          <div class="address">
            <a target="_new" href="https://maps.google.com/maps?q=<?=$story->address.','.$story->city.','.$story->state.','.$story->country?>">
              <?=$story->address?>
            </a>
          </div>

          <div class="hood"><?=isset($story->hood) ? $story->hood : ''?></div>
          <div class="citystate">
            <?=isset($story->city) && !empty($story->city) ? $story->city.',' : ''?>
            <?=isset($story->state) && !empty($story->state) ? $story->state : ''?>
            <?=isset($story->country) && !empty($story->country) ? $story->country : ''?>
          </div>

          <?php endif?>
        </div><!-- subsection -->

        <div class="subsection subsection-tags">
          <div class="tags-title">Tags</div>
          <hr class="separator"/>
          <div class="clear"></div>
          <div class="tags">
            <a class="tag"><?=implode('</a>, <a class="tag">', $story->tags)?></a>
          </div>
        </div><!-- subsection -->
      </div><!-- section -->

      <div class="clear"></div>
      <?php if (user::isAdmin() && !$bio): ?>
         <a href="/share/<?=$story->slug?>/">edit story</a>
      <?php endif?>

    </div><!-- story -->

  </div><!-- container -->

  <?php 

    if (!isset($useSlider) || !$useSlider) {
      require_once('_story_photos_static.php');
    } else {
      require_once('_story_photos_sliding.php');
    }
  ?>

  <div class="container">

    <div class="story">

      <div class="text"><?=$story->text_format_html?></div>

    </div><!-- story -->

    <div class="clear"></div>

  </div><!-- container -->
