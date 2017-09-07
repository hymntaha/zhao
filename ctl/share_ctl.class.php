<?php 

class share_ctl {

  public function index($slug=false) {

    $bio = false;

    if ($slug && $slug == 'bio') {

      $bio = true;
      $story = new story(story::findOne(array('status' => 'bio', 'authorSlug' => $_SESSION['user']['slug'])));

      if (!$story->exists()) {
        unset($story);
      } else {

        $files = array();
        $grid = story::grid();
        foreach ($story->photos as $key=>$value) {

          if (is_array($value)) {
            $files[$key] = $grid->get($value['id']);
          }

        }


      }

    } elseif ($slug && ($story = new story(story::findOne(array('slug' => $slug)))) && $story->exists()) {

      $files = array();
      $grid = story::grid();
      foreach ($story->photos as $key=>$value) {

        if (is_array($value)) {
          $files[$key] = $grid->get($value['id']);
        }

      }

    } else {
      unset($story);
    }

    if (isset($story) && !self::canEdit($story)) {
      header('Location: '.G_URL);
      return true;
    }

    if (isset($story)) {
      $comments = comment::find(array('story' => $story->id()))->sort(array('created' => -1));
    }

    $data = $GLOBALS['_D']['forms']['share'];
    
    $__more_script = array('ui');
    $__more_style = array('ui/pepper-grinder/ui', 'message');

    if (!$bio) {

      message::loadTypeToDisplayRegion(
        message::TYPE_SHARE_INSTRUCTION,
        $_SESSION['user']['_id'],
        message::DISPLAY_REGION_LEFT_OF_GREETING
      );

      message::loadTypeToDisplayRegion(
        message::TYPE_SHARE_FULLWIDTH_BANNER,
        $_SESSION['user']['_id'],
        message::DISPLAY_REGION_SHARE_PAGE_TOP
      );

    }

    require_once 'tpl/share.php';

  }

  protected static function canEdit($story) {

    if ($story->status == 'invited') {
      return true;
    }

    if (!isset($_SESSION['user'])) {
      return false;
    }

    if (isset($story) && isset($_SESSION['user']) && $_SESSION['user']['username'] == $story->author) {
      return true;
    }

    if (isset($story) && user::isAdmin()) {
      return true;
    }

    return false;

  }

  public function __call($method, $args) {

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'DELETE':
      self::delete($method);
      break;
    default:
      self::index($method);
    }
  }

  public function status() {

    if (user::isAdmin() && strlen($_REQUEST['id']) == 24) {

      define('KDEBUG_JSON', true);

      $success = false;

      $story = new story($_REQUEST['id']);

      if (in_array($_REQUEST['status'], array('invited','pending','draft','accepted','rejected'))) {

        $success = true;
        $story->status = $_REQUEST['status'];
        $story->updated = time();
        $story->save(); 

      }

      /* If the story is accepted, send back an indication to proceed with allowing the editor to send the writer a message
       * If the editor isn't connected via facebook, send back access_token=false*/
      if ($_REQUEST['status'] == 'accepted') {

      	if (isset($_SESSION['user']['fb_uid'])) {
          echo json_encode(array('success' => $success, 'fb_access' => true));
      	} else {
          echo json_encode(array('success' => $success, 'fb_access' => false));      	
      	}

      }	else if ($_REQUEST['status'] == 'rejected') {

      	echo json_encode(array('success' => $success, 'rejected' => true));

      } else {

      	echo json_encode(array('success' =>$success));

      }

      return true;

    }

    return false;

  }

  public function freshen() {

    $success = false;
    if (user::isAdmin() && strlen($_REQUEST['id']) == 24) {

      $story = new story($_REQUEST['id']);
      if ($story) {
        $success = true;
        $story->created = time();
        $story->updated = time();
        $story->save(); 
      }

    }

    echo json_encode(array('success' => $success));

    return $success;
  }

  public function submit() {

    define('KDEBUG_JSON', true);
    //header('Content-type: text/json');

    $data = $GLOBALS['_D']['forms']['share'];

    $response = json_decode(file_get_contents('php://input'), true);

    // Request data is JSON-encoded and doesn't show up in $_[GET|POST|REQUEST], so we have to
    // explicitly santize it.
    $sanitizer = new sanitizer();
    $sanitizer->excludeKey('data'); //data is a base64 encoded image
    $sanitizer->cleanArray($response);

    $errors = array();

    // switches
    $success = true;
    $new = true;
    $allowPartialSave = true;
    $saveWithInvitedStatus = false;
    $isClaiming = false;
    $microguideSlug = null;

    // check if this is an edit
    if (isset($response['id']) && $response['id'] != false && strlen($response['id']) == 24) {

      $story = new story($response['id']);

      if ($story->exists()) {
        $new = false;
      }

    }

		
    if (!$new && !self::canEdit($story)) {
      $errors['title'] = 'You are not allowed to edit this story';
      $success = false;
    }
     
    if (!isset($_SESSION['user'])) {
      $errors[] = 'You are not currently logged in';
      $success = false;
    }

    // Check if the story is being claimed.
    if ( !$new 
         && $story->status == 'invited'
         && $story->authorSlug != $_SESSION['user']['slug']
         ) {
      $isClaiming = true;
      $saveWithInvitedStatus = false;
    }

    // Check this is an admin working on an invited story
    if (!$isClaiming && user::isAdmin()) {
      if ($new) {
        if (isset($response['status']) && $response['status'] == 'invited') {
          $saveWithInvitedStatus = true;
        }
      } else {
        if ($story->status == 'invited') {
          $saveWithInvitedStatus = true;
        }
      }
    }

    // Partial saves not allowed if this is a bio or final submit
    if (!$bio && isset($response['submit']) && $response['submit'] == 1 && !$saveWithInvitedStatus) {
      $allowPartialSave = false;
    }

    if (isset($response['microguide'])) {
      $microguideSlug = $response['microguide'];
    }

    if ($response['bio'] != 1) {

      if (!isset($response['title']) || empty($response['title']) || $response['title'] == $data['title']['tip']) {
        $errors['title'] = $data['title']['error_none'];
        $success = false;
      } elseif (strlen($response['title']) > 40) {
        $errors['title'] = $data['title']['error_long'];
      } elseif (
           ( $new && story::findOne(array('slug' => user::slugify($response['title']))) )
           || (!$new && story::findOne(array('slug' => user::slugify($response['title']), '_id' => array('$ne' => new MongoId($response['id']))))  )
      ) {
        $errors['title'] = $data['title']['error_duplicate'];
        $success = false;
      }

    }

    if (!isset($response['text']) || empty($response['text']) || $response['text'] == $data['text']['tip']) {
      if (!$allowPartialSave) {
        $errors['text'] = $data['text']['error_none'];
        $success = false;
      } else {
        $response['text'] = '';
      }
    }

    if (!is_array($response['tags']) || count($response['tags']) < 1) {
      if (!$allowPartialSave) {
        $errors['tags'] = $data['tags']['error_less'];
        $success = false;
      } else {
        $response['tags'] = array();
      }
    }

    if (!is_array($response['tags']) || count($response['tags']) > 20) {
      $errors['tags'] = $data['tags']['error_more'];
      $success = false;
    }

    if (!is_array($response['location']) || $response['location']['formatted'] == '') {
      if (!$allowPartialSave) {
        $errors['location'] = $data['location']['error_none'];
        $success = false;
      } else {
        $response['location'] = array(
                                      'name' => '',
                                      'formatted' => '',
                                      'lattitude' => '',
                                      'longitude' => '',
                                      );
      }
    }

    if (!is_array($response['files']) || count($response['files']) < 1) {
      if (!$allowPartialSave) {
        $errors['photo_select'] = 'you need at least one photo';
        $success = false;
      } else {
        $response['files'] = array();
      }
    }

    // sanitization passed, time to store our story
    if ($success == true) {

      $grid = story::grid();
      $photos = array();

      if ($new) {
        $story = new story();

        // Add hook if we're putting this story in a microguide after it's created.
        if ($microguideSlug) {
          hook::add('story_new', function($story) use ($microguideSlug) {
              $microguide = microguide::findOne(array('slug' => $microguideSlug));
              if ($microguide) {
                $microguide = microguide::i($microguide);
                $microguide->addStory($story);
              }
            });
        }

      } else {

        // delete the old photos/files
        foreach ($story->photos as $key=>$value) {
          $grid->remove(array('_id' => $value['id']));
        }
        unset($story->photos);

      }

      // lets store the files now and grab id's for them
      foreach ($response['files'] as $key=>$value) {

        $bytes = base64_decode(substr($value['data'], strpos($value['data'], ',')+1));
        unset($value['data']);
        unset($value['src']);

        if (isset($value['caption']) && $value['caption'] == $data['caption']['tip']) {
          unset($value['caption']);
        }

        $tmp = $grid->storeBytes($bytes, $value);
        $photos[] = array_merge($value, array('id' => $tmp)); 

      }

      if ($response['bio'] != 1) {
        $story->title = $response['title'];
      }
      $story->slug = user::slugify($response['title']);
      $story->photos = $photos;
      $story->text = $response['text'];

      foreach ($response['tags'] as $key=>$value) {
        $response['tags'][$key] = strtolower($value);
      }
      $story->tags = $response['tags'];

      foreach (
        array('phone','url') as $opt) {
        if (isset($response[$opt])) {
          if ($response[$opt] != $data[$opt]['tip']) {
            $story->$opt = $response[$opt];
          } else {
            $story->$opt = '';
          }
        }
      }

      $story->location = $response['location'];

      $story->updated = time();

      if ($new || $isClaiming) {
        $story->created = time();
        $story->author = $_SESSION['user']['username'];
        $story->authorSlug = $_SESSION['user']['slug'];
      }

      if ($saveWithInvitedStatus) {

        $story->status = 'invited';

      } else {

        if (isset($response['submit']) && $response['submit'] == 1) {

          if (user::isAdmin()) {
            $story->status = 'accepted';
          } else {
            $story->status = 'pending';
          }

        } else {
          $story->status = 'draft';
        }

      }

      if ($response['bio'] == 1) {
        $story->status = 'bio';
      }

      $story->save();

      $user = user::i($_SESSION['user']);
      if ($response['username'] && $response['username'] != $user->username) {
        $user->updateUsername($response['username']);
      }

      if ($response['bio'] == 1) {
        echo json_encode(array('success' => true, 'slug' => '/story/bio/'.$story->authorSlug));
      } else {
        echo json_encode(array('success' => true, 'slug' => '/story/'.$story->slug, 'id' => (string)$story->_id));
      }

      return true;

    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;

  }

  public function delete($args) {

    $story = new story($args);

    $errors = array();
    $success = true;

    if ($story->slug === NULL) {
      $errors[] = 'We could not find the story';
      $success = false;
    }

    if ($story->slug !== NULL && !self::canEdit($story)) {
      $errors[] = 'You are not allowed to delete this story';
      $success = false;
    }

    // query to see if it's a microguide
    $microguide_count = microguide::find(array('storyIds' => new MongoId($args)))->count();
    if ($microguide_count && $story->status == 'accepted') {
      if ($microguide_count == 1) {
        $quantifier = "a microguide";
      } else {
        $quantifier = "$microguide_count microguides";
      }
      $errors[] = "This story is in $quantifier and cannot be deleted.";
      $success = false;
    }

    // remove story
    if ($success) {
      $story->remove();
    }

    echo json_encode(array('success' => $success, 'errors' => $errors));

    return true;
  }

  public function comment() {

    define('KDEBUG_JSON', true);

    $comment = new comment();
    $comment->created = time();
    $comment->updated = time();
    $comment->author = $_SESSION['user']['username'];
    $comment->authorSlug = $_SESSION['user']['slug'];
    $comment->text = $_REQUEST['comment'];
    $comment->story = new MongoId($_REQUEST['story']);
    $comment->save();

    $comments = comment::find(array('story' => new MongoId($_REQUEST['story'])))->sort(array('created' => -1));
    ob_start();
    require_once 'tpl/_comments.php';
    $html = ob_get_clean();

    echo json_encode(array('success' => true, 'html' => $html));

  }
  
  /* For sending emails to authors of stories, prompting them to send a facebook message to the owner of the 
  facebook page. */
  public function message($args) {

    $errors = array();

    if ($story->slug !== NULL && !self::canEdit($story)) {
      $errors[] = 'You need to be admin to send emails to authors.';
      $success = false;
      echo json_encode(array('success' => $success, 'errors' => $errors));
      return true;
    }
		
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
      define('KDEBUG_JSON', true);
      $success = false;
  		
      $storyId = $_REQUEST['story_id'];
      $story = new story($storyId);
      $storySlug = $story->slug;
      $fbId = $_REQUEST['fb_id'];
      $authorSlug =  $story->authorSlug;
  		
      /* Now we know the author of the story, so we search the db to get their email */
      $user = user::i(user::findOne(array('slug' => $authorSlug)));
      $email = $user->email;

      $userName = $user->username;
      if ($email != NULL) {

        //Send email to $email - eventual link will be facebook.com/messages/fbId
        $subject = "Your BYC story has been approved!";

        if (isset($_REQUEST['message'])) {

          $body = $_REQUEST['message'];

        } else {

          if (isset($_REQUEST['prompt']) && $_REQUEST['prompt'] == 'true') {
            $prompt = true;
            $link = G_URL . 'share/redirect?fb_id=' . $fbId . '&story_slug=' . $storySlug;
          } else {
            $prompt = false;
            $link = G_URL . 'story/' . $storySlug;
          }

          $comments = comment::find(array('story' => new MongoId($storyId)))->sort(array('created' => -1));

          ob_start();
          require 'tpl/_story_email_approved.php';
          $body = ob_get_clean();

        }

        if (!isset($_REQUEST['preview']) || $_REQUEST['preview'] == 'false') {
          $success = email::mail($email, $subject, $body);
        } else {
          $success = true;
        }

      }
  	
      if (!$success) {
        $errors[] = 'Mail not sent';
      }

      echo json_encode(array('success' => $success, 'errors' => $errors, 'body' => $body));
      return true;
  	
    } else {
      return false;
    }
  
  }
  
  /* sendRejectedMessage will send an email to the author of a story with the 
   * notification that their story has been declined */
  public function sendRejectedMessage() {
  	
    $errors = array();
  	
    $storyId = $_REQUEST['story_id'];
    $story = new story($storyId);

    if ($story->slug !== NULL && !self::canEdit($story)) {
      $errors[] = 'You need to be admin to send emails to authors.';
      $success = false;
      echo json_encode(array('success' => $success, 'errors' => $errors));
      return true;
    }
  
    $authorSlug =  $story->authorSlug;
  	
    /* Now we know the author of the story, so we search the db to get their email */
    $user = user::i(user::findOne(array('slug' => $authorSlug)));
    $email = $user->email;

    $body = $_REQUEST['message'];
  	
    if ($email != NULL) {

      $subject = "Story Declined";

      $success = email::mail($email, $subject, $body);

    } else {
      $success = false;
      $errors[] = "Email address not found";
    }

    if (!$success) {
      $errors[] = 'Mail not sent';
    }

    echo json_encode(array('success' => $success, 'errors' => $errors));
    return true;
  	
  }
  
  /* getRejectedMessage will allow our front-end js to get the appropriate contents
   * of the rejection message */
  public function getRejectedMessage() {

    $storyId = $_REQUEST['story_id'];
    $story = new story($storyId);

    $storySlug = $story->slug;
    $authorSlug =  $story->authorSlug;

    /* Now we know the author of the story, so we search the db to get their email */
    $user = user::i(user::findOne(array('slug' => $authorSlug)));
    $email = $user->email;

    $userName = $user->username;

    if ($email != NULL) {

      $link = G_URL . 'story/' . $storySlug;

      $comments = comment::find(array('story' => new MongoId($storyId)))->sort(array('created' => -1));

      ob_start();
      require 'tpl/_story_email_declined.php';
      $body = ob_get_clean();
      $sucess = true;

    } else {

      $sucess = false;
      $errors = array("Email not found");
    }

    echo json_encode(array('sucess' => $sucess, 'errors' => $errors, 'message' => $body));
    return true;

  }
  
  public function redirect() {
  	
  	$storySlug = $_REQUEST['story_slug'];
  	$fbId = $_REQUEST['fb_id'];
  	//$storyMessage = $_SERVER['HTTP_HOST'] . '/story/' . $storySlug . '?utm_source=facebook.com&utm_medium=referral&utm_campaign=fbalert';
  	$storyMessage = $_SERVER['HTTP_HOST'] . '/story/' . $storySlug . '?utm_campaign=fbAlert';
  	$fbMessageLink = 'https://facebook.com/messages/' . $fbId; 
  	$fbRedirect = true;
  	
  	$__more_style = array('ui/pepper-grinder/ui');
  	$__more_script = array('ui','zclip/zclip');
  	
  	require_once 'tpl/redirect.php';
   	return true;
  
  }
  
  public function fbProxy(){
  	
  	if (isset($_SESSION['user']['fb_uid'])) {

  		define('KDEBUG_JSON', true);

  		$fb = new fb($_SESSION['user']['fb_access_token']);
  		$access_token = $fb->session["oauth_token"];
  		
  		if ($_REQUEST['page']) {
  			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
  			$params = array();

  			$response = $fb->api(
  					'/' . $id,
                                        array(),
  					'get'
  			);

  			echo json_encode($response);
  		} else {
				$query = isset($_REQUEST['query']) ? $_REQUEST['query'] : false;
				$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : false;
				$center = isset($_REQUEST['center']) ? $_REQUEST['center'] : false;
				
				if (!$query){
					echo json_encode(array('success' => false, 'errors' => array("query not set")));
				} else if (!$type && !$center) {
					$params = array('q'=>$query);
				} else if (!$center) {
					$params = array('q'=>$query, 'type'=>$type);
				} else {
					$params = array('q'=>$query, 'type'=>$type, 'center'=>$center);
				}

	  		$response = $fb->api(
	  				'/search',
	  				$params,
	  				'GET'
	  		);
	  		
	  		echo json_encode($response);
  		}
  	} else {
  		echo json_encode(array('success' => false, 'errors' => array("can't access facebook account")));
  	}
  	 
  }
	
  public function preview() {

    $name = $_REQUEST['story'];
    $story = story::i(story::findOne(array('slug' => $name)));
    // Add image urls
    $story->photos = story_ctl::cache($story);
    $useSlider = true;
    require_once 'tpl/_story.php';
  	
  }

}
