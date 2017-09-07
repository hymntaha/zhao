    <div class="microguide-landing">

      <div class="microguide-container">
        <div class="title-container">
          <div class="byc-icon-container responsive-margin-left">
            <a href="/microguide"><div class="byc-icon"></div></a>
          </div>
          <div class="front-title responsive-margin-left"><?= $microguide->title ?></div>
          <? if (count($microguide->availableOn) > 0): ?>
          <div class="available-on responsive-margin-left">
            <span class="available-on-text">Now Available On:</span>
            <? foreach ($microguide->availableOn as $store => $link): ?>
            <a href="<?=$link?>" target="_blank"><icon class="ebook <?=$store?> dark"></icon></a>
            <? endforeach ?>
          <hr class="separator" />
          </div>
          <? endif ?>
          <div class="author responsive-margin-left">Curated by <a href="/#authors=<?= $microguide->authorSlug ?>"><?= $microguide->author ?></a></div>
          <div class="microguide-description responsive-margin-left">
            <?= $microguide->description ?>
          </div>
          <?php if ($hasInvitedStories || $microguide->access == 'open'): ?>
          <div class="open-microguide">
            <div class="open-guide-mark">
              <div class="open-guide-o"></div>
              <div class="open-guide-text">Open Guide</div>
            </div>
            <div class="help-note"><div class="carrot"></div>Help complete this Microguide! Claim an available story or add a new one!</div>
          </div>
          <?php endif ?>
        </div><!-- title-container -->
        <div class="microguide-columns">
          <?php if (count($microguide->stories)): ?><?php $story = $microguide->stories[0]; ?>
          <div class="first-story <?= in_array($story->status, array('invited','draft','pending')) ? 'preview ' . $story->status : '' ?>" data-slug="<?=$microguide->slug?>">
            <div class="img-container">
              <div class="slide-show-link">
                <div class="slide-show-icon"></div>
              </div>
              <div class="story-box-overlay"><div class="story-heading"><div class="story-num"><hr/>1<hr/></div><div class="story-title"><?=$firstStory->title?></div></div></div>
              <img class="front-photo" src="<?=image::scaleUrl($firstStory->photos[0]['id'], 'cropThumbnail', 479, 358) ?>" />
            </div>
          </div><!-- first-story -->
          <?php endif ?>
          <?php // NB: inter-div whitespace is intentionally removed below, to achieve the desired layout ?>
       <?php for ($i = 1, $max = count($microguide->stories); $i < $max; $i++): ?><?php $story = $microguide->stories[$i]; ?><div class="microguide-story <?= in_array($story->status, array('invited','draft','pending')) ? 'preview ' . $story->status : '' ?>"><div class="story-box" data-slug="<?=$microguide->slug?>" data-title="<?=$microguide->title?>" data-story_slug="<?=($story->slug)?>" data-index="<?=($i+1)?>" data-story_title="<?=($i+1)?>"><div class="story-box-overlay"><div class="story-heading"><div class="story-num"><hr/><?=($i+1)?><hr/></div><div class="story-title"><?=$story->title?></div></div></div><img src="<?= $story->status == 'accepted' ? image::scaleUrl($story->photos[0]['id'], 'cropThumbnail', 230, 171) : '/img/invitedstory-bg.png?r=1' ?>" /><div class="preview-info"><div class="exclamation"></div><div><a class="button"><?= $story->status == 'invited' ? 'Claim It!' : 'Pending' ?></a></div></div><!-- preview-info --><div class="border-top"></div><div class="border-left"></div><div class="border-bottom"></div><div class="border-right"></div></div><!-- story-box  --></div><!-- microguide-story  --><?php endfor ?><?php if ($microguide->access == 'open'): ?><span class="microguide-create"><div class="microguide-story add-story preview new"><div class="story-box" data-story_slug="new" data-index="" data-story_title=""><div class="story-box-overlay"><div class="story-heading add-story-icon-wrapper"><div class="add-story-icon"></div><div class="add-story-label" style="">Add a story</div></div></div><img src="/img/invitedstory-bg.png?r=1" /><div class="border-top"></div><div class="border-left"></div><div class="border-bottom"></div><div class="border-right"></div></div><!-- story-box  --></div><!-- microguide-story  --></span><!-- microguide-create --><? endif ?>
          <?php if (count($microguide->tags)): ?>
          <div class="microguide-tags tags responsive-margin-left" style="clear: both;">
            <span class="read-more">Read more stories in:</span>
            <a class="tag"><?=implode('</a> <a class="tag">', $microguide->tags)?></a>
          </div>
          <?php endif ?>
        </div><!-- microguide-columns -->

      </div>
    </div><!-- microguide-landing -->
