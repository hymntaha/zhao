<?php

class story_ctl {

  public function __call($name, $args) {

    if ($name == 'index') {
      header('Location: '.G_URL);
      return true;
    }

    $bio = false;

    if ($name == 'bio' && isset($args) && isset($args[0])) {
      $story = story::i(story::findOne(array('status' => 'bio', 'authorSlug' => $args[0])));
      $author = $story->author;
      $usersMicroguides = microguide::getByUser($author);
      $bio = true;
    } else {
      $story = story::i(story::findOne(array('slug' => $name)));
    }

    if ($bio && !$story->exists()) {
      header('Location: '.G_URL.'share/bio');
      return true;
    }

    $bravoed = false;
    if (isset($_SESSION['user']) &&
      count(bravo::getUserBravos($_SESSION['user']['_id'], array($story->id()))) > 0) {
      $bravoed = true;
    }

    // Add image urls
    $story->photos = self::cache($story);

    $ogMetadata = self::openGraphMetadata($story);

    $useSlider = true;


    $featuredMicroguides = microguide::getFeatured($story);
    $containingMicroguides = microguide::getByStory($story);

    $displayEbookMicroguides = false;
    $ebookMicroguide = microguide::getByStory($story, array('$in' => array('pending', 'accepted')));
    
    if(count($ebookMicroguide) > 0) {
      $ebookMicroguide = microguide::i($ebookMicroguide[0]); 
      if (count($ebookMicroguide->availableOn) > 0) {
        $displayEbookMicroguides = true;   
      }
    }

    if ($displayEbookMicroguides) {
      $__more_style = array('message', 'related_microguides', 'users_microguides', 'ebook_microguides');
    } else {
      $__more_style = array('message', 'related_microguides', 'users_microguides');
    }
    require_once 'tpl/story.php';

    if (KDEBUG) {
      global $_OTHER;
      $_OTHER['story'] = $story->data();
    }

  }

  public static function cache($story) {

    $photos = array();

    foreach ($story->photos as $num=>$photo) {
      $photo['path']['original'] = image::scaleUrl($photo['id'], 'scale');
      $photo['path']['390'] = image::scaleUrl($photo['id'], 'scale', 390);
      $photo['path']['800'] = image::scaleUrl($photo['id'], 'scale', 800);
      $photo['path']['x425'] = image::scaleUrl($photo['id'], 'scale', '', 425);
      $photo['path']['82x'] = image::scaleUrl($photo['id'], 'scale', 82, '');
      $photo['path']['x509'] = image::scaleUrl($photo['id'], 'scale', '', 509);
      $photo['path']['390x500'] = image::scaleUrl($photo['id'], 'scale', 390, 500);
      $photo['path']['640x360'] = image::scaleUrl($photo['id'], 'cropThumbnail', 640, 360);
      $photo['path']['800x800'] = image::scaleUrl($photo['id'], 'scale', 800, 800);
      $photo['scaled']['x425']['width'] = (int) ($photo['width']*425/$photo['height']);
      $photo['scaled']['390x500']['width'] = 390;
      $photos[$num] = $photo;

    }

    return $photos;

  }

  public function search($arg) {

    $query = urldecode($arg);
    $search = array('$or' => array(
                                  array( 'title' => new MongoRegex("/$query/i") ),
                                  array( 'slug' => new MongoRegex("/$query/i") ),
                                   ),
                    'status' => array('$in' => array('accepted', 'invited'))
                    );

    if (isset($_REQUEST['exclude'])) {
      $exclude = json_decode($_REQUEST['exclude']);
      $search['_id'] = array('$nin' => db::idList($exclude));
    }

    $cursor = story::find($search)->limit(15);
    $results = array();

    foreach ($cursor as $id => $story) {
      $results[] = array(
                         'title' => $story['title'],
                         'slug' => $story['slug'],
                         'id' => $story['_id']->{'$id'},
                         );
    }
    echo json_encode($results);
    return;
  }

  /***
   * Returns an array of key/value pairs to be rendered as opengraph metadata.
   */
  public static function openGraphMetadata($story) {

    $metadata = array(
      'og:type'        => FB_NAMESPACE . ':story',
      'og:description' => $story->text_short,
      'og:image'       => $story->photos[0]['path'][390],
      'description'    => $story->text_short,
      'author'         => $story->author,
      'keywords'       => implode(',', $story->tags),
    );

    if ($story->status == 'bio') {
      $metadata['og:url'] = G_URL . 'story/bio/' . $story->authorSlug;
      $metadata['og:title'] = "Bio of $story->author";
    } else {
      $metadata['og:title'] = $story->title;
      $metadata['og:url'] = G_URL . 'story/' . $story->slug;
    }

    return $metadata;
  }

  public function previewfragment($slug) {
    $story = story::findOne(array('slug'=>$slug));
    $story = story::i($story);
    $stories = array($story);
    if (isset($_REQUEST['offset'])) {
      $storyListOffset = (int)$_REQUEST['offset'];
    } else {
      $storyListOffset = 0;
    }
    ob_start();
    require 'tpl/_stories-list.php';
    $html = ob_get_contents();
    ob_end_clean();
    echo json_encode(
                       array('html' => $html)
    );
    return true;
  }

}
