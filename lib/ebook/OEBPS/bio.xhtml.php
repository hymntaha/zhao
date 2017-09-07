<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>cover</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body id="<? preg_match($regexIDPattern, htmlspecialchars($microguide->authorSlug, ENT_QUOTES | ENT_XHTML), $matches); echo $matches[0]; ?>" xml:lang="en-US">
    <div class="frame">
      <div class="chapter-break">.  .  .  .  .</div>
      <p class="decoration"></p>
      <p class="byline">About <?= htmlspecialchars($microguide->author, ENT_QUOTES | ENT_XHTML) ?></p>
    </div>
      
    <div class="image-container">
      <div class="image">
        <p class="image-container">
          <img class="frame-1" src="image/bio_image" alt="bio_image" />
        </p>
      </div>
    </div>
    <div class="body-copy">
        <?= preg_replace('[<\s*/u\s*>]', '</span>', preg_replace('[<\s*u\s*>]', '<span class="underline">', str_replace('target="_new"', '', $purifier->purify($bio->text_format_html)))) ?>
    </div>
    <? if ($rear_ad_image != null): ?>
      <div class="byc-ad">
        <? if (trim($rear_ad_image['link']) != ''): ?>
        <a href="http://<?= $rear_ad_image['link'] ?>">
        <? endif ?>
          <img src="image/rear_ad" alt="Rear Image"/>
        <? if (trim($rear_ad_image['link']) != ''): ?>
        </a>
        <? endif ?>
      </div>
    <? endif ?>
  </body>
</html>
