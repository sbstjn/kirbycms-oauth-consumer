<?php 

// Remove user session
RAuth::logout(); 

// Check if user has to be redirected to previous page
if (r::get('redirect')) {
  go(r::get('redirect'));
} else {
  go('/auth');
}