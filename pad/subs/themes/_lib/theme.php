<?php

  // The themes subsystem: the frame's colours swapped for another set.
  //
  //   $padTheme = 'paper';        _config/config.php - every page of the application
  //   {theme 'terminal'/}         the <style> of a theme, in this page
  //   {themes/}                   a switcher: the visitor's choice, kept in a cookie
  //
  // A theme is a file of subs/themes/types/ - its name, its scheme (light or dark) and its
  // colours, by the roles of the frame's palette - and the files there are the themes there
  // are (padKinds, lib/subsystem.php); no tag type: a theme is the parameter of {theme} and
  // the value of $padTheme. Its colours become the custom properties of the frame
  // (www/_common/pad.css) - the --frame-* ones of the side, the bar and the menu, and the
  // palette the pages build on, --bg, --text, --accent ... - set on :root:root:root, a
  // selector that outweighs the frame's own light and dark rules wherever it stands in the
  // page, with color-scheme, so every part that follows the scheme with light-dark() - the
  // charts, the widgets, the toys - follows the theme. A theme may set the page's font
  // (--sans), the hero background of the sections (--pad-section-hero) and rules of its
  // own - underlined links. The frame's light/dark switch is hidden under a theme: the theme
  // is the scheme. The sections and the effects read their colours from the same palette, so
  // a theme colours them too. The frame's files are not touched: the theme is a <style> on
  // top of them.
  //
  // Which theme a page gets - padThemeAdd: the visitor's choice from a {themes/} switcher,
  // kept in the padTheme cookie of the PAD applications, on a page built on the
  // frame or in an application that chose a theme itself, else $padTheme; FALSE in $padTheme
  // turns both off. exits/output.php puts its <style> into the page's <head> on the way out
  // (padThemeAdd) - after the page cache has stored the page, as the toolbar goes in, so a
  // cached page is served in the theme of whoever asks for it, and the ETag is made again
  // from what goes out - before </head>, else before <body>, else after the doctype. {theme
  // 'x'/} writes a theme where it stands, and a page that has one gets no other.
  //
  // Every theme's text and its backgrounds - the text, the soft and dim greys and the links
  // on the page and on a panel, a button's text on the accent, the side's texts on the side
  // and the menu, the status colours on theirs - contrast 4.5 to 1 at least, WCAG AA, and
  // contrast's 7 to 1, AAA (padThemeContrasts; the framework case themes/ holds them).
  //
  // Included on use: by {theme}, {themes} and exits/output.php when a theme is asked for.

  include_once PAD . 'subs/palettes/_lib/palette.php';

  function padThemeTypes () {

    return padKinds ( 'themes' );

  }

  function padThemeType ( $name ) {

    return padKind ( 'themes', $name );

  }

  function padThemeData ( $name ) {

    static $memo = [];

    if ( ! padThemeType ( $name ) )
      return NULL;

    return $memo [$name] ??= include PAD . "subs/themes/types/$name.php";

  }

  // The pairs of a theme's colours that are text on a background - [ text, background ] - and
  // what each ratio has to reach: 4.5 (WCAG AA for text), 7 for the contrast theme (AAA).

  function padThemePairs () {

    return [ [ 'text', 'bg' ], [ 'text', 'panel' ], [ 'soft', 'bg' ], [ 'soft', 'panel' ], [ 'dim', 'bg' ], [ 'dim', 'panel' ],
             [ 'accent', 'bg' ], [ 'accent', 'panel' ], [ 'accent-text', 'accent' ], [ 'accent', 'accent-soft' ],
             [ 'text', 'accent-soft' ], [ 'text', 'head' ], [ 'text', 'code' ],
             [ 'side-text', 'side' ], [ 'side-dim', 'side' ], [ 'side-strong', 'side' ], [ 'side-text', 'side-hover' ],
             [ 'side-text', 'menu' ], [ 'side-dim', 'menu' ],
             [ 'ok', 'ok-bg' ], [ 'warn', 'warn-bg' ], [ 'bad', 'bad-bg' ] ];

  }

  function padThemeNeeds ( $name ) {

    return ( $name == 'contrast' ) ? 7 : 4.5;

  }

  // The contrast of each pair of a theme, "text on bg" => the ratio rounded to two decimals.

  function padThemeContrasts ( $name ) {

    $colors = padThemeData ( $name ) ['colors'] ?? [];
    $ratios = [];

    foreach ( padThemePairs () as [ $text, $back ] )
      $ratios ["$text on $back"] = round ( padPaletteContrast ( $colors [$text], $colors [$back] ), 2 );

    return $ratios;

  }

  // The CSS of a theme: the frame's properties and the palette's, the scheme, the body's
  // colours - a page without the frame gets them too - its font, its sections' hero and its
  // own rules, and the frame's light/dark switch hidden. $inside: the theme on the elements
  // of the class pad-theme-<name> alone - the box of a {theme 'x'}...{/theme} pair, which
  // gets the background, the text colour and a little room - and not on the page.

  function padThemeCss ( $name, $inside = FALSE ) {

    $theme = padThemeData ( $name );

    if ( ! $theme )
      return '';

    $c    = $theme ['colors'];
    $dark = ( $theme ['scheme'] == 'dark' );

    $frame = [ 'bg' => 'bg', 'panel' => 'panel', 'text' => 'text', 'dim' => 'dim', 'line' => 'line', 'accent' => 'accent',
               'accent-text' => 'accent-text', 'side' => 'side', 'side-text' => 'side-text', 'side-dim' => 'side-dim',
               'side-hover' => 'side-hover', 'side-line' => 'side-line', 'side-strong' => 'side-strong', 'menu' => 'menu' ];

    $page  = [ 'bg', 'panel', 'text', 'soft', 'dim', 'line', 'accent', 'accent-text', 'accent-soft', 'head', 'code',
               'ok', 'ok-bg', 'warn', 'warn-bg', 'bad', 'bad-bg' ];

    $vars = '';

    foreach ( $frame as $property => $role )
      $vars .= "--frame-$property:{$c [$role]};";

    $vars .= '--frame-menu-shadow:' . ( $dark ? '0 18px 40px -12px rgba(0,0,0,.6)' : '0 16px 36px -12px rgba(20,30,50,.28)' ) . ';';

    foreach ( $page as $role )
      $vars .= "--$role:{$c [$role]};";

    $vars .= '--shadow:' . ( $dark ? '0 1px 2px rgba(0,0,0,.4),0 12px 32px -16px rgba(0,0,0,.7)' : '0 1px 2px rgba(20,22,31,.05),0 8px 24px -12px rgba(30,40,90,.18)' ) . ';';

    if ( isset ( $theme ['sans'] ) )
      $vars .= "--sans:{$theme ['sans']};";

    $root = $inside ? ".pad-theme-$name" : ':root:root:root';
    $body = 'background-color:var(--bg);color:var(--text)' . ( isset ( $theme ['sans'] ) ? ';font-family:var(--sans)' : '' );

    $css = $inside ? "$root{{$vars}color-scheme:{$theme ['scheme']};$body;padding:16px 20px;border-radius:10px}"
                   : "$root{{$vars}color-scheme:{$theme ['scheme']}}$root body{{$body}}$root .frame-theme{display:none}";

    if ( isset ( $theme ['hero'] ) )
      $css .= "$root .pad-section{--pad-section-hero:{$theme ['hero']}}";

    return $css . str_replace ( ':root:root:root', $root, $theme ['css'] ?? '' );

  }

  // The <style> of a theme, with the request's nonce; data-pad-theme names it.

  function padThemeStyle ( $name, $inside = FALSE ) {

    $css = padThemeCss ( $name, $inside );

    return ( $css === '' ) ? '' : '<style data-pad-theme="' . $name . '"' . padStyleNonce () . ">$css</style>";

  }

  // The visitor's choice is a cookie of its own, padTheme, on the directory the PAD
  // applications are served under: read and checked against the themes on every request - a name that is
  // no theme is no choice - and never kept in the session. It was: the hook started a session
  // at the end of every page of every application for a visitor who had the host's session
  // cookie - a stale one got a new session on every request. A choice made in one
  // application colours the others too - their pages built on the frame.

  const padThemeCookie = 'padTheme';

  function padThemeVisitor () {

    $chosen = $_COOKIE [padThemeCookie] ?? '';

    return ( is_string ( $chosen ) and padThemeType ( $chosen ) ) ? $chosen : '';

  }

  // The $padTheme setting as a theme, '' for none. A setting that names no theme is reported
  // under the strict check.

  function padThemeSetting () {

    global $padTheme, $padCheckSyntax;

    if ( ! is_string ( $padTheme ) or $padTheme === '' )
      return '';

    if ( padThemeType ( $padTheme ) )
      return $padTheme;

    if ( $padCheckSyntax and ! padSecondTime ( 'themeSetting' ) )
      padError ( "there is no theme named '" . padMakeSafe ( $padTheme, 20 ) . "' for \$padTheme - " . implode ( ', ', padThemeTypes () ) );

    return '';

  }

  // The theme the visitor has: the choice, else the $padTheme setting; '' for none, and
  // always '' with $padTheme FALSE.

  function padThemeChosen () {

    global $padTheme;

    if ( $padTheme === FALSE )
      return '';

    $chosen = padThemeVisitor ();

    return ( $chosen !== '' ) ? $chosen : padThemeSetting ();

  }

  // Whether a page is built on the frame's palette - it links the frame's stylesheet,
  // www/_common/pad.css, or it is the frame itself, whose <body> has the class frame: offline
  // (pad pack) the frame writes pad.css into the page, and a theme chosen there was kept but
  // never shown - so a theme, which sets that palette, colours it as it should. A
  // page with a design of its own - demo's, kanban's - went unreadable under a theme a
  // visitor chose elsewhere: the theme's text colour on its own white panels.

  function padThemeFramed ( $html ) {

    return (bool) preg_match ( '#<link\b[^>]*/_common/pad\.css|<body\b[^>]*\bclass="frame\b#i', (string) $html );

  }

  // exits/output.php: the theme's <style> into the page on its way out - before </head>
  // (padThemeInsert) - for an HTML page of the web answered 200; never an include request, a
  // {live} region's or a response fragment's answer, which are parts of a page that has its
  // theme already, and never a page with a {theme 'x'/} of its own.

  function padThemeAdd () {

    global $padOutput, $padStop, $padContentType, $padOutputType, $padInclude, $padEtag, $padTheme,
           $padCacheStop, $padCacheServerGzip, $padFragmentSent, $padFragmentOnly, $padFragmentTarget;

    if ( $padTheme === FALSE or $padOutputType != 'web' or $padInclude or $padStop != 200
         or ! str_starts_with ( (string) $padContentType, 'text/html' ) or padLive () !== '' )
      return;

    if ( $padFragmentSent or ( (string) $padFragmentOnly !== '' and ! $padFragmentTarget ) )
      return;

    $theme   = padThemeVisitor ();
    $setting = padThemeSetting ();

    if ( $theme === '' and $setting === '' )
      return;

    if ( $padCacheStop == 200 and $padCacheServerGzip ) {
      $padOutput          = padUnzip ( $padOutput );
      $padCacheServerGzip = FALSE;
    }

    // The visitor's choice colours a page built on the frame, or any page of an application
    // that chose a theme of its own; another page gets the application's theme, if any.

    if ( $theme === '' or ( $setting === '' and ! padThemeFramed ( $padOutput ) ) )
      $theme = $setting;

    // A page that wrote a theme of its own - {theme 'x'/}, its style on :root:root:root, where
    // the tag stands or in the head where tidy moved it - is in that theme: the hook's style,
    // put after it, outweighed it. A box of a {theme}...{/theme} pair is no such theme.

    if ( $theme === '' or preg_match ( '/<style data-pad-theme="[a-z0-9_-]+"[^>]*>\s*:root:root:root/', $padOutput ) )
      return;

    $padOutput = padThemeInsert ( $padOutput, padThemeStyle ( $theme ) );
    $padEtag   = padMD5 ( $padOutput );

  }

  // A style into a page: before </head>; in a page without one before its <body>, or after its
  // doctype, or at its start. It went in front of the doctype of a page without </head> -
  // valid HTML, the head is optional - which put the browser in quirks mode.

  function padThemeInsert ( $html, $style ) {

    if ( ( $at = stripos ( $html, '</head>' ) ) !== FALSE )
      return substr_replace ( $html, $style, $at, 0 );

    if ( preg_match ( '/<body\b/i', $html, $m, PREG_OFFSET_CAPTURE ) )
      return substr_replace ( $html, $style, $m [0] [1], 0 );

    if ( preg_match ( '/\A\s*<!DOCTYPE\b[^>]*>/i', $html, $m ) )
      return $m [0] . $style . substr ( $html, strlen ( $m [0] ) );

    return $style . $html;

  }

  // A post of a {themes/} switcher, taken by inits/themes.php before the application runs:
  // the theme chosen kept in the padTheme cookie - Default ('') takes it away; a name that is no theme changes nothing - for this request too. The
  // switcher writes no token of its own, so showing it starts no session, and keeping the
  // choice starts none either. A post another site sends - its Sec-Fetch-Site says
  // cross-site - changes nothing: it started a session, which replaced the visitor's own and
  // logged them out of every application on the host, and planted its theme. With $padCsrf
  // on the form carries the session's token as every form does, and a post without it never
  // gets here (lib/csrf.php).

  function padThemeSwitch () {

    if ( ! padFormCameBack ( 'padThemes' ) )
      return;

    if ( strtolower ( (string) ( $_SERVER ['HTTP_SEC_FETCH_SITE'] ?? '' ) ) === 'cross-site' )
      return;

    $theme = $_POST ['padTheme'] ?? NULL;

    if ( $theme === '' or ( is_string ( $theme ) and padThemeType ( $theme ) ) )
      padThemeKeep ( $theme );

  }

  // The choice into its cookie, for a year, and into $_COOKIE for this request - '' takes it
  // away. On the directory the PAD applications are served under - /pad/, $padRoot - so a
  // choice colours every application of the host, as the light/dark choice does: they share
  // the frame, and a page with a design of its own is not coloured by it (padThemeFramed).
  // Where the address does not begin with that directory, or it holds what a cookie's path
  // cannot, the browser's own choice stands. A script may read it: SameSite=Lax, not HttpOnly.

  function padThemeKeep ( $theme ) {

    if ( $theme === '' )
      unset ( $_COOKIE [padThemeCookie] );
    else
      $_COOKIE [padThemeCookie] = $theme;

    if ( headers_sent () )
      return;

    $flags = [ 'expires'  => ( $theme === '' ) ? 1 : time () + 60 * 60 * 24 * 366,
               'samesite' => 'Lax',
               'secure'   => padCookieSecure () ];

    $dir = (string) ( $GLOBALS ['padRoot'] ?? '/' );

    if ( str_starts_with ( (string) ( $_SERVER ['REQUEST_URI'] ?? '' ), $dir ) and strpbrk ( $dir, ",; \t\r\n\013\014" ) === FALSE )
      $flags ['path'] = $dir;

    setcookie ( padThemeCookie, $theme, $flags );

  }

  // After the switch: back to the address the form was posted to - the path and the query it
  // was asked by, on $padHost - as a GET (303), so the page answers in the new theme and a
  // reload posts nothing. An address outside the mount prefix, which the server would not
  // have sent here, goes to the application's home. Control characters are percent-encoded,
  // as padRedirect does.

  function padThemeBack () {

    global $padHost, $padRoot, $padApp;

    $asked = '/' . ltrim ( (string) ( $_SERVER ['REQUEST_URI'] ?? '' ), '/' );
    $root  = $padRoot ?? '/';

    $back = str_starts_with ( $asked, $root ) ? $padHost . substr ( $asked, strlen ( $root ) ) : "$padHost$padApp/";
    $back = preg_replace_callback ( '/[\x00-\x20\x7f]/', fn ( $match ) => rawurlencode ( $match [0] ), $back );

    padHeader ( 'Cache-Control: no-cache, no-store' );
    padHeader ( "Location: $back" );

    padExit ( 303 );

  }

?>
