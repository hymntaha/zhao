
<?php  require_once('tpl/header.php'); ?>

<div class="outer-container submissions-header">

  <div class="container submissions-stats" style="min-width: 80%;">
    <select id="my-selector" class="header">
      <option value="stories">MY STORIES</option>
      <option value="microguides">MY MICROGUIDES</option>
    </select>
    <a class="major-nav navlink-draft" href="#draft">Drafts (<span class="number-draft"></span>)</a>
    <a class="major-nav navlink-pending" href="#pending">Pending (<span class="number-pending"></span>)</a>
    <a class="major-nav navlink-rejected" href="#rejected">Declined (<span class="number-rejected"></span>)</a>
    <a class="major-nav navlink-accepted last" href="#accepted">Published (<span class="number-accepted"></span>)</a>
    <a class="major-nav navlink-submit-a-story button last" href="/share" alt="Submit a Story">Submit a Story</a>
  </div>

</div>

<div class="outer-container">

  <div class="container settings-container">

<?php  require_once 'tpl/_my-stories.php'; ?>

  <div class="clear"></div>

  </div>
</div>

<?php  require_once 'tpl/footer.php'; ?>
