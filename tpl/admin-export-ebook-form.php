<?php require_once('header.php'); ?>

<div class="outer-container">
  <div class="container">

    <div class="adminpage export-ebook-form">
      <h1>Configure EPub</h1>
      <h2><?=$microguide->title?></h2>
      
      <div>
        <form action="/admin/exportEbook/<?=$microguide->slug?>" method="post" enctype="multipart/form-data">
          
          <div class="cover-image image-container">
            <h3>Cover Image</h3>
            <input type="button" name="upload" value="Select Cover Photo" class="button upload_button" id="upload-cover" />
            <div class="content"></div>
          </div>
          <div class="front-ad image-container">
            <h3>Front Ad</h3>
            <input type="button" name="upload" value="Select Front Ad" class="button upload_button" id="upload-front-ad" />
            <div class="content hidden">
              <input type="radio" class="ad-link-type" name="front-ad-link-type" value="http" checked> Http Link
              <input type="radio" class="ad-link-type" name="front-ad-link-type" value="mailto"> Mailto Link <br/>
              <span class="http-link input-links">Link: <input type="text" name="front-ad-http" data-help="No http:// necessary" class="input-text"/></span>
              <div class="mailto-link input-links hidden">
                Email Address: <input type="text" name="front-ad-email-address" /> <br/>
                Subject: <input type="text" class="input-text" name="front-ad-subject" data-help="This will be prepopulated when the user clicks on the link" />
              </div>
            </div>
          </div>
          <div class="rear-ad image-container">
            <h3>Rear Ad</h3>
            <input type="button" name="upload" value="Select Rear Ad" class="button upload_button" id="upload-rear-ad" />
            <div class="content hidden">
              <input type="radio" class="ad-link-type" name="rear-ad-link-type" value="http" checked> Http Link
              <input type="radio" class="ad-link-type" name="rear-ad-link-type" value="mailto"> Mailto Link <br/>
              <span class="http-link input-links">Link: <input type="text" name="rear-ad-http" data-help="No http:// necessary" class="input-text"/></span>
              <div class="mailto-link input-links hidden">
                Email Address: <input type="text" name="rear-ad-email-address" /> <br/>
                Subject: <input type="text" class="input-text" name="rear-ad-subject" data-help="This will be prepopulated when the user clicks on the link" />
              </div>
              
            </div>
          </div>
          
          <div class="clear"></div>
          
          <p><h5>The default compression should be fine for most microguides. However, if you need to make an
            ebook smaller for the kindle store, you can manipulate this to make the filesize smaller. Be careful of going too
            far--some images look worse than others at lower compression qualities. 
            Please be patient waiting for your ebook to download.</h5></p>
          
          <div class="slider-container">
            <label for="amount">Compression:</label>
            <input type="text" id="amount-compression" name="compression"/>
            <div id="slider-compression" class="slider-bar"></div>
          </div>
          
          <div class="clear"></div>
          
          <label>Include Author's Bio:</label><input type="checkbox" name="bio" checked/>
          <label>Display Author's Byline on Each Story:</label><input type="checkbox" name="byline_stories" checked/>
          
          <input type="file" name="cover" value="" id="real-upload-cover" data-target="cover-image" class="real_upload_button" />
          <input type="file" name="rear_ad" value="" id="real-upload-rear-ad" data-target="rear-ad" class="real_upload_button" />
          <input type="file" name="front_ad" value="" id="real-upload-front-ad" data-target="front-ad" class="real_upload_button" />
          <input type="hidden" name="export" value="true" />
          <input id="export-ebook" type="submit" class="button submit" name="submit" value="Export Ebook" />
          
        </form>   
      </div>
    </div>

  </div><!-- container -->
</div><!-- outer-container -->

<script type="text/javascript">
admin.exportEbook.i();
</script>

<?php require_once('tpl/footer.php'); ?>