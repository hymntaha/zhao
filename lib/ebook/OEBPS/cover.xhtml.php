<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>cover</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body id="bio" xml:lang="en-US">
    <div id="toc_marker-1" class="frame-2">
      <img class="frame-1" src="image/cover_photo" alt="cover_photo" />
    </div>
    <? if ($front_ad_image != null): ?>
    <div class="frame-2">
      <? if (trim($front_ad_image['link']) != '' || trim($front_ad_image['email']) != ''): ?>
      <? if ($front_ad_image['link-type'] == 'mailto'): ?>
      <a href="mailto:<?= $front_ad_image['email'] ?>?subject=<?= htmlentities($front_ad_image['subject'], ENT_COMPAT, "UTF-8") ?>">
      <? else: ?>
      <a href="http://<?= $front_ad_image['link'] ?>">
      <? endif ?>
      <? endif ?>
        <img class="frame-3" src="image/front_ad" alt="front_ad" />     
      <? if (trim($front_ad_image['link']) != '' || trim($front_ad_image['email']) != ''): ?>
      </a>
      <? endif ?>
    </div>
    <? endif; ?>
  </body>
</html>
