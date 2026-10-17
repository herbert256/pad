<?php

  // The template source map: where in which template file a spot of the page came from, so
  // an error can name apps/shop/orders/list.pad line 14, column 11 instead of the engine's
  // own PHP file and line.
  //
  // The build step (pad/build) joins _lib, the _inits.pad and _exits.pad of every directory,
  // and the page into one text. Next to that text it now assembles the same text as a list
  // of pieces - [ text, file, offset in the file ], file '' for what PHP printed and for the
  // markers the engine adds - with the same @page@ replacements. When the pieces join up to
  // exactly the text the engine runs, padSrcMake keeps them as the level's map; when they do
  // not, there is no map, and an error goes without a template position rather than with a
  // wrong one.
  //
  // Nothing else is tracked while a page renders: the tag loop resolves the innermost tag
  // first and left to right, so behind the tag it is working on, a level's text is still
  // the template as written. At error time padSrcWhere reads that untouched tail and finds
  // it at the end of the level's own base; a pair level's base is the text between its
  // tags in the level below, which leads down level by level to the built page and its map.
  // A level whose text came from a file - an _include snippet, a tag's .pad half - is found
  // among the template files read so far (padSrcRead, kept by padFileGet).
  //
  // padSrcPieces / padSrcReplace / padSrcMake  build the map, alongside build/*.php
  // padSrcPage     the pieces of the page's own part, its template among what PHP printed
  // padSrcWhere    the current error's template position, or NULL - error/_lib/source.php,
  //                read with the rest of the error-time walk by padErrorTemplate
  // padSrcReport   the position as the text block the error pages show

  function padSrcPieces ( $text, $file = '', $offset = 0 ) {

    return [ [ (string) $text, $file, $offset ] ];

  }

  // str_replace on a piece list: every $needle inside a piece is replaced by the pieces of
  // $with, the parts around it keeping their file and their offset in it. A needle that
  // straddles two pieces is not seen - padSrcMake then finds the text differs and drops the
  // map.

  function padSrcReplace ( $pieces, $needle, $with ) {

    $out = [];

    foreach ( $pieces as $piece ) {

      list ( $text, $file, $offset ) = $piece;

      $parts = explode ( $needle, $text );
      $at    = $offset;

      foreach ( $parts as $index => $part ) {

        if ( $index )
          foreach ( $with as $one )
            $out [] = $one;

        if ( $part !== '' )
          $out [] = [ $part, $file, $at ];

        $at += strlen ( $part ) + strlen ( $needle );

      }

    }

    return $out;

  }

  // The page's part of the build: what build/page.php returns is $lead characters the
  // engine wrote ({padBuild ...}), then the stretch of the page source starting at $from
  // and $length long, then the engine's closing tag. The template file sat at $at in that
  // source for $size characters; everything else in it is what the PHP files printed.

  function padSrcPage ( $text, $lead, $from, $length, $at, $size, $file ) {

    $begin = max ( $at, $from );
    $end   = min ( $at + $size, $from + $length );

    if ( $file === '' or $end <= $begin )
      return padSrcPieces ( $text );

    $start = $lead + $begin - $from;

    return [
      [ substr ( $text, 0, $start ),               '',    0              ],
      [ substr ( $text, $start, $end - $begin ),   $file, $begin - $at   ],
      [ substr ( $text, $start + $end - $begin ),  '',    0              ]
    ];

  }

  // The checked map: the pieces must join up to the text the level runs, or there is none.
  // What is kept is per piece its start in that text, its length, its file and its offset
  // in the file; the texts themselves are not needed any more.

  function padSrcMake ( $pieces, $text, $wrap = [] ) {

    $join = '';
    $map  = [];

    foreach ( $pieces as $piece ) {

      if ( $piece [0] === '' )
        continue;

      $map [] = [ strlen ( $join ), strlen ( $piece [0] ), $piece [1], $piece [2] ];
      $join  .= $piece [0];

    }

    if ( $join !== $text )
      return NULL;

    return [ 'map' => $map, 'wrap' => $wrap ];

  }

  // Every template file read during the request, newest last - what a level whose text came
  // from a file is matched against. Called by padFileGet for .pad and .html files.

  function padSrcRead ( $file ) {

    global $padSrcRead;

    unset ( $padSrcRead [$file] );

    $padSrcRead [$file] = TRUE;

  }


  // The name an error message points at, when it names one: a field, a pipe function, a
  // tag, an option. Quoted names in a message are the engine's habit.

  function padSrcNamed ( $error ) {

    if ( preg_match ( "/(?:field named|Field) '([\$!#&?^]?[A-Za-z_][\w:.@-]*)'/", $error, $m ) )
      return $m [1];

    if ( preg_match ( "/(?:pipe function|tag|property|option) named '([A-Za-z_][\w:-]*)'/", $error, $m ) )
      return $m [1];

    return '';

  }

  // A file name as the author knows it: from the repository root down.

  function padSrcName ( $file ) {

    $root = dirname ( PAD ) . '/';

    return str_starts_with ( $file, $root ) ? substr ( $file, strlen ( $root ) ) : $file;

  }

  // The template position as a block of text for the error pages and the console: where,
  // the lines around it, a "did you mean" when there is a near name, the wrappers.

  function padSrcReport ( $where ) {

    if ( ! $where )
      return '';

    $out = '';

    $near    = ( $where ['suggest'] ?? '' ) ? 'did you mean ' . $where ['suggest'] . '?' : '';
    $excerpt = $where ['excerpt'] ?? '';

    if ( $near and preg_match ( '/\^\n/', $excerpt ) ) {
      $excerpt = preg_replace ( '/\^\n/', "^ $near\n", $excerpt, 1 );
      $near    = '';
    }

    if ( isset ( $where ['file'] ) )
      $out .= $where ['file'] . '  line ' . $where ['line'] . ', column ' . $where ['column'] . "\n\n"
            . $excerpt;

    if ( $near )
      $out .= ( $out ? "\n" : '' ) . "$near\n";

    foreach ( $where ['included'] ?? [] as $one )
      $out .= "\nincluded by $one";

    if ( $where ['included'] ?? [] )
      $out .= "\n";

    if ( $where ['wrapped'] ?? [] )
      $out .= "\nwrapped by " . implode ( ' › ', $where ['wrapped'] ) . "\n";

    return $out;

  }

  // What the error reports add for the error at hand: its template position and a near
  // name for one that does not exist. It runs inside the error handling, so nothing in it
  // may raise an error of its own - any failure is no position.

  function padErrorTemplate ( $error ) {

    include_once PAD . 'error/_lib/source.php';

    set_error_handler ( function () { throw new \ErrorException ( 'source' ); } );

    try {

      $where = padSrcWhere ( $error ) ?? [];
      $near  = padErrorSuggest ( $error );

      if ( $near !== '' )
        $where ['suggest'] = $near;

    } catch ( Throwable $e ) {

      $where = [];

    }

    restore_error_handler ();

    return $where ?: NULL;

  }

?>
