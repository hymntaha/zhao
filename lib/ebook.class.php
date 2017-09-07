<?php

class ebook {
  
  /**
   * Exports ebook
   */
  public static function exportEbook($slug = null, $include_bio = false, $byline_every_page = false, $cover_image = null, 
                                     $front_ad_image = null, $rear_ad_image = null, $compression = 80) {
    
    if ($slug === null || $cover_image === null ) {
      return false;
    }
    
    
    $microguide = microguide::i(microguide::findOne(array('slug'=>$slug)));

    if ($include_bio) {
      $bio = story::i(story::findOne(array('status' => 'bio', 'authorSlug' => $microguide->authorSlug)));
      
      $photo = $bio->photos[0];
      $photo = image::scaleUrl($photo['id'], 'scale', '', '', $compression);
      
      $bio_image = $photo;
      $bio_image_type = $bio->photos[0]['type'];
      
    }

    $fileDir = 'lib/ebook/';
    $includeDir = 'lib/ebook/';
    
    $fileTitle = $microguide->slug . ".epub";
    
    foreach ($microguide->stories as $story) {
      
      $photos = array();
        
      foreach ($story->photos as $num=>$photo) {
        $photo['path']['original'] = image::scaleUrl($photo['id'], 'scale', '', '', $compression);
        $photos[$num] = $photo;
      }
      
      $story->photos = $photos;
      
    }
    
    include($includeDir . "Zip.php");
    
    include($includeDir . 'uid_gen.php');
    
    // HTML Purifier cleans our story html so that ebook readers won't complain
    require_once($includeDir . "html_purifier/HTMLPurifier.auto.php");
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Doctype', 'XHTML 1.1');
    $purifier = new HTMLPurifier($config);
    
    $uid = gen_uuid();
    $fileTime = date("D, d M Y H:i:s T");
    
    $zip = new Zip();
    $zip->setExtraField(FALSE);
    
    $content = "application/epub+zip";
    $zip->addFile($content, "mimetype");
    
    $zip->addFile(file_get_contents($fileDir . 'META-INF/encryption.xml'), 'META-INF/encryption.xml');
    $zip->addFile(file_get_contents($fileDir . 'META-INF/com.apple.ibooks.display-options.xml'), 'META-INF/com.apple.ibooks.display-options.xml');
    $zip->addFile(file_get_contents($fileDir . 'META-INF/container.xml'), 'META-INF/container.xml');
    
    $zip->addFile(file_get_contents($fileDir . 'OEBPS/css/style.css'), 'OEBPS/css/style.css');
     
    $imgFileDir = $fileDir . "OEBPS/image/";
    if (!file_exists($imgFileDir)) {
      mkdir($imgFileDir, 0777, true);
    }
    @$handle = opendir($imgFileDir);
    
    if ($handle) {
      while (($file = readdir($handle)) !== false) {
        if ( (strpos($file, ".png") !== false) || 
             (strpos($file, ".gif") !== false) || 
             (strpos($file, ".jpg") !== false) || 
             (strpos($file, ".jpeg") !== false) ) {

          $pathData = pathinfo($fileDir . $file);
          $fileName = $pathData['filename'];

          $zip->addFile(file_get_contents($imgFileDir . $file), "OEBPS/image/" . $file);
        }
      }
    }
    
    if ($include_bio) {
      $zip->addFile(file_get_contents($bio_image), "OEBPS/image/bio_image");
    }
    
    $fontFileDir = $fileDir . "OEBPS/font/";
    if (!file_exists($fontFileDir)) {
      mkdir($fontFileDir, 0777, true);
    }
    @$handle = opendir($fontFileDir);
    
    if ($handle) {
      while (($file = readdir($handle)) !== false) {
        if ( (strpos($file, ".otf") !== false) || (strpos($file, ".ttf") !== false) ) {
          $pathData = pathinfo($fileDir . $file);
          $fileName = $pathData['filename'];

          $zip->addFile(file_get_contents($fontFileDir . $file), "OEBPS/font/" . $file);
        }
      }
    }
    
    $regexIDPattern = '/[A-Za-z][A-Za-z0-9\-\.]*/';
    
    $xmlVersionTag = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . PHP_EOL;
    
    $content = $xmlVersionTag;
    ob_start();
    include($includeDir . 'OEBPS/content.opf.php');
    $content .= ob_get_clean() . PHP_EOL;   
    $zip->addFile($content, 'OEBPS/content.opf');
    
    $cover = $xmlVersionTag;
    ob_start();
    include($includeDir . 'OEBPS/cover.xhtml.php');
    $cover .= ob_get_clean();
    $zip->addFile($cover, 'OEBPS/cover.xhtml');
    
    if ($include_bio) {
      $bio_page = $xmlVersionTag;
      ob_start();
      include($includeDir . 'OEBPS/bio.xhtml.php');
      $bio_page .= ob_get_clean();
      $zip->addFile($bio_page, 'OEBPS/bio.xhtml');
    }
    
    // Add uploaded cover photo 
    $zip->addFile(file_get_contents($cover_image['file']), "OEBPS/image/cover_photo");
    
    // Add uploaded front ad 
    if ($front_ad_image != null) {
      $zip->addFile(file_get_contents($front_ad_image['file']), "OEBPS/image/front_ad");
    }
    
    // Add uploaded rear/byc ad 
    if ($rear_ad_image != null) {
      $zip->addFile(file_get_contents($rear_ad_image['file']), "OEBPS/image/rear_ad"); 
    }
    
    $toc = $xmlVersionTag;
    ob_start();
    include($includeDir . 'OEBPS/toc.ncx.php');
    $toc .= ob_get_clean();
    $zip->addFile($toc, 'OEBPS/toc.ncx');
    
    $imgCounter = 0;
    $i = 0;
    $storyCounter = 2;

    foreach($microguide->stories as $story) {
      $storyContent = $xmlVersionTag;
      ob_start();
      include($includeDir . 'OEBPS/story.xhtml.php');
      $storyContent .= ob_get_clean();
      $fileName = 'OEBPS/' . $story->slug . '.xhtml' ;
      $zip->addFile($storyContent, $fileName);
    
      foreach($story->photos as $photo) {
        $zip->addFile(file_get_contents($photo['path']['original']), "OEBPS/image/image-" . $i);
        $i++;
      }
      $storyCounter++;
    }
    
    $zip->sendZip($fileTitle);
    
    
  }
}
