<?php

  // The way from an example to the playground and to the live application: two little icons
  // at the top right of an example, links with the example in the boxes of their editors -
  // the template in the template box, and its data in the data box or its PHP in the PHP box
  // - carried in the #s= hash both keep their boxes in (www/playground/playground.js,
  // www/live/live.js). The playground renders a template inside its own request; the live
  // application runs it as a page of this server, with its PHP, request values, sessions,
  // _common and its demo database, forms that post back. Both fill the boxes and wait for
  // Render or Run, as they do for every link: the hash is anybody's to write.
  //
  // padPlayLink  the icons, <a class="pad-play"> for the playground and <a class="pad-play
  //              pad-live"> for the live application beside it, with the link to their
  //              stylesheet the first time on a page (www/_common/play.css); '' offline
  //              (padWasm), where there is neither, and for an example without a template.
  //              $only = 'live' leaves the playground's out, for an example only a page of
  //              its own can run
  // padPlayData  the data text that hands a _data file to a template as the variable named
  //              after it - what data='name' finds in its application
  // padPlayPage  the icon for a page of an application: its template less a {meta} line on
  //              top, which belongs to the application that shows it, with its paired PHP or
  //              else the _data file its template names with data='...'
  // padPlayEditor, padPlaySamples  what the editors of the playground and the live
  //              application start with: Monaco's PAD grammar and its names, the sample
  //
  // The icon stands at the top right of the nearest positioned box, so an example's box is
  // position: relative - the class pad-play-box, or a style of its own. Inline, it floats at
  // the right of the box it stands in, for an example that has no box of its own.
  //
  // An application whose examples cannot run in the playground - they read the database, or
  // tables its _inits.php declares - sets $playExamples = 'live', and its examples have the
  // live icon alone; FALSE leaves both out. $playLivePhp is PHP the live link puts before an
  // example's own: the declarations of its _inits.php, which its examples need.
  //
  // Loaded with _common's _lib; an application without _common includes the file itself.

  function padPlayLink ( $template, $data = '', $php = '', $inline = FALSE, $only = '' ) {

    global $padRoot;

    static $styled = FALSE;

    $template = (string) $template;
    $examples = $GLOBALS ['playExamples'] ?? TRUE;

    if ( padWasm () or trim ( $template ) === '' or $examples === FALSE )
      return '';

    if ( $examples === 'live' )
      $only = 'live';

    $boxes = function ( $php ) use ( $template, $data ) {
      $boxes = [ 'source' => $template, 'data' => (string) $data, 'mode' => 'data' ];
      if ( trim ( (string) $php ) !== '' ) {
        $boxes ['php']  = (string) $php;
        $boxes ['mode'] = 'php';
      }
      $json = json_encode ( $boxes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
      return base64_encode ( (string) $json );
    };

    $before = (string) ( $GLOBALS ['playLivePhp'] ?? '' );
    $href   = ( $padRoot ?? '/' ) . 'playground/#s=' . $boxes ( $php );
    $live   = ( $padRoot ?? '/' ) . 'live/#s=' . $boxes ( ( $before !== '' and trim ( (string) $php ) !== '' ) ? rtrim ( $before ) . "\n" . $php : $before . $php );

    $style = '';

    if ( ! $styled ) {
      $styled = TRUE;
      $file   = dirname ( APPS ) . '/www/_common/play.css';
      $algo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
      $hash   = is_file ( $file ) ? '?v=' . substr ( (string) hash_file ( $algo, $file ), 0, 10 ) : '';
      $style  = '<link rel="stylesheet" href="' . htmlspecialchars ( ( $padRoot ?? '/' ) . "_common/play.css$hash" ) . '">';
    }

    include_once PAD . 'icons/_lib/icon.php';

    $class = $inline ? ' pad-play-inline' : '';

    return $style
         . ( ( $only == 'live' ) ? '' :
             '<a class="pad-play' . $class . '" href="' . htmlspecialchars ( $href ) . '"'
           . ' title="Try it in the playground" aria-label="Try this example in the playground">'
           . padIcon ( 'play', 16 ) . '</a>' )
         . '<a class="pad-play pad-live' . $class . ( ( $only == 'live' ) ? ' pad-live-alone' : '' ) . '" href="' . htmlspecialchars ( $live ) . '"'
         . ' title="Run it live, as a page of this server" aria-label="Run this example as a page of this server">'
         . padIcon ( 'zap', 16 ) . '</a>';

  }

  function padPlayPage ( $root, $page, $inline = FALSE, $only = '' ) {

    $template = trim ( preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', (string) @file_get_contents ( "$root$page.pad" ) ) );
    $php      = is_file ( "$root$page.php" ) ? (string) file_get_contents ( "$root$page.php" ) : '';
    $data     = '';

    if ( $php === '' and preg_match ( "/data='(\w+)'/", $template, $found ) )
      foreach ( [ 'json', 'yaml', 'yml', 'csv', 'xml' ] as $type )
        if ( is_file ( "{$root}_data/$found[1].$type" ) ) {
          $data = padPlayData ( $found [1], "{$root}_data/$found[1].$type" );
          break;
        }

    return padPlayLink ( ( $template === '' ) ? '' : "$template\n", $data, $php, $inline, $only );

  }

  // The boxes of the playground and of the live application as editors with colours (their
  // index.php): where Monaco's grammar for PAD is - the edit application's own
  // www/edit/pad-mode.js, with the version of its contents as {asset} gives one - and the
  // names it colours, PAD's built-in tags, functions, options, properties and prefixes, as
  // the edit application gathers them (apps/edit/_lib/language.php) from
  // editors/lsp/completions.json, which editors/generate.php keeps in step with pad/. No
  // documentation: the colours and the completion of names need none.

  function padPlayEditor () {

    global $padRoot;

    $file   = dirname ( APPS ) . '/www/edit/pad-mode.js';
    $algo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
    $mode   = is_file ( $file ) ? ( $padRoot ?? '/' ) . 'edit/pad-mode.js?v=' . substr ( (string) hash_file ( $algo, $file ), 0, 10 ) : '';

    $groups = [ 'PAD tag' => 'tags', 'pipe function' => 'functions', 'property@tag' => 'properties',
                'option' => 'options', 'type prefix' => 'prefixes', 'operator' => 'operators',
                'sequence type' => 'sequences' ];

    $names  = array_fill_keys ( array_values ( $groups ), [] ) + [ 'single' => [] ];

    foreach ( json_decode ( (string) @file_get_contents ( dirname ( APPS ) . '/editors/lsp/completions.json' ), TRUE ) ?: [] as $one ) {
      if ( isset ( $groups [ $one ['detail'] ?? '' ] ) )
        $names [ $groups [ $one ['detail'] ] ] [] = rtrim ( $one ['label'], ':' );
      if ( ! empty ( $one ['single'] ) and ( $one ['detail'] ?? '' ) == 'PAD tag' )
        $names ['single'] [] = $one ['label'];
    }

    $files = fn ( $dir ) => array_values ( array_filter ( array_map ( fn ( $f ) => basename ( $f, '.php' ), glob ( "$dir/*.php" ) ?: [] ),
                                                          fn ( $n ) => $n [0] != '_' ) );

    $names ['actions']    = $files ( PAD . 'sequence/actions/types' );
    $names ['constructs'] = $files ( PAD . 'constructs' );
    $names ['branch']     = [ 'else', 'elseif', 'when' ];

    return [ $mode, $names ];

  }

  // The sample both of them start with: a template, its data as JSON and the same as PHP.

  function padPlaySamples () {

    return [
      "<h2>{\$title}</h2>\n<ul>\n  {items}\n    <li>{\$name}{if \$stock eq 0} - sold out{/if}</li>\n  {/items}\n</ul>\n",
      "{\n  \"title\": \"Fruit\",\n  \"items\": [\n    { \"name\": \"Apple\", \"stock\": 3 },\n    { \"name\": \"Pear\",  \"stock\": 0 }\n  ]\n}\n",
      "<?php\n\n  \$title = 'Fruit';\n\n  \$items = [\n    [ 'name' => 'Apple', 'stock' => 3 ],\n    [ 'name' => 'Pear',  'stock' => 0 ]\n  ];\n\n?>\n"
    ];

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
