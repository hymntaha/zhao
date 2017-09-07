<?php require_once('header.php'); ?>

<div class="outer-container">
  <div class="container">

    <div class="adminpage">
      <p>
        <a href="/microguide/create/">&gt; Create a new microguide&gt;</a>
      </p>
      <?php foreach (array('pending' => 'Pending', 'draft' => 'Draft' , 'accepted' => 'Published', 'rejected' => 'Declined') as $status => $displayStatus): ?>
      <p>
        <?= $displayStatus ?>:
        <ul style="list-style-type: none;">
        <?php foreach ($microguides as $m): ?>
          <?php   if ($m->status != $status) continue; ?>
          <li>
            <a href="/microguide/create/<?= $m->slug ?>"><?= $m->title ?></a>, by <?= $m->author ?> 
            <a href="/admin/exportEbook/<?= $m->slug ?>?export=false">Export to EPub</a>
          </li>
        <?php endforeach ?>
        </ul>
      </p>
      <?php endforeach ?>
    </div>

  </div><!-- container -->
</div><!-- outer-container -->

<?php require_once('tpl/footer.php'); ?>