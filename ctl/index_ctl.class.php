<?php 

class index_ctl {

  public function index() {

    $status = 0;
    $stories = array();

    // STEP 1: Get features

    if (isset($_REQUEST['previewdate'])) {
      $time = strtotime($_REQUEST['previewdate']);
    } else {
      $time = time();
    }
    $features = featuredMicroguide::getFeatures($time);

    // STEP 2: Build data structures

    $microguideIds = array();
    $storyIds = array();
    $microguides = array();
    $stories = array();
    $maxStories = 5;

    // BATCH FETCH MICROGUIDES
    for ($i = 0, $max = count($features); $i < $max; $i++) {
      $microguideIds[] = $features[$i]['microguideId'];
    }
    $cursor = microguide::find(array('_id' => array('$in' => $microguideIds)));
    foreach ($cursor as $id => $microguide) {
      $microguides[$id] = $microguide;
      for ($j = 0, $maxj = count($microguide['storyIds']); $j < $maxj; $j++) {
        $storyId = $microguide['storyIds'][$j];
        $storyIds[(string)$storyId] = $storyId;
      }
    }

    // BATCH FETCH STORIES

    //BEGIN TEMP FIX
    //Deal with an empty storyId, which brought down
    //the site. How did that happen? Once you figure
    //that out, do a proper fix and remove this code.
    $_tmp_storyIds = $storyIds;
    $storyIds = array();
    foreach ($_tmp_storyIds as $_storyId) {
      if (count($_storyId) > 0) {
        $storyIds[] = $_storyId;
      } else {
        error_log("FIX ME: empty storyId on home page.");
      }
    }
    //END TEMP HACK

    $cursor = story::find(array('_id' => array('$in' => $storyIds)));
    foreach ($cursor as $id => $story) {
      $stories[$id] = $story;
    }

    // PREPARE TEMPLATE DATA
    for ($i = 0, $max = count($features); $i < $max; $i++) {

      // Merry-go-round points to a deleted microguide
      if (!isset($microguides[(string)$features[$i]['microguideId']])) continue;

      $microguide = microguide::i($microguides[(string)$features[$i]['microguideId']]);
      $microguide->setStories($stories);
      $features[$i]['coverPhoto'] = image::scaleUrl($microguide->coverStory->photos[0]['id'], 'cropThumbnail', 470, 348);
      $features[$i]['microguideTitle'] = $microguide->title;
      $features[$i]['microguideSlug'] = $microguide->slug;
      $features[$i]['microguideAuthor'] = $microguide->author; 
      $features[$i]['microguideAuthorSlug'] = $microguide->authorSlug; 
      
      for($j = 1; $j < 5; $j++) {
        $features[$i]['stories'][$j]['photo'] = image::scaleUrl($microguide->stories[$j]->photos[0]['id'], 'cropThumbnail', 230, 171);
        $features[$i]['stories'][$j]['title'] = $microguide->stories[$j]->title;
        $features[$i]['stories'][$j]['slug'] = $microguide->stories[$j]->slug;
      }
    }

    // Hack for mobile phones, which only display one featured microguide.
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobi') !== FALSE) shuffle($features);

    $__more_style = array('home', 'message');
    $__more_script = array('home', 'jquery.cycle.all');

    $contributor_photos = explode('|', CONTRIBUTOR_PHOTOS);
    $contributor_links = explode('|', CONTRIBUTOR_LINKS);
    $contributor_info = array();
    for ($i = 0, $max = count($contributor_photos); $i < $max; $i++) {
      $contributor_info[] = array($contributor_photos[$i], $contributor_links[$i]);
    }
    shuffle($contributor_info);

    require_once 'tpl/home.php';

  }

  public function status($status) {

    
    if (!user::isAdmin()) {
      header('Location: '.G_URL);
      return false;

    }

    $stories = array();
    $__more_style = array('home');
    $__body_class = "no-home-top";
    require_once 'tpl/home.php';

  }
  
  public function microguides($path, $return=false, $defaultRowLimit=2, $smallColumns=5, $largeColumns=3, $limit=16, $offset=0) { 
    /* Right now it will only sort by most recent */
    $errors = array();
    $success = true;

    $featuredSlugs = array();

    if (isset($_REQUEST['columns']) && is_numeric($_REQUEST['columns'])) {
      $smallColumns = intval($_REQUEST['columns']);
    }
    if (isset($_REQUEST['smallColumns']) && is_numeric($_REQUEST['smallColumns'])) {
      $smallColumns = intval($_REQUEST['smallColumns']);
    }
    
    if ($smallColumns == 4) $smallColumns = 5; // Right now we don't have the css to support 4 small columns

    if ($smallColumns > 6)
      $largeColumns = 4;
    if ($smallColumns > 8)
      $largeColumns = 5;

    $forceLargeColumns = false;
    $forceSmallColumns = false;
    
    if (isset($_REQUEST['rowLimit']) && is_numeric($_REQUEST['rowLimit'])) {
      $defaultRowLimit = intval($_REQUEST['rowLimit']);
    }

    if (isset($_REQUEST['forceSmall']) && is_numeric($_REQUEST['forceSmall'])) {
      $forceSmallColumns = (intval($_REQUEST['forceSmall']) == 1) ? true : false;
    }
    
    if ($smallColumns <= 3) {
      $forceSmallColumns = true;
      $smallColumns = 3;
    }
    
    if (isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) {
      $limit = intval($_REQUEST['limit']);
    } else {
      if ($forceSmallColumns) {
        $limit = ($smallColumns * 2) * $defaultRowLimit;
      } else {
        $limit = ($smallColumns + $largeColumns) * $defaultRowLimit;
      }
    }

    if (isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset'])) {
      $offset = intval($_REQUEST['offset']);
    }
    
    /* Search conditions here */
    $criteria = array();

    $query = false;
    $filter = false;
    
    
    $queryString = '';
    /* Combine all of the "search terms" into a string that we can pass to sphinx */
    if (isset($_REQUEST['filters']['query']) && is_array($_REQUEST['filters']['query'])) {
      $query = true;
      foreach ($_REQUEST['filters']['query'] as $query) {
        $queryString .= ' "' . $query .'"'; // Hopefully nice and simple - we'll see how sphinx handles this
      }
    }
    
    // author search
    if (isset($_REQUEST['filters']['authors']) && is_array($_REQUEST['filters']['authors'])) {
      $filter = true;
      foreach ($_REQUEST['filters']['authors'] as $author) {
        $criteria['$and'][]['authorSlug'] = new MongoRegex("/$author/i");
      }
    }
    
    // tag search
    if (isset($_REQUEST['filters']['tags']) && is_array($_REQUEST['filters']['tags'])) {
      $query = true;
      foreach ($_REQUEST['filters']['tags'] as $tag) {
        $queryString .= ' "' . $tag .'"';
      }
    }
    
    if (
      isset($_REQUEST['status']) && 
      in_array($_REQUEST['status'], array('pending','draft')) 
      && user::isAdmin()) {
      $filter = true;
      $criteria['$and'][] = array('status' => $_REQUEST['status']);

    } else {
      $filter = true;
      $criteria['$and'][] = array('status' => 'accepted');
    }
      
    $microguides = array();
    $rawRecords = array();

    $searchTimeout = false;
    $moreAvailable = false;
    
    /* Effect the search */
    
    if (!$query) {

      $cursor = microguide::find($criteria)
        ->sort(array('issueNumber' => -1))
        ->limit($limit)
        ->skip($offset*$limit);
      foreach ($cursor as $key=>$microguide) {
        $rawRecords[] = $microguide;
      }
      
      /* Looking to see if there will be more records available for another query */
      $testCursor = microguide::find($criteria)
        ->sort(array('issueNumber' => -1))
        ->limit($limit)
        ->skip($offset*($limit+1));
      if (count($testCursor) > 0) {
        $moreAvailable = true;
      }
      
    } else {
      $results = search::query($queryString, array('microguide'), $offset*$limit, $limit); 
      
      /* Looking to see if there will be more records available for another query */
      $testResults = search::query($queryString, array('microguide'), ($offset + 1) * $limit, $limit);
      if (isset($testResults['microguide']['matches']) && count($testResults['microguide']['matches']) > 0) $moreAvailable = true;
      
      if (isset($results['microguide']['matches'])) {
        foreach ($results['microguide']['matches'] as $match) {
          $microguide = $match['objectData'];
          $addItem = true;
          /* Filter now */
          if ($filter) {
            $addItem = false;
            foreach ($criteria['$and'] as $tags) {
              foreach($tags as $tag_type => $tag) {

                switch ($tag_type) {
                    
                  case 'status':
                    if (is_array($tag)) {
                      if (in_array($microguide['status'], $tag)) {
                        $addItem = true;
                      }
                    } else {
                      if ($microguide['status'] == $tag) {
                        $addItem = true;
                      }
                    }
                    break;
                    
                  case 'authorSlug':
                    if ($story['authorSlug'] == $key) {
                      $addItem = true;
                    }
                    break;
                  
                  default:
                    break;
  
                }
              }
            }
          }
          
          if ($addItem) {
            $rawRecords[$microguide['_id']->{'$id'}] = $microguide;
          }
        }
        
      } else { // No matches returned
        $records = array();
        $timeoutStr = "query time exceeded max_query_time";
        if (isset($results['microguide']['warning']) && (strpos($timeoutStr, strtolower($results['microguide']['warning'])) !== FALSE)) {
          $searchTimeout = true;
        } 
      }
    }

    /* Get photos for the microguides */
    foreach($rawRecords as $microguide) {

      if (!microguide::i($microguide)->storyCount) continue;

      $coverStory = microguide::i($microguide)->coverStory->data();
      
      if (count($coverStory['photos'])) {

        $smallThumbnail = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 230, 171);
        $largeThumbnail = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 398, 240);

        $coverStory['smallThumbnail'] = $smallThumbnail;
        $coverStory['largeThumbnail'] = $largeThumbnail;

        $microguide['coverStory'] = $coverStory;
       
      }

     $microguides[] = $microguide; 
    }

    // call is ajax, format html and spit out json
    if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {

      ob_start();
      require 'tpl/_microguides_columns.php';
      $html = ob_get_contents();
      ob_end_clean();
   
      $success = true;
      
      $result = array('success' => $success, 
                      'errors' => $errors, 
                      'html' => $html, 
                      'count' => count($microguides), 
                      'moreAvailable' => $moreAvailable,
                      'searchTimeout' => $searchTimeout);
      
      if ($return) {
        return $result;
      }
      echo json_encode($result);
      return true;

    }

    return $microguides;

  }
  
  public function stories($return=false, $columns=5, $offset=0, $status=false) {
    /* TODO: At the moment, this function can't display a query search with $_REQUEST['filters']['microguides'] set 
     * It should work fine if there is no query however. 
     * TODO: This function also cannot do proper sorting with a search query. */

    $criteria = array();

    if (isset($_REQUEST['columns']) && is_numeric($_REQUEST['columns'])) {
      $columns = intval($_REQUEST['columns']);
    }

    if (isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset'])) {
      $offset = intval($_REQUEST['offset']);
    }

    $limit = 50;
    if (isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) {
      $limit = intval($_REQUEST['limit']);
    }

    if (
      isset($_REQUEST['status']) && 
      in_array($_REQUEST['status'], array('pending','rejected')) 
      && user::isAdmin()) {

      $criteria['$and'][] = array('status' => $_REQUEST['status']);

    } else {
      $criteria['$and'][] = array('status' => 'accepted');
    }

    $stories = array();
    $keys = array();
    
    $query = false;
    $filter = false;
    
    /* Combine all of the "search terms" into a string that we can pass to sphinx */
    if (isset($_REQUEST['filters']['query']) && is_array($_REQUEST['filters']['query'])) {
      $query = true;
      $queryString = '';
      foreach ($_REQUEST['filters']['query'] as $query) {
        $queryString .= ' "' . $query .'"'; // Hopefully nice and simple - we'll see how sphinx handles this
      }
    }

    // tag search
    if (isset($_REQUEST['filters']['tags']) && is_array($_REQUEST['filters']['tags'])) {
      $filter = true;
      foreach ($_REQUEST['filters']['tags'] as $tag) {
        $criteria['$and'][]['tags'] = array('$in' => array(strtolower($tag)));
      }
    }

    // author search
    if (isset($_REQUEST['filters']['authors']) && is_array($_REQUEST['filters']['authors'])) {
      $filter = true;
      foreach ($_REQUEST['filters']['authors'] as $author) {
        $criteria['$and'][]['authorSlug'] = new MongoRegex("/$author/i");
      }
      /* This bio redirect is already in display(), but I'm leaving it here for now in case
       * there might be a reason to call this function from somewhere else and this code here 
       * would be necessary
       */
      if (
          $_REQUEST['bio'] == 'false'                    // not already on bio page
          && isset($_REQUEST['filters']['authors'])      // this is an author query
          && count($_REQUEST['filters']['authors']) == 1 // request for single author
          && count($_REQUEST['filters']) == 1            // no other query terms are present
      ) {
        $bioCriteria = array('authorSlug' => $_REQUEST['filters']['authors'][0], 'status' => 'bio');
        if (story::i(story::findOne($bioCriteria))->exists()) {
          define('KDEBUG_JSON', true);
          // this causes the frontend to redirect to the bio page 
          echo json_encode(array('bio' => '/story/bio/'.$_REQUEST['filters']['authors'][0]));
          return true;
        }
      }

    }

    // TODO: Get this to work in conjunction with query searching.
    $microguide_sort = false;
    if (isset($_REQUEST['filters']['microguides']) && is_array($_REQUEST['filters']['microguides'])) {
      $filter = true;
      $microguide_sort = true;
      foreach ($_REQUEST['filters']['microguides'] as $slug) {
        $microguideData = microguide::findOne(array('slug' => $slug));
        $criteria['$and'][]['_id']['$in'] = $microguideData['storyIds'];
      }
    }

    /* This sorting is only effective if there's no search query */
    $sort = array('created' => -1);
    // sorts: most bravos, least bravos, newest, oldest, random
    if (isset($_REQUEST['sort'])) {
        switch ($_REQUEST['sort']) {
        case 'most':
            $sort = array('bravoCount' => -1);
            break;
        case 'least':
            $sort = array('bravoCount' => 1);
            break;
        case 'oldest':
            $sort = array('created' => 1);
            break;
        case 'random':
            $sort = array('created' => -1);
            $limit = 20;
            break;
        case 'newest':
        default:
            break;
        }
    }

    $searchTimeout = false;
    
    if (!$query) {

      $cursor = story::find($criteria)
            ->sort($sort)
            ->limit($limit)
            ->skip($offset*$limit);
  
      foreach ($cursor as $key=>$object) {
        $raw_records[$object['_id']->{'$id'}] = $object;
      }
    
      $records = $raw_records;
    
    } else { /* If we need to do a query and then filter by the rest of the criteria */

      $results = search::query($queryString, array('story'), $offset * $limit, $limit);

      if (isset($results['story']['matches'])) {
        foreach ($results['story']['matches'] as $match) {
          $story = $match['objectData'];
          $addItem = true;
          /* Filter now */
          if ($filter) {
            $addItem = false;

            foreach ($criteria['$and'] as $tags) {
              foreach($tags as $tag_type => $tag) {

                switch ($tag_type) {
                    
                  case 'status':
                    if ($story['status'] == $tag) { 
                      $addItem = true;
                    }
                    break;
                    
                  case 'tags':
                    foreach ($tags as $tag) {
                      if (in_array($tag, $story['tags'])) {
                        $addItem = true;
                      }
                    } 
                    
                    break;
                    
                  case 'authorSlug':
                    if ($story['authorSlug'] == $key) $addItem = true; 
                    break;
                  
                  default:
                    break;
  
                }
              }
            }
          }
          
          if ($addItem) {
            $raw_records[$story['_id']->{'$id'}] = $story;
          }
        }

        if (isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'random') { // TODO: what about sorting by newest, etc if there's a query??
          $records = $raw_records;
          shuffle($records); 
        } else {
          $records = $raw_records;
        }
        
      } else { // No matches returned - check for timeout warning
        $records = array();
        $timeoutStr = "query time exceeded max_query_time";
        if (isset($results['story']['warning']) && (strpos($timeoutStr, strtolower($results['story']['warning'])) !== FALSE)) {
          $searchTimeout = true;
        } 

      }
    }

    $i = 0;
    /* Get photos for stories */
    foreach ($records as $key=>$story) {

      if (isset($story['photos'])) {

        $thumbnail = self::thumbnailUrl($story);
        $stories[$i%$columns][$key] = story::i($story)->data();
        $stories[$i%$columns][$key]['thumbnail'] = $thumbnail;

        $keys[] = new MongoId($key);

      }

      $i++;

    }

    $bravos = array();

    if (isset($_SESSION['user'])) {
      $bravos = bravo::getUserBravos($_SESSION['user']['_id'], $keys);
    }

    /* MaxB: Is there any way to tell if this is still neccessary? */
    // Some layouts want the stories to be wrapped in ul/li
    if (isset($_REQUEST['liwrap'])) {
      $liwrap['ul'] = array();
      switch ($_REQUEST['liwrap']) {
      case 'microguide':
         $liwrap['ul']['id'] = 'microguide-carousel';
         $liwrap['ul']['class'] = 'jcarousel-skin-microguide';
         break; 
      default:
         $liwrap['ul']['id'] = '';
         $liwrap['ul']['class'] = '';
      }
    }

    // call is ajax, format html and spit out json
    if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {

      define('KDEBUG_JSON', true);

      if (isset($_REQUEST['more']) && $_REQUEST['more'] == 1) {

        $html = array();

       foreach ($stories as $col=>$stories_column) {
          ob_start();
          require 'tpl/_stories_column.php';
          $html[$col] = ob_get_clean();
        }

      } else {

        ob_start();
        require 'tpl/_stories.php';
        $html = ob_get_contents();
        ob_end_clean();

      }

      $result = array('html' => $html, 
                      'count' => $i, 
                      'success' => true,
                      'searchTimeout' => $searchTimeout);
      if ($i == 0) {
        $result['img'] = search::random_noresult_image();
        $result['url'] = G_URL . 'spinner';
      }
      if ($return) {
        return $result;
      }
      echo json_encode($result);
      return true;

    }

    return $stories;

  }

  public function display() {
    /* MaxB: I added this function to facilitate the presentation of mixed search results from
     * the front-end AJAX. It determines which of microguides() and stories() from above to call
     * and compiles their results including their html and error/sucess info into
     * a JSON object that the front-end can digest  
    /*TODO: Remove debugging lines when done */
     
    /* This will tell us which objects to grab and return to the front-end
     * $type defaults to stories only -- use model class names for type here */
    $type = array('story');

    if (isset($_REQUEST['type']) && is_array($_REQUEST['type'])) {
      $type = $_REQUEST['type'];
    }
    
    /* If this search should redirect the front-end to a bio page then send that back immediately. */
    if (
        $_REQUEST['bio'] == 'false'                    // not already on bio page
        && isset($_REQUEST['filters']['authors'])      // this is an author query
        && count($_REQUEST['filters']['authors']) == 1 // request for single author
        && count($_REQUEST['filters']) == 1            // no other query terms are present
    ) {
      $bioCriteria = array('authorSlug' => $_REQUEST['filters']['authors'][0], 'status' => 'bio');
      if (story::i(story::findOne($bioCriteria))->exists()) {
        define('KDEBUG_JSON', true);
        // this causes the frontend to redirect to the bio page
        echo json_encode(array('bio' => '/story/bio/'.$_REQUEST['filters']['authors'][0]));
        return true;
      }
    }
    
    $storiesResult = array();
    $microguidesResult = array();
    
    /* $type tells us whether or not to return stories results */
    if (in_array('story', $type)) {
      $storiesResult = self::stories(true);
    }
    
    /* $type tells us whether or not to return microguide results */
    if (in_array('microguide', $type)) {
      /* microguides() behaves a little differently depending on whether or not
       * it should be displaying stories below the microguides */
      if (in_array('story', $type)) {
        $microguidesResult = self::microguides('', true, 1);
      } else {
        $microguidesResult = self::microguides('', true);
      }
    }
    
    $result = array();
    $success = false;
    $count = 0;
    $html = '';
    $url = '';
    $img = '';
    
    /* If we're spitting out both microguides and stories */
    if (in_array('microguide', $type) && in_array('story', $type)) {
      if (isset($microguidesResult['success']) && $microguidesResult['success'] &&
          isset($storiesResult['success']) && $storiesResult['success']) {
            
        $success = true;
        $count += $microguidesResult['count'];
        
        if ($microguidesResult['count'] > 0) {
          ob_start();
          require 'tpl/_stories_search_results_header.php';
          $html .= ob_get_contents();
          ob_end_clean();
          $html .= $microguidesResult['html'];
          
          
          if ($storiesResult['count'] > 0) {
            ob_start();
            require 'tpl/_stories_microguides_separator.php';
            $html .= ob_get_contents();
            ob_end_clean();
          }  
        }
        
        if ($storiesResult['count'] > 0) {
          $count += $storiesResult['count'];
          $html .= $storiesResult['html'];
        }
        
      }
        
    } else {
      /* We're just returning one or the other */
      if (isset($microguidesResult['success']) && $microguidesResult['success'] ) {
        $success = true;
        $result['microguides'] = $microguidesResult;
        $count += $microguidesResult['count'];
        $html = $microguidesResult['html'];
      } else if (isset($storiesResult['success']) && $storiesResult['success']) {
        $success = true;
        $result['stories'] = $storiesResult;
        $count += $storiesResult['count'];
        $html = $storiesResult['html'];
      }
    }
    
    if ($success) {
      
      if ($count <= 0) {
        $result['img'] = search::random_noresult_image();
        $result['url'] = G_URL . 'spinner';
      }
      if (isset($microguidesResult['moreAvailable'])) {
        $result['moreMicroguidesAvailable'] = $microguidesResult['moreAvailable'];
      }
      $result['microguidesTimeout'] = $microguidesResult['searchTimeout'];
      $result['storiesTimeout'] = $storiesResult['searchTimeout'];
      $result['microguides'] = $microguidesResult;
      $result['stories'] = $storiesResult;
      $result['count'] = $count;
      $result['html'] = $html;
    } 
    
    $result['success'] = $success;
    echo json_encode($result);
    return true;
        
  }

  private function thumbnailUrl($story) {

    return image::scaleUrl($story['photos'][0]['id']->{'$id'}, 'scale', 230);

  }

  public function submissions($columns=5, $offset=0, $limit=12) {

    if (!isset($_SESSION['user'])) {
      return false;
    }

    if (isset($_REQUEST['columns']) && is_numeric($_REQUEST['columns'])) {
      $columns = $_REQUEST['columns'];
    }
    $_columns = $columns; //$columns is overwritten in the tpl which we run multiple times

    if (isset($_REQUEST['append']) && $_REQUEST['append'] == 1) {
      $append = true;
    } else {
      $append = false;
    }

    $validStatuses = array('pending', 'draft', 'rejected', 'accepted');
    if (isset($_REQUEST['statuses'])) {
      $statuses = array();
      foreach ($_REQUEST['statuses'] as $status) {
        if (in_array($status, $validStatuses)) {
          $statuses[] = $status;
        }
      }
    } else {
      $statuses = array('pending', 'draft', 'rejected', 'accepted');
    }


    $sort = array('created' => -1);

    $search = array();
    $search['authorSlug'] = $_SESSION['user']['slug'];


    $result = array('stories' => array());

    foreach ($statuses as $status) {

      $stories = array();
      $keys = array();
      $search['status'] = $status;

      $cursor = story::find($search)
        ->sort($sort);


      if (isset($_REQUEST['offset'])
        && isset($_REQUEST['offset'][$status])
          && is_numeric($_REQUEST['offset'][$status])) {
        $_offset = $_REQUEST['offset'][$status];
      } else {
        $_offset = $offset;
      }

      if (isset($_REQUEST['limit'])
        && isset($_REQUEST['limit'][$status])
          && is_numeric($_REQUEST['limit'][$status])) {
        $_limit = $_REQUEST['limit'][$status];
      } else {
        $_limit = $limit;
      }


      if ($_limit >= 0) {
        $cursor->limit($_limit)->skip($_offset*$_limit);
      } elseif ($status == 'accepted') {
        $cursor->limit(12)->skip($_offset*12);
      }

      $i = 0;
      foreach ($cursor as $key => $story) {

        if (isset($story['photos'])) {

          $thumbnail = self::thumbnailUrl($story);
          $stories[$i%$_columns][$key] = story::i($story)->data();
          $stories[$i%$_columns][$key]['thumbnail'] = $thumbnail;

          $keys[] = new MongoId($key);

        }

        $i++;

      }

      if ($append) {

        $result['stories'][$status] = array(
          'countReturned' => $i,
          'countTotlal'   => $cursor->count()
        );

        foreach ($stories as $col => $stories_column) {

          ob_start();
          require 'tpl/_stories_column.php';
          $result['stories'][$status]['html'][$col] = ob_get_clean();

        }


      } else {

        ob_start();
        require 'tpl/_stories.php';
        $html = ob_get_contents();
        ob_end_clean();

        $result['stories'][$status] = array('html' => $html, 'countReturned' => $i, 'countTotal' => $cursor->count());

      }

    }


    echo json_encode($result);
    return true;

  }

  public function mymicroguides($columns=5, $offset=0, $limit=12) {

    if (!isset($_SESSION['user'])) {
      return false;
    }

    if (isset($_REQUEST['columns']) && is_numeric($_REQUEST['columns'])) {
      $columns = $_REQUEST['columns'];
    }

    $validStatuses = array('pending', 'draft', 'rejected', 'accepted');
    if (isset($_REQUEST['statuses'])) {
      $statuses = array();
      foreach ($_REQUEST['statuses'] as $status) {
        if (in_array($status, $validStatuses)) {
          $statuses[] = $status;
        }
      }
    } else {
      $statuses = array('pending', 'draft', 'rejected', 'accepted');
    }

    $smallColumns = 5; $largeColumns = 3;

    $sort = array('created' => -1);

    $search = array();
    $search['authorSlug'] = $_SESSION['user']['slug'];


    $result = array('stories' => array());

    foreach ($statuses as $status) {

      $stories = array();
      $keys = array();
      $search['status'] = $status;

      $cursor = microguide::find($search)
        ->sort($sort);


      if (isset($_REQUEST['offset'])
        && isset($_REQUEST['offset'][$status])
          && is_numeric($_REQUEST['offset'][$status])) {
        $_offset = $_REQUEST['offset'][$status];
      } else {
        $_offset = $offset;
      }

      if (isset($_REQUEST['limit'])
        && isset($_REQUEST['limit'][$status])
          && is_numeric($_REQUEST['limit'][$status])) {
        $_limit = $_REQUEST['limit'][$status];
      } else {
        $_limit = $limit;
      }


      if ($_limit >= 0) {
        $cursor->limit($_limit)->skip($_offset*$_limit);
      } elseif ($status == 'accepted') {
        $cursor->limit(12)->skip($_offset*12);
      }

      $i = 0;
      $microguides = array();
      foreach ($cursor as $key => $microguide) {

        $microguideObject = microguide::i($microguide);
        if ($microguideObject->storyCount) {
          $coverStory = $microguideObject->coverStory->data();
        } else {
          $coverStory = null;
        }

        if ($coverStory && count($coverStory['photos'])) {

          $smallThumbnail = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 230, 171);
          $largeThumbnail = image::scaleUrl($coverStory['photos'][0]['id']->{'$id'}, 'cropThumbnail', 398, 240);

          $coverStory['smallThumbnail'] = $smallThumbnail;
          $coverStory['largeThumbnail'] = $largeThumbnail;

          $microguide['coverStory'] = $coverStory;

        } else {
          $microguide['coverStory'] = array(
                                            'smallThumbnail' => '/img/placeholder-small.png',
                                            'largeThumbnail' => '/img/placeholder-large.png',
                                            );
        }

        $microguides[] = $microguide;
        $i++;

      }

      switch ($status) {
      case 'draft':
      case 'pending':
        $forceLargeColumns = true;
        $forceSmallColumns = false;
        break;
      case 'declined':
      case 'accepted':
      default:
        $forceLargeColumns = false;
        $forceSmallColumns = true;
        break;
      }

      ob_start();
      require 'tpl/_microguides_columns.php';
      $html = ob_get_contents();
      ob_end_clean();

      $result['microguides'][$status] = array('html' => $html, 'countReturned' => $i, 'countTotal' => $cursor->count());

    }


    echo json_encode($result);
    return true;

  }


  public function picks($name, $columns = 5, $exclude = '') {

    if (isset($_REQUEST['columns']) && is_numeric($_REQUEST['columns'])) {
      $columns = $_REQUEST['columns'];
    }

    if (isset($_REQUEST['exclude'])) {
      $exclude = $_REQUEST['exclude'];
    }

    $limit = $columns; //1 row

    // Get the story ids
    $storyIds = array();
    $criteria = array('user_id' => new MongoId(EDITORS_PICKS_USER_ID));
    if (!empty($exclude)) {
      $criteria['slug'] = array('$nin' => array($exclude));
    }

    $cursor = bravo::find($criteria)->sort(array('created' => -1))->limit($limit);
    foreach ($cursor as $id => $bravo) {
      $storyIds[] = $bravo['story_id'];
    }

    // Get the unsorted story objects
    $rows = array();
    $cursor = story::find(array('_id' => array('$in' => $storyIds)));
    foreach ($cursor as $id => $story) {
      $rows[$id] = $story;
    }

    // Return the story objects in order
    $stories = array();
    for ($i = 0; $i < count($storyIds); $i++) {
      $story = story::i($rows[$storyIds[$i]->{'$id'}])->data();
      $story['thumbnail'] = self::thumbnailUrl($story);
      $stories[$i][$storyIds[$i]->{'$id'}] = $story;
    }


    ob_start();
    require 'tpl/_stories.php';
    $html = ob_get_contents();
    ob_end_clean();

    $result = array('stories' => $html, 'count' => $i);

    echo json_encode($result);
    return true;
    
  }

}
