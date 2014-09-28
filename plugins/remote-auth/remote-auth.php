<?php

/**
 * Basic interface for OAuth service consumer
 */
interface OAuthServiceInterface {
  public function handleRequest();
}

/**
 * RAuth - Remote Auth Kirby plugin
 */
class RAuth {
  private $type;

  /**
   * Enable usage of RAuth on current site
   */
  static function enable() {
    s::start();
  }

  /**
   * Mark current page as protected. Users have to authenticated to proceed
   */
  static function isProtected() {
    self::enable();
    
    if (!self::valid()) {
      go('/auth');
    }
  }

  /** 
   * Check if current RAuth session is authenticated
   */
  static function valid() {
    self::enable();
    
    return s::get('rauth.status') == 'verified';
  }
  
  /**
   * Get authenticated username
   */
  static function username() {
    self::enable();
    
    return s::get('rauth.username');
  }
  
  /**
   * Get authenticated avatar
   */
  static function avatar() {
    self::enable();
    
    return s::get('rauth.avatar');
  }
  
  /**
   * Get authenticated provider
   */
  static function provider() {
    self::enable();
    
    return s::get('rauth.provider');
  }
  
  /**
   * Remove RAuth session aka. Logout
   */
  static function logout() {
    self::enable();
    
    s::remove('rauth.status');
    s::remove('rauth.username');
    s::remove('rauth.provider');
    s::remove('rauth.avatar');
    s::remove('rauth.redirect');
    
    s::destroy();
  }

  /**
   * Initialize authentication request to proivder
   */
  public function __construct($site, $redirect) {
    $this->type = $site;
    $this->redirect = $redirect;
    
    $this->loadHandler();
    $this->storeRedirect();
    $this->handler->handleRequest();
  }
  
  /**
   * Save URL for redirect after login in session
   */
  private function storeRedirect() {
    if (urL::valid($this->redirect)) {
      s::set('rauth.redirect', $this->redirect);
    }
  }
  
  /**
   * Load selected authentication handler
   */
  private function loadHandler() {
    require_once(dirname(__FILE__) . '/services/' . $this->type . '.php');
    
    $handlerName = 'RemoteAuth' . ucfirst(strtolower($this->type));
    $this->handler = new $handlerName();
  }
  
  /**
   * Got OAuth token
   */
  public function processToken($token, $secret, $verifier) {
    return $this->handler->processToken($token, $secret, $verifier);
  }
  
  /**
   * Redirect user to provider's login page
   */
  public function redirectToLogin() {
    return $this->handler->redirectToLogin();
  }
}

?>