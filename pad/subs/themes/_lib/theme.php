<?php

  // The themes subsystem: the frame's colours swapped for another set.
  //
  //   $padTheme = 'paper';        _config/config.php - every page of the application
  //   {theme 'terminal'/}         the <style> of a theme, in this page
  //   {themes/}                   a switcher: the visitor's choice, kept in the session
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
  // Which theme a page gets - padThemeChosen: the visitor's choice from a {themes/}
  // switcher, kept in the session under padTheme, else $padTheme; FALSE in $padTheme turns
  // both off. exits/output.php puts its <style> into the page's <head> on the way out
  // (padThemeAdd) - after the page cache has stored the page, as the toolbar goes in, so a
  // cached page is served in the theme of whoever asks for it, and the ETag is made again
  // from what goes out. {theme 'x'/} writes a theme where it stands; it comes after the
  // <head>'s in the page, so it wins.
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

  // The theme this request's pages get: the visitor's choice kept in the session - read only
  // when there is a session, none is started for it - else the $padTheme setting; '' for
  // none, and always '' with $padTheme FALSE. A setting that names no theme is reported under
  // the strict check.

  function padThemeChosen () {

    global $padTheme, $padCheckSyntax;

    if ( $padTheme === FALSE )
      return '';

    $chosen = padSession ( 'padTheme', '' );

    if ( is_string ( $chosen ) and padThemeType ( $chosen ) )
      return $chosen;

    if ( ! is_string ( $padTheme ) or $padTheme === '' )
      return '';

    if ( padThemeType ( $padTheme ) )
      return $padTheme;

    if ( $padCheckSyntax and ! padSecondTime ( 'themeSetting' ) )
      padError ( "there is no theme named '" . padMakeSafe ( $padTheme, 20 ) . "' for \$padTheme - " . implode ( ', ', padThemeTypes () ) );

    return '';

  }

  // exits/output.php: the theme's <style> into the page on its way out - before </head>, or
  // at the start of a page without one - for an HTML page of the web answered 200; never an
  // include request, a {live} region's or a response fragment's answer, which are parts of a
  // page that has its theme already.

  function padThemeAdd () {

    global $padOutput, $padStop, $padContentType, $padOutputType, $padInclude, $padEtag,
           $padCacheStop, $padCacheServerGzip, $padFragmentSent, $padFragmentOnly, $padFragmentTarget;

    if ( $padOutputType != 'web' or $padInclude or $padStop != 200
         or ! str_starts_with ( (string) $padContentType, 'text/html' ) or padLive () !== '' )
      return;

    if ( $padFragmentSent or ( (string) $padFragmentOnly !== '' and ! $padFragmentTarget ) )
      return;

    $theme = padThemeChosen ();

    if ( $theme === '' )
      return;

    if ( $padCacheStop == 200 and $padCacheServerGzip ) {
      $padOutput          = padUnzip ( $padOutput );
      $padCacheServerGzip = FALSE;
    }

    $style = padThemeStyle ( $theme );
    $at    = stripos ( $padOutput, '</head>' );

    $padOutput = ( $at === FALSE ) ? $style . $padOutput : substr_replace ( $padOutput, $style, $at, 0 );
    $padEtag   = padMD5 ( $padOutput );

  }

  // A post of a {themes/} switcher: the theme chosen kept in the session under padTheme -
  // Default ('') takes it out again; a name that is no theme changes nothing. The switcher
  // writes no token of its own: a choice of colours is no change a forged post could do harm
  // with, and a token would start a session - and send its cookie - for every visitor of
  // every page that shows the switcher, where now the session starts when a visitor chooses.
  // With $padCsrf on the form carries the session's token as every form does, and a post
  // without it never gets here (lib/csrf.php).

  function padThemeSwitch () {

    if ( ! padFormCameBack ( 'padThemes' ) or padSecondTime ( 'themeSwitch' ) )
      return;

    $theme = $_POST ['padTheme'] ?? NULL;

    if ( $theme === '' )
      padSessionForget ( 'padTheme' );
    elseif ( is_string ( $theme ) and padThemeType ( $theme ) )
      padSessionPut ( 'padTheme', $theme );

  }

?>
