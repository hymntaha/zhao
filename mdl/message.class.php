<?php

class message extends kcol {

  const TYPE_SHARE_INSTRUCTION = 'share_instruction';
  const TYPE_SHARE_FULLWIDTH_BANNER = 'share_fullwidth_banner';
  const TYPE_HOME_FULLWIDTH_BANNER = 'home_fullwidth_banner';

  const NAME_SHARE_INSTRUCTION_SAVED_LOCATION = 'share_instruction_saved_location';
  const NAME_SHARE_STEPS = 'share_steps';
  const NAME_MICROGUIDES_MAKE = 'microguides_make';

  const HIDE_AFTER = 5;

  const DISPLAY_REGION_LEFT_OF_GREETING = 1;
  const DISPLAY_REGION_SHARE_PAGE_TOP = 2;
  const DISPLAY_REGION_HOME_PAGE_TOP = 3;

  private static $runtimeDisplayQueue = array();

  public function __construct($id=null) {

    parent::__construct($id);

    // Default values for new messages.
    if ($id === null) {
      $this->senderId = null;
      $this->sentDate = time();
      $this->lastViewedDate = null;
      $this->viewCount = 0;
      $this->status = 'new';
    }
      
  }

  /**
   * Returns a cursor containing messages for the recipientId matching the supplied type,
   * ordered newest first.
   */
  public static function getByType($messageType, $recipientId) {
    $cursor = self::find(
      array('recipientId' => self::mongoId($recipientId), 
            'messageType' => $messageType,
            'status'      => array('$ne' => 'hidden')
      ))->sort(
        array('sentDate' => -1)
      );
    return $cursor;
  }

  public static function loadTypeToDisplayRegion($messageType, $recipientId, $displayRegion) {

    if (!isset(self::$runtimeDisplayQueue[$displayRegion])) {
      self::$runtimeDisplayQueue[$displayRegion] = array();
    }

    $addedCount = 0;

    $cursor = self::getByType($messageType, $recipientId);
    foreach ($cursor as $message) {
      $addedCount++;
      self::$runtimeDisplayQueue[$displayRegion][] = $message;
    }

    return $addedCount;

  }

  public function getDisplayRegionMessages($displayRegion) {

    if (isset(self::$runtimeDisplayQueue[$displayRegion])) {
      return self::$runtimeDisplayQueue[$displayRegion];
    } else {
      return array();
    }
  }

  /**
   * Returns true if the message has already been sent to the recipient,
   * false otherwise.
   */
  public static function alreadySent($messageName, $recipientId) {

    $count = self::find(array('recipientId' => self::mongoId($recipientId), 'messageName' => $messageName))->count();

    return $count > 0;

  }

  /**
   * Increment the view count for the specific message id.
   * recipientId is not required for locating this message in the db, but we
   * require it to discourage forged impressions.
   */
  public static function incrementViewCount($messageId, $recipientId) {

    // Require recipientId to discourage forged impressions.
    $message = self::db()->command(
      array(
            'findandmodify' => 'message',
            'query'         => array('recipientId' => self::mongoId($recipientId), '_id' => self::mongoId($messageId)),
            'update'        => array('$inc' => array('viewCount' => 1), '$set' => array('status' => 'viewed')),
            'new'           => true
      )
    );

    if ($message['value'] !== NULL && $message['value']['viewCount'] >= self::HIDE_AFTER) {
      $message = self::db()->command(
        array(
            'findandmodify' => 'message',
            'query'         => array('recipientId' => self::mongoId($recipientId), '_id' => self::mongoId($messageId)),
            'update'        => array('$set' => array('status' => 'hidden')),
            'new'           => true
        )
      );
    }

    return $message['value'];
  }

  /**
   * Marks the specific messageId as hidden, typically when a user clicks x to hide it.
   * recipientId is not required for locating this message in the db, but we
   * require it to discourage forged impressions.
   */
  public static function hide($messageId, $recipientId) {

    $message = self::db()->command(
      array(
            'findandmodify' => 'message',
            'query'         => array('recipientId' => self::mongoId($recipientId), '_id' => self::mongoId($messageId)),
            'update'        => array('$set' => array('status' => 'hidden')),
            'new'           => true
      )
    );

    return $message['value'];
  }

  /*** USECASE-SPECIFIC CODE ***/

  public static function sendShareInstruction($recipient) {

    if ($recipient instanceof user) {
      $recipientId = $recipient->_id;
    } else {
      $recipientId = $recipient;
    }

    $messageName = self::NAME_SHARE_INSTRUCTION_SAVED_LOCATION;

    if (!self::alreadySent($messageName, $recipientId)) {

      $i = new message();
      $i->messageName = $messageName;
      $i->messageType = self::TYPE_SHARE_INSTRUCTION;
      $i->recipientId = self::mongoId($recipientId);
      $i->text = 'Your stories will be saved here';
      $i->save();

      return true;

    } else {

      return false;

    }

  }

  public static function sendShareSteps($recipient) {

    static $messageHtml = '';

    if ($recipient instanceof user) {
      $recipientId = $recipient->_id;
    } else {
      $recipientId = $recipient;
    }

    $messageName = self::NAME_SHARE_STEPS;

    if (empty($messageHtml)) {
      ob_start();
      require('tpl/_share-steps.php');
      $messageHtml = ob_get_contents();
      ob_end_clean();
    }

    if (!self::alreadySent($messageName, $recipientId)) {

      $i = new message();
      $i->messageName = $messageName;
      $i->messageType = self::TYPE_SHARE_FULLWIDTH_BANNER;
      $i->recipientId = self::mongoId($recipientId);
      $i->text = $messageHtml;

      $i->save();

      return true;

    } else {

      return false;

    }


  }

  public static function sendMakeMicroguides($recipient) {

    static $messageHtml = '';

    if ($recipient instanceof user) {
      $recipientId = $recipient->_id;
    } else {
      $recipientId = $recipient;
    }

    $messageName = self::NAME_MICROGUIDES_MAKE;

    if (empty($messageHtml)) {
      ob_start();
      require('tpl/_message-make-your-microguide.php');
      $messageHtml = ob_get_contents();
      ob_end_clean();
    }

    if (!self::alreadySent($messageName, $recipientId)) {

      $i = new message();
      $i->messageName = $messageName;
      $i->messageType = self::TYPE_HOME_FULLWIDTH_BANNER;
      $i->recipientId = self::mongoId($recipientId);
      $i->text = $messageHtml;

      $i->save();

      return true;

    } else {

      return false;

    }

  }

}
