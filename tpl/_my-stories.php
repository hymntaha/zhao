
<div class="submissions">

  <div class="draft-stories-container">

    <a name="draft"></a>
    <h1>Drafts</h1>

    <div class="draft-stories"></div>

    <div class="no-draft-stories">
    You have no drafts.  <a href="/share/">Get started on one!</a>
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="pending-stories-container">

    <a name="pending"></a>
    <h1>Pending stories</h1>

    <div class="pending-stories"></div>

    <div class="no-pending-stories">
    No stories are pending.  <a href="/share/">Submit one!</a>
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="rejected-stories-container">

    <a name="rejected"></a>
    <h1>Declined Stories</h1>

    <div class="rejected-stories"></div>

    <div class="no-rejected-stories">
    You have no declined stories.
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="accepted-stories-container">

    <a name="accepted"></a>
    <h1>Published Stories</h1>

    <div class="accepted-stories"></div>

    <div class="no-accepted-stories">
    You have no Published stories.
    </div>

    <div class="clear"></div>

  </div>

</div>

<script>
my.stories.infiniteScroll = true;
my.stories.i(<?= json_encode($statuses) ?>);
</script>

