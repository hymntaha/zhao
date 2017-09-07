<div class="featured-microguides">
	<div class="featured-microguides-header">
          <hr/>
          <div class="see-all-microguides"><a class="see-all-microguides-link" href="/microguide/" onClick="br.handleSeeAllMicroguides(this, 'story detail page'); return false;">See all Microguides</a></div>
        </div>
	<div class="featured-microguides-row">
		<?php for ($i = 0, $max = count($featuredMicroguides); $i < $max; $i++):?>
		<?php $featuredMicroguide = $featuredMicroguides[$i];?>
		<div class="featured-microguides-column featured-microguides-column-<?= $i ?>">
			<div class="microguide-header"><img class="byc-modal-icon" src="/img/byc-microguide-icon.png" /></div>
			<div class="story-box large-box" >
				<div id="featured-microguide-image" class="img-container" data-slug="<?=$featuredMicroguide['slug']?>" data-title="<?= $featuredMicroguide['title']?>" >
					<div class="slide-show-link">
						<div class="slide-show-icon-wrapper">
							<div class="slide-show-icon"></div>
						</div>
					</div>
					<img src="<?=$featuredMicroguide['coverPhoto']?>"/>
				</div>
				<div class="microguide-info">
					<div id="featured-microguide-title" class="microguide-title" data-slug="<?=$featuredMicroguide['slug']?>" data-title="<?= $featuredMicroguide['title']?>" ><?= $featuredMicroguide['title']?></div>
					<div class="author">Curated by <a data-slug="<?= $featuredMicroguide['authorSlug']?>"><?= $featuredMicroguide['author']?></a></div>
				</div>
			</div>
		</div>
		<?php endfor;?>
	</div>
</div>

<script type="text/javascript">
/* Any JS that needs to get called to display all of the microguides */
$(window).load(function() {
  microguides.featuredHandlers();
});

</script>
