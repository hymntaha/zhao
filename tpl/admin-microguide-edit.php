<?php require_once('header.php'); ?>

<div class="outer-container">
  <div class="container">

    <div class="adminpage microguide-edit">

      <a href="/admin/microguides/">&lt;back to all microguides</a>
      <h1>Edit Microguide</h1>

      <input id="microguideid" value="<?= $microguide->_id ?>" data-slug="<?= $microguide->slug ?>" type="hidden" />

      <div>
        <label for="status">Status:</label>
        <?php foreach (array('draft' => 'draft', 'pending' => 'pending', 'rejected' => 'declined', 'accepted' => 'accepted') as $status => $displayStatus): ?>
        <input type="radio" name="status" value="<?= $status ?>" <?= ($microguide->status == $status) ? 'checked' : '' ?> /> <?= $displayStatus ?>
        <?php endforeach ?>
      </div>

      <div>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlentities($microguide->title, ENT_COMPAT, 'UTF-8') ?>" />
      </div>

      <div>
        <label for="slug">URL:</label>
        <?= G_URL ?>microguide/<input type="text" id="slug" name="slug" value="<?= htmlentities($microguide->slug, ENT_COMPAT, 'UTF-8') ?>" />
        <span class="view-microguide"><a target="_blank">view microguide</a></span>
        <div class="warning">*note: changing this value could break existing links.</div>
      </div>

      <div>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" data-slug="<?= $microguide->authorSlug ?>" value="<?= htmlentities($microguide->author, ENT_COMPAT, 'UTF-8') ?>" />

      <br /><br />
      <div>
        <label>Stories:</label> (not-live stories are highlighted)
        <ul id="story-list">
        <?php for ($i = 0, $max = count($microguide->stories); $i < $max; $i++): ?>
        <?php $story = $microguide->stories[$i]; ?>
          <li id="story-<?= $i ?>" data-storyid="<?= $story->_id ?>" class="<?= $story->status != 'accepted' ? 'not-live' : '' ?>">
            <a class="remove-story">[x]</a>
            <a class="view-story" target="_blank" href="/story/<?= $story->slug ?>"><?= htmlentities($story->title, ENT_COMPAT, 'UTF-8') ?> <?= $story->status != 'accepted' ? '(<em>' . $story->status . '</em>)' : '' ?></a>
          </li>
        <?php endfor ?>
       </ul>
       <ul>
          <li>
            <input type="text" id="new-story" />
          </li>
       </ul>
      </div>

      <div class="action-buttons">
        <input id="save" type="button" value="save" />
        <input id="delete" type="button" value="delete" />
      </div>

      <br /><br />
      <div>
        <a href="/share/?invited=1&microguide=<?= $microguide->slug ?>&returnurl=<?= G_URL ?>admin/microguides/edit/<?= $microguide->slug ?>">Invite a story</a> (this will navigate away from the page; be sure to save your work first)
      </div>

    </div>


  </div><!-- container -->
</div><!-- outer-container -->

<script type="text/javascript">
admin.microguideEdit.i();
</script>

<?php require_once('tpl/footer.php'); ?>