# Remote Auth Plugin for Kirby CMS

Remote Auth is Kirby CMS plugin for adding a "Login with Twitter" and "Login with GitHub" button to your website. Let your users authenticate them using Twitter or GitHub OAuth.

## Installation

- Copy `content/auth` to `content/auth`
- Copy `plugins/remote-auth` to `plugins/remote-auth`
- Copy `snippets/login.php` to `snippets/login.php`
- Copy `templates/*.php` to `templates/*.php`

## Configuration

For using Twitter and GitHub as OAuth providers, you have to register an application at both sites first and add the following configuration variables to your `site/config/config.php` file:

```php
c::set('rauth.twitter.key', 'TwitterClientID');
c::set('rauth.twitter.secret', 'TwitterClientSecret');
c::set('rauth.twitter.callback', 'YOURKIRBYURL/auth/auth:twitter');

c::set('rauth.github.key', 'GitHubClientID');
c::set('rauth.github.secret', 'GitHubClientSecret');
c::set('rauth.github.callback', 'YOURKIRBYURL/auth/auth:github');
```

## Dependencies

Remote Auth uses TwitterOAuth by [Abraham Williams](abraham@abrah.am).

## License

[MIT License](http://opensource.org/licenses/MIT)

## Copyright

2014 Sebastian Müller

[sbstjn.com](http://sbstjn.com)
[@sbstjn at twitter](https://twitter.com/sbstjn)