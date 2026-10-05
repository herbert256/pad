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
  // padSrcWhere    the current error's template position, or NULL
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

  // The template position of the error being reported, or NULL when there is none to give:
  // the engine is not running a page yet, the spot is in text PHP printed, or the text no
  // longer lines up with any template - then nothing is said rather than something wrong.
  //
  // The spot is the tag the innermost level is working on; when that level is just being
  // opened or closed, the tag that opened it, in the level below. $padErrorAt overrides it
  // for errors about a spot other than a tag (padErrorAt).

  function padSrcWhere ( $error = '' ) {

    global $pad, $padOut, $padErrorAt;

    $at = $padErrorAt ?? NULL;

    if ( $at and isset ( $at ['file'] ) )
      return is_int ( $at ['pos'] ?? NULL ) ? padSrcSpot ( [ 'file' => $at ['file'], 'pos' => $at ['pos'] ], $at ['length'] ?? 1, $error ) : NULL;

    if ( $at and isset ( $at ['search'] ) )
      return padSrcSearch ( $at ['search'], $error );

    if ( ! isset ( $pad ) or ! is_int ( $pad ) or $pad < 0 or ! is_array ( $padOut ?? NULL ) )
      return NULL;

    if ( $at and isset ( $at ['base'] ) ) {
      $where = is_int ( $at ['base'] ) ? padSrcBase ( $at ['level'], $at ['base'] ) : NULL;
      return $where ? padSrcSpot ( $where, $at ['length'] ?? 1, $error ) : NULL;
    }

    if ( $at and isset ( $at ['out'] ) ) {
      $where = is_int ( $at ['out'] ) ? padSrcOut ( $at ['level'], $at ['out'] ) : NULL;
      return $where ? padSrcSpot ( $where, $at ['length'] ?? 1, $error ) : NULL;
    }

    for ( $level = $pad; $level >= 0; $level-- )
      if ( padSrcTagOk ( $level, $level == $pad ) )
        return padSrcTag ( $level, $error );

    return NULL;

  }


  // Is the level in the middle of a tag: its start on a {, a } behind it. The innermost
  // level must also have found that } - between occurrences and while it closes, its start
  // and end are left over from the last tag.

  function padSrcTagOk ( $level, $innermost ) {

    global $padOut, $padStart, $padEnd;

    $out   = $padOut   [$level] ?? '';
    $start = $padStart [$level] ?? FALSE;

    if ( ! is_string ( $out ) or ! is_int ( $start ) or ( $out [$start] ?? '' ) !== '{' )
      return FALSE;

    if ( $innermost ) {
      $end = $padEnd [$level] ?? FALSE;
      if ( ! is_int ( $end ) or $end <= $start or ( $out [$end] ?? '' ) !== '}' )
        return FALSE;
    }

    return strpos ( $out, '}', $start ) !== FALSE;

  }

  // The tag at the level's start, in its template. Its own } is located through the text
  // behind it, then the { that opens it by brace depth in the template itself - the tag as
  // the level holds it may already have had a tag inside it resolved. When the positions
  // do not line up, the tag's text is looked for in the template files read, and taken
  // when it stands in exactly one place.

  function padSrcTag ( $level, $error ) {

    global $padOut, $padStart;

    $out   = $padOut [$level];
    $start = $padStart [$level];
    $close = strpos ( $out, '}', $start );

    $where = padSrcOut ( $level, $close );

    if ( $where ) {

      $text = padSrcText ( $where );

      if ( ( $text [ $where ['pos'] ] ?? '' ) === '}' ) {

        $depth = 0;

        for ( $i = $where ['pos']; $i >= 0; $i-- )
          if ( $text [$i] == '}' )
            $depth++;
          elseif ( $text [$i] == '{' and --$depth == 0 )
            break;

        if ( $i >= 0 ) {
          $length = $where ['pos'] - $i + 1;
          $where ['pos'] = $i;
          return padSrcSpot ( $where, $length, $error );
        }

      }

    }

    return padSrcSearch ( substr ( $out, $start, $close - $start + 1 ), $error );

  }

  // A position in the level's working text whose tail is still the template as written,
  // located in the level's base: the base must end in that tail. A {# comment #} or ~ taken
  // out further on breaks the match, and then there is no answer from here.

  function padSrcOut ( $level, $pos ) {

    global $padOut, $padBase;

    $tail = substr ( $padOut [$level] ?? '', $pos );
    $base = $padBase [$level] ?? '';

    if ( ! is_string ( $base ) or ! str_ends_with ( $base, $tail ) )
      return NULL;

    return padSrcBase ( $level, strlen ( $base ) - strlen ( $tail ) );

  }

  // A position in a level's base, located in a template: through the map of a built page,
  // down to the level below for a pair whose base is (part of) the text between its tags,
  // or in the template file the base was read from.

  function padSrcBase ( $level, $pos ) {

    global $padSrcMap, $padPair, $padSource, $padBase, $padOut, $padStart;

    if ( $level < 0 )
      return NULL;

    if ( isset ( $padSrcMap [$level] ) )
      return [ 'root' => $level, 'pos' => $pos ];

    $base = $padBase [$level] ?? '';

    if ( $level > 0 and ( $padPair [$level] ?? FALSE ) ) {

      $content = $padSource [$level] ?? '';

      if     ( $base === '' and $content === '' )                        $offset = 0;
      elseif ( $base === ''                     )                        $offset = FALSE;
      elseif ( str_starts_with ( $content, $base ) )                     $offset = 0;
      elseif ( str_ends_with   ( $content, $base ) )                     $offset = strlen ( $content ) - strlen ( $base );
      elseif ( substr_count    ( $content, $base ) == 1 )                $offset = strpos ( $content, $base );
      else                                                               $offset = FALSE;

      $below = $padOut [$level-1] ?? '';
      $open  = is_int ( $padStart [$level-1] ?? FALSE ) ? strpos ( $below, '}', $padStart [$level-1] ) : FALSE;

      if ( $offset !== FALSE and $open !== FALSE
           and substr ( $below, $open + 1, strlen ( $content ) ) === $content ) {

        $where = padSrcOut ( $level - 1, $open + 1 );

        if ( $where )
          $where ['pos'] += $offset + $pos;

        return $where;

      }

    }

    // A nested pass over the very text of the tag that started it - {code}...{/code}.

    if ( $level > 0 and $base !== '' and ! ( $padPair [$level] ?? FALSE ) and ( $padBase [$level-1] ?? NULL ) === $base )
      return padSrcBase ( $level - 1, $pos );

    $where = padSrcFile ( $base, $pos );

    if ( $where )
      $where ['via'] = $level;

    return $where;

  }

  // A base read from a template file: the file's text is the base's end - a snippet's .php
  // half prints in front of it. The newest file read wins.

  function padSrcFile ( $base, $pos ) {

    global $padSrcRead;

    if ( ! is_string ( $base ) or $base === '' )
      return NULL;

    foreach ( array_reverse ( array_keys ( $padSrcRead ?? [] ) ) as $file ) {

      $text = @file_get_contents ( $file );

      if ( ! is_string ( $text ) or $text === '' or ! str_ends_with ( $base, $text ) )
        continue;

      $lead = strlen ( $base ) - strlen ( $text );

      if ( $pos >= $lead )
        return [ 'file' => $file, 'pos' => $pos - $lead ];

    }

    return NULL;

  }

  // The last resort: the tag's text in the template files read, taken only when it stands
  // in exactly one place.

  function padSrcSearch ( $tag, $error ) {

    global $padSrcRead;

    if ( strlen ( $tag ) < 3 )
      return NULL;

    $found = NULL;

    foreach ( array_keys ( $padSrcRead ?? [] ) as $file ) {

      $text  = @file_get_contents ( $file );
      $count = is_string ( $text ) ? substr_count ( $text, $tag ) : 0;

      if ( $count > 1 or ( $count and $found ) )
        return NULL;

      if ( $count )
        $found = [ 'file' => $file, 'pos' => strpos ( $text, $tag ) ];

    }

    return $found ? padSrcSpot ( $found, strlen ( $tag ), $error ) : NULL;

  }

  // The text a located position is in: a built page's whole base, or a template file.

  function padSrcText ( $where ) {

    global $padBase;

    if ( isset ( $where ['root'] ) )
      return $padBase [ $where ['root'] ] ?? '';

    return (string) @file_get_contents ( $where ['file'] );

  }

  // A located spot as what the error pages show: the file, its line and column, the tag
  // as written, the lines around it with a marker under the spot, the wrappers the page
  // sits in and an editor link. A position in a built page is first mapped to its file;
  // in a stretch PHP printed there is nothing to show.

  function padSrcSpot ( $where, $length, $error ) {

    global $padSrcMap;

    $wrap = [];

    if ( isset ( $where ['root'] ) ) {

      $entry = $padSrcMap [ $where ['root'] ];
      $wrap  = $entry ['wrap'] ?? [];
      $file  = NULL;

      foreach ( $entry ['map'] as $piece )
        if ( $where ['pos'] >= $piece [0] and $where ['pos'] < $piece [0] + $piece [1] ) {
          if ( $piece [2] === '' )
            return NULL;
          $file = $piece [2];
          $pos  = $piece [3] + $where ['pos'] - $piece [0];
          break;
        }

      if ( $file === NULL )
        return NULL;

      // A page built inside another one - {page} - opens its levels on top of the tag
      // that asked for it, and that tag stands in the level below that.

      $via   = ( $where ['root'] > 1 ) ? $where ['root'] - 1 : 0;
      $where = [ 'file' => $file, 'pos' => $pos, 'via' => $via, 'keep' => TRUE ];

    }

    // A snippet's text came in through a tag in the level below: that tag's own position
    // is where it was included, and the wrappers are the includer's.

    $included = [];

    if ( ( $where ['via'] ?? 0 ) > 0 and padSrcTagOk ( $where ['via'] - 1, FALSE ) ) {

      $by = padSrcTag ( $where ['via'] - 1, '' );

      if ( $by ) {
        $included = array_merge ( [ $by ['file'] . ':' . $by ['line'] . ':' . $by ['column'] ], $by ['included'] );
        $wrap     = ( $where ['keep'] ?? FALSE ) ? $wrap : $by ['wrapped'];
      }

    }

    $file   = $where ['file'];
    $source = (string) @file_get_contents ( $file );
    $pos    = $where ['pos'];

    if ( $pos < 0 or $pos >= strlen ( $source ) )
      return NULL;

    // The marker goes under what the message names when the tag holds it - the field of
    // {$totl | money}, the function of {echo $x | uppr} - and under the tag otherwise.

    $tag   = substr ( $source, $pos, max ( 1, $length ) );
    $mark  = $pos;
    $width = strcspn ( $tag, "\n" );
    $named = padSrcNamed ( $error );

    if ( $named !== '' and ( $inside = strpos ( $tag, $named ) ) !== FALSE ) {
      $mark  = $pos + $inside;
      $width = strlen ( $named );
    }

    $from   = strrpos ( substr ( $source, 0, $mark ), "\n" );
    $from   = ( $from === FALSE ) ? 0 : $from + 1;
    $line   = substr_count ( $source, "\n", 0, $mark ) + 1;
    $before = substr ( $source, $from, $mark - $from );
    $column = padSrcColumn ( $before );

    $wrap = array_values ( array_filter ( $wrap, fn ( $one ) => $one !== $file and $one !== padSrcName ( $file ) ) );

    return [
      'file'     => padSrcName ( $file ),
      'path'     => $file,
      'line'     => $line,
      'column'   => $column,
      'tag'      => substr ( $tag, 0, strcspn ( $tag, "\n" ) ),
      'excerpt'  => padSrcExcerpt ( $source, $line, $before, $width ),
      'wrapped'  => array_map ( 'padSrcName', $wrap ),
      'included' => $included,
      'link'     => "vscode://file$file:$line:$column"
    ];

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

  // A column counts characters, not bytes - the column an editor shows.

  function padSrcColumn ( $before ) {

    return ( function_exists ( 'mb_strlen' ) ? mb_strlen ( $before, 'UTF-8' ) : strlen ( $before ) ) + 1;

  }

  // The lines around the spot, numbered, with a row of ^ under it - two lines either side.

  function padSrcExcerpt ( $source, $line, $before, $width ) {

    $lines = explode ( "\n", $source );
    $first = max ( 1, $line - 2 );
    $last  = min ( count ( $lines ), $line + 2 );
    $size  = strlen ( (string) $last );
    $out   = '';

    for ( $n = $first; $n <= $last; $n++ ) {

      $out .= str_pad ( $n, $size + 2, ' ', STR_PAD_LEFT ) . ' │ ' . rtrim ( $lines [$n-1], "\r" ) . "\n";

      if ( $n == $line )
        $out .= str_repeat ( ' ', $size + 2 ) . ' │ '
              . ( preg_replace ( '/[^\t]/u', ' ', $before ) ?? str_repeat ( ' ', strlen ( $before ) ) )
              . str_repeat ( '^', max ( 1, $width ) ) . "\n";

    }

    return $out;

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

  // "did you mean": a field, a pipe function or a tag the message says does not exist,
  // against the names there are - the fields visible where the error stands, the pipe
  // functions and the tags of the engine, the application and _common. The nearest is
  // offered when it is one edit away for a short name, two or a third of the name for a
  // longer one - a swap of two letters counts as two.

  function padErrorSuggest ( $error ) {

    if ( preg_match ( "/(?:field named|Field) '\\$([A-Za-z_]\\w*)'/", $error, $m ) )
      return padErrorNear ( $m [1], padErrorFields (), '$' );

    if ( preg_match ( "/pipe function named '([A-Za-z_]\\w*)'/", $error, $m ) )
      return padErrorNear ( $m [1], padErrorNames ( 'functions', '_functions' ) );

    if ( preg_match ( "/there is no tag named '([A-Za-z_]\\w*)'/", $error, $m ) )
      return padErrorNear ( $m [1], array_merge ( padErrorNames ( 'tags', '_tags' ), padErrorNames ( '', '_include' ) ) );

    return '';

  }

  function padErrorNear ( $name, $names, $sigil = '' ) {

    $best = '';
    $dist = ( strlen ( $name ) <= 3 ? 1 : max ( 2, intdiv ( strlen ( $name ), 3 ) ) ) + 1;

    foreach ( array_unique ( $names ) as $one ) {

      $one = (string) $one;

      if ( $one === $name or $one === '' )
        continue;

      $now = levenshtein ( strtolower ( $name ), strtolower ( $one ) );

      if ( $now < $dist ) {
        $dist = $now;
        $best = $one;
      }

    }

    return $best === '' ? '' : $sigil . $best;

  }

  // The field names visible where the error stands: the rows and {set} names of the levels
  // around it, and the application's own variables.

  function padErrorFields () {

    global $pad, $padCurrent, $padSetLvl, $padSetOcc;

    $names = [];

    for ( $level = $pad; $level >= 0; $level-- )
      foreach ( [ $padCurrent [$level] ?? [], $padSetLvl [$level] ?? [], $padSetOcc [$level] ?? [] ] as $set )
        if ( is_array ( $set ) )
          foreach ( array_keys ( $set ) as $key )
            $names [] = $key;

    foreach ( array_keys ( $GLOBALS ) as $key )
      if ( padValidStore ( $key ) and ! in_array ( $key, [ 'GLOBALS', 'argv', 'argc' ] ) )
        $names [] = $key;

    return $names;

  }

  // The names of the engine's pad/<dir>/*.php, and of the <sub>/ files of the application's
  // directory chain and of _common.

  function padErrorNames ( $dir, $sub ) {

    $files = $dir ? glob ( PAD . "$dir/*.php" ) : [];

    if ( defined ( 'APP2' ) and function_exists ( 'padDirs' ) )
      foreach ( padDirs () as $one )
        $files = array_merge ( $files, glob ( APP2 . $one . "$sub/*.{php,pad}", GLOB_BRACE ) ?: [] );

    if ( defined ( 'COMMON' ) )
      $files = array_merge ( $files, glob ( COMMON . "$sub/*.{php,pad}", GLOB_BRACE ) ?: [] );

    return array_map ( fn ( $file ) => pathinfo ( $file, PATHINFO_FILENAME ), $files );

  }

?>
