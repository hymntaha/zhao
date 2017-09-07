<?php 

class admin_ctl {

  public function __construct() {

    if (!user::isAdmin()) {
      header('Location: '.G_URL);
      return false;
    }

  }


  public function users() {

    $users = array();
    foreach (user::find()->sort(array('created' => -1)) as $id=>$user) {
      $users[$id] = user::i($user);
    }

    $__more_style = array('admin');
    require_once 'tpl/admin_users.php';

  }

  public function dynamicConfig() {
    $name = 'pitchdeckEmbedId';
    $config = dynamicConfig::i(dynamicConfig::findOne(array('name' => $name)));

    $__more_style = array('admin');

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      $success = true;
      $errors = array();
      $id = $_POST['id'];
      if (!preg_match('!^[0-9a-f]+$!i', $id)) {
        $success = false;
        $errors[] = "Something doesn't look right with that pitchdeck id.";
      } else {
        $config->value = $id;
        $config->updatedAt = time();
        $config->updatedBy = $_SESSION['user']['username'];
        $config->save();
      }

      echo json_encode(array(
        'success' => $success,
        'errors' => implode(' , ',$errors)
      ));
      break;
    case 'GET':
    default:
      $__body_class = "left-aligned";
      require_once('tpl/dynamic-config-edit.php');
    }
  }

  public function microguides($arg) {

    switch ($arg) {
    case false:
    case '':
      $cursor = microguide::find()->sort(array("title"=>1));
      $microguides = array();
      foreach ($cursor as $m) {
        $microguides[] = microguide::i($m);
      }
      $__more_style = array('admin');
      require_once('tpl/admin-microguides.php');
      break;
    case 'edit':
      $slug = basename($_SERVER['SCRIPT_URI']);
      if ($slug != 'edit') {
        $microguide = microguide::i(microguide::findOne(array('slug'=>$slug)));
      } else {
        $microguide = new microguide();
      }
      $microguide->addStoryStatus(array('invited', 'pending', 'draft'));
      $__more_script = array('admin', 'ui');
      $__more_style = array('admin', 'ui/pepper-grinder/ui');
      require_once('tpl/admin-microguide-edit.php');
      break;
    }

  }
  
  public function exportEbook($slug) {
    
    $microguide = microguide::i(microguide::findOne(array('slug'=>$slug)));
    
    if (filter_var($_POST['export'], FILTER_VALIDATE_BOOLEAN, array("flags" => FILTER_NULL_ON_FAILURE)) == true) {
      
      $cover_image = array();
      $front_ad_image = array();
      $rear_ad_image = array();
      
      // Some default values for size and compression
      $compression = 80;
      
      error_reporting(E_ERROR);
      ini_set('display_errors', 'On');

      foreach ($_FILES as $file) {
        if ($file['error'] == 1) {
          echo "There's an issue uploading the file " . $file['name'] . " because it is too large. Try resizing it smaller first.";
          return false;
        } else if ($file['error'] != 0 && $file['error'] != 4) { // If there's an error that isn't due to not being specified
          echo "There's an issue uploading the file " . $file['name'] . " . The error code is " . $file['error'];
          return false;
        }
      }
  
      if ($_FILES['cover']['error'] == 0) {
        $cover_image['file'] = $_FILES['cover']['tmp_name'];
        $cover_image['type'] = $_FILES['cover']['type'];
      } else {
        echo "Please add a cover image.";
        return false;
      }
      

      if ($_FILES['front_ad']['error'] == 0) {
        $front_ad_image['file'] = $_FILES['front_ad']['tmp_name'];
        $front_ad_image['type'] = $_FILES['front_ad']['type'];
        $front_ad_image['link-type'] = $_POST['front-ad-link-type'];
        if ($front_ad_image['link-type'] == 'mailto') {
          $front_ad_image['email'] = $_POST['front-ad-email-address'];
          $front_ad_image['subject'] = $_POST['front-ad-subject'];
        } else {
          $front_ad_image['link'] = $_POST['front-ad-http'];
        }
        
      } else {
        $front_ad_image = null;
      }

      if ($_FILES['rear_ad']['error'] == 0) {
        $rear_ad_image['file'] = $_FILES['rear_ad']['tmp_name'];
        $rear_ad_image['type'] = $_FILES['rear_ad']['type'];
        $rear_ad_image['link-type'] = $_POST['rear-ad-link-type'];
        if ($rear_ad_image['link-type'] == 'mailto') {
          $rear_ad_image['email'] = $_POST['rear-ad-email-address'];
          $rear_ad_image['subject'] = $_POST['rear-ad-subject'];
        } else {
          $rear_ad_image['link'] = $_POST['rear-ad-http'];
        }
      } else {
        $rear_ad_image = null;
      }
      
      if (isset($_POST['byline_stories']) && $_POST['byline_stories'] == 'on') {
        $byline_every_page = true;
      } else {
        $byline_every_page = false;
      }
      
      
      if (isset($_POST['bio']) && $_POST['bio'] == 'on') {
        $include_bio = true;
      } else {
        $include_bio = false;
      }
      
      if (isset($_POST['compression'])) {
        $compression = $_POST['compression'];
      }

      ebook::exportEbook($slug, $include_bio, $byline_every_page, $cover_image, $front_ad_image, $rear_ad_image,$compression);
      
    } else {
      
      $__more_script = array('admin', 'ui');
      $__more_style = array('admin', 'ui/pepper-grinder/ui');
      
      require_once('tpl/admin-export-ebook-form.php');
    }
    return true;
  }

  public function merryGoRound() {

    $featureCount = featuredMicroguide::find(array('status' => 'active'))->count();

    $featureDates = array();
    $microguideIds = array();
    $microguides = array();

    for ($i = 0; $i < ceil($featureCount/5); $i++) {
      $time = strtotime("+$i days");
      $features = featuredMicroguide::getFeatures($time);
      $featureDates[date('Y-m-d', $time)] = $features;
      for ($j = 0, $max = count($features); $j < $max; $j++) {
        $microguideIds[] = $features[$j]['microguideId'];
      }
    }

    $cursor = microguide::find(array('_id' => array('$in' => $microguideIds)));
    foreach ($cursor as $id => $microguide) {
      $microguides[$id] = $microguide;
    }

    $__more_script = array('admin');
    $__more_style = array('admin');
    require_once('tpl/admin-merry-go-round.php');

    return true;
  }

}
