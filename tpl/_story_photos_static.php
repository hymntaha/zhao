
  <div class="container">

    <div class="story">

      <div class="photos">

      <!-- odd images we start w/ the top one centered -->
      <?php if (count($story->photos)&1): ?>
        <div class="photo"
          data-path="<?=$story->photos[0]['path']['original']?>" 
          data-label="<?=isset($story->photos[0]['caption']) ? $story->photos[0]['caption'] : '&nbsp;'?>"
          data-height="<?=$story->photos[0]['height']?>" 
        >

          <div class="single"><img src="<?=$story->photos[0]['path']['800']?>" /></div>
          <div class="zoomicon-container" <?=$story->photos[0]['width'] < 800 ? 'style="width: '.$story->photos[0]['width'].'px"' : ''?>><div class="zoomicon zoomicon-white zoomhidden"></div></div>

          <label><?=isset($story->photos[0]['caption']) ? $story->photos[0]['caption'] : '&nbsp;'?></label>

        </div>
      <?php endif?>

        <!-- columns -->
        <?php if (count($story->photos) > 1):?>
        
        <?php if (count($story->photos)&1): ?>
        <?php $j = 1;?>
        <?php else:?>
        <?php $j = 0;?>
        <?php endif?>

        <!-- figure this math shit out DOG -->
        <?php foreach (array(0,1) as $column):?>
        <?php $j+= $column;?>
        <div class="column column_<?=$column?>">

          <?php for ( $i = $j, $max = count($story->photos); $i < $max; $i+=2):?>
          <div class="photo" 
            data-path="<?=$story->photos[$i]['path']['original']?>"
            data-label="<?=isset($story->photos[$i]['caption']) ? $story->photos[$i]['caption'] : '&nbsp;'?>"
            data-height="<?=$story->photos[$i]['height']?>" 
            data-width="<?=$story->photos[$i]['width']?>" 
            >
            <div class="double"><img src="<?=$story->photos[$i]['path'][390]?>" /></div>
            <div class="zoomicon-container" <?=$story->photos[$i]['width'] < 390 ? 'style="width: '.$story->photos[$i]['width'].'px"' : ''?>><div class="zoomicon zoomicon-white zoomhidden"></div></div>
            <label><?=isset($story->photos[$i]['caption']) ? $story->photos[$i]['caption'] : '&nbsp;'?></label>
          </div>

          <?php endfor?>
        </div>
        <?php endforeach?>

        <?php endif?>

        <div class="clear"></div>

      </div><!-- photos -->

    </div><!-- story -->

  </div><!-- container -->
