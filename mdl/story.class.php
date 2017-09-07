<?php 

class story extends kcol {

  protected $_types = array(
  /*
    'created' => 'date',
    'updated' => 'date'
  */
  );

  private $_text_short_length = 120;
  private $_url_short_length = 20;

  protected $_ols = array(
    'created_readable',
    'updated_readable',
    'created_diff',
    'text_short',
    'url_short',
    'url_format',
    'text_format_html'
  );

  public function __get($name) {

    switch ($name) {

      case 'created_readable' :
      case 'updated_readable' :
        $field = substr($name, 0, strpos($name, '_'));
        return date('Y-m-d h:i:s', parent::__get($field));
        break;

      case 'created_diff' :
        return clock::duration(parent::__get('created'));
        break;

      case 'text_short' :

        $text = self::bb(parent::__get('text'), false);


        if (strlen($text) < $this->_text_short_length) {
          return $text;
        }

        $last_space = strrpos(substr($text, 0, $this->_text_short_length), ' ');
        $text = substr($text, 0, $last_space);

        return htmlspecialchars($text).'..';
        break;


      case 'url_format' :

        $url = parent::__get('url');

        if (substr($url, 0, 5) == 'http:') {
          return $url;
        }

        return 'http://'.$url;


      case 'url_short' :

        $url = parent::__get('url');


        if (strlen($url) < $this->_url_short_length) {
          return $url;
        }

        return substr($url, 0, $this->_url_short_length).'..';
        break;


      case 'text_format_html' :

        $text = self::bb(parent::__get('text'));

        $body = str_replace("\n\n", "</p><p>", $text);
        $body = str_replace("\n", "<br />", $text);

        return '<p>'.$body.'</p>';

    }

    return parent::__get($name);

  }

  public function bb($text, $replace=true) {

    $strip = $swap = array();

    foreach (array('b','i','u') as $chr) {

      $strip[] = '['.$chr.']';
      $strip[] = '[/'.$chr.']';

      if ($replace) {
        $swap[] = '<'.$chr.'>';
        $swap[] = '</'.$chr.'>';
      } else {
        $swap[] = null;
        $swap[] = null;
      }
    }

    $text = str_replace($strip, $swap, $text);

    if ($replace) {
      $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/i', '<a target="_new" href="${1}">${2}</a>', $text);
    } else {
      $text = preg_replace('/\[url=.*?\]/i', '', $text);
      $text = preg_replace('/\[\/url\]/i', '', $text);
    }

    return $text;

  }

  public static function imgExtension($type) {

    switch ($type) {
      case 'image/jpeg':
      case 'image/jeg':
        return 'jpg';
      case 'image/png':
        return 'png';
      case 'image/gif':
        return 'gif';
      
      default :
        return false;

    }

    return false;

  }

  public static function imgFunction($type) {

    switch ($type) {
      case 'image/jpeg':
      case 'image/jeg':
        return array('imagecreatefromjpeg','imagejpeg');
      case 'image/png':
        return array('imagecreatefrompng','imagepng');
      case 'image/gif':
        return array('imagecreatefromgif','imagegif');
      
      default :
        return false;

    }

    return false;

  }

  public static function shrink($width, $readPath, $writePath, $type) {

    list($createfunc, $imgfunc) = self::imgFunction($type);

    if ($readPath instanceof MongoGridFSFile) {
      $img = imagecreatefromstring($readPath->getBytes());
      $size = array(imagesx($img),imagesy($img));
    } else {
      $img = $createfunc($readPath);
      $size = getimagesize($readPath);
    }

    if ($size[0] < $width) {
      $width = $size[0];
    }

    $ratio = $size[0]/$size[1];
    $height = floor($width/$ratio);
    $new_img = imagecreatetruecolor($width, $height);

    imagecopyresampled($new_img, $img, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

    return $imgfunc($new_img, $writePath);

  }

  public function save() {

    $newStory = ($this->_id === NULL);

    if ($this->bravoCount === NULL) {
      $this->bravoCount = 0;
    }

    parent::save();

    if ($newStory) {
      hook::run('story_new', $this);
    }

  }

  public static function findRandom() {
    $start = strtotime("2012-04-01");
    $end = time();
    $pivot = mt_rand($start,$end);
    $width = 43200;
    $maxIterations = 4;
    $resultCount = 0;
    $iteration = 0;
    $cursor = NULL;
    $fallbackSampleSize = 100;

    while ($resultCount == 0 && $iteration < $maxIterations) {
      $iteration++;
      $cursor = self::find(
        array('created' => array('$gte' => $pivot - $width, '$lt' => $pivot + $width),
              '$and'    => array(
                                 array('status'  => 'accepted'),
                                 array('status'  => array('$ne' => 'bio'))
                           )
        )
      );
      $resultCount = $cursor->count();
      $width *= 2;
    } 

    if ($resultCount) {
      $skip = mt_rand(0,$resultCount-1);
      $story = $cursor->skip($skip)->getNext();
    } else {
      // We did't find a content-containing window, pick randomly within the last batch.
      $story = self::find(
        array('$and' => array(
                              array('status' => 'accepted'),
                              array('status'  => array('$ne' => 'bio'))
                        )
        )
      )->sort(array('created' => -1))->limit($fallbackSampleSize)->skip(mt_rand(0,$fallbackSampleSize-1))->getNext();
    }
    
    return $story;

  }

  public static function usernameChanged($user) {

    story::col()->update(
        array("authorSlug" => $user->slug),
        array('$set' => array('author' => $user->username)),
        array("multiple" => true)
    );

  }

  // soft delete
  public function remove() {
    $this->status = 'deleted';
    $this->save();
    hook::run('story_remove', $this);
  }

  // hard delete
  private function _remove() {

    foreach ($this->photos as $key=>$value) {
      story::grid()->remove(array('_id' => $value['id']));
    }
    unset($this->photos);

    hook::run('story_remove', $this);

    parent::remove();
  }

}

