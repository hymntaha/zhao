
<div class="submissions mymicroguides">

  <div class="draft-microguides-container">

    <a name="draft"></a>
    <h1>Drafts</h1>

    <div class="draft-microguides microguides"></div>

    <div class="no-draft-microguides">
    You have no drafts.  <a href="/microguide/create">Get started on one!</a>
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="pending-microguides-container">

    <a name="pending"></a>
    <h1>Pending</h1>

    <div class="pending-microguides microguides"></div>

    <div class="no-pending-microguides">
    No microguides are pending.  <a href="/microguide/create">Create one!</a>
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="rejected-microguides-container">

    <a name="rejected"></a>
    <h1>Declined</h1>

    <div class="rejected-microguides microguides"></div>

    <div class="no-rejected-microguides">
    You have no declined microguides.
    </div>

    <div class="clear"></div>

    <hr />

  </div>

  <div class="accepted-microguides-container">

    <a name="accepted"></a>
    <h1>Published</h1>

    <div class="accepted-microguides microguides"></div>

    <div class="no-accepted-microguides">
    You have no Published microguides.
    </div>

    <div class="clear"></div>

  </div>

</div>

<script>
my.microguides.i(<?= json_encode($statuses) ?>);
</script>
