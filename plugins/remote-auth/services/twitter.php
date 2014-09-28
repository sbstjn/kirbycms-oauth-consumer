<?php 

// Load external Twitter and OAuth helpers
require_once(dirname(__FILE__) . '/../vendor/OAuthConsumer.class.php');
require_once(dirname(__FILE__) . '/../vendor/TwitterOAuth.class.php');

class RemoteAuthTwitter implements OAuthServiceInterface {

  /**
   * Handle Twitter OAuth request
   */
  public function handleRequest() {
    if (r::get('oauth_token') && s::get('rauth.twitter.token') === r::get('oauth_token')) {
      if ($this->processToken(s::get('rauth.twitter.token'), s::get('rauth.twitter.token_secret'), r::get('oauth_verifier'))) {          
        
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
    } else {
      $this->redirectToLogin();
    }
  }

  /**
   * Process Twitter OAuth token 
   */
  public function processToken($token, $secret, $verifier) {
    $conn = new TwitterOAuth(c::get('rauth.twitter.key'), c::get('rauth.twitter.secret'), $token , $secret);
    $access_token = $conn->getAccessToken($verifier);
    if ($conn->http_code === 200) {
      s::set('rauth.status', 'verified');
      s::set('rauth.username', $access_token['screen_name']);
      s::set('rauth.provider', 'twitter');

      $data = $conn->oAuthRequest("https://api.twitter.com/1.1/users/show.json", 'GET', array('screen_name' => $access_token['screen_name']));
      $data = json_decode($data, true);
      
      s::set('rauth.avatar', str_replace('_normal', '', $data['profile_image_url_https']));

      s::remove('rauth.twitter.token');
      s::remove('rauth.twitter.token_secret');
      
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Redirect user to Twitter login page
   */
  public function redirectToLogin() {
	  $conn = new TwitterOAuth(c::get('rauth.twitter.key'), c::get('rauth.twitter.secret'));
    $token = $conn->getRequestToken(c::get('rauth.twitter.callback'));

    s::set('rauth.twitter.token',         $token['oauth_token']);
    s::set('rauth.twitter.token_secret',  $token['oauth_token_secret']);

    if ($conn->http_code == '200') {
      go($conn->getAuthorizeURL($token['oauth_token']));
    } else {
      die("error connecting to twitter! try again later!");
    }
  }
}
