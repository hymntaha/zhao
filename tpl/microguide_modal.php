<div class="nav-arrows fullsize">
  <div class="nav-container">
    <img src="/img/modal_left_arrow.svg" class="left-arrow" data-index="<?= $prevStory->microIndex ?>" data-slug="<?= $prevStory->slug?>" data-title="<?= $prevStory->title?>">
    <img src="/img/modal_right_arrow.svg" class="right-arrow" data-index="<?= $nextStory->microIndex ?>"  data-slug="<?= $nextStory->slug?>" data-title="<?= $nextStory->title?>">
  </div>
</div>

<div class="modal-header">

	<div class="left">
		<img class = "byc-modal-icon" src="/img/byc-microguide-icon.png"/>
		
	</div>
	
	<div class="center"><span class="modal-title"><a class="major-nav" href="<?= G_URL . "microguide/" . $microguide->slug?>"><?=$microguide->title?></a></span>
	<span class="author">Curated by <a data-slug="<?=$microguide->authorSlug?>"><?=$microguide->author?></a></span>
	</div>

	<div class="right">
		<div class="close"><img class="close-button" src="/img/modal-close.png"/></div>
	</div>
	<div class="clear"></div>
	
</div>
<div class="clear"></div>
<div class="modal-body">

  <div class="clear"></div>

  <div class="image-container">

    <div class="nav-arrows mobile">
      <div class="nav-container">
        <div class="left-arrow" data-index="<?= $prevStory->microIndex ?>" data-slug="<?= $prevStory->slug?>" data-title="<?= $prevStory->title?>"></div>
        <span class="microguide-position"><?= $storyIndex ?> of <?= $microguide->storyCount ?>
        </span>
        <div class="right-arrow" data-index="<?= $nextStory->microIndex ?>"  data-slug="<?= $nextStory->slug?>" data-title="<?= $nextStory->title?>"></div>
      </div>
    </div>

		<div class="story-img-container">
			<div class="story-image main">
				<img src="<?=$story->photos[0]['path']['800x800']?>" />  
      </div>
    </div>

    <?php if (isset($story->photos[0]['caption']) && !empty($story->photos[0]['caption'])): ?>
    <div class="image-caption-container main">
      <div class="image-caption">
         <?= $story->photos[0]['caption'] ?>
      </div>
    </div><!-- image-caption-container -->
    <?php endif ?>

	

	</div>
	<div class="text-container">
    <div class="mobile">
      <? if (count($microguide->availableOn) > 0): ?>
      <div class="available-on">
        <span class="available-on-text">Now Available On:</span>
        <? foreach ($microguide->availableOn as $store => $link): ?>
        <a href="<?=$link?>" target="_blank"><icon class="ebook <?=$store?> dark"></icon></a>
        <? endforeach ?>
      </div>
      <? endif ?>
      <div class="modal-calls-to-action">

        <div class="modal-share" data-slug="<?=$microguide->slug?>">
          
            <span class="share-this-microguide">Share This Microguide</span><a
            href="https://www.facebook.com/dialog/feed?app_id=<?=FB_APPID?>&link=<?=
            G_URL . 'microguide/' . $microguide->slug?>&redirect_uri=<?= G_URL ?>user/closeFacebookShare&display=popup"
            target="_blank"><div class="facebook-share"></div></a>
            <a href="https://twitter.com/share?text=<?=urlencode($microguide->title)?>&hashtags=byc&url=<?=G_URL . 'microguide/' . $microguide->slug?>" target="_blank" ><div class="twitter-share"></div></a>
          
        </div>

        <div class="modal-bravo-container">
          <div class="modal-bravo">
            <div data-tip-before="bravo this story"
              data-tip-after="bravo it again!"
              data-datas='<?=json_encode(array('slug' => $story->slug, 'title' => $story->title, 'id' => $story->id(true)), JSON_HEX_APOS)?>'
              class="bravoLarge bravo<?=($bravoed) ? 'On' : 'Off'?> no-hover"> &nbsp;</div>
            <div
              class="bravo-count-bubble <?php if ($story->bravoCount > 0):?>visible<?php else: ?>hidden<?php endif?>">
              <div class="bravo-count-left grey-triangle"></div><div class="bravo-count-left white-triangle"></div>
              <div class="bravo-count-middle">
                <span class="bravo_count"><?= $story->bravoCount ?> </span>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
	
		<div class="text-header">
      <span class="microguide-position"><?= $storyIndex ?> of <?= $microguide->storyCount ?></span>
			<div class="info-box">
				<div class="info-text">INFO</div>
				<div class="url">
					<?php if (isset($story->url)):?>
					<a target="_new" href="<?=$story->url_format?>"><?=$story->url?>
					</a>
					<?php endif?>
				</div>

				<?php if (isset($story->location)): ?>

				<div class="address">
					<a target="_new"
						href="https://maps.google.com/maps?q=<?=$story->location['formatted']?>">
						<?=$story->location['formatted']?>
					</a>
				</div>

				<?php else:?>

				<div class="address">
					<a target="_new"
						href="https://maps.google.com/maps?q=<?=$story->address.','.$story->city.','.$story->state.','.$story->country?>">
						<?=$story->address?>
					</a>
				</div>

				<div class="hood">
					<?=isset($story->hood) ? $story->hood : ''?>
				</div>
				
				<div class="citystate">
					<?=isset($story->city) && !empty($story->city) ? $story->city.',' : ''?>
					<?=isset($story->state) && !empty($story->state) ? $story->state : ''?>
					<?=isset($story->country) && !empty($story->country) ? $story->country : ''?>
				</div>

				<?php endif?>

			</div><!-- info-box -->
			
		</div><!-- text-header -->
    <? if(count($story->photos) > 1): ?>
    <div class="thumb-column">
      
      <?php foreach($story->photos as $key => $photo): ?>
      <div class="thumb-image">

        <img class ="<?= ($key == 0) ? 'faded' : ''?>" data-num="<?=$key?>" data-caption ="<?= isset($photo['caption']) ? $photo['caption'] : '' ?>" data-fullsize="<?=$photo['path']['800x800']?>" src="<?=image::scaleUrl($photo['id'], 'cropThumbnail', 74, 49);?>" />

      </div>
      <?php endforeach?>
      
    </div>
    <? endif ?>
		
			
		<div class="text-body">
			<span class="story-title"><a href="/story/<?= $story->slug ?>"><?= $story->title?></a></span>
			<div class="author">by <a data-slug=<?=$story->authorSlug?>><?=$story->author?></a> <span class="date"><?=$story->created_diff?> ago</span></div>
			
			<?=$story->text_format_html?>
		</div>
	</div><!-- text-container -->
	
	
	
</div> <!-- modal-body -->

<div class="clear"></div>

<div class="modal-trailing-images">
   <?php for ($i = 1, $max = count($story->photos); $i < $max; $i++): ?>

	<div class="image-container">
		<div class="story-img-container">
			<div class="story-image">
				<img src="<?=$story->photos[$i]['path']['x425']?>" />  
			</div>
		</div>
    <?php if (isset($story->photos[$i]['caption']) && !empty($story->photos[$i]['caption'])): ?>
      <div class="image-caption-container mobile">
        <div class="image-caption">
				<?= $story->photos[$i]['caption'] ?>
        </div>
      </div><!-- image-caption-container -->
    <?php endif ?>
	</div>

   <?php endfor ?>
</div><!-- modal-trailing images -->

<div class="modal-footer">
  <div class="available-on">
    <? if (count($microguide->availableOn) > 0): ?>
    <span class="available-on-text">Now Available On:</span>
    <? foreach ($microguide->availableOn as $store => $link): ?>
    <a href="<?=$link?>" target="_blank"><icon class="ebook <?=$store?> light"></icon></a>
    <? endforeach ?>
    <? endif ?>
  </div>

  <div class="modal-calls-to-action">

		<div class="modal-share" data-slug="<?=$microguide->slug?>">
			
				<span class="share-this-microguide">Share This Microguide</span><a
				href="https://www.facebook.com/dialog/feed?app_id=<?=FB_APPID?>&link=<?=
				G_URL . 'microguide/' . $microguide->slug?>&redirect_uri=<?= G_URL ?>user/closeFacebookShare&display=popup"
				target="_blank"><div class="facebook-share"></div></a>
				<a href="https://twitter.com/share?text=<?=urlencode($microguide->title)?>&hashtags=byc&url=<?=G_URL . 'microguide/' . $microguide->slug?>" target="_blank" ><div class="twitter-share"></div></a>
			
		</div>

		<div class="modal-bravo-container">
			<div class="modal-bravo">
				<div data-tip-before="bravo this story"
					data-tip-after="bravo it again!"
					data-datas='<?=json_encode(array('slug' => $story->slug, 'title' => $story->title, 'id' => $story->id(true)), JSON_HEX_APOS)?>'
					class="bravoLarge bravo<?=($bravoed) ? 'On' : 'Off'?> no-hover"> &nbsp;</div>
				<div
					class="bravo-count-bubble <?php if ($story->bravoCount > 0):?>visible<?php else: ?>hidden<?php endif?>">
					<div class="bravo-count-left grey-triangle"></div><div class="bravo-count-left white-triangle"></div>
					<div class="bravo-count-middle">
						<span class="bravo_count"><?= $story->bravoCount ?> </span>
					</div>
				</div>

			</div>
		</div>
	</div>
	
	<div class="back-to-top" onclick="br.scrollTo(0)">
	  Back to top
	</div>
</div>

