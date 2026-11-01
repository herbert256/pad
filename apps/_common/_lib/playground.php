<?php

  // The way from an example to the playground: a little icon at the top right of an example,
  // a link to the playground application with the example in its boxes - the template in the
  // template box, and its data in the data box or its PHP in the PHP box - carried in the #s=
  // hash the playground keeps its boxes in (www/playground/playground.js). The playground
  // fills the boxes and waits for Render, as it does for every link: the hash is anybody's
  // to write.
  //
  // padPlayLink  the icon, an <a class="pad-play">, with the link to its stylesheet the first
  //              time on a page (www/_common/play.css); '' offline (padWasm), where there is
  //              no playground, and for an example without a template
  // padPlayData  the data text that hands a _data file to a template as the variable named
  //              after it - what data='name' finds in its application
  // padPlayPage  the icon for a page of an application: its template less a {meta} line on
  //              top, which belongs to the application that shows it, with its paired PHP or
  //              else the _data file its template names with data='...'
  //
  // The icon stands at the top right of the nearest positioned box, so an example's box is
  // position: relative - the class pad-play-box, or a style of its own. Inline, it floats at
  // the right of the box it stands in, for an example that has no box of its own.
  //
  // An application whose examples cannot run there - they read its own database, or tables
  // its _inits.php declares - sets $playExamples = FALSE, and its examples have no icon.
  //
  // Loaded with _common's _lib; an application without _common includes the file itself.

  function padPlayLink ( $template, $data = '', $php = '', $inline = FALSE ) {

    global $padRoot;

    static $styled = FALSE;

    $template = (string) $template;

    if ( padWasm () or trim ( $template ) === '' or ( $GLOBALS ['playExamples'] ?? TRUE ) === FALSE )
      return '';

    $boxes = [ 'source' => $template, 'data' => (string) $data, 'mode' => 'data' ];

    if ( trim ( (string) $php ) !== '' ) {
      $boxes ['php']  = (string) $php;
      $boxes ['mode'] = 'php';
    }

    $json = json_encode ( $boxes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
    $href = ( $padRoot ?? '/' ) . 'playground/#s=' . base64_encode ( (string) $json );

    $style = '';

    if ( ! $styled ) {
      $styled = TRUE;
      $file   = dirname ( APPS ) . '/www/_common/play.css';
      $algo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
      $hash   = is_file ( $file ) ? '?v=' . substr ( (string) hash_file ( $algo, $file ), 0, 10 ) : '';
      $style  = '<link rel="stylesheet" href="' . htmlspecialchars ( ( $padRoot ?? '/' ) . "_common/play.css$hash" ) . '">';
    }

    include_once PAD . 'icons/_lib/icon.php';

    return $style
         . '<a class="pad-play' . ( $inline ? ' pad-play-inline' : '' ) . '" href="' . htmlspecialchars ( $href ) . '"'
         . ' title="Try it in the playground" aria-label="Try this example in the playground">'
         . padIcon ( 'play', 16 ) . '</a>';

  }

  function padPlayPage ( $root, $page, $inline = FALSE ) {

    $template = trim ( preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', (string) @file_get_contents ( "$root$page.pad" ) ) );
    $php      = is_file ( "$root$page.php" ) ? (string) file_get_contents ( "$root$page.php" ) : '';
    $data     = '';

    if ( $php === '' and preg_match ( "/data='(\w+)'/", $template, $found ) )
      foreach ( [ 'json', 'yaml', 'yml', 'csv', 'xml' ] as $type )
        if ( is_file ( "{$root}_data/$found[1].$type" ) ) {
          $data = padPlayData ( $found [1], "{$root}_data/$found[1].$type" );
          break;
        }

    return padPlayLink ( ( $template === '' ) ? '' : "$template\n", $data, $php, $inline );

  }

  // A _data file as a data text of the playground that holds it under its name: JSON inside
  // an object, YAML under a key - both as written, with their own layout - and any other
  // format (CSV, XML) read as PAD reads it and written as JSON.

  function padPlayData ( $name, $file ) {

    $text = trim ( (string) @file_get_contents ( $file ) );
    $type = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );

    if ( $text === '' )
      return '';

    $indent = fn ( $text ) => preg_replace ( '/\n(?=.)/', "\n  ", $text );

    if ( $type == 'json' )
      return "{\n  " . json_encode ( (string) $name ) . ': ' . $indent ( $text ) . "\n}\n";

    if ( $type == 'yaml' or $type == 'yml' )
      return "$name:\n  " . $indent ( preg_replace ( '/\A---\s*\n/', '', $text ) ) . "\n";

    $rows = padData ( $text, $type );

    return json_encode ( [ (string) $name => array_values ( $rows ) ],
                         JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";

  }

?>
