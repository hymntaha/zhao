
<div class="border-light"></div>

<div class="slides-outer-container">

  <div class="slides-arrow slides-arrow-left"><a></a></div>
  <div class="slides-left-margin"></div>
  <div class="slides-arrow slides-arrow-right"><a></a></div>
  <div class="slides-right-margin"></div>

<div class="slides-container">

  <div class="slides">
  <?php
      $initShift = 0; 
      $max = count($story->photos);
      if ($max == 2
          && $story->photos[0]['height'] == $story->photos[1]['height']
          && $story->photos[0]['height']/$story->photos[0]['width'] > 1.25
          ) { // do side-by-side if there are two images, they are same height, and portrait
        $imgDimensionIndex = '390x500';
        $imgDimensionAttribute = 'width="390"';
      } else {
        $imgDimensionIndex = 'x425';
        $imgDimensionAttribute = 'height="425"';
      }
  ?>
  <?php for ($i = 0, $sliderWidth=0; $i < $max; $i++): ?>
    <div id="photo-<?= $i ?>" class="slide">
    <?php $_i = $i; //we use this var to re-arrange the first and last items when appropriate ?>
    <?php if ($max > 1): ?>
    <?php 
            if ($max > 2 && !useragent::isMobile()) {
              if ($i == 0) {
                $_i = $max - 1;
              } elseif ($i == 1) {
                $_i = 0;
              } else {
                $_i = $i - 1;
              }
            }
      ?>
      <img class="standard-image" <?= $imgDimensionAttribute ?> src="<?=$story->photos[$_i]['path'][$imgDimensionIndex]?>" />
      <?php if ($max > 2 && $i == 0) { $initShift = $story->photos[$_i]['scaled'][$imgDimensionIndex]['width'] + 20; } ?>
    <?php else: ?>
      <img class="standard-image" src="<?=$story->photos[$_i]['path']['800x800']?>" />
    <?php endif ?>
    <img class="mobile-image" src="<?=$story->photos[$_i]['path']['640x360']?>" />
    <?php if (isset($story->photos[$_i]['caption']) && strlen($story->photos[$_i]['caption'])): ?>
      <div class="caption"><div><?= $story->photos[$_i]['caption'] ?></div></div>
    <?php endif ?>
    </div><!-- slide -->
    <?php $sliderWidth += $story->photos[$_i]['scaled']['x425']['width']; ?>
    <?php if ($i > 0) $sliderWidth += 20; ?>
  <?php endfor ?>
  </div>
</div>

</div>

<script type="text/javascript">
var sliderWidth = <?= $sliderWidth ?>;
var initShift = <?= $initShift ?>;
var initMarginLeft = null;
if (sliderWidth < $('.story').width() || $(".slides .slide").length == 1) {
  $(".slides").css({ textAlign: 'center' });
} else {
  if (sliderWidth < $(window).width()) {
    initShift = 0;
  }
  story.handleSliderClicks = true;
  story.slideCount = <?= count($story->photos) ?>;
  if (br.isMobileLayout()) {
    initMarginLeft = 0;
    story.sliderBaseLeft = 0;
    $('.mobile-image').css({width: $(window).width()});
  } else {
    initMarginLeft = $('.story').offset().left - 4 - initShift;
    story.sliderBaseLeft = $('.title').offset().left;
  }

  $(".slides").css({ marginLeft: initMarginLeft});
  $(".slides-outer-container").addClass("with-arrows");
}
</script>

