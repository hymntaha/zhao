
<?php require_once('header.php'); ?>

<?php if (isset($story) && $story->status == 'invited'): ?>
  <?php require('tpl/_invited-steps.php'); ?>
<?php else: ?>
<?php if (isset($_SESSION['user'])):?>

    <?php $messages = message::getDisplayRegionMessages(message::DISPLAY_REGION_SHARE_PAGE_TOP); ?>
    <?php if (count($messages)): ?>
    <?php $message = $messages[0]; ?>
    <div class="message message-full-width">
      <a class="close" href="/message/hide/<?= $message['_id'] ?>?returnUrl=<?= G_URL ?>share"><span class="close-icon"></span></a><img class="beacon" src="<?= G_URL ?>message/logView/<?= $message['_id'] ?>"/>
      <?= $message['text'] ?>
    </div>
    <?php endif ?>

    <?php else: ?>
    <div class="message message-share-page-top">
       <?php require('tpl/_share-steps.php'); ?>
    </div>
<?php endif ?>
<?php endif ?>

<div class="outer-container">

  <div class="container">

  <div class="content_main">

    <div class="share"> 

      <?php if (isset($_REQUEST['returnurl'])): ?>
      <input type="hidden" id="returnurl" value="<?= htmlentities($_REQUEST['returnurl']) ?>" />
      <?php endif ?>

      <input type="hidden" value="<?=$bio?>" id="bio" name="bio" />

      <?php if ($bio):?>
      <div class="share_header">update your bio</div>
      <?php else:?>
      <?php if (!isset($story)): ?>
      <div class="share_header">share your story</div>
        <?php if (isset($_REQUEST['microguide'])): ?>
      <input type="hidden" id="microguide" value="<?= htmlentities($_REQUEST['microguide']) ?>" />
        <?php endif ?>
        <?php if (user::isAdmin()): ?>
          <?php if (isset($_REQUEST['invited']) && $_REQUEST['invited'] == 1): ?>
      <input type="hidden" id="status" value="invited" />
          <?php endif ?>
        <?php endif ?>
      <?php endif?>
      <?php endif?>
      <div class="share_border"></div>
      <div class="browser-message hidden"></div>

      <?php if (user::isAdmin() && isset($story)):?>
      <ul class="adminStatus">
        <li data-id="<?=$story->id()?>" <?=($story->status == 'invited' ? 'class="active"' : '')?>>invited</li>
        <li data-id="<?=$story->id()?>" <?=($story->status == 'pending' ? 'class="active"' : '')?>>pending</li>
        <li data-id="<?=$story->id()?>" <?=($story->status == 'accepted' ? 'class="active"' : '')?>>accepted</li>
        <li data-id="<?=$story->id()?>" <?=($story->status == 'rejected' ? 'class="active"' : '')?>>rejected</li>
      </ul>
      <div class="facebook-share hidden" title="Would you like to prompt the author to contact a facebook page?"></div>
      <div class="accepted-message" title="Confirm message">
       <div class="accepted-header">Modify the contents of the message as you&apos;d like and then press Send:</div>
      	<div class="accepted-content"><textarea class="accepted-textarea"></textarea></div>
      </div>
      <div class="rejected-message hidden" title="Would you like to notify the author their story has been declined?">
      	<div class="rejected-header">Modify the contents of the message as you'd like and then press Send:</div>
      	<div class="rejected-content"><textarea class="rejected-textarea"></textarea></div>
      </div>
      <?php endif?>

      <?php if ($bio):?>

      <div class="clear"></div>

      <label>your name:</label>
      <input type="text" name="username" id="username" value="<?= $_SESSION['user']['username'] ?>" class="input-text" data-tip="<?= $_SESSION['user']['username'] ?>" data-help="Enter your Full Name" />

      <?php endif ?>

      <?php if (!$bio):?>
      <label>title:</label>
      <input type="text" name="title" id="title" class="input-text" 
        <?php if (isset($story)):?>
        value="<?=$story->title?>" 
        <?php else:?>
        value="<?=$data['title']['tip']?>" 
        <?php endif?>
        data-tip="<?=$data['title']['tip']?>" 
        data-help="<?=$data['title']['help']?>" 
      />
      <?php endif?>

      <div class="clear"></div>

      <span class="words">0 word(s)</span>

      <div class="rich toolbar1">
        <ul>
          <li>B</li>
          <li>U</li>
          <li>I</li>
          <li>link</li>
        </ul>
      </div>

      <label>story:</label>
      <textarea 
        name="story" id="text" class="input-text text" 
        data-tip="<?=$data['text']['tip']?>" 
        data-help="<?=$data['text']['help']?>"><?php if (isset($story)): ?><?=$story->text?><?php else:?><?=$data['text']['tip']?><?php endif?></textarea>

      <div class="clear"></div>

      <label>tags: <span>enter tags seperated by commas or just hit enter after each tag</span></label>
      <input type="text" name="tags" id="tags" value="<?=$data['tags']['tip']?>" class="input-text" data-tip="<?=$data['tags']['tip']?>" data-help="<?=$data['tags']['help']?>" />

      <div class="tags">
        <?php if (isset($story)): ?>
        <?php foreach ($story->tags as $tag):?>
        <div class="tag"><div class="tag_close"></div><p><?=$tag?></p></div>
        <?php endforeach?>
        <?php endif?>
      </div>

      <div class="clear"></div>

      <div class="share_subheader">optional info <span>the more specific, the better</span></div>
      <div class="share_border"></div>

      <script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
      <script src="/jst/map.js"></script>

      <div class="location">

        <input id="location" type="text" class="input-text" 
          data-tip="<?=$data['location']['tip']?>" 
          placeholder="<?=$data['location']['tip']?>" 
          data-help="<?=$data['location']['help']?>"
        >

        <div id="map_canvas"> </div>

      </div>

      <input type="hidden" name="location_name" id="location_name" 
        value="<?=(isset($story->location['name']) ? $story->location['name'] : '')?>" />
      <input type="hidden" name="location_formatted" id="location_formatted" 
        value="<?=(isset($story->location['formatted']) ? $story->location['formatted'] : '')?>" />
      <input type="hidden" name="location_latitude" id="location_latitude" 
        value="<?=(isset($story->location['latitude']) ? $story->location['latitude'] : '')?>" />
      <input type="hidden" name="location_longitude" id="location_longitude" 
        value="<?=(isset($story->location['longitude']) ? $story->location['longitude'] : '')?>" />

      <div class="optional">
        <?php  $inputs = array('phone' => 'business phone', 'url' => 'webpage'); ?>
        <?php  foreach ($inputs as $key=>$value):?>

        <?php if (is_numeric($key)):?>

        <label><?=$value?>:</label>
        <input type="text" name="<?=$value?>" id="<?=$value?>" 

          <?php if (isset($story->$value)): ?>
          value="<?=$story->$value?>" 
          <?php else:?>
          value="<?=$data[$value]['tip']?>" 
          <?php endif?>

          data-tip="<?=$data[$value]['tip']?>" 
          data-help="<?=$data[$value]['help']?>" class="input-text" />
        <div class="clear"></div>

        <?php else:?>

        <label><?=$value?>:</label>
        <input type="text" name="<?=$key?>" id="<?=$key?>" 

          <?php if (isset($story->$key)): ?>
          value="<?=$story->$key?>" 
          <?php else:?>
          value="<?=$data[$key]['tip']?>" 
          <?php endif?>

          data-tip="<?=$data[$key]['tip']?>" 
          data-help="<?=$data[$key]['help']?>" class="input-text" />
        <div class="clear"></div>

        <?php endif?>
        <?php endforeach?>
      </div>

      <div class="share_subheader">images <span>minimum of 1 maximum of 5</span></div>
      <div class="share_border"></div>
      <input type="button" name="upload" value="Select photos" class="button upload_button" id="photo_select" />
      <input type="file" name="upload" value="" id="real_upload_button" class="real_upload_button" multiple />
      <div class="clear"></div>
      <div class="images">

      <?php for ($i = 0; $i != 5; $i++): ?>
        <div class="image_outer image_outer_<?=$i?>" data-num="<?=$i?>">
          <div style=""><div class="delete" data-num="<?=$i?>" type="button" style=""></div></div>
          <div class="image image_<?=$i?>">
          <?php if (isset($files) && isset($files[$i])):?>
            <img src="data:<?=$files[$i]->file['type']?>;base64,<?=base64_encode($files[$i]->getBytes())?>" />
          <?php else:?>
            photo <?=$i+1?>
          <?php endif?>
          </div>
          <div class="details details_<?=$i?>"></div>
          <input type="text" name="caption" id="caption_<?=$i?>" 
            <?php if (isset($story) && isset($story->photos[$i]['caption'])):?>
            value="<?=$story->photos[$i]['caption']?>" 
            <?php else:?>
            value="<?=$data['caption']['tip']?>" 
            <?php endif?>
            data-tip="<?=$data['caption']['tip']?>" 
            data-help="<?=$data['caption']['help']?>"
            class="input-text caption" 
          />
          <div class="clear"></div>
        </div>
      <?php endfor?>

      </div>

      <div class="clear"></div>

      <?php if (user::isAdmin() && isset($story)):?>
      <div class="freshen-wrapper">
        Admin only: <a data-id="<?=$story->id()?>" class="freshen">Move this story to the top of the homepage</a>
      </div>
      <?php endif ?>

      <div class="buttons">
        <input type="button" class="button submit" name="submit" value="<?= $bio ? 'Save Bio' : 'Submit to Editors' ?>" />
        <?php if ($bio == false): ?>
        <input type="button" class="button save" name="save" value="Save &amp Preview" />
        <?php endif?>
        <input type="button" class="button plainbutton delete-story" name="delete" value="<?= $bio ? 'Clear Bio' : 'Delete Story' ?>" />
        <input type="button" class="button plainbutton cancel" name="cancel" value="Cancel" />
      </div>
			
      <div class="clear"></div>

      <?php if (isset($story) && $story->exists() && $bio == false): ?>

      <div class="share_subheader">comments <span>approval/reject conversation</span></div>
      <div class="share_border"></div>

      <textarea 
        name="comment" id="comment" class="input-text" 
        data-tip="<?=$data['comment']['tip']?>" 
        data-help="<?=$data['comment']['help']?>"><?=$data['comment']['tip']?></textarea>

      <input type="hidden" id="story_id" value="<?=$story->id()?>" />

      <input type="button" class="button comment_submit" name="comment" value="comment" />

      <div class="clear"></div>

      <?php if (isset($comments) && $bio == false):?>
      <div class="comments">
      <?php require_once 'tpl/_comments.php';?>
      </div>
      <?php endif?>


      <?php endif?>
      </div>

    </div><!-- content_main -->

  </div><!-- container -->

      <div class="share-preview hidden">
        <div class="share-preview-title">Preview</div>
        <hr class="" />
        <div class="share-preview-content"></div>
        <hr class="" />
        <div class="buttons">
          <?php if (isset($_REQUEST['returnurl'])): ?>
          <input type="button" class="button goback" name="back-lower" value="Back To Microguide" />
          <?php endif ?>
          <input type="button" class="button submit" name="submit-lower" value="<?= $bio ? 'Save Bio' : 'Submit to Editors' ?>" />
        </div>
        <div class="scroll-to-top">
          <input type="button" value="Scroll To Top" name="top" class="button top">
        </div>
      </div>

</div><!-- outer-container -->

<?php  require_once 'footer.php'; ?>

