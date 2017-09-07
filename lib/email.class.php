<?php

class email {

  /**
   * Shadows php's mail() function, adds dev filter and from.
   */
  public static function mail($to, $subject, $message, $additional_headers = NULL, $additional_parameters = NULL) {

    // Ensure from; without it we don't route through SES
    if (!$additional_parameters ||
        strpos($additional_parameters, '-f') === FALSE) {

      if (strlen($additional_parameters)) {
        $additional_parameters .= ' ';
      }
      $additional_parameters .= '-f' . EMAIL_FROM;

    }

    if (!$additional_parameters ||
        strpos($additional_parameters, '-F') === FALSE) {

      if (strlen($additional_parameters)) {
        $additional_parameters .= ' ';
      }
      $additional_parameters .= '-F"' . EMAIL_FROM_FULL_NAME . '"';

    }

    $send = false;

    switch (EMAIL_MODE) {
    case 'send':
      $send = true;
      break;
    case 'filter':
      $allowed_recipients = explode(',', EMAIL_FILTER);
      if (in_array($to, $allowed_recipients)) {
        $send = true;
      }
      break;
    case 'log':
    default:
      break;
    }

    $return_value = true;

    if ($send) {
      $return_value = mail($to, $subject, $message, $additional_headers, $additional_parameters);
    } else {
      error_log("Logged mail: To: $to, Subject: $subject, Message: $message");
      $return_value = true;
    }

    return $return_value;

  }
}