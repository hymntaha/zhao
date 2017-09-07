<?php for ($i = 0; $i < count($microguides); $i++): ?>
	<?php $offset = $i % ($smallColumns + $largeColumns);?>
	<?php $microguide = $microguides[$i];?>
	<?php if ( !$forceSmallColumns && ($forceLargeColumns || $offset < $largeColumns)): ?>
		<div class="microguides-column microguides-large-column column<?= ($offset - $smallColumns)?>">
			<div class="delete" data-microguide-id="<?=$microguide['_id']?>" data-microguide-status="<?=$microguide['status']?>"></div>

			<div class="microguide-header"><img class="byc-modal-icon" src="/img/byc-microguide-icon.png" /></div>
			<div class="story-box large-box" >
				<div class="img-container" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>" >
        <div class="slide-show-link"><div class="slide-show-icon"></div></div>
					<img src="<?=$microguide['coverStory']['largeThumbnail']?>"/>
				</div>
				<div class="microguide-info">
					<div class="microguide-title" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>" ><?= $microguide['title']?></div>
					<div class="author">Curated by <a data-slug="<?= $microguide['authorSlug']?>"><?= $microguide['author']?></a></div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="microguides-column microguides-small-column column<?= $offset?>">
			<div class="delete" data-microguide-id="<?=$microguide['_id']?>" data-microguide-status="<?=$microguide['status']?>"></div>

			<div class="microguide-header"></div>
			<div class="story-box small-box">
				<div class="img-container" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>" >
				<div class="slide-show-link"><div class="slide-show-icon"></div></div>
					<img src="<?=$microguide['coverStory']['smallThumbnail']?>"/>
				</div>
				<div class="microguide-info">
					<div class="microguide-title" data-slug="<?=$microguide['slug']?>" data-title="<?= $microguide['title']?>" ><?= $microguide['title']?></div>
					<div class="author">Curated by <a data-slug="<?= $microguide['authorSlug']?>"><?= $microguide['author']?></a></div>
				</div>
			</div>
		</div>
	<?php endif;?>
<?php endfor;?>



