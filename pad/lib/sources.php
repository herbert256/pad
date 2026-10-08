<?php

  // The files behind a page as tabs, each coloured by lib/highlight.php - what {source}
  // writes: a documentation page, an example gallery, a tutorial shows the template, the PHP
  // and the script of what it demonstrates, read from the files themselves so the code shown
  // can never drift from the code that runs.
  //
  //   {source}                                  the page asked for: its template and PHP
  //   {source 'examples/props'}                 another page of the application
  //   {source files='_tags/card.pad, www:app.js'}
  //                                             files after the page's - a file of the
  //                                             application, or www: one in its www/ directory
  //   {source only='www:app.js'}                those files alone
  //
  // padSourceTabs  the tabs: a radio button and a label per file, then a panel per file -
  //                the tabs work without a script, by CSS alone
  // padSourceList  the files a {source} names, a list or a text with commas
  // padSourcePath  where a name of such a file is on the disk, FALSE for one that is not
  //                there or may not be shown - the rule of {highlight file=}: inside the
  //                application (or its www/ directory), not under _config/, no dotfile
  // padSourceStyle the default look, once a page, every rule under :where() so the page's own
  //                CSS wins
  //
  // A tab is marked data-side="server" for a file of the application and "client" for one
  // under www/ - what runs on the server, what the browser gets.

  function padSourceTabs ( $page, $files = [], $only = [] ) {

    global $padCheckSyntax;

    static $count = 0;

    $names = [];

    if ( ! $only ) {

      $page     = trim ( (string) $page, '/' );
      $template = padSourcePath ( "$page.pad" ) ? "$page.pad" : ( padSourcePath ( "$page.html" ) ? "$page.html" : '' );
      $php      = padSourcePath ( "$page.php" ) ? "$page.php" : '';

      if ( $template === '' and $php === '' ) {
        padError ( "{source} has no page '" . padMakeSafe ( $page, 60 ) . "' of the application to show" );
        return '';
      }

      if ( $template !== '' ) $names [] = $template;
      if ( $php      !== '' ) $names [] = $php;

    }

    foreach ( $only ?: $files as $name )
      $names [] = $name;

    $count++;
    $group = "pad-source-$count";
    $tabs  = $panels = '';
    $at    = 0;

    foreach ( $names as $name ) {

      $path = padSourcePath ( $name );

      if ( $path === FALSE ) {
        if ( $padCheckSyntax )
          padError ( "{source} has no file '" . padMakeSafe ( $name, 60 ) . "' of the application to show" );
        continue;
      }

      $at++;
      $www   = str_starts_with ( $name, 'www:' );
      $shown = $www ? 'www/' . $GLOBALS ['padApp'] . '/' . ltrim ( substr ( $name, 4 ), '/' ) : $name;
      $id    = "$group-$at";
      $lang  = pathinfo ( $path, PATHINFO_EXTENSION );
      $lang  = padHighlightKnown ( $lang ) ? $lang : 'text';

      $tabs   .= '<input type="radio" name="' . $group . '" id="' . $id . '"' . ( $at == 1 ? ' checked' : '' ) . '>'
               . '<label for="' . $id . '" data-side="' . ( $www ? 'client' : 'server' ) . '">' . htmlspecialchars ( $shown ) . '</label>';
      $panels .= '<div class="pad-source-panel">' . padHighlight ( file_get_contents ( $path ), $lang, TRUE ) . '</div>';

    }

    if ( ! $at )
      return '';

    return padSourceStyle () . '<div class="pad-source">' . $tabs . $panels . '</div>';

  }

  function padSourceList ( $value ) {

    if ( $value === NULL or $value === FALSE or $value === '' )
      return [];

    $list = is_array ( $value ) ? $value : explode ( ',', (string) $value );

    return array_values ( array_filter ( array_map ( fn ( $one ) => trim ( (string) $one ), $list ), 'strlen' ) );

  }

  function padSourcePath ( $name ) {

    $name = (string) $name;
    $www  = str_starts_with ( $name, 'www:' );
    $base = $www ? dirname ( APPS ) . '/www/' . $GLOBALS ['padApp'] . '/' : APP;
    $name = ltrim ( $www ? substr ( $name, 4 ) : $name, '/' );

    $root = realpath ( $base );
    $path = realpath ( $base . $name );

    if ( $root === FALSE or $path === FALSE or ! is_file ( $path ) or ! str_starts_with ( $path, $root . DIRECTORY_SEPARATOR ) )
      return FALSE;

    if ( preg_match ( '#(^|/)(_config/|\.)#', substr ( $path, strlen ( $root ) + 1 ) ) )
      return FALSE;

    return $path;

  }

  // The tabs by CSS: a radio is hidden, its label is the tab, and the n-th checked radio shows
  // the n-th panel. The code itself looks as {highlight} makes it look.

  function padSourceStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $css = ':where(.pad-source){margin:1em 0;border:1px solid rgba(127,127,127,.3);border-radius:8px;overflow:hidden}'
         . ':where(.pad-source>input){position:absolute;opacity:0;pointer-events:none}'
         . ':where(.pad-source>label){display:inline-block;padding:8px 14px;cursor:pointer;font:600 13px/1.2 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;opacity:.65;border-bottom:2px solid transparent}'
         . ':where(.pad-source>input:checked+label){opacity:1;border-bottom-color:currentColor}'
         . ':where(.pad-source>input:focus-visible+label){outline:2px solid currentColor;outline-offset:-2px}'
         . ':where(.pad-source-panel){display:none}'
         . ':where(.pad-source-panel .pad-highlight){margin:0;border-radius:0}';

    for ( $n = 1; $n <= 12; $n++ )
      $css .= ":where(.pad-source>input:nth-of-type($n):checked~.pad-source-panel:nth-of-type($n)){display:block}";

    return "<style>$css</style>";

  }

?>
