<?php if (isset($_SESSION['user'])):?>

  <?php $messages = message::getDisplayRegionMessages(message::DISPLAY_REGION_HOME_PAGE_TOP); ?>
  <?php if (count($messages)): ?>
  <?php $message = $messages[0]; ?>
  <div class="message message-full-width">
    <a class="close" href="/message/hide/<?= $message['_id'] ?>?returnUrl=<?= G_URL ?>"><span class="close-icon"></span></a><img class="beacon" src="<?= G_URL ?>message/logView/<?= $message['_id'] ?>"/>
    <?= $message['text'] ?>
  </div>
  <?php endif ?>

<?php endif ?>

<div class="moz-hack" style="position:relative;">

<div class="container container-home-top container-home">

  <div class="container-home-row">

    <div class="container-home-row-spacer home-top-left-spacer"> </div>

    <div class="home-top-left">

      <div class="global-stories">

        <div class="global-stories-inner">

          <div class="global-stories-inner-text">Global Stories</div>
          <div><hr /><span class="global-stories-inner-text global-stories-inner-by">BY</span><hr /></div>
          <div class="global-stories-inner-text">Local People</div>

        </div>

      </div><!-- global-stories -->

      <div class="contributor-photos">
        <div class="contributor-photos-upper-row">
          <div class="contributor-photos-left"><a href="<?= $contributor_info[0][1] ?>"><img src="<?= $contributor_info[0][0] ?>" /></a></div>
          <div class="contributor-photos-right"><a href="<?= $contributor_info[1][1] ?>"><img src="<?= $contributor_info[1][0] ?>" /></a></div>
        </div>
        <div class="clear"></div>
        <div class="contributor-photos-lower-row">
          <div class="contributor-photos-left"><a href="<?= $contributor_info[2][1] ?>"><img src="<?= $contributor_info[2][0] ?>" /></a></div>
          <div class="contributor-photos-right"><a href="<?= $contributor_info[3][1] ?>"><img src="<?= $contributor_info[3][0] ?>" /></a></div>
        </div>
      </div><!-- contributor-photos -->
      <div class="clear"></div>

      <hr class="top-left-separator" />

      <div class="fifty-fifty">
        <div class="fifty-fifty-fraction">
          <div class="fifty-fifty-numerator">50</div>
          <hr />
          <div class="fifty-fifty-denominator">50</div>
        </div>
        <div class="fifty-fifty-about">
          <div>Anyone can submit a story! We will pay global people 50% of the profit from their BYC stories.</div>
          <div class=""><a href="/static/fifty">Learn about our mission&nbsp;&gt;</a></div>
        </div>
      </div><!-- fifty-fifty -->
      <div class="clear"></div>

    </div><!-- home-top-left -->

    <div class="container-home-row-spacer"> </div>

    <div class="home-top-right">

      <div class="question">
        <?= $features[0]['question'] ?>
      </div>

      <div class="extra-featured-microguides">
      <?php for ($j = 1, $max = 5; $j < $max; $j++): ?>
        <?php $microguide = $features[$j];?>
        <div class="microguide-container container<?=$j?>">
          <div class="microguide-box" data-slug="" data-index="" data-title="">
            <div class="microguide-heading">
              <div class="microguide-num"></div>
              <div class="microguide-title"></div>
            </div>
            <img src="" />
          </div><!-- microguide-box  -->
        </div><!-- microguide-story  -->
      <?php endfor?>
      </div><!-- extra-featured-microguides -->

      <div id="featured-microguides-scroller">
        <?php for ($j = 0, $max = count($features); $j < $max; $j++): ?>
        <div id="scroller-item-<?= $j ?>" class="featured-microguides-image-nav" <?= $j > 0 ? 'style="display: none;"' : ''?>>
          <div class="slide-show-header">
            <div class="microguide-icon"> </div>
            <div class="microguide-label">MICROGUIDE</div>
          </div>
          <img src="<?= $features[$j]['coverPhoto'] ?>" <?= $j > 0 ? '' : 'onload="home.initScroller();"'?> />
          <div id="home-slide-show-link" class="slide-show-link" data-slug="<?= $features[$j]['microguideSlug']?>"><div class="slide-show-icon"><img src="/img/sprite/home-top-elements.png?r=1" /> </div><div class="slide-show-icon-shim">&nbsp;</div></div>
        </div>
        <?php endfor ?>
      </div><!-- featured-microguides-scroller -->
  
      <div class="featured-microguides-answer">    
        <div class="featured-microguide-info-pointer">&nbsp;</div>
        <div class="featured-microguide-info">
          <div id="home-featured-microguide-title" class="featured-microguide-title"><a class="major-nav microguideTitle"><?= $features[0]['microguideTitle'] ?></a></div>
          <div class="author">Curated by <a data-slug="<?= $features[0]['microguideAuthorSlug'] ?>" class="microguideAuthor"><?= $features[0]['microguideAuthor'] ?></a></div>
        </div>
      </div><!-- featured-micrguides-answer -->

      <div class="nav-items">
        <?php for ($i = 0, $max = count($features); $i < $max; $i++): ?>
          <div id="home-nav-item-<?= $i ?>" class="nav-item-<?= $i ?> nav-item nav-normal" style="" data-slug="<?= $features[$i]['microguideSlug']?>">
            <div><span class="nav-item-question-verb"><?= $features[$i]['questionVerb'] ?></span></div>
            <div class="nav-item-question-place"><?= $features[$i]['questionPlace'] ?></div>
          </div>
        <?php endfor ?>
      </div><!-- nav-items -->

      <div class="see-all-microguides"><a class="see-all-microguides-link" href="/microguide/" onClick="br.handleSeeAllMicroguides(this, 'home page'); return false;">See all Microguides</a></div>

    </div><!-- home-top-right -->

    <div class="clear"></div>

  </div><!-- container-home-row -->

</div><!-- container-home-top -->

</div><!-- moz hack -->

<div style="clear: both;"></div>

<hr class="responsive-margin-left container-home" />

<div class="container container-home-nav">

  <div class="home-sort-criteria">
    <div class="home-sort-criteria-row">
      <div class="container-home-row-spacer"> </div>
      <a class="major-nav home-sort-criteria-cell highlighted" data-query='["newest", "sort"]'>Recent Stories</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["most", "sort"]'>Most Popular</a>
<!--      <a class="major-nav home-sort-criteria-cell" data-query='["editors", "special"]'>Editor&apos;s Picks</a> -->
      <a class="major-nav home-sort-criteria-cell" data-query='[["New York", "query"], ["NYC", "query"]]'>New York</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["San Francisco", "query"]'>San Francisco</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["Hong Kong", "query"]'>Hong Kong</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["Seoul", "query"]'>Seoul</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["Paris", "query"]'>Paris</a>
      <a class="major-nav home-sort-criteria-cell" data-query='["Ireland", "query"]'>Ireland</a>
    </div><!-- home-sort-criteria-row -->
  </div><!-- home-sort-criteria -->

</div><!-- container-home-nav -->

<script>
$('.home-sort-criteria-cell').on('click', function() {
  search.multiSearch($(this).data('query'));
  $('.home-sort-criteria-cell').removeClass('highlighted');
  $(this).addClass('highlighted');
});
</script>
