<?php 

if (!param('auth')) {
  // No authentication provider is selected
  go('/auth/select');
} else if (!RAuth::valid()) {
  // Handling OAuth workflow
  new RAuth(param('auth'), r::get('redirect'));
}