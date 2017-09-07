
<?php if (isset($stories)): ?> 

    <?php for ($col = 0, $columns = count($stories); $col != $columns; $col++): ?>
    <?php $stories_column = $stories[$col]?>

    <?php if (count($stories_column) > 0):?>

    <div class="column column_<?=$col?>">

    <?php if (isset($liwrap)): ?>
    <ul id=<?= json_encode($liwrap['ul']['id']) ?> class=<?= json_encode($liwrap['ul']['class']) ?>>
    <?php endif ?>

      <?php require '_stories_column.php'; ?>

    <?php if (isset($liwrap)): ?>
    </ul>
    <?php endif ?>

    </div>

    <?php endif?>

    <?php endfor?>
  <?php endif?>
