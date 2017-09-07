<?php // NB: inter-div whitespace is intentionally removed below, to achieve the desired layout ?>
<?php for ($i = 0, $max = count($stories); $i < $max; $i++): ?>
  <?php $story = $stories[$i]; ?>
  <div class="microguide-story <?= in_array($story->status, array('invited','draft','pending')) ? 'preview ' . $story->status : '' ?>">
    <div class="story-box" data-story-slug="<?=($story->slug)?>" data-index="<?=($storyListOffset+$i+1)?>" data-story-id="<?=($story->_id)?>">
      <div class="delete" data-story-id="<?=$story->_id?>"></div>
      <div class="story-box-overlay">
        <div class="story-heading">
          <div class="story-num">
            <hr/>
            <span class="story-number"><?=($storyListOffset+$i+1)?></span>
            <hr/>
          </div>
          <div class="story-title"><?=$story->title?></div>
        </div>
      </div>
      <? $storyImage = $story->status == 'draft' ? '/img/invitedstory-bg.png?r=1' : '/img/invitedstory-bg.png?r=1' ?>
      <img src="<?= $story->status == 'accepted' ? image::scaleUrl($story->photos[0]['id'], 'cropThumbnail', 230, 171) : $storyImage ?>" />
      <div class="preview-info">
        <div class="exclamation">
          
        </div>
        <div>
          <a class="button"><?= $story->status == 'invited' ? 'Claim It!' : $story->status ?></a>
        </div>
      </div><!-- preview-info -->
      <div class="border-top"></div>
      <div class="border-left"></div>
      <div class="border-bottom"></div>
      <div class="border-right"></div>
    </div><!-- story-box  -->
  </div><!-- microguide-story  -->
<?php endfor ?>