<?php 

class bravo_ctl {

  public function bravo($data) {

    define('KDEBUG_JSON', true);

    $errors = array();

    if (!isset($_SESSION['user'])) {
      $errors[] = 'not logged in';
      echo json_encode(array('success' => false, 'errors' => $errors));
      return true;
    }

    if (!isset($_REQUEST['data']) || !($data = json_decode($_REQUEST['data'], true))) {
      $errors[] = 'invalid or no data';
      echo json_encode(array('success' => false, 'errors' => $errors));
      return true;
    }

    $params = array(
      'story_id' => new MongoId($data['id']),
      'user_id' => $_SESSION['user']['_id']
    );

    // bravo'ing
    if ($data['bit'] === true) {

      $bravo = bravo::findOne($params);
      if ($bravo != null) {

        $bravo = bravo::i($bravo);

        // check for rate limit violations

        if ($this->exceedsLimits($bravo)) {
          $errors[] = "you're bravoing too fast, take a break :-)";
          echo json_encode(array('success' => false, 'errors' => $errors));
          return true;
        }

        // increase the weight

        $bravoingAgain = true;
        $bravo->weight = $bravo->weight+1;
        $bravo->save();

      } else {

        // for new bravos check for facebook, and attempt timeline
        if (isset($_SESSION['user']['fb_uid'])) {
          $fb = new fb($_SESSION['user']['fb_access_token']);
          $response = $fb->api(
            '/me/' . FB_NAMESPACE . ':bravo',
            array('story' => G_URL.'story/'.$data['slug']),
            'post'
          );
          if (isset($response['id']) && is_numeric($response['id'])) {
          $fb_og_id = $response['id'];
          }
        }


        $bravo = new bravo();
        $bravo->created = time();
        $bravo->user_id = $params['user_id'];
        $bravo->story_id = $params['story_id'];
        $bravo->title = $data['title'];
        $bravo->slug = $data['slug'];

        if (isset($fb_og_id)) {
          $bravo->fb_og_id = $fb_og_id;
        }

        $bravo->save();

      }

      $this->setBravoCount($params['story_id']);

      echo json_encode(array('success' => true, 'errors' => $errors));
      return true;

    }

    if ($data['bit'] === false) {

      // check for duplicates
      if (!($bravo = bravo::findOne($params))) {
        $errors[] = 'no bravo found';
        echo json_encode(array('success' => false, 'errors' => $errors));
        return true;
      }

      $bravo = bravo::i($bravo);

      if (isset($bravo->fb_og_id)) {

        $fb = new fb($_SESSION['user']['fb_access_token']);
        $response = $fb->api(
          '/'.$bravo->fb_og_id,
          array('story' => G_URL.'story/'.$data['slug']),
          'delete'
        );

      }

      if (!$bravo->remove()) {
        $errors[] = 'error removing bravo';
        echo json_encode(array('success' => false, 'errors' => $errors));
        return true;
      }

      $this->setBravoCount($params['story_id']);
      echo json_encode(array('success' => true));
      return true;

    }


  }

  private function setBravoCount($story_id) {

    // get bravo count for story

    $keys = array("story_id" => 1);
    $initial = array("bravoCount" => 0);
    $reduce = "function (obj, prev) { prev.bravoCount += obj.weight; }";
    $condition = array("story_id" => new MongoId($story_id));
    $g = bravo::col()->group($keys, $initial, $reduce, $condition);
    if ($g['ok'] && count($g['retval'])) {
      $bravoCount = (int) $g['retval'][0]['bravoCount'];
    }

    // update story 

    $story = story::i(story::findOne(array('_id' => new MongoId($story_id))));
    if ($story->slug !== NULL) {
      $story->bravoCount = $bravoCount;
      $story->save();
    } else {
      error_log("No story found for bravo with request ".$_GET['data']);
    }
  }

  public function exceedsLimits($bravo) {

    $max_hourly = 10;
    $min_seconds = .5;

    $rate = array();
    if ($bravo->rate !== NULL) {
      // copy over just two keys to keep the rate record from growing in the db
      foreach (array(date('Y-m-d H'),'last') as $key) {
        if (isset($bravo->rate[$key])) {
          $rate[$key] = $bravo->rate[$key];
        }
      }
    }

    if (isset($rate['last']) && (microtime(true) - $rate['last'] < $min_seconds)) {
      return true;
    }
    if (isset($rate[date('Y-m-d H')]) && $rate[date('Y-m-d H')] >= $max_hourly) {
      return true;
    }
    if (isset($rate[date('Y-m-d H')])) {
      $rate[date('Y-m-d H')] = $rate[date('Y-m-d H')] + 1;
    } else {
      $rate[date('Y-m-d H')] = 1;
    }

    $rate['last'] = microtime(true);
    $bravo->rate = $rate;

    return false;
  }
}
