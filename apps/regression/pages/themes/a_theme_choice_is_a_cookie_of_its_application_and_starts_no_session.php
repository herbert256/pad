<?php

  // A visitor's theme (subs/themes/_lib/theme.php) is a cookie of its own - padTheme, on the
  // directory of the application it was chosen in, SameSite=Lax - read and checked against
  // the themes on every request; no session is started for it, to keep it or to read it. It
  // colours the pages of its application that are built on the frame (themes/framed links
  // _common/pad.css) or whose application chose a theme ($padTheme, themes/chosen), and no
  // other (themes/plain); a post of the switcher from another site changes nothing. It was
  // kept in the session: the theme hook started one at the end of every page of every
  // application for a visitor with the host's session cookie - a stale cookie got a new
  // session on every request - a cookieless cross-site post of the switcher started one and
  // planted its theme, and a theme chosen in one application coloured every other one, its
  // own design or not.

  $thUrl  = $padHost . 'regression/pages/?themes/';
  $thDir  = parse_url ( $padHost, PHP_URL_PATH ) . 'regression/pages/';
  $thSess = session_name ();
  $thSaid = '';

  $thAsk = function ( $how, $page, $cookies = [], $post = NULL, $headers = [] ) use ( $thUrl, $thDir, $thSess, &$thSaid ) {

    $input = [ 'url' => $thUrl . $page, 'cookies' => $cookies, 'headers' => $headers ];

    if ( $post !== NULL )
      $input ['post'] = $post;

    $got    = padCurl ( $input );
    $theme  = preg_match ( '/<style data-pad-theme="([a-z-]+)"/', (string) ( $got ['data'] ?? '' ), $m ) ? $m [1] : 'none';
    $cookie = $got ['cookieAttributes'] ['padTheme'] ?? NULL;
    $value  = $got ['cookies'] ['padTheme'] ?? NULL;

    if ( $cookie === NULL )
      $kept = 'no theme cookie';
    elseif ( $value === 'deleted' or $value === '' or ( $cookie ['max-age'] ?? 1 ) === '0' )
      $kept = 'the theme cookie taken away';
    else
      $kept = "theme cookie $value on " . ( ( $cookie ['path'] ?? '' ) === $thDir ? "the application's directory" : ( $cookie ['path'] ?? 'no path' ) )
            . ', SameSite ' . ( $cookie ['samesite'] ?? 'none' );

    $thSaid .= "$how: " . substr ( (string) $got ['result'], 0, 3 ) . ", theme $theme, "
             . ( isset ( $got ['cookies'] [$thSess] ) ? 'a session started' : 'no session' ) . ", $kept\n";

  };

  $thAsk ( 'a stale session cookie',       'plain',  [ $thSess => '0123456789abcdef0123456789abcdef' ] );
  $thAsk ( 'terminal chosen',              'framed', [], [ 'padForm' => 'padThemes', 'padTheme' => 'terminal' ] );
  $thAsk ( 'a framed page',                'framed', [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'a page without the frame',     'plain',  [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'an application with a theme',  'chosen', [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'the same without the choice',  'chosen' );
  $thAsk ( 'a name that is no theme',      'framed', [ 'padTheme' => 'nothing' ] );
  $thAsk ( 'chosen from another site',     'framed', [], [ 'padForm' => 'padThemes', 'padTheme' => 'terminal' ], [ 'Sec-Fetch-Site' => 'cross-site' ] );
  $thAsk ( 'Default chosen',               'framed', [ 'padTheme' => 'terminal' ], [ 'padForm' => 'padThemes', 'padTheme' => '' ] );

?>
