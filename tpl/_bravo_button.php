<div class="bravo-container">
<div data-tip-before="bravo this story"
	data-tip-after="bravo it again!"
	data-datas='<?=json_encode(array('slug' => $story->slug, 'title' => $story->title, 'id' => $story->id(true)), JSON_HEX_APOS)?>'
	class="bravo<?=($bravoSize == "small") ? 'Small': 'Large'?> bravo<?=($bravoed) ? 'On' : 'Off'?>"> &nbsp;</div>
<div
	class="bravo-count-bubble <?php if ($story->bravoCount > 0):?>visible<?php else: ?>hidden<?php endif?>">
	<div class="bravo-count-left grey-triangle"></div><div class="bravo-count-left <?=($backgroundGrey) ? 'grey-foreground-triangle' : 'white-foreground-triangle' ?>"></div>
	<div class="bravo-count-middle">
		<span class="bravo_count"><?= $story->bravoCount ?> </span>
	</div>
</div>
</div>
