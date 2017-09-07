<?php 

require_once '../config.php';

// copy stories to story - do this when i f up
// db.stories.find().forEach( function(x) db.story.insert(x)} );

$grid = story::grid();

foreach (story::find()->sort(array('created' => -1)) as $story) {

  $story = story::i($story);

  if (isset($story->photos) && !is_array($story->photos[0])) {

    hpr('we got an old story '.$story->title );
    $photos = array();

    foreach ($story->photos as $i=>$_photo) {

      $file = $grid->get(new MongoId($_photo));
      $ext = strtolower(substr($file->file['filename'], strrpos($file->file['filename'], '.')+1));

      switch ($ext) {
        case 'jpg':
        case 'jpeg':
          $photos[$i]['type'] = 'image/jpeg';
          $function = 'imagejpeg';
          break;
        case 'png':
          $photos[$i]['type'] = 'image/png';
          $function = 'imagepng';
          break;
        case 'gif':
          $photos[$i]['type'] = 'image/gif';
          $function = 'imagegif';
          break;
        default:
          hpr('unknown file type' . $ext);
          hpr($file);
          $photos[$i]['type'] = 'image/jpg';
          $function = 'imagejpg';
          break;
      }

      if (
          isset($story->captions) && 
          isset($story->captions[$i]) && 
          !empty($story->captions[$i])
          ) {
        $photos[$i]['caption'] = $story->captions[$i];
      }

      $im = imagecreatefromstring($file->getBytes());

      list($width, $height) = array(imagesx($im), imagesy($im));

      // check for a resizing
      if (resize($width, $height)) {

        list($new_width, $new_height) = resize($width, $height);
        $new = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        $img = output($new, $function);

        $photos[$i]['width'] = $new_width;
        $photos[$i]['height'] = $new_height;

        $grid->remove(array('_id' => $file->file['_id']));
        $photos[$i]['id'] = $grid->storeBytes($img, $photos[$i]);

      } else {

        $photos[$i]['width'] = $width;
        $photos[$i]['height'] = $height;

        $img = $file->getBytes();
        $grid->remove(array('_id' => $file->file['_id']));
        $photos[$i]['id'] = $grid->storeBytes($img, $photos[$i]);

      }

    }

    $story->photos = $photos;
    $story->save();

  }

}

function resize($width, $height) {

  $maxWidth = 960;
  $maxHeight = 3000;

  if ($width <= $maxWidth && $height <= $maxHeight) {
    return false;
  }

  $ratio = min($maxWidth/$width, $maxHeight/$height);
  return array(round($ratio*$width), round($ratio*$height));

}

function output($im, $function) {
  ob_start();
  $function($im);
  return  ob_get_clean();
}

