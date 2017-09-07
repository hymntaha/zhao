<?php 

require_once(dirname(__FILE__) . '/../config.php');

error_reporting(0);
@ini_set('display_errors', 0);

// THIS IS ABSOLUTELY ESSENTIAL - DO NOT FORGET TO SET THIS 
@date_default_timezone_set("GMT"); 

/* 50k is the maximum number of urls that google will allow */
$urlCount = 0;
$urlMax = 50000;

$writer = new XMLWriter(); 

$writer->openURI('php://output');
$writer->startDocument('1.0', 'utf-8');
$writer->setIndent(true);

$writer->startElement('urlset');

$writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$writer->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
{
  /* Static pages first */
  $pages = array( array('loc' => G_URL, 'changefreq' => 'hourly', 'priority' => '1.0'),
                        array('loc' => G_URL . 'static/faq', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/about', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/fifty', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/team', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/terms', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/privacy', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'static/how-it-works', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'forms/publisher', 'changefreq' => 'yearly', 'priority' => '0.6'),
                        array('loc' => G_URL . 'microguide', 'changefreq' => 'hourly', 'priority' => '1.0') );
                        
  /* Now we go through all of the microguides */
  $cursor = microguide::find(array('status' => 'accepted'));
  foreach ($cursor as $id => $microguide) { 
    $hasCover = false;
    $coverStory = microguide::i($microguide)->coverStory->data();
  
    if (count($coverStory['photos'])) {
      $hasCover = true;
      $largeThumbnail = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'scale');
    }
    
    $microguideEntry = array('loc' => G_URL . 'microguide/' . $microguide['slug'], 'changefreq' => 'monthly', 'priority' => '0.9');
    
    if ($hasCover) {
      $microguideEntry['images'] = array($largeThumbnail); 
    }
    
    $pages[] = $microguideEntry;
      
  }

  /* Now we go through all of the stories */
  $cursor = story::find(array('status' => 'accepted'))->sort(array('created' => -1));
  foreach ($cursor as $id => $story) {
    $hasImage = false;
    
    $storyEntry = array('loc' => G_URL . 'story/' . $story['slug'], 'changefreq' => 'monthly', 'priority' => '0.8');
    
    $photos = array();
    foreach ($story['photos'] as $photo) {
      $hasImage = true;
      $photos[] = image::scaleUrl($photo['id'], 'scale');
    }
  
    if ($hasImage) {
      $storyEntry['images'] = $photos;
    }
    
    $pages[] = $storyEntry;
  }

  /* Now print out all of the xml */
  foreach ($pages as $page) {
    if ($urlCount < $urlMax) {
      $writer->startElement('url');
      $writer->writeElement('loc', $page['loc']);
      $writer->writeElement('changefreq', $page['changefreq']);
      $writer->writeElement('priority', $page['priority']);
      
      if (isset($page['images'])) {
        foreach ($page['images'] as $image) {
          $writer->startElement('image:image');
          $writer->writeElement('image:loc', $image);
          $writer->endElement();
        }
      }
      
      $writer->endElement();
      $urlCount++;
    } else break;
  } 
}
$writer->endElement();
$writer->endDocument();

$writer->flush(); 