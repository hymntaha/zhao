<div class="related-microguides">
  <div class="related-microguides-row">
    <? if (count($containingMicroguides) == 1):?>
    <h5>See this story in this Microguide:</h5>
    <div class="border-light"></div>
    <? elseif (count($containingMicroguides) > 1): ?>
    <h5>See this story in these Microguides:</h5>
    <div class="border-light"></div>
    <? endif ?>
    <?php for ($i = 0, $max = count($containingMicroguides); $i < $max; $i++):?>
    <?php $relatedMicroguide = $containingMicroguides[$i];?>
    <div class="related-microguides-column related-microguides-column-<?= $i ?>">
      <div class="microguide-header"><img class="byc-modal-icon" src="/img/byc-microguide-icon.png" /></div>
      <div class="story-box large-box" >
        <div id="related-microguide-image" class="img-container" data-slug="<?=$relatedMicroguide['slug']?>" data-title="<?= $relatedMicroguide['title']?>"> 
          <div class="slide-show-link">
            <div class="slide-show-icon-wrapper">
              <div class="slide-show-icon"></div>
            </div>
          </div>
          <img src="<?=$relatedMicroguide['coverPhoto']?>"/>
        </div>
      </div>
      <div class="microguide-info">
        <div id="related-microguide-title" class="microguide-title" data-slug="<?=$relatedMicroguide['slug']?>" data-title="<?= $relatedMicroguide['title']?>" ><?= $relatedMicroguide['title']?></div>
        <span class="author">Curated by <a data-slug="<?= $relatedMicroguide['authorSlug']?>"><?= $relatedMicroguide['author']?></a></span>
      </div>
    </div>
    <?php endfor;?>
  </div>
</div>

<script type="text/javascript">
/* Any JS that needs to get called to display all of the microguides */
$(window).load(function() {
  microguides.relatedHandlers();
});

</script>
