<?php

class mailsync {

  /***
   * Returns a cursor of user objects needing sync, sorted by ascending updated values.
   * Client code should keep track of the largest value of the updated field and pass it
   * back in $since for pagination.  $excludeUsers prevents syncing users twice.
   */
  public static function findUsersToSync($since, $excludeUsers=array()) {

    $search = array(

      '$or' => array(

         array(
           'mailSync' => array(
               '$exists' => false
           )
         ),

         array(
           'updated' => array(
               '$gte' => $since
           )
         )
      ),

      'slug' => array(
        '$nin' => $excludeUsers
      ),

      'public' => 1,

    );

    $sort = array(
      'updated' => 1
    );

    return user::find($search)->sort($sort);

  }

  /**
   * Syncs the passed in user objects to MailChimp.
   */
  public static function syncToMailchimp($syncBatch) {

    $api = new MCAPI(MAILCHIMP_APIKEY);
    $listId = MAILCHIMP_LISTID;

    $batch = array();

    foreach ($syncBatch as $user) {

      $email = trim($user->email);
      if (!strlen($email))  continue;

      $chimpUser = array('EMAIL' => $email);

      $bioData = story::findOne(array('status' => 'bio', 'authorSlug' => $user->slug));

      if ($bioData) {

        $bio = story::i($bioData);
        $location_parts = explode(',', $bio->location['formatted']);
        $country = trim($location_parts[count($location_parts)-1]);
        if ($country) {
          $chimpUser['COUNTRY'] = $country;
        }

      }

      $batch[] = $chimpUser;

    }

    $optin = MAILCHIMP_OPTIN; //yes, send optin emails
    $up_exist = true; // yes, update currently subscribed users
    $replace_int = false; // no, add interest, don't replace

    $vals = $api->listBatchSubscribe($listId,$batch,$optin, $up_exist, $replace_int);

    if ($api->errorCode){
      syslog(LOG_ERR, __CLASS__ . '|' . "Batch Subscribe failed!");
      syslog(LOG_ERR, __CLASS__ . '|' . "code:".$api->errorCode);
      syslog(LOG_ERR, __CLASS__ . '|' . "msg :".$api->errorMessage);
    } else {
      syslog(LOG_INFO, __CLASS__ . '|' . "added:   ".$vals['add_count']);
      syslog(LOG_INFO, __CLASS__ . '|' . "updated: ".$vals['update_count']);
      syslog(LOG_INFO, __CLASS__ . '|' . "errors:  ".$vals['error_count']);
      foreach($vals['errors'] as $val){
        syslog(LOG_ERR, __CLASS__ . '|' . $val['email_address']. " failed");
        syslog(LOG_ERR, __CLASS__ . '|' . "code:".$val['code']);
        syslog(LOG_ERR, __CLASS__ . '|' . "msg :".$val['message']);
      }
    }

    // Update sync times in user records

    $syncTime = time();
    foreach ($syncBatch as $user) {

      user::db()->command(
        array(
          'findandmodify' => 'user',
          'query'         => array('slug' => $user->slug),
          'update'        => array('$set' => array('mailSync' => $syncTime )),
          'new'           => true
        )
      );

    }

  }

}
