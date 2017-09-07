<?php

class api_ctl {

  private static $filters = array(
    'story' => array(
      'name'          => 'rename::title',
      'ref'           => 'apifilters::storyRef',
      'html'          => 'rename::text_format_html',
      'url'           => 'apifilters::storyUrl',
      'tags'          => NULL,
      'location'      => 'apifilters::storyLocation',
      'bravoCount'    => NULL,
      'author'        => 'apifilters::storyAuthor',
      'created'       => NULL,
      'updated'       => NULL,
      'photos'        => 'apifilters::storyPhotos',
    ),
    'microguide' => array(
      'name'          => 'rename::title',
      'ref'           => 'apifilters::microguideRef',
      'html'          => 'rename::description', //TODO: HTML
      'url'           => 'apifilters::microguideUrl',
      'tags'          => NULL,
      'access'        => NULL,
      'availableOn'   => NULL,
      'author'        => 'apifilters::microguideAuthor',
      'storyCount'    => NULL,
      'photos'        => 'apifilters::microguidePhotos',
    )
  );

  private static $errors = array(
    404 => 'HTTP/1.0 404 Not Found'
  );

  public function story($data) {

    if (strlen($data)) {
      $list = $this->_story($data);
      $this->sendResponse($list);
    }

    $page = 1;
    if (isset($_GET['page']) && (int)$_GET['page']) {
      $page = (int)$_GET['page'];
    }

    $limit = 30;
    $sort = array('created' => -1);
    $criteria = array('status' => 'accepted');
    $cursor = story::find($criteria)
      ->sort($sort)
      ->limit($limit)
      ->skip(($page-1)*$limit);

    $storyList = array();
    foreach ($cursor as $id => $storyData) {
      $storyList[] = story::i($storyData);
    }

    $summary = array(
                     'count' => count($storyList),
                     'page' => $page,
                     'totalitems' => $cursor->count(),
                     'totalpages' => ceil($cursor->count()/$limit),
                     );

    $this->sendResponse($this->filterObjects('story', $storyList),$summary);

  }

  public function _story($data) {

    $storyData = story::findOne(
      array(
        'slug' => $data,
        'status' => 'accepted',
      )
    );

    if (!$storyData) {
      $this->sendError(404, 'Story not found');
      exit;
    }

    return $this->filterObjects('story', array(story::i($storyData)));

  }

  public function microguide($data) {

    if (strlen($data)) {
      $list = $this->_microguide($data);
      $this->sendResponse($list);
    }

    $page = 1;
    if (isset($_GET['page']) && (int)$_GET['page']) {
      $page = (int)$_GET['page'];
    }
    $limit = 10;

    //TODO: cleaner job of branching here
    if (isset($_GET['q']) && $_GET['q'] === '_featured') {
      // Get featured microguides
      $time = time();
      $features = featuredMicroguide::getFeatures($time);
      $flipped = array();
      for ($i = 0, $max = count($features); $i < $max; $i++) {
        $microguideIds[] = $features[$i]['microguideId'];
        $flipped[(string)$features[$i]['microguideId']] = $i;
      }
      $cursor = microguide::find(array('_id' => array('$in' => $microguideIds)));
      $new = array_fill(0, count($features), null);
      foreach ($cursor as $id => $microguideData) {
        if (!isset($flipped[$id])) continue;
        $new[$flipped[$id]] = $microguideData;
      }
      $cursor = $new;
    } else {
      // Get reverse-ordered list of microguides
      $sort = array('created' => -1);
      $criteria = array('status' => 'accepted');
      $cursor = microguide::find($criteria)
        ->sort($sort)
        ->limit($limit)
        ->skip(($page-1)*$limit);
    }

    $storyIds = array();
    $microguideDataList = array();
    foreach ($cursor as $ignore => $microguideData) {
      if (!$microguideData) continue;
      $microguideDataList[] = $microguideData;
      for ($j = 0, $maxj = count($microguideData['storyIds']); $j < $maxj; $j++) {
        $storyId = $microguideData['storyIds'][$j];
        $storyIds[(string)$storyId] = $storyId;
      }
    }

    // BATCH FETCH STORIES
    $stories = array();
    $cursor2 = story::find(array('_id' => array('$in' => $storyIds), 'status' => 'accepted'));
    foreach ($cursor2 as $id => $story) {
      $stories[$id] = $story;
    }

    $objectList = array();
    for ($i = 0, $max = count($microguideDataList); $i < $max; $i++) {

      $microguide = microguide::i($microguideDataList[$i]);
      $microguide->setStories($stories);
      $objectList[] = $microguide;

    }

    if (method_exists($cursor, 'count')) {
      $theCount = $cursor->count();
    } else {
      $theCount = count($cursor);
    }

    $summary = array(
                     'count' => count($objectList),
                     'page' => $page,
                     'totalitems' => $theCount,
                     'totalpages' => ceil($theCount/$limit),
                     );

    $this->sendResponse($this->filterObjects('microguide', $objectList), $summary);

  }

  public function _microguide($data) {

    $microguideData = microguide::findOne(
      array(
        'slug' => $data,
        'status' => 'accepted',
      )
    );

    if (!$microguideData) {
      $this->sendError(404, 'Story not found');
      exit;
    }

    return $this->filterObjects('microguide', array(microguide::i($microguideData)));

  }

  // GENERIC FUNCTIONS

  public function filterObjects($type, $objectList) {

    $mappings = self::$filters[$type];

    $filteredObjects = array();

    foreach ($objectList as $object) {

      $objectData = array();

      foreach ($mappings as $key => $val) {

        if ($val === NULL) {
          $objectData[$key] = $object->$key;
        } else if (strpos($val, 'rename::') === 0) {
          list($ignore, $oldkey) = explode('::', $val);
          $objectData[$key] = $object->$oldkey;
        } else {
          $objectData[$key] = call_user_func_array($val, array($object));
        }
      }

      $filteredObjects[] = $objectData;

    }

    return $filteredObjects;

  }

  public function sendError($code, $message) {
    header(self::$errors[$code]);
    echo json_encode(
      array(
        'message' => $message
      )
    );
  }

  public function sendResponse($list, $summary = NULL) {

    if ($summary === NULL) {

      $summary = array(
        'items' => count($list),
        'page' => 1,
        'totalitems' => count($list),
        'totalpages' => 1,
      );
    }

    header('Content-Type: application/json');
    echo json_encode(
      array(
        'summary' => $summary,
        'list' => $list
      )
    );
    exit;
  }

}