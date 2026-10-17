<?php

  // The checks behind the editor's markers.
  //
  // editCheckPhp  a PHP file's syntax: token_get_all with TOKEN_PARSE, in this request -
  //               nothing of the file runs, so it is safe to do while typing
  // editCheckPad  a page as PAD sees it: PAD itself renders the page on the command line,
  //               editors/render.php under the strict syntax check, exactly as the language
  //               server does, and the error it reports is placed in the text. The page's
  //               PHP runs for it, as it would for a visitor.
  //
  // Rendered from its saved file, an error comes with its place in the template - file,
  // line and column (lib/source.php). Rendered from text not saved yet (--source=-) it
  // comes without, since that text is no file the source map can point into; the error is
  // then placed the way pad-lsp.js places it: on the tag the message quotes, or on the tag
  // the engine was busy with, or on a quoted name. A wrapper (_inits.pad, _exits.pad) has no
  // page of its own: the index of its directory is rendered for it. Snippets - _include,
  // _tags - are checked through the pages that use them.
  //
  // Of what render.php prints on an error - the message and every global of the engine,
  // the environment included - only the message and the place leave this file.

  function editPhpBinary () {

    return ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';

  }

  // A command in a child process - an argument list, never a shell - with $stdin as its
  // input and at most $timeout seconds: [ exit code, stdout, stderr, timed out ].

  function editRun ( $args, $stdin = NULL, $timeout = 10, $env = [] ) {

    $proc = proc_open ( $args, [ 0 => [ 'pipe', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
                        $pipes, editHome (), array_merge ( getenv (), $env ) );

    if ( ! is_resource ( $proc ) )
      return [ -1, '', 'the process could not be started', FALSE ];

    if ( $stdin !== NULL )
      fwrite ( $pipes [0], $stdin );

    fclose ( $pipes [0] );

    stream_set_blocking ( $pipes [1], FALSE );
    stream_set_blocking ( $pipes [2], FALSE );

    $out = $err = '';
    $end = microtime ( TRUE ) + $timeout;

    while ( TRUE ) {

      $read = array_filter ( [ $pipes [1], $pipes [2] ], fn ( $p ) => ! feof ( $p ) );

      if ( ! $read )
        break;

      $left = $end - microtime ( TRUE );

      if ( $left <= 0 ) {
        proc_terminate ( $proc, 9 );
        fclose ( $pipes [1] );
        fclose ( $pipes [2] );
        proc_close ( $proc );
        return [ -1, $out, $err, TRUE ];
      }

      $write = $except = NULL;

      if ( @stream_select ( $read, $write, $except, (int) $left, (int) ( ( $left - (int) $left ) * 1000000 ) ) === FALSE )
        break;

      foreach ( $read as $pipe )
        if ( $pipe === $pipes [1] )
          $out .= (string) fread ( $pipe, 65536 );
        else
          $err .= (string) fread ( $pipe, 65536 );

    }

    fclose ( $pipes [1] );
    fclose ( $pipes [2] );

    return [ proc_close ( $proc ), $out, $err, FALSE ];

  }

  // ---------------------------------------------------------------------------------------

  function editCheckPhp ( $text ) {

    try {
      token_get_all ( (string) $text, TOKEN_PARSE );
    } catch ( ParseError $e ) {
      return [ editMarker ( $text, $e->getLine (), 0, 0, $e->getMessage (), 'php' ) ];
    }

    return [];

  }

  // The page PAD renders to check a template: a .pad or .html outside the _xxx directories
  // is a page itself, a wrapper stands for its directory's index; anything else has none.

  function editPageFor ( $app, $rel ) {

    $parts = explode ( '/', (string) $rel );
    $base  = array_pop ( $parts );

    if ( ! preg_match ( '/^(.+)\.(pad|html)$/', $base, $m ) )
      return NULL;

    foreach ( $parts as $part )
      if ( str_starts_with ( $part, '_' ) or str_contains ( $part, '[' ) )
        return NULL;

    if ( preg_match ( '/^_(inits|exits)$/', $m [1] ) ) {
      $index = implode ( '/', array_merge ( $parts, [ 'index' ] ) );
      foreach ( [ 'php', 'pad', 'html' ] as $ext )
        if ( is_file ( APPS . "$app/$index.$ext" ) )
          return $index;
      return NULL;
    }

    if ( str_starts_with ( $m [1], '_' ) or str_contains ( $m [1], '[' ) )
      return NULL;

    return implode ( '/', array_merge ( $parts, [ $m [1] ] ) );

  }

  // $source NULL renders the saved file; a text renders that text in the page's place.
  // Answers [ 'checked' => whether there was a page to render, 'markers' => [...] ].

  function editCheckPad ( $app, $rel, $source = NULL, $host = '' ) {

    $page = editPageFor ( $app, $rel );

    if ( $page === NULL )
      return [ 'checked' => FALSE, 'page' => '', 'markers' => [] ];

    $file = APPS . "$app/$rel";
    $text = $source ?? (string) @file_get_contents ( $file );

    $args = [ editPhpBinary (), editHome () . '/editors/render.php', $app, $page ];

    if ( $host !== '' )
      $args [] = "--host=$host";

    if ( $source !== NULL )
      $args [] = '--source=-';

    [ $code, $out, $err, $late ] = editRun ( $args, $source, 10 );

    $done = fn ( $markers ) => [ 'checked' => TRUE, 'page' => $page, 'markers' => $markers ];

    if ( $late )
      return $done ( [ editMarker ( $text, 1, 0, 0, 'the render of ?' . $page . ' took longer than 10 seconds', 'pad', 'warning' ) ] );

    if ( $code === 0 )
      return $done ( [] );

    if ( $code === 2 )
      return $done ( [ editMarker ( $text, 1, 0, 0, trim ( $err ) ?: 'the page could not be rendered', 'pad' ) ] );

    $json = json_decode ( $out, TRUE );

    if ( ! is_array ( $json ) or ! is_string ( $json ['error'] ?? NULL ) ) {
      $message = trim ( preg_replace ( '/\s+/', ' ', strip_tags ( $out !== '' ? $out : $err ) ) );
      return $done ( [ editMarker ( $text, 1, 0, 0, padMakeSafe ( $message ?: 'the render failed', 300 ), 'pad' ) ] );
    }

    return $done ( [ editPadMarker ( $app, $file, $text, $json, $source !== NULL, $page ) ] );

  }

  function editPadMarker ( $app, $file, $text, $json, $fromSource, $page ) {

    $message  = trim ( preg_replace ( '/^PAD:\s*/', '', $json ['error'] ) );
    $template = is_array ( $json ['template'] ?? NULL ) ? $json ['template'] : NULL;
    $appDir   = APPS . "$app/";

    if ( ! empty ( $template ['suggest'] ) and is_string ( $template ['suggest'] ) )
      $message .= ' - did you mean ' . $template ['suggest'] . '?';

    // The source map placed it, in this file: that is where it is - its column counts
    // characters, recounted in the editor's units from the line's text.

    $where = (string) ( $template ['path'] ?? '' );

    if ( $template and ! $fromSource and $where !== '' and realpath ( $where ) === realpath ( $file ) ) {
      $line   = (int) $template ['line'];
      $before = mb_substr ( explode ( "\n", (string) $text ) [ $line - 1 ] ?? '', 0, max ( 0, (int) ( $template ['column'] ?? 1 ) - 1 ) );
      return editMarker ( $text, $line, editUnits ( $before ) + 1, editUnits ( (string) ( $template ['tag'] ?? '' ) ), $message, 'pad' );
    }

    // In another file - a snippet the page includes, its wrapper, a .pad custom tag: said in
    // the message, and given to the browser, which offers to open it there when it is a file
    // of this application. The marker goes on this file's own tag that brought it in, the
    // first of the engine's includers (file:line:column, innermost first) that is this file
    // - line 1 when none is, as the language server does. The text search below found the
    // snippet's tag where this file held the same text, the {$lost} of an {if} branch that
    // never runs, instead of the {lost} that included it.

    $other = NULL;
    $real  = $where !== '' ? realpath ( $where ) : FALSE;

    if ( $template and $real !== FALSE and $real !== realpath ( $file ) and (int) ( $template ['line'] ?? 0 ) > 0 ) {

      $home    = realpath ( editHome () );
      $appReal = realpath ( $appDir );

      if ( $appReal !== FALSE and str_starts_with ( $real, "$appReal/" ) ) {
        $other = [ 'path' => substr ( $real, strlen ( $appReal ) + 1 ),
                   'line' => (int) $template ['line'], 'column' => (int) ( $template ['column'] ?? 1 ) ];
        $shown = $other ['path'];
      } else
        $shown = str_starts_with ( $real, "$home/" ) ? substr ( $real, strlen ( $home ) + 1 ) : $real;

      $message .= ' - in ' . $shown . ':' . (int) $template ['line'];

      if ( ! $fromSource )
        foreach ( is_array ( $template ['included'] ?? NULL ) ? $template ['included'] : [] as $by ) {
          if ( ! preg_match ( '/^(.*):(\d+):(\d+)$/', (string) $by, $m ) )
            continue;
          $byFile = str_starts_with ( $m [1], '/' ) ? $m [1] : editHome () . '/' . $m [1];
          if ( realpath ( $byFile ) !== realpath ( $file ) )
            continue;
          $spot = editSpot ( $text, (int) $m [2], (int) $m [3], '' );
          if ( $spot ) {
            $marker = editMarker ( $text, (int) $m [2], $spot [0], $spot [1], $message, 'pad' );
            $marker ['other'] = $other;
            return $marker;
          }
        }

      $marker = editMarker ( $text, 1, 0, 0, $message, 'pad' );
      $marker ['other'] = $other;
      return $marker;

    } elseif ( ! empty ( $json ['file'] ) and str_starts_with ( (string) realpath ( $json ['file'] ), realpath ( $appDir ) . '/' )
               and realpath ( $json ['file'] ) !== realpath ( $file ) ) {
      $other   = [ 'path' => substr ( (string) realpath ( $json ['file'] ), strlen ( realpath ( $appDir ) ) + 1 ),
                   'line' => (int) ( $json ['line'] ?? 1 ), 'column' => 1 ];
      $message .= ' - in ' . $other ['path'] . ':' . $other ['line'];
    }

    // Otherwise the first of the candidates the text holds.

    $candidates = [];

    preg_match_all ( '/\{[^{}\n]+\}/', $message, $all );
    foreach ( $all [0] as $one )
      $candidates [] = $one;

    foreach ( [ 'padBetweenOrg', 'padOrgSet', 'padBetween' ] as $key ) {
      $value = $json ['pad'] [$key] ?? '';
      if ( is_string ( $value ) and trim ( $value ) !== '' )
        array_push ( $candidates, '{' . $value . '}', '{' . $value );
    }

    preg_match_all ( "/'([^'\n]{2,})'/", $message, $all );
    foreach ( $all [1] as $one )
      $candidates [] = $one;

    if ( ! empty ( $template ['tag'] ) )
      array_unshift ( $candidates, (string) $template ['tag'] );

    // The column counts the editor's units (editUnits): it was the byte offset in the line,
    // so every é before the tag put the marker one further right - under typo}</ in
    // <p>Één {$typo}</p>.

    foreach ( $candidates as $candidate ) {
      $at = strpos ( $text, $candidate );
      if ( $at !== FALSE ) {
        $line   = substr_count ( $text, "\n", 0, $at ) + 1;
        $start  = ( $line == 1 ) ? 0 : strrpos ( substr ( $text, 0, $at ), "\n" ) + 1;
        $column = editUnits ( substr ( $text, $start, $at - $start ) ) + 1;
        $marker = editMarker ( $text, $line, $column, editUnits ( $candidate ), $message, 'pad' );
        $marker ['other'] = $other;
        return $marker;
      }
    }

    if ( preg_match ( '/^_(inits|exits)\./', basename ( $file ) ) and ! $other )
      $message .= ' - rendering ?' . $page;

    $marker = editMarker ( $text, 1, 0, 0, $message, 'pad' );
    $marker ['other'] = $other;

    return $marker;

  }

  // A place the engine names in a text - a line, and a column counted in characters - as
  // [ column, width ] in the editor's units (editUnits): the width the tag's when the line
  // holds it there, else the name or the {tag} that starts there - pad-lsp.js's spot().
  // NULL when the text has no such line.

  function editSpot ( $text, $line, $column, $tag ) {

    $lines = explode ( "\n", (string) $text );

    if ( $line < 1 or $line > count ( $lines ) )
      return NULL;

    $before = mb_substr ( $lines [ $line - 1 ], 0, max ( 0, $column - 1 ) );
    $rest   = substr ( $lines [ $line - 1 ], strlen ( $before ) );

    if ( $tag !== '' and str_starts_with ( $rest, $tag ) )
      $width = editUnits ( $tag );
    elseif ( preg_match ( '/^[$!#&?^]?[A-Za-z_][\w:.@-]*/', $rest, $m ) or preg_match ( '/^\{[^{}\n]*\}/', $rest, $m ) )
      $width = editUnits ( $m [0] );
    else
      $width = 1;

    return [ editUnits ( $before ) + 1, $width ];

  }

  // A marker in the shape Monaco takes: 1-based line and column, in its units (editUnits),
  // the end on the same line; a column of 0 marks the whole line.

  function editMarker ( $text, $line, $column, $length, $message, $source, $severity = 'error' ) {

    $lines  = explode ( "\n", (string) $text );
    $line   = max ( 1, min ( (int) $line, count ( $lines ) ) );
    $width  = editUnits ( $lines [ $line - 1 ] ?? '' );

    if ( $column <= 0 ) {
      $start = 1 + strlen ( $lines [ $line - 1 ] ?? '' ) - strlen ( ltrim ( $lines [ $line - 1 ] ?? '' ) );
      $end   = max ( $start + 1, $width + 1 );
    } else {
      $start = $column;
      $end   = max ( $start + 1, min ( $start + max ( 1, $length ), $width + 1 ) );
    }

    return [ 'line' => $line, 'column' => $start, 'endColumn' => $end, 'message' => (string) $message,
             'source' => $source, 'severity' => $severity ];

  }

  // The length of a text as Monaco counts its columns - a JavaScript string's, in UTF-16
  // units: an emoji, outside the Basic Multilingual Plane, is two. Counted in characters the
  // marker stood one column to the left for every emoji before the tag.

  function editUnits ( $text ) {

    return intdiv ( strlen ( mb_convert_encoding ( (string) $text, 'UTF-16LE', 'UTF-8' ) ), 2 );

  }

?>
