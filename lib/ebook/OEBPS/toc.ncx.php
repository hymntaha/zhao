<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
  <head>
    <meta name="dtb:uid" content="urn:uuid:<?= $uid ?>" />
    <meta name="dtb:depth" content="1" />
    <meta name="dtb:totalPageCount" content="0" />
    <meta name="dtb:maxPageNumber" content="0" />
  </head>
  <docTitle>
    <text></text>
  </docTitle>
  <navMap>
    <navPoint id="navpoint1" playOrder="1">
      <navLabel>
        <text>BYC MICROGUIDE</text>
      </navLabel>
      <content src="cover.xhtml#toc_marker-1" />
    </navPoint>
    <? $i = 2 ?>
    <? foreach($microguide->stories as $story): ?>
    <navPoint id="navpoint<?= $i ?>" playOrder="<?= $i ?>">
      <navLabel>
        <text><?= htmlspecialchars($story->title, ENT_QUOTES | ENT_XHTML) ?></text>
      </navLabel>
      <content src="<?= $story->slug ?>.xhtml" />
    </navPoint>
    <? $i++ ?>
    <? endforeach ?>
  </navMap>
</ncx>