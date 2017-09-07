<?php 

class microguide extends kcol {

  private $_stories;
  private $_storyStatuses;

  protected $_ols = array(
    'coverStory',
    'storyCount',
    'stories'
  );

  public function __construct($id=null) {
    parent::__construct($id);
    if ($this->storyIds === null) {
      $this->storyIds = array();
    }
    if ($this->availableOn === null) {
      $this->availableOn = array();
    }
    $this->_storyStatuses = array('accepted');
  }

  public function __get($name) {

    switch ($name) {

      case 'coverStory' :
        return $this->stories[0];
        break;

      case 'storyCount' :
        return count($this->stories);
        break;

      case 'stories' :
        if (!empty($this->_stories)) {
          return $this->_stories;
        }
        $stories = array();
        foreach (story::find(array('_id' => array('$in' => $this->storyIds), 'status' => array('$in' => $this->_storyStatuses))) as $st) {
          $stories[array_search($st['_id'],$this->storyIds)] = story::i($st);
        }
        ksort($stories); // order the keys
        $this->_stories = array_values($stories); // re-base at zero

        return $this->_stories;
        break;
    }

    return parent::__get($name);

  }

  public function addStoryStatus($status) {
    switch (gettype($status)) {
    case 'array':
      $this->_storyStatuses = array_merge($this->_storyStatuses, $status);
      break;
    case 'string':
      $this->_storyStatuses[] = $status;
      break;
    }
  }

  public function setStories($storiesData) {

    $stories = array();
    for ($i = 0, $max = count($this->storyIds); $i < $max; $i++) {
      $stories[] = story::i($storiesData[(string)$this->storyIds[$i]]);
    }
    $this->_stories = $stories;

  }

  /**
   * Returns featured microguides for $context, a microguide, story, or request uri.
   **/
  public function getFeatured($context = null) {

    $featuredMicroguides = array();
    $featuredMicroguideIds = array();
    $maxFeaturedMicroguides = 4;
    $featuredMicroguideCount = 0;

    // GET PROMOTED MICROGUIDES FOR THIS CONTEXT

    $promotedMicroguidesConfig = array(
      '/microguide/study-abroad-tips' =>
        array(
          '507357a66747df6a10000000', // Seoul Style
          '50aabdb46747dff357000000', // Budget London Weekender
          '50ed08956747df6b02000000', // Mexico Culture
        ),
      '.*' =>
        array(
          '50ed08956747df6b02000000' //Mexico
        )
    );

    $promotedMicroguideIds = array();

    if (is_null($context)) {
      $contextString = $_SERVER['REQUEST_URI'];
    } elseif (is_string($context)) {
      $contextString = $context;
    } elseif ($context instanceof story) {
      $contextString = '/story/' . $context->slug;
    } elseif ($context instanceof microguide) {
      $contextString = '/microguide/' . $context->slug;
    } else {
      $contextString = (string)$context;
    }

    // Process the rule chain ... winner-take-all 

    foreach ($promotedMicroguidesConfig as $re => $ids) {
      if (preg_match('|' . $re . '|', $contextString)) {
        $promotedMicroguideIds = $ids;
        break;
      }
    }

    // First choice: Microguides we're promoting
    foreach ($promotedMicroguideIds as $promotedMicroguideId) {
      if (
          ($context instanceof microguide && (string)$context->_id == $promotedMicroguideId) ||
          ($featuredMicroguideCount >= $maxFeaturedMicroguides) ||
          in_array($promotedMicroguideId, $featuredMicroguideIds)
          ) {
        continue;
      }
      $featuredMicroguideIds[] = $promotedMicroguideId;
      $featuredMicroguideCount++;
    }

    // Second choice: random selection from our feature bag
    if ($featuredMicroguideCount < $maxFeaturedMicroguides) {

      $randomAdditionalMicroguideIds = array();
      $excludedMicroguideIds = $featuredMicroguideIds;
      if ($context instanceof microguide) {
        $excludedMicroguideIds[] = (string)$context->_id;
      }
      $cursor = featuredMicroguide::find(
        array('status'=>'active',
              'microguideId' => array('$nin' => db::idList($excludedMicroguideIds))
        ))
        ->sort(array("sequenceNumber" => 1));

      foreach ($cursor as $id => $feature) {
        $randomAdditionalMicroguideIds[] = (string) $feature['microguideId'];
      }

      shuffle($randomAdditionalMicroguideIds);

      $featuredMicroguideIds = array_merge(
        $featuredMicroguideIds,
        array_slice(
          $randomAdditionalMicroguideIds, 0,
          $maxFeaturedMicroguides - $featuredMicroguideCount
        )
      );
    }

    // GET THE MICROGUIDE OBJECTS IN LIST ORDER AND RETURN

    $featuredMicroguides = array();
    $cursor = microguide::find(
      array('_id' => array('$in' => db::idList($featuredMicroguideIds)))
    );

    foreach ($cursor as $id => $microguide) {
      $microguideObject = microguide::i($microguide);
      $microguide['coverPhoto'] = image::scaleUrl($microguideObject->coverStory->photos[0]['id'], 'cropThumbnail', 478, 287);
          $featuredMicroguides[array_search($id, $featuredMicroguideIds)] = $microguide;
    }

    return $featuredMicroguides;

  }
  
    /**
   * Returns any microguides that contain the story that's passed in
   **/
  public function getByStory($story = null, $status = 'accepted', $titleLength = 88) {
   
    if($story == null) {
      return false;
    }
    
    $containingMicroguides = array();
    $containingMicroguideIds = array();
    $maxContainingMicroguides = 2;
    $containingMicroguideCount = 0;  
    
    $cursor = microguide::find(array('storyIds' => $story->_id, 'status' => $status));
    
    foreach ($cursor as $id => $microguide) {
      $containingMicroguideIds[] = $id;
      $containingMicroguideCount++;
      if ($containingMicroguideCount >= $maxContainingMicroguides) {
        break;
      }
    }
    
    // GET THE MICROGUIDE OBJECTS IN LIST ORDER AND RETURN

    $cursor = microguide::find(
      array('_id' => array('$in' => db::idList($containingMicroguideIds)))
    );

    foreach ($cursor as $id => $microguide) {
      $microguideObject = microguide::i($microguide);
      $microguide['title'] = self::truncateTitle($microguide['title'], $titleLength);
      $microguide['coverPhoto'] = image::scaleUrl($microguideObject->coverStory->photos[0]['id'], 'cropThumbnail', 230, 171);

      $containingMicroguides[array_search($id, $containingMicroguideIds)] = $microguide;
    }

    return $containingMicroguides;
  }

  public function truncateTitle($title, $length) {
    if (strlen($title) > $length) {
      $title = substr($title, 0, ($length - 3));
      $title = substr($title, 0, strrpos($title, ' ')).'...';
    } 
    return $title;
  }
  
  public function getByUser($author = null, $imgWidth = 230, $imgHeight = 171) {
    if($author == null) {
      return false;
    }
    
    $usersMicroguides = array();
    $usersMicroguideIds = array();
    $maxMicroguides = 50; // Arbitrary maximum right now
    $usersMicroguideCount = 0;  
    
    $cursor = microguide::find(array('author' => $author, 'status' => 'accepted'));
  
    foreach ($cursor as $id => $microguide) {
      $usersMicroguideIds[] = $id;
      $usersMicroguideCount++;
      if ($usersMicroguideCount >= $maxMicroguides) {
        break;
      }
    }
    
    // GET THE MICROGUIDE OBJECTS IN LIST ORDER AND RETURN

    $cursor = microguide::find(
      array('_id' => array('$in' => db::idList($usersMicroguideIds)))
    );

    foreach ($cursor as $id => $microguide) {
      $microguideObject = microguide::i($microguide);
      $microguide['coverPhoto'] = image::scaleUrl($microguideObject->coverStory->photos[0]['id'], 'cropThumbnail', $imgWidth, $imgHeight);
      
      $usersMicroguides[array_search($id, $usersMicroguideIds)] = $microguide;
    }
    
    return $usersMicroguides;
  }

  public function addStory($story) {

    if (!$story) {
      return false;
    }

    $storyId = $story->_id;

    if (in_array($storyId, $this->storyIds)) {
      return false;
    }

    $storyIds = $this->storyIds;
    $storyIds[] = $storyId;
    $this->storyIds = $storyIds;
    $this->save();

    return true;
  }

  public function removeStory($story) {

    if (!$story) {
      return false;
    }

    $storyId = (string) $story->_id;

    if (!in_array($storyId, $this->storyIds)) {
      return false;
    }

    $storyIds = array_diff($this->storyIds, array($storyId));
    $this->storyIds = $storyIds;
    $this->save();

    return true;

  }

  static public function removeStoryHandler($story) {
    $cursor = microguide::find(array('storyIds' => $story->_id));
    foreach ($cursor as $id => $microguide) {
      $microguide = microguide::i($microguide);
      $microguide->removeStory($story);
    }
  }

  static public function generateUniqueSlug($name) {
    $attempts = 0;
    $slug = user::slugify($name);
    $candidateSlug = $slug;
    while ($attempts < 11 && microguide::findOne(array('slug' => $candidateSlug))) {
      $attempts++;
      $candidateSlug = $slug . '-' . $attempts;
    }

    return $candidateSlug;
  }

  public static function usernameChanged($user) {

    microguide::col()->update(
       array("authorSlug" => $user->slug),
       array('$set' => array('author' => $user->username)),
       array("multiple" => true)
    );

  }

  // soft delete
  public function remove() {
    $this->status = 'deleted';
    $this->save();
  }

}
