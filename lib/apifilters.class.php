<?php

class apifilters {

  // STORY

  public static function storyRef($story) {

    $ref = array(
      'id'  => (string) $story->_id,
      'slug' => '/story/' . $story->slug,
    );
    return $ref;

  }

  public static function storyUrl($story) {

    return G_URL . 'story/' . $story->slug;

  }

  public static function storyLocation($story) {

    $location = $story->location;
    $location['phone'] = $story->phone;
    $location['url'] = $story->url;

    if (strpos($location['url'], 'http') !== 0) {
      $location['url'] = 'http://' . $location['url'];
    }

    return $location;
  }

  public static function storyAuthor($story) {

    $_author = array(
                    'name' => $story->author,
                    'url'  => G_URL . 'story/bio/' . $story->authorSlug,
                    );

    return $_author;

  }

  public static function storyPhotos($story) {

    $sizeSpec = array(
                      '82x' => 'scale',
                      'x425' => 'scale',
                      'x509' => 'scale',
                      '390x500' => 'scale',
                      '640x360' => 'cropThumbnail',
                      '800x800' => 'scale',
                      );

    $_photos = array();

    foreach ($story->photos as $photo) {
      $_photo = array(
                      'caption' => isset($photo['caption']) ? $photo['caption'] : '',
                      );
      $_sizes = array('original' => image::scaleUrl($photo['id'], 'scale'));
      foreach ($sizeSpec as $size => $operation) {
        list($width,$height) = explode('x', $size);
        $_sizes[$size] = image::scaleUrl($photo['id'], $operation, $width, $height);
      }
      $_photo['sizes'] = $_sizes;

      $_photos[] = $_photo;
    }

    return $_photos;

  }

  // MICROGUIDE

  public static function microguideRef($object) {

    $ref = array(
      'id'  => (string) $object->_id,
      'slug' => '/microguide/' . $object->slug,
    );
    return $ref;

  }

  public static function microguideUrl($object) {

    return G_URL . 'microguide/' . $object->slug;

  }

  public static function microguideAuthor($object) {

    $_author = array(
                    'name' => $object->author,
                    'url'  => G_URL . 'story/bio/' . $object->authorSlug,
                    );

    return $_author;

  }

  public static function microguidePhotos($object) {

    $sizeSpec = array(
                      '82x' => 'scale',
                      'x425' => 'scale',
                      'x509' => 'scale',
                      '390x500' => 'scale',
                      '640x360' => 'cropThumbnail',
                      '800x800' => 'scale',
                      );

    $_photos = array();

    foreach (array($object->coverStory->photos[0]) as $photo) {
      $_photo = array(
                      'caption' => isset($photo['caption']) ? $photo['caption'] : '',
                      );
      $_sizes = array('original' => image::scaleUrl($photo['id'], 'scale'));
      foreach ($sizeSpec as $size => $operation) {
        list($width,$height) = explode('x', $size);
        $_sizes[$size] = image::scaleUrl($photo['id'], $operation, $width, $height);
      }
      $_photo['sizes'] = $_sizes;

      $_photos[] = $_photo;
    }

    return $_photos;

  }

}