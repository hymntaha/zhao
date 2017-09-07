<?php 

class fb {

  public $session = false;
  public static $graphurl = 'https://graph.facebook.com';

  public function __construct($request=null, $appid=FB_APPID, $secret=FB_SECRET) {

    if ($request != null) {

      if (strpos($request, '.') !== false) {
        $this->session = self::parse($request, $secret);
      // not a signed request .. external app support
      } else {
        $this->session = array('oauth_token' => $request);
      }

    } else if ($request == null && isset($_REQUEST['signed_request'])) {

      $this->session = self::parse($_REQUEST['signed_request'], $secret);

    } else if ($request == false) {

      $this->session = array('oauth_token' => $appid.'|'.$secret);

    }

  }

  public function added() {

    if (!isset($this->session['user_id'])) {
      return false;
    }

    return true;

  }

  public function uid() {
    return $this->session['user_id'];
  }

  public function api($url, $params=array(), $type='get') {

    return json_decode(self::get(self::$graphurl.$url, array_merge(array('access_token' => $this->session['oauth_token']), $params), $type), true);

  }

  public static function get($url, $params, $type='get') {
	
    $handler = curl_init();

    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handler, CURLOPT_USERAGENT, 'facebook-php-2.0');

    if (strtolower($type) == 'post') {

      curl_setopt($handler, CURLOPT_POST, true);
      curl_setopt($handler, CURLOPT_POSTFIELDS, $params);

    } elseif (strtolower($type) == 'delete') {
      hpr('deleteing');

      /*
      curl_setopt($handler, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($handler, CURLOPT_HEADER, 0); 
      curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
      */
      curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($handler, CURLOPT_POSTFIELDS, $params);

    } else {

      $url .= '?'.http_build_query($params, null, '&');

    }
    
    curl_setopt($handler, CURLOPT_URL, $url);

    return curl_exec($handler);


  }

  public static function access($code, $redirect=G_URL) {

    $url = self::$graphurl.'/oauth/access_token';

    $params = array(
      'client_id' => FB_APPID,
      'redirect_uri' => $redirect,
      'client_secret' => FB_SECRET,
      'code' => $code
    );

    $result = self::get($url, $params);
    parse_str($result, $return);
    return $return;

  }

  static public function parse($signed_request, $secret) {

    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    $data = json_decode(self::decode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
      Throw new Exception('Expected HMAC-SHA256, Unknown algorithm: '.$data['algorithm']);
      return false;
    }

    if (self::decode($encoded_sig) !== hash_hmac('sha256', $payload, $secret, $raw = true)) {
      Throw new Exception('Bad signed JSON signature');
      return false;
    }

    return $data;
  }

  static private function decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
  }

}

