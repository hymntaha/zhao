<?php 

class microguide_ctl {

  public function __call($name, $args) {

    $bio = false;
    $__withStoriesScroll = true;
    $hasInvitedStories = false;
    $showAllMicroguides = false;

    if ($name == 'index') {
      $showAllMicroguides = true;
    } else {
      $microguide = microguide::i(microguide::findOne(array('slug' => $name)));
      $microguide->addStoryStatus(array('invited', 'pending', 'draft'));

      for ($i = 0, $max = $microguide->storyCount; $i < $max; $i++) {
        if ($microguide->stories[$i]->status == 'invited') {
          $hasInvitedStories = true;
          break;
        }
      }
    }

    $__more_style = array('microguide', 'message');

    $storyIndex = -1;
    $story = null;
    $isCoverPage = !count($args) || !$args[0] || $args[0] == 'index';
    
    if ($showAllMicroguides) {
      $storySlug = NULL;
      $__withStoriesScroll = false;
      require_once 'tpl/microguides.php';

    } else if ($isCoverPage) {
      $storySlug = NULL;
      $firstStory = $microguide->stories[0];
      $firstStory->photos = story_ctl::cache($firstStory);
      $ogMetadata = self::openGraphMetadata($microguide, $firstStory);

      $featuredMicroguides = microguide::getFeatured($microguide);

      require_once 'tpl/microguide.php';
        
    } else {
        
      $storySlug = $args[0];
      $story = story::i(story::findOne(array('slug' => $storySlug)));

      foreach($microguide->stories as $index => $value) {
        if($value->slug == $story->slug) {
          $storyIndex = $index + 1;
        }
      }

      $firstStory = $microguide->stories[0];
      $firstStory->photos = story_ctl::cache($firstStory);

      $story->photos = story_ctl::cache($story);
      $ogMetadata = story_ctl::openGraphMetadata($story);
      $ogMetadata['og:url'] = G_URL . 'microguide/' .$microguide->slug . '/' . $story->slug;

      $featuredMicroguides = microguide::getFeatured($microguide);

      require_once 'tpl/microguide.php';

    }

  }
  
  public function microguideModal() {

    $errors = array();
    $success = true;

    if(isset($_REQUEST['slug'])) {
      $slug = $_REQUEST['slug'];

      $microguide = microguide::findOne(array('slug' => $slug));

      if(!is_null($microguide)){
        $microguide = microguide::i($microguide);

        if(count($microguide->stories) > 0 ) {

          if(isset($_REQUEST['story'])){
            $storyIndex = $_REQUEST['story'];
            $story = $microguide->stories[$storyIndex-1];

          } else {
            $storyIndex = 1;
            $story = $microguide->stories[$storyIndex-1];

          }
                                        
          if($storyIndex == 1) {
            $nextStory = $microguide->stories[$storyIndex];
            $prevStory = $microguide->stories[count($microguide->stories) - 1];

            $nextStory->microIndex = $storyIndex + 1;
            $prevStory->microIndex = count($microguide->stories);

          } else if ($storyIndex == count($microguide->stories) ) {
            $nextStory = $microguide->stories[0];
            $prevStory = $microguide->stories[$storyIndex-2];

            $nextStory->microIndex = 1;
            $prevStory->microIndex = $storyIndex - 1;

          } else {
            $nextStory = $microguide->stories[$storyIndex];
            $prevStory = $microguide->stories[$storyIndex - 2];

            $nextStory->microIndex = $storyIndex + 1;
            $prevStory->microIndex = $storyIndex - 1;
          }

          $story->photos = story_ctl::cache($story);

          $bravoed = false;
          if (isset($_SESSION['user']) &&
              count(bravo::getUserBravos($_SESSION['user']['_id'], array($story->id()))) > 0) {
            $bravoed = true;
          }

          ob_start();

          require_once 'tpl/microguide_modal.php';

          $html = ob_get_clean();

          echo json_encode(array('success' => $success, 'errors' => $errors, 'count' => $microguide->storyCount, 'microguideSlug' => $microguide->slug, 'microguideTitle' => $microguide->title, 'storySlug' => $story->slug, 'storyTitle' => $story->title, 'html' => $html));
          return true;
                                        
        } else {
          $success = false;
          $errors[] = 'Microguide slug has no stories';

        }
      } else {
        $errors[] = "Microguide not found";
        $success = false;
      }

    } else {
      $success = false;
      $errors[] = 'Slug not set';
    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;
  }

  /***
   * Returns an array of key/value pairs to be rendered as opengraph metadata.
   */
  public static function openGraphMetadata($microguide, $firstStory) {

    $metadata = array(
      'og:type'        => FB_NAMESPACE . ':microguide',
      'og:title'       => $microguide->title,
      'og:url'         => G_URL . 'microguide/' . $microguide->slug . '/',
      'og:description' => 'A Bravo Your City! Microguide by ' . $microguide->author,
    );

    if (count($microguide->stories) > 0) {
      $metadata['og:image'] = $firstStory->photos[0]['path'][390];
    } else {
      $metadata['og:image'] = G_URL . '/img/fb-logo.png';
    }

    return $metadata;
  }

  public function edit() {

    $errors = array();
    $success = true;
    $operation = 'updated';

    $id = $_REQUEST['microguideId'];
    $title = trim($_REQUEST['title']);
    $access = trim($_REQUEST['access']);
    $slug = isset($_REQUEST['slug']) ? trim($_REQUEST['slug']) : NULL;
    $authorSlug = isset($_REQUEST['authorSlug']) ? trim($_REQUEST['authorSlug']) : NULL;
    $storyIds = isset($_REQUEST['storyIds']) ? $_REQUEST['storyIds'] : array();
    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : NULL;
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'save';
    $description = isset($_REQUEST['description']) ? $_REQUEST['description'] : NULL;
    $tags = isset($_REQUEST['tags']) ? $_REQUEST['tags'] : array();
    
    $kindle = isset($_REQUEST['kindle']) ? $_REQUEST['kindle'] : NULL;
    $itunes = isset($_REQUEST['itunes']) ? $_REQUEST['itunes'] : NULL;

    $featureid = isset($_REQUEST['featureid']) ? $_REQUEST['featureid'] : NULL;
    $featurestatus = isset($_REQUEST['featurestatus']) ? $_REQUEST['featurestatus'] : NULL;
    $featurequestion = isset($_REQUEST['featurequestion']) ? $_REQUEST['featurequestion'] : NULL;
    $featurequestionverb = isset($_REQUEST['featurequestionverb']) ? $_REQUEST['featurequestionverb'] : NULL;
    $featurequestionplace = isset($_REQUEST['featurequestionplace']) ? $_REQUEST['featurequestionplace'] : NULL;

    $isNew = false;

    // Check permissions
    if (!empty($id)) {

      // Existing microguide

      $microguide = new microguide($id);

      if (!$microguide->_id) {

        $errors['#title'] = "Could not find this microguide";
        $success = false;

      } else {

        if (!self::canEdit($microguide)) {
          $errors['#title'] = "You do not have permission to edit this microguide.";
          $success = false;
        }

        if (empty($slug)) {
          $slug = $microguide->slug;
        }

      }

    } else {

      // New microguide

      $isNew = true;

      if (!isset($_SESSION['user'])) {

        $errors['#title'] = "You must be logged in to create a new microguide.";
        $success = false;

      } else {

        $microguide = new microguide();
        if (empty($slug)) {
          $slug = microguide::generateUniqueSlug($title);
        }
        $operation = 'created';

      }

    }

    // Check access
    if ($access != 'open' && $access != 'closed') {
      $access = 'closed';
    }

    // Check title
    $data = $GLOBALS['_D']['forms']['microguide'];

    if (!$title || $title == $data['title']['tip']) {

      $errors['#title'] = "You must specify a title.";
      $success = false;

    }

    // Check URL
    if (!$slug) {
      $errors['#slug'] = "You must specify a URL.";
      $success = false;
    } else {
      $duplicateMicroguide = microguide::findOne(array('slug' => $slug));
      if ($duplicateMicroguide && (string)$duplicateMicroguide['_id'] != $id) {
        $errors['#slug'] = "There is already another microguide with this URL.";
        $success = false;
      }
    }

    // Check author
    if (!$authorSlug) {
      $author = $_SESSION['user'];
    } else {
      if (!user::isAdmin()) {
        $errors['#author'] = "You do not have permission to assign this microguide to another author.";
        $success = false;
      } else {
        $author = user::findOne(array('slug' => $authorSlug));
        if (!$author) {
          $errors['#author'] = "We could not find this author.";
          $success = false;
        }
      }
    }


    // Check description

    if ($description == $data['description']['tip']) {
      $description = '';
    }

    $wordLimit = 40;
    if (count(preg_split('/[[:space:]]/',$description)) > $wordLimit) {
      $errors['#description'] = "Please limit your description to $wordLimit words.";
      $success = false;
    }

    // Check tags

    // Check stories
    // Note: we're allowing statuses, so that invited stories will work
    $cursor = story::find(array('_id' => array('$in' => db::idList($storyIds))));
    if ($cursor->count() != count($storyIds)) {
      foreach ($cursor as $id => $story) {
        $key = array_search($id, $storyIds);
        if ($key !== FALSE) {
          unset($storyIds[$key]);
        }
      }
      $errors['storyIds'] = array_values($storyIds);
      $success = false;
    }

    // Check status
    switch ($action) {
      case 'submit':
        if ($isNew || $microguide->status != 'accepted') {
          $status = 'pending';
        } else {
          // Live microguides stay live.
          $status = $microguide->status;
        }
        break;
      case 'save':
      default:
        if (user::isAdmin()) {
          if (!in_array($status, array('draft', 'pending', 'rejected', 'accepted'))) {
            $status = 'draft';
          }
        } else {
          $status = 'draft';
        }
        break;
    }
    
    // Check Ebook links
    $availableOn = array();
    if (user::isAdmin()) {

      $kindle = ($kindle === null || trim($kindle) === '') ? null : trim($kindle);
      $itunes = ($itunes === null || trim($itunes) === '') ? null : trim($itunes);

      if (!is_null($kindle) || !is_null($itunes)) {
        $availableOn = array();

        if (!is_null($kindle)) {
          $availableOn['kindle'] = $kindle;
        }

        if (!is_null($itunes)) {
          $availableOn['itunes'] = $itunes;
        }
      }
    }

    // Check feature info
    $feature = null;
    $isNewFeature = false;
    $hasFeatureFields = false;
    if (user::isAdmin()) {

      if ($featurestatus != 'active' && $featurestatus != 'inactive') {
        $featurestatus = 'inactive';
      }

      if ($featureid) {
        $feature = new featuredMicroguide($featureid);
        if (!$feature->_id) {
          $errors['#featurequestion'] = 'We could not find this feature';
          $success = false;
        }
      } else {
        $feature = new featuredMicroguide();
        $isNewFeature = true;
      }

      if ($success && $featurestatus != 'inactive') {
        if (stripos($featurequestion, $featurequestionverb) === FALSE) {
          $errors['#featurequestionplace'] = 'Identifying word not found in title for front page';
          $success = false;
        }
        if (!$featurequestion) {
          $errors['#featurequestion'] = 'Please set a title for the front page.';
          $success = false;
        }
        if (!$featurequestionverb) {
          $errors['#featurequestionverb'] = 'Please set an identifying word.';
          $success = false;
        }
        if (!$featurequestionplace) {
          $errors['#featurequestionplace'] = 'Please set an identifying sub-word.';
          $success = false;
        }
      }

      if ($featurequestion || $featurequestionverb || $featurequestionplace) {
        $hasFeatureFields = true;
      }

    }

    // Ready to save
    if ($success) {
      $microguide->access = $access;
      $microguide->title = $title;
      $microguide->description = $description;
      $microguide->availableOn = $availableOn;
      $microguide->tags = $tags;
      $microguide->slug = $slug;
      $author = user::i($author);
      $microguide->author = $author->username;
      $microguide->authorSlug = $author->slug;
      $microguide->storyIds = db::idList($storyIds);
      $microguide->status = $status;
      if (!$microguide->issueNumber) {
        $sequence = new sequence('microguide-issue-number');
        $microguide->issueNumber = $sequence->nextVal();
      };

      $microguide->save();
      $id = (string)$microguide->_id;

      if ($feature && $hasFeatureFields) {
        $feature->microguideId = $microguide->_id;
        $feature->question = str_ireplace($featurequestionverb, "<span class='verb'>" . $featurequestionverb . "</span>", $featurequestion);
        $feature->questionVerb = $featurequestionverb;
        if (strlen($featurequestionplace) > 8) {
          $feature->questionPlace = "<span style='font-size: 90%;'>" . $featurequestionplace . "</span>";
        } else {
          $feature->questionPlace = $featurequestionplace;
        }
        $feature->status = $featurestatus;
        if ($feature->sequenceNumber === NULL) {
          $sequence = new sequence('featured-microguide-number');
          $feature->sequenceNumber = $sequence->nextVal();
        }
        $feature->save();
      }
    }

    echo json_encode(array('success' => $success, 'errors' => $errors, 'operation' => $operation, 'id' => $id, 'slug' => $slug));
    return true;
  }

  public function slideshowPhotos() {

    $success = false;
    $slideshowPhotos = array();
    $results = array();

    if (isset($_GET['slug'])) {

      $slideshowIndex = 0;
      
      $slug = $_GET['slug'];
      $microguide = microguide::findOne(array('slug' => $slug));

      $microguideObject = microguide::i($microguide);

      if ($microguideObject->storyCount) {
        $coverStory = $microguideObject->coverStory->data();
      } else {
        $coverStory = null;
      }

      if ($coverStory && count($coverStory['photos'])) {

        $slideshowPhotos['small'][$slideshowIndex] = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 230, 171);
        $slideshowPhotos['large'][$slideshowIndex] = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 398, 240);

        $slideshowIndex++;

      }

      $stories = array();
      $criteria = array();


      $criteria['$and'][] = array('_id' => array('$in' => $microguide['storyIds']));
      $criteria['$and'][] = array('_id' => array('$nin' => array($coverStory['_id'])));

      $cursor = story::find($criteria, array('photos' => true));

      foreach ($cursor as $story) {
        
        if (count($story['photos'])) {
          $photo = $story['photos'][0];

          $slideshowPhotos['small'][$slideshowIndex] = image::scaleUrl($photo['id']->{'$id'}, 'cropThumbnail', 230, 171);
          $slideshowPhotos['large'][$slideshowIndex] = image::scaleUrl($photo['id']->{'$id'}, 'cropThumbnail', 398, 240);
          
          $slideshowIndex++;
        }

      }

      if (count($slideshowPhotos)) {
        $success = true;
        $results['photos'] = $slideshowPhotos;
      }

    }

    $results['success'] = $success;
    echo json_encode($results);
    return true;

  }

  public function canEdit($microguide) {

    if (user::isAdmin()) {
      return true;
    }

    if (! $microguide instanceof microguide) {
      return false;
    }

    if (isset($_SESSION['user']) && $_SESSION['user']['slug'] == $microguide->authorSlug) {
      return true;
    }

    return false;
  }

  public function create($slug) {

    $microguide = microguide::findOne(array('slug' => $slug));
    if ($microguide) {
      $microguide = microguide::i($microguide);
    } else {
      $microguide = new microguide();
      $microguide->tags = array(); //this is needed for the page js
    }

    $microguide->addStoryStatus(array('invited', 'pending', 'draft'));
    $stories = $microguide->stories;
    $storyListOffset = 0;

    $__more_script = array('microguide', 'ui');
    $__more_style = array('microguide', 'ui/pepper-grinder/ui');

    $data = $GLOBALS['_D']['forms']['microguide'];

    $feature = featuredMicroguide::findOne(array('microguideId' => $microguide->_id));

    require_once 'tpl/microguide-create.php';
    return true;
  }

  public function delete() {

    $errors = array();
    $success = true;
    $operation = 'removed';
    $id = $_REQUEST['microguideId'];

    // Check permissions
    if (!user::isAdmin()) {
      $errors['#title'] = "You do not have permission to edit this microguide.";
      $success = false;
    }

    // Check microguide
    if ($id) {
      $microguide = microguide::findOne(array('_id' => new MongoId($id)));
      if (!$microguide) {
        $errors['#title'] = "Could not find this microguide";
        $success = false;
      }
      $microguide = microguide::i($microguide);
    } else {
      $errors['#title'] = "Could not find this microguide";
      $success = false;
    }

    $microguide->remove();

    echo json_encode(array('success' => $success, 'errors' => $errors, 'operation' => $operation, 'id' => $id));
    return true;
  }

}
