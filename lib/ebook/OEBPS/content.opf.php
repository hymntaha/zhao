<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="bookid" version="2.0">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/">
    <meta name="generator" content="Adobe InDesign" />
    <meta name="cover" content="cover_photo" />
    <dc:title><?= htmlspecialchars($microguide->title, ENT_COMPAT, "UTF-8") ?></dc:title>
    <dc:creator><?= htmlspecialchars($microguide->author, ENT_COMPAT, "UTF-8") ?></dc:creator>
    <dc:subject></dc:subject>
    <dc:description></dc:description>
    <dc:publisher>Bravo Your City</dc:publisher>
    <dc:date><?= date("Y-m-d") ?></dc:date>
    <dc:source></dc:source>
    <dc:relation></dc:relation>
    <dc:coverage></dc:coverage>
    <dc:rights></dc:rights>
    <dc:language>en-US</dc:language>
    <dc:identifier id="bookid">urn:uuid:<?= $uid ?></dc:identifier>
  </metadata>
  
  <manifest>
    <item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml" />
    <item id="style.css" href="css/style.css" media-type="text/css" />
    <item id="cover" href="cover.xhtml" media-type="application/xhtml+xml" />
    <item id="AbrilDisplay-Regular.otf" href="font/AbrilDisplay-Regular.otf" media-type="application/vnd.ms-opentype" />
    <item id="DagnyPro.otf" href="font/DagnyPro.otf" media-type="application/vnd.ms-opentype" />
    <item id="Georgia.ttf" href="font/Georgia.ttf" media-type="application/vnd.ms-opentype" />
    <item id="cover_photo" href="image/cover_photo" media-type="<?= $cover_image['type'] ?>" />
    <? if ($front_ad_image != null): ?>
    <item id="front_ad" href="image/front_ad" media-type="<?= $front_ad_image['type'] ?>" />
    <? endif; ?>
    <? if ($rear_ad_image != null): ?>
    <item id="rear_ad" href="image/rear_ad" media-type="<?= $rear_ad_image['type'] ?>" />
    <? endif; ?>
    
    <? if ($include_bio): ?>
    <item id="bio" href="bio.xhtml" media-type="application/xhtml+xml" />
    <item id="bio_image" href="image/bio_image" media-type="image/jpeg" />
    <? endif; ?>
    
    <? $i = 0; ?>
    <? foreach($microguide->stories as $story): ?>
    
    <item id="<? preg_match($regexIDPattern, $story->slug, $matches); echo $matches[0]; ?>" href="<?= $story->slug ?>.xhtml" media-type="application/xhtml+xml" />
    
    <? foreach($story->photos as $photo): ?>
    <item id="image-<?= $i ?>" href="image/image-<?= $i ?>" media-type="image/jpeg" />
    <? $i++ ?>
    <? endforeach ?>
    <? endforeach ?>
    
    
  </manifest>
  
  <spine toc="ncx">
    <itemref idref="cover" />
    <? foreach($microguide->stories as $story): ?>
    
    <itemref idref="<? preg_match($regexIDPattern, $story->slug, $matches); echo $matches[0]; ?>" />
      
    <? endforeach ?>
    
    
    <? if ($include_bio): ?>
    <itemref idref="bio" />
    <? endif; ?>
    
  </spine>
  
</package>


