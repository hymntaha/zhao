<?php 

class message_ctl {

  const onePixelPng = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=';

  /**
   * Logs a message view and returns a 1 pixel png.
   * Expects to be called from the src attribute of an img tag.
   */
  public function logView($messageId) {

    $recipientId = $_SESSION['user']['_id'];
    message::incrementViewCount($messageId, $recipientId);
    header('Content-Type: image/png');
    echo base64_decode(self::onePixelPng);

  }

  /**
   * Explicitly hides a specific message for a specific user.
   * Redirects to request param returnUrl if provided.
   * Returns a short json structure otherwise.
   **/
  public function hide($messageId) {
    $recipientId = $_SESSION['user']['_id'];
    message::hide($messageId, $recipientId);
    if (isset($_REQUEST['returnUrl'])) {
      header('Location: ' . $_REQUEST['returnUrl']);
    } else {
      echo "{ok: true}";
    }
  }

  /**
   * Resets the share instruction; for testing.
   */
  public function resetShareInstruction() {

    $recipientId = $_SESSION['user']['_id'];
    $result = message::sendShareInstruction($recipientId);

    $message = message::db()->command(
      array(
            'findandmodify' => 'message',
            'query'         => array('recipientId' => $recipientId, 'messageName' => message::NAME_SHARE_INSTRUCTION_SAVED_LOCATION),
            'update'        => array('$set' => array('status' => 'new', 'viewCount' => 0)),
            'new'           => true
      )
    );
    header('Location: '.G_URL.'share');    
  }

}
