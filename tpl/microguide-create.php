
<?php  require_once('tpl/header.php'); ?>

<div class="outer-container microguide-create-header">

  <div class="top-navigation">
    <div class="top-navigation-hoverable">
      <div class="left-arrow"></div>
      <div class="my-microguides">My Microguides</div>
    </div>
  </div>

  <div class="about-my-microguides">
    <div class="about-my-microguides-image"></div>
    <div class="about-my-microguides-text">Make a mini-magazine with multiple stories and Microguide your life!</div>
  </div>

</div>

<div class="outer-container microguide-create">

  <?php if (user::isAdmin()): ?>

  <div class="field-wrapper">
    <div class="field-row admin">
      <div class="field-label status">
          Status:
      </div>
      <div class="field-input">
        <?php foreach (array('draft' => 'draft', 'pending' => 'pending', 'rejected' => 'declined', 'accepted' => 'accepted') as $status => $displayStatus): ?>
   <input type="radio" name="status" value="<?= $status ?>" <?= (!$microguide->status && $status == 'draft') ? 'checked' : '' ?> <?= ($microguide->status == $status) ? 'checked' : '' ?> /> <?= $displayStatus ?>
        <?php endforeach ?>
      </div>
    </div>
  </div>

  <hr class="field-separator" />
  <?php endif ?>
  
  <div class="field-wrapper">
    <div class="field-row">
      <div class="field-label">
          Can other people contribute stories to this Microguide?
      </div>
      <div class="field-input">
          <input name="access" value="open" type="radio" <?= $microguide->access == 'open' ? 'checked' : ''?>/>
          <span class="microguide-access-type">Yes, make this an open guide</span> &nbsp; &nbsp;
          <input name="access" value="closed" type="radio" <?= $microguide->access == 'closed' ? 'checked' : '' ?>/>
          <span class="microguide-access-type">No, make this a closed guide</span>
      </div>
    </div>
  </div>

  <hr class="field-separator" />

  <div class="field-wrapper">
    <div class="field-row">
      <div class="field-label">
          Name your Microguide:
      </div>
      <div class="field-input">
        <input id="title" class="input-text text" data-tip="<?= htmlentities($data['title']['tip']) ?>" type="text" value="<?= htmlentities($microguide->title ? $microguide->title : $data['title']['tip']) ?>" />
      </div>
    </div>
  </div>

  <hr class="field-separator" />

  <?php if (user::isAdmin()): ?>
  <div class="field-wrapper">
    <div class="field-row admin">
      <div class="field-label">
          Curator:
      </div>
      <div class="field-input">
        <input type="text" id="author" name="author" data-slug="<?= $microguide->authorSlug ?>" value="<?= htmlentities($microguide->author, ENT_COMPAT, 'UTF-8') ?>" />
      </div>
    </div>
  </div>

  <hr class="field-separator" />
  <?php endif ?>

  <hr class="field-separator" />

  <?php if (user::isAdmin()): ?>
  <div class="field-wrapper">
    <div class="field-row admin">
      <div class="field-label">
          Ibooks Link:
      </div>
      <div class="field-input">
        <input type="text" id="itunes" name="itunes" data-slug="<?= $microguide->availableOn['itunes'] ?>" value="<?= htmlentities($microguide->availableOn['itunes'], ENT_COMPAT, 'UTF-8') ?>" />
      </div>
    </div>
  </div>
  <div class="field-wrapper">
    <div class="field-row admin">
      <div class="field-label">
          Kindle Link:
      </div>
      <div class="field-input">
        <input type="text" id="kindle" name="kindle" data-slug="<?= $microguide->availableOn['kindle'] ?>" value="<?= htmlentities($microguide->availableOn['kindle'], ENT_COMPAT, 'UTF-8') ?>" />
      </div>
    </div>
  </div>

  <hr class="field-separator" />
  <?php endif ?>

  <div class="field-wrapper">
    <div class="field-row">
      <div class="field-label">
        What is your Microguide about?
        <div class="field-label-instruction">A description helps people find your guide</div>
        <div class="word-count"></div>
      </div>
      <div class="field-input">
      <textarea id="description" class="input-text text" rows="4" data-tip="<?= htmlentities($data['description']['tip']) ?>"><?= htmlentities($microguide->description ? $microguide->description : $data['description']['tip']) ?></textarea>
      </div>
    </div>
  </div>

  <hr class="field-separator" />

  <div class="field-wrapper">
    <div class="field-row">
      <div class="field-label">
        Add tags
        <div class="field-label-instruction">Enter tags seperated by commas or just hit enter after each tag</div>
      </div>
      <div class="field-input">
        <input id="tags" class="input-text text" data-tip="" type="text" />

        <div class="tags">
          <?php foreach ($microguide->tags as $tag):?>
          <div class="tag"><div class="tag_close"></div><p><?=$tag?></p></div>
          <?php endforeach?>
        </div>

      </div>
    </div>

  </div>

  <?php if (user::isAdmin()): ?>
  <hr class="field-separator" />

  <div class="field-wrapper">
    <div class="field-row admin">
      <div class="field-label">
          Merry Go Round:
      </div>
      <div class="field-input">
        <input id="featureid" type="hidden" value="<?= $feature['_id'] ?>" />
        <div>
          Status:
          <input name="featurestatus" value="active" type="radio" <?= $feature && $feature['status'] == 'active' ? 'checked' : ''?>/> active
          <input name="featurestatus" value="inactive" type="radio" <?= !$feature || $feature['status'] == 'inactive' ? 'checked' : '' ?>/> inactive
        </div>
        <div>
          Title for front page:
          <input id="featurequestion" value="<?= $feature ? strip_tags($feature['question']) : '' ?>" class="input-text text" type="text" style="float: none;"/>
        </div>
        <div>
          Identifying word:
          <input id="featurequestionverb" value="<?= $feature ? strip_tags($feature['questionVerb']) : '' ?>" class="input-text text" type="text" style="float: none;"/>
        </div>
        <div>
          Identifying sub-word:
          <input id="featurequestionplace" value="<?= $feature ? strip_tags($feature['questionPlace']) : '' ?>" class="input-text text" type="text" style="float: none;"/>
        </div>
      </div>
    </div>
  </div>

  <?php endif ?>

  <input id="microguideid" type="hidden" value="<?= $microguide->_id ?>" data-slug="<?= $microguide->slug ?>" />

</div>

<div class="clear"></div>

<div class="outer-container microguide-create stories-container">

  <div class="container">

    <div class="microguide-landing">

      <div class="microguide-container">

          <span id="story-list"><?php include_once('tpl/_stories-list.php'); ?></span><div class="microguide-story add-story preview"><div class="story-box" data-story_slug="new" data-index="" data-story_title=""><div class="story-box-overlay"><div class="story-heading add-story-icon-wrapper"><div class="add-story-icon"></div><div class="add-story-label">Add a story</div></div></div><img src="/img/invitedstory-bg.png?r=1" /><div class="border-top"></div><div class="border-left"></div><div class="border-bottom"></div><div class="border-right"></div></div><!-- story-box  --></div><!-- microguide-story  -->

      </div><!-- microguide-container -->

    </div><!-- microguide-landing -->

  </div>

</div>

<div id="new-story">
  <div class="new-story-carrot"></div>
  <div class="new-story-search-wrapper">
    <div class="search-icon"></div>
    <input type="text" id="new-story-search" class="input-text text" data-tip="Find a story by title or keyword, or paste URL" value="Find a story by title or keyword, or paste URL" />
  </div>
  <div>Or <a data-action="save" data-post-action="createStory" class="create-new-story">create a new story</a></div>
  <?php if (user::isAdmin()): ?>
  <div>Or <a data-action="save" data-post-action="createStory"  data-invited="1" class="create-new-story">create a new invited story</a> (admin only)</div>
  <?php endif ?>
</div>

<div class="clear"></div>

<div class="outer-container microguide-create microguide-create-footer">

  <div class="submit-buttons">
    <?php if (user::isAdmin()): ?>
    <a class="button save" data-action="save" data-post-action="viewAllMicroguides">Save</a>
    <?php else: ?>
    <a class="button save" data-action="save" data-post-action="viewMyMicroguides">Save as Draft</a>
    <a class="button save" data-action="submit">Submit to Editors</a>
    <?php endif ?>
  </div>

</div>

<script type="text/javascript">
  microguide.edit.i();
  microguide.edit.tags = <?= json_encode($microguide->tags); ?>;
</script>

<?php  require_once('tpl/footer.php'); ?>
