<?php

class image {

  public static function scaleUrl($fileId, $operation, $width='', $height='', $compressionQuality = IMAGE_COMPRESSION_QUALITY) {

    $fileId = (string)$fileId; //converts MongoIds

    $queryComponents = array(
      'id' => $fileId,
      'op' => $operation,
      'compressionQuality' => $compressionQuality,
      'version' => IMAGE_VERSION
    );
    ksort($queryComponents);
    $query = http_build_query($queryComponents);

    $hash = sha1(IMAGE_SECRET . '&' . $query);

    $path = IMAGE_ROOT . '/' 
      . substr($hash, 0, 3) . '/'
      . substr($hash, 3, 3) . '/'
      . $hash . '-'
      . $width . 'x' . $height
      . '.jpg';
    $path .= '?';
    $path .= $query;

    return $path;
  }

  // Compute upshift for tall photos.
  // This is hard to do smoothly in js and css so we do it here.
  public static function marginTop($photo,$width,$aspect) {

    if ($photo['width'] > $width) {
      $height = round($width*$photo['height']/$photo['width']);
    } else {
      $height = $photo['height'];
    }
    $visibleHeight = (int)($width/$aspect);
    if ($height > $visibleHeight) {
      $marginTop = ($visibleHeight - $height) . "px";
    } else {
      $marginTop = 0;
    }

    return $marginTop;

  }

}