Hi <?= $userName ?>,
  				
Your story has been approved! <?php if ($prompt): ?>If you'd like to share it with the subject's facebook page, we've made it easy.
  				
Just follow this link:<?php else: ?>Check it out here:<?php endif ?>

<?= $link ?>
  				
<?php require_once('tpl/_story_email_comments.php'); ?>
  				
-----
Bravo Your City!
info@bravoyourcity.com
2140 Shattuck Avenue
Suite 302
Berkeley, CA, USA
