  
      <?php foreach ($stories_column as $id=>$story): ?>

      <?php if (isset($liwrap)): ?>
      <li data-slug="<?=$story['slug']?>">
      <?php endif ?>

      <div class="stories" >

        <div class="delete" data-story-id="<?=$id?>" data-story-status="<?=$story['status']?>"></div>

        <?php if (isset($story['storyIndex'])): ?>
        <div class="index"><?= $story['storyIndex'] ?></div>
        <?php endif ?>
        <div class="thumbnail" data-slug="<?=$story['slug']?>"><img src="<?= $story['thumbnail'] ?>" /></div>
        <div class="title" data-slug="<?=$story['slug']?>"><?=$story['title']?></div>
        <hr class = "separator">
        <div class="summary"><?=$story['text_short']?></div>

        <div class="stories-footer">

          <div class = "bravo">
            <span 
              class="bravoSmall bravo<?=isset($bravos[$id]) ? 'On' : 'Off'?>" 
              data-tip-before="bravo this story" 
              data-tip-after="bravo it again!"
              data-datas='<?=json_encode(array('slug' => $story['slug'], 'title' => $story['title'], 'id' => $id), JSON_HEX_APOS)?>'
            >
            </span>
                                        
            <span class = "bravo-count-bubble <?php if ($story['bravoCount'] > 0):?>visible<?php else: ?>hidden<?php endif?>">
            <div class="bravo-count-left grey-triangle"></div><div class="bravo-count-left white-foreground-triangle"></div>
            <span class = "bravo-count-middle"><span class="bravo_count"><?= $story['bravoCount'] ?></span></span>
            </span>

            <?php if (user::isAdmin()): ?>
            <a href='/share/<?=$story['slug']?>' class="bravo-a-button"></a>
            <?php endif?>
          </div>
          <div class="author" title="created <?=$story['created_readable']?>">by 
            <a data-slug="<?=$story['authorSlug']?>"><?=$story['author']?></a>
            <?=$story['created_diff']?> ago
          </div>
        
        </div>

      </div>

      <?php if (isset($liwrap)): ?>
      </li>
      <?php endif ?>

      <?php endforeach?>
