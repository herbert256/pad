<?php

  // A visitor's theme (subs/themes/_lib/theme.php) is a cookie of its own - padTheme, on the
  // directory the PAD applications are served under, SameSite=Lax - read and checked against
  // the themes on every request; no session is started for it, to keep it or to read it. It
  // colours the pages built on the frame (themes/framed links _common/pad.css) or whose
  // application chose a theme ($padTheme, themes/chosen), and no other (themes/plain); a post
  // of the switcher from another site changes nothing. The post is answered before the
  // application runs (inits/themes.php), a 303 back to the page it came from. It was kept in
  // the session: the theme hook started one at the end of every page of every application
  // for a visitor with the host's session cookie - a stale cookie got a new session on every
  // request - a cookieless cross-site post of the switcher started one and planted its theme,
  // and a theme chosen in one application coloured every other one, its own design or not.
  // A page with a {pad:theme 'sepia'/} of its own (themes/own) gets no other theme: the hook's
  // went in after the one tidy had moved into the head, and outweighed it. The frame with its
  // stylesheet written into the page, as offline (themes/inline, <body class="frame">), is
  // a framed page too: a theme chosen in a packed application was kept and never shown.

  $thUrl  = $padHost . 'regression/pages/?themes/';
  $thDir  = parse_url ( $padHost, PHP_URL_PATH );
  $thSess = session_name ();
  $thSaid = '';

  $thAsk = function ( $how, $page, $cookies = [], $post = NULL, $headers = [] ) use ( $thUrl, $thDir, $thSess, &$thSaid ) {

    $input = [ 'url' => $thUrl . $page, 'cookies' => $cookies, 'headers' => $headers ];

    if ( $post !== NULL ) {
      $input ['post']    = $post;
      $input ['options'] = [ 'FOLLOWLOCATION' => FALSE ];
    }

    $got    = padCurl ( $input );
    $theme  = preg_match_all ( '/<style data-pad-theme="([a-z-]+)"/', (string) ( $got ['data'] ?? '' ), $m ) ? implode ( ' then ', $m [1] ) : 'none';
    $cookie = $got ['cookieAttributes'] ['padTheme'] ?? NULL;
    $value  = $got ['cookies'] ['padTheme'] ?? NULL;

    if ( $cookie === NULL )
      $kept = 'no theme cookie';
    elseif ( $value === 'deleted' or $value === '' or ( $cookie ['max-age'] ?? 1 ) === '0' )
      $kept = 'the theme cookie taken away';
    else
      $kept = "theme cookie $value on " . ( ( $cookie ['path'] ?? '' ) === $thDir ? "the applications' directory" : ( $cookie ['path'] ?? 'no path' ) )
            . ', SameSite ' . ( $cookie ['samesite'] ?? 'none' );

    $back    = isset ( $got ['headers'] ['Location'] ) ? ' to ' . str_replace ( $thUrl, '', $got ['headers'] ['Location'] ) : '';

    $thSaid .= "$how: " . substr ( (string) $got ['result'], 0, 3 ) . "$back, theme $theme, "
             . ( isset ( $got ['cookies'] [$thSess] ) ? 'a session started' : 'no session' ) . ", $kept\n";

  };

  $thAsk ( 'a stale session cookie',       'plain',  [ $thSess => '0123456789abcdef0123456789abcdef' ] );
  $thAsk ( 'terminal chosen',              'framed', [], [ 'padForm' => 'padThemes', 'padTheme' => 'terminal' ] );
  $thAsk ( 'a framed page',                'framed', [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'a page without the frame',     'plain',  [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'the frame without its link',   'inline', [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'an application with a theme',  'chosen', [ 'padTheme' => 'terminal' ] );
  $thAsk ( 'the same without the choice',  'chosen' );
  $thAsk ( 'a name that is no theme',      'framed', [ 'padTheme' => 'nothing' ] );
  $thAsk ( 'chosen from another site',     'framed', [], [ 'padForm' => 'padThemes', 'padTheme' => 'terminal' ], [ 'Sec-Fetch-Site' => 'cross-site' ] );
  $thAsk ( 'Default chosen',               'framed', [ 'padTheme' => 'terminal' ], [ 'padForm' => 'padThemes', 'padTheme' => '' ] );
  $thAsk ( 'a page in a theme of its own', 'own',    [ 'padTheme' => 'terminal' ] );

?>
