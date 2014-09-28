<?php 

/**
 * Consumer for GitHub OAuth workflow
 */
class RemoteAuthGithub implements OAuthServiceInterface {
  private $useragent = 'RemoteAuthGithub for Kirby';

  /**
   * Redirect user to GitHub login page
   */
  private function redirectToLogin() {
    go("https://github.com/login/oauth/authorize?client_id=" . c::get('rauth.github.key') . "&redirect_uri=" . c::get('rauth.github.callback') . "&scope=user");  
  }
  
  /**
   * Set default cURL settings
   */
  private function basicCurlSettings($ci) {
    curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, 30);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HEADER, FALSE);
  }
  
  /**
   * Send POST request to URL with data
   */
  private function __curlPost($url, $data) {
    $ci = curl_init();
    $this->basicCurlSettings($ci);
    curl_setopt($ci, CURLOPT_POST, TRUE);
    curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    curl_close ($ci);
    
    return $response;
  }
  
  /**
   * Send GET request to URL
   */
  private function __curlGet($url) {
    $ci = curl_init();
    $this->basicCurlSettings($ci);
    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    curl_close ($ci);
    
    return $response;
  }

  /**
   * Handle OAuth redirect from GitHub with code parameter
   */
  private function handleCode($code) {    
    $response = $this->__curlPost("https://github.com/login/oauth/access_token", array(
      'client_id' => c::get('rauth.github.key') ,
      'redirect_uri' => c::get('rauth.github.callback') ,
      'client_secret' => c::get('rauth.github.secret'),
      'code' => $code));
    parse_str($response, $data);
    
    if (!isset($data['error']) && isset($data['access_token'])) {
      $response = $this->__curlGet("https://api.github.com/user?access_token=" . $data['access_token']);
      $user_data  = json_decode($response , true);
      
      s::set('rauth.status', 'verified');
      s::set('rauth.username', $user_data['login']);
      s::set('rauth.provider', 'github');
      s::set('rauth.avatar', $user_data['avatar_url']);
      
      if (s::get('rauth.redirect')) {
        $url = s::get('rauth.redirect');
        
        s::remove('rauth.redirect');
        go($url);
      } else {
        go('/auth');
      }
    } else {
      go('/auth/failed');
    }    
  }

  /**
   * Handle OAuth callback
   */
  public function handleRequest() {
    if (!r::get('code')) {
      $this->redirectToLogin();
    } else {
      $this->handleCode(r::get('code'));
    }
  }

}
