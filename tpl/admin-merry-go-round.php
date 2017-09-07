<?php require_once('header.php'); ?>

<div class="outer-container">
  <div class="container">

    <div class="merry-go-round-preview">

    <?php foreach ($featureDates as $date => $features): ?>
      <div><a href="/?previewdate=<?= $date ?>" target="_blank"><?= $date ?></a></div>
      <ul>
      <?php foreach ($features as $feature): ?>
     <li><?= $feature['question'] ?> (<?= $microguides[(string)$feature['microguideId']]['title'] ?>)</li>
      <?php endforeach ?>
      </ul>
    <?php endforeach ?>

    </div><!-- merry-go-round-preview -->

  </div><!-- container -->
</div><!-- outer-container -->

<?php require_once('tpl/footer.php'); ?>