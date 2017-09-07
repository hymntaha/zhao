<div class="header">
  <hr />
</div>

<?php for ($i = 0; $i < count($usersMicroguides); $i++): ?>
  <?php $microguide = $usersMicroguides[$i];?>
  
  <div class="microguides-column microguides-small-column column-<?= $i?>">
    <div class="microguide-header"></div>
    <div class="story-box small-box">
      <div class="img-container" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>">
      <div class="slide-show-link"><div class="slide-show-icon"></div></div>
        <img src="<?=$microguide['coverPhoto']?>"/>
      </div>
      <div class="microguide-info">
        <div class="microguide-title" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>" ><?= $microguide['title']?></div>
        <div class="author">Curated by <a data-slug="<?= $microguide['authorSlug']?>"><?= $microguide['author']?></a></div>
      </div>
    </div>
  </div>
  
<?php endfor; ?>

<script type="text/javascript">
/* Any JS that needs to get called to display all of the microguides */
$(window).load(function() {
  microguides.featuredHandlers();
});

</script>