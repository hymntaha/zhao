<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <title><?= htmlspecialchars(mb_strtoupper($story->title, 'UTF-8'), ENT_QUOTES | ENT_XHTML) ?></title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body id="<? preg_match($regexIDPattern, htmlspecialchars($story->slug, ENT_QUOTES | ENT_XHTML), $matches); echo $matches[0]; ?>" xml:lang="en-US">
    <div>
      <div class="frame-2 chapter-break">.  .  .  .  .</div>
      <p class="decoration para-style-override-2"></p>
      <p id="toc_marker-<?= $storyCounter ?>" class="title"><?= htmlspecialchars(mb_strtoupper($story->title, 'UTF-8'), ENT_QUOTES | ENT_XHTML) ?></p>
      <? if ($byline_every_page): ?>
      <p class="byline">by <?= htmlspecialchars($story->author, ENT_QUOTES | ENT_XHTML) ?></p>
      <? endif ?>
      <p class="byline"></p>
    </div>
    
    <? foreach ($story->photos as $photo): ?>
    <div class="image-container">
      <div class="image"><img src="image/image-<?= $imgCounter ?>" alt="image-<?= $imgCounter ?>" /></div>
      <? $imgCounter++ ?>
      <p class="caption"><?= htmlspecialchars($photo['caption'], ENT_QUOTES | ENT_XHTML) ?></p>
    </div>
    <? endforeach ?>
    
    <div>
      <div class="body-copy"><?= preg_replace('#href=[\'"](?!http)#', 'href="http://', preg_replace('?<\s*/u\s*>?', '</span>', preg_replace('?<\s*u\s*>?', '<span class="underline">', str_replace('target="_new"', '', $purifier->purify($story->text_format_html))))) ?> </div>
      <? if(isset($story->url) || isset($story->location) || isset($story->address)): ?>
      <div class="info-content">  
        <p class="info">INFO</p>
        <? if(isset($story->url) && trim($story->url) != ''):?>
        <p class="hyperlink"><a href="<?= htmlspecialchars($story->url_format, ENT_QUOTES | ENT_XHTML) ?>"><span class="body-hyperlink"><?= htmlspecialchars($story->url, ENT_QUOTES | ENT_XHTML) ?></span></a></p>
        <? endif ?>
        <? if (isset($story->location) && trim($story->location['formatted']) != ''): ?>
        <p class="hyperlink">
          <a href="https://maps.google.com/maps?q=<?= htmlspecialchars(urlencode($story->location['formatted']), ENT_QUOTES | ENT_XHTML) ?>"><?= htmlspecialchars($story->location['formatted'], ENT_QUOTES | ENT_XHTML) ?></a>
        </p>
        <? elseif (isset($story->address)): ?>
        <p class="hyperlink">
          <a href="https://maps.google.com/maps?q=<?= htmlspecialchars(urlencode($story->address.','.$story->city.','.$story->state.','.$story->country), ENT_QUOTES | ENT_XHTML)?>">
            <?=$story->address?>
          </a>
        </p>
        <? endif ?>
        <? endif ?>
      </div>
    </div>
    <? if ($story == $microguide->stories[count($microguide->stories) - 1] && $rear_ad_image != null && $include_bio == false): ?>
    <div class="byc-ad">
      <? if (trim($rear_ad_image['link']) != '' || trim($rear_ad_image['email']) != ''): ?>
      <? if ($rear_ad_image['link-type'] == 'mailto'): ?>
      <a href="mailto:<?= $rear_ad_image['email'] ?>?subject=<?= htmlentities($rear_ad_image['subject'], ENT_COMPAT, "UTF-8") ?>">
      <? else: ?>
      <a href="http://<?= $rear_ad_image['link'] ?>">
      <? endif ?>
      <? endif ?>
        <img class="frame-3" src="image/rear_ad" alt="rear_ad" />     
      <? if (trim($rear_ad_image['link']) != '' || trim($rear_ad_image['email']) != ''): ?>
      </a>
      <? endif ?>
    </div>
    <? endif ?>
  </body>
</html>
