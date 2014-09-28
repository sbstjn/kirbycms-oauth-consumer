<?php if (!RAuth::valid()): ?>
  <div class="login">
    <p>Please login with your <a href="/auth/auth:twitter?redirect=<?php echo url::current() ?>" class="twitter">Twitter</a> or <a href="/auth/auth:github?redirect=<?php echo url::current() ?>" class="github">GitHub</a> account!</p>
  </div>
<?php else: ?>
  <div class="login">
    <p>Welcome <?php echo RAuth::username() ?>, thanks for logging in with <?php echo RAuth::provider() ?>.</p>
    <p><a href="/auth/logout?redirect=<?php echo url::current() ?>">Logout</a></p>
  </div>
<?php endif; ?>