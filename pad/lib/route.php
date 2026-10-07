<?php

  // Clean URLs: /shop/products/42 reaching products/[id].pad with $id = 42. The path below
  // the application's entry point is mapped onto the file tree, and a file or directory
  // whose name is a bracketed variable name stands for any one segment, binding it:
  //
  //   products/[id].pad          /shop/products/42           $id   = '42'
  //   blog/[year]/[slug].pad     /shop/blog/2026/hello-pad   $year = '2026', $slug = 'hello-pad'
  //   docs/[path+].pad           /shop/docs/a/b/c            $path = 'a/b/c'  (the rest)
  //
  // The web server hands such a path to the entry point: php -S does it by itself (a path
  // that is no file runs the nearest index.php, the rest as PATH_INFO), Apache with one
  // FallbackResource line, or a rewrite to index.php/... - see docs/APP.md. Nothing of it is
  // required: ?page URLs keep working beside it, and the same matching answers
  // ?products/42, so an application written for clean URLs runs on a server that routes
  // nothing. $padCleanUrls makes $padGo write the clean form.
  //
  // padRequestPath   the path below the entry point - PATH_INFO, or what the request URI
  //                  has beyond the entry point's directory when a FallbackResource left
  //                  PATH_INFO unset - with an &name=value tail moved into the request
  //                  values, so a link written {$padGo}page&x=1 works in either form
  // padRequestPathRaw  that path as the request URI writes it, still encoded
  // padRequestPathEnd  the raw end of the request URI that decodes to PATH_INFO, for a URI
  //                  that does not show the entry point's directory
  // padRouteQuery    whether the query string names the page instead: ?about on a clean
  //                  URL - the relative link every existing template writes - goes to about
  // padRouteQueryName  that name as the query string sent it, the dots and spaces PHP made _
  // padPageRoute     a page name resolved: [ 'page' => ..., 'vars' => [...] ] or FALSE; an
  //                  existing page as it is, otherwise the bracketed names, a literal name
  //                  before a bracket and a single segment before the rest
  // padRouteWalk     that walk, one directory and one segment at a time
  // padRouteNames    the bracketed names in one directory - pages, directories, rests
  // padRouteBind     the bound segments as variables of the request

  function padRequestPath () {

    if ( PHP_SAPI == 'cli' )
      return '';

    // The path as the client wrote it, when the request URI shows it - and says what
    // PATH_INFO says, where there is one: the & that starts the tail is a raw one there,
    // and an encoded %26 inside a segment is part of the segment.
    // PATH_INFO arrives decoded, and the path was decoded before the tail was split off, so
    // products/a%26b reached products/[id] as a with a value b - and the encoding {get},
    // {ajax} and padRedirect () give a routed segment was undone under clean URLs.

    $info = trim ( (string) ( $_SERVER ['PATH_INFO'] ?? '' ), '/' );
    $raw  = padRequestPathRaw ();

    // A request URI that does not show the entry point's directory - an Alias, a rewrite, a
    // proxy's prefix - still ends with the path as the client wrote it: the raw end that
    // decodes to PATH_INFO is taken.

    if ( $info !== '' and ( $raw === NULL or trim ( rawurldecode ( $raw ), '/' ) !== $info ) )
      $raw = padRequestPathEnd ( $info );

    if ( $raw !== NULL and ( $info === '' or trim ( rawurldecode ( $raw ), '/' ) === $info ) ) {

      list ( $path, $tail ) = array_pad ( explode ( '&', trim ( $raw, '/' ), 2 ), 2, NULL );

      $path = trim ( rawurldecode ( $path ), '/' );

    } else {

      $path = $info;
      $tail = NULL;

      if ( str_contains ( $path, '&' ) )
        list ( $path, $tail ) = explode ( '&', $path, 2 );

    }

    if ( $tail !== NULL ) {

      parse_str ( $tail, $values );

      foreach ( $values as $key => $value ) {
        if ( ! isset ( $_GET     [$key] ) ) $_GET     [$key] = $value;
        if ( ! isset ( $_REQUEST [$key] ) ) $_REQUEST [$key] = $value;
      }

      // Values with no path in front of them, /shop/&x=1, are values of the index page.

      $path = trim ( $path, '/' );

      if ( $path === '' )
        $path = 'index';

    }

    return $path;

  }

  // The path below the entry point as it stands in the request URI, still encoded, or NULL
  // when the URI does not begin with the entry point's directory: each segment of that
  // directory is compared decoded, and the entry script's own name after it - index.php/... -
  // is passed over.

  function padRequestPathRaw () {

    $uri    = explode ( '?', (string) ( $_SERVER ['REQUEST_URI'] ?? '' ), 2 ) [0];
    $script = explode ( '/', str_replace ( '\\', '/', (string) ( $_SERVER ['SCRIPT_NAME'] ?? '' ) ) );

    if ( $uri === '' or count ( $script ) < 2 )
      return NULL;

    $have = explode ( '/', $uri );
    $base = count ( $script ) - 1;

    for ( $i = 0; $i < $base; $i++ )
      if ( ! isset ( $have [$i] ) or rawurldecode ( $have [$i] ) !== $script [$i] )
        return NULL;

    $rest = array_slice ( $have, $base );

    if ( isset ( $rest [0] ) and rawurldecode ( $rest [0] ) === $script [$base] )
      array_shift ( $rest );

    return implode ( '/', $rest );

  }

  // The raw end of the request URI's path that decodes to $info, segment by segment from
  // the end, or NULL when no end of it does.

  function padRequestPathEnd ( $info ) {

    $have = explode ( '/', trim ( explode ( '?', (string) ( $_SERVER ['REQUEST_URI'] ?? '' ), 2 ) [0], '/' ) );

    for ( $k = 1; $k <= count ( $have ); $k++ ) {
      $end = implode ( '/', array_slice ( $have, -$k ) );
      if ( trim ( rawurldecode ( $end ), '/' ) === $info )
        return $end;
    }

    return NULL;

  }

  // A query string that starts with a bare page name - ?about, no value - names the page
  // even on a clean URL: that is the link a template writes as href="?about", and on
  // /shop/products/42 the browser makes it /shop/products/42?about. A first value with a
  // value, ?sort=price, is a parameter of the page the path names.
  //
  // A bare single name only a bracket at the root takes - [slug].pad binds any word - is no
  // page name but a value: with such a route ?padInclude, ?back or ?expand on a clean URL
  // rendered [slug] in place of the page the path names. A name that goes on past a bound
  // segment - ?nl/about under [lang]/about.pad - is a page's, as a relative link writes it.
  // A key the path's own &name tail moved into the request values (padRequestPath) is a
  // value whatever it is.

  function padRouteQuery () {

    $first = array_key_first ( $_GET );

    if ( $first === NULL or ( $_GET [$first] ?? NULL ) !== '' )
      return FALSE;

    if ( (string) ( $_SERVER ['QUERY_STRING'] ?? '' ) === '' )
      return FALSE;

    $name  = padRouteQueryName ( (string) $first );
    $route = padPageRoute ( $name );

    return $route !== FALSE and ( str_contains ( $name, '/' ) or ! str_starts_with ( $route ['page'], '[' ) );

  }

  // The page name the query string starts with, as it was sent: PHP writes a dot or a space
  // in a query key as _, so ?products/v1.2 reached the router as products/v1_2 and bound $id
  // v1_2, where the path form binds v1.2. The first name of the query string is read again,
  // and taken when it is the same key spelled as PHP would and the router finds a page for
  // it - a page whose file has the _ in its name is still found by either spelling.

  function padRouteQueryName ( $key ) {

    $raw = urldecode ( explode ( '=', explode ( '&', (string) ( $_SERVER ['QUERY_STRING'] ?? '' ), 2 ) [0], 2 ) [0] );

    if ( $raw !== $key and strtr ( $raw, ' .', '__' ) === $key and padPageRoute ( $raw ) !== FALSE )
      return $raw;

    return $key;

  }

  // Every segment has to be there and may not start with _ or a dot, so the _xxx directories
  // stay private and no walk leaves the application; and none holds a bracket, so a routed
  // file is reached through its route only - ?products/[id] by its own name is not found.
  // A segment a bracket binds may hold what a page name may not - a dot, a space, any
  // letter - since it becomes a value, never a file name; the existing page is looked for
  // only where the name is a page name.

  function padPageRoute ( $page, $app=APP ) {

    $page = (string) $page;

    if ( $page === '' )
      return FALSE;

    foreach ( explode ( '/', $page ) as $segment )
      if ( ! preg_match ( '/^[^._\[\]\x00-\x1f\x7f\\\\][^\[\]\x00-\x1f\x7f\\\\]*$/D', $segment ) )
        return FALSE;

    if ( preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/D', $page ) ) {

      $exact = padPage ( $page, $app );

      if ( $exact )
        return [ 'page' => $exact, 'vars' => [] ];

    }

    return padRouteWalk ( $app, explode ( '/', $page ), [] );

  }

  function padRouteWalk ( $dir, $segments, $vars ) {

    $segment = array_shift ( $segments );
    $last    = ! count ( $segments );
    $literal = preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_-]*$/D', $segment );

    if ( $last ) {

      if ( $literal and padPageExists ( "$dir$segment" ) )
        return [ 'page' => $segment, 'vars' => $vars ];

      if ( $literal and is_dir ( "$dir$segment" ) and padPageExists ( "$dir$segment/index" ) )
        return [ 'page' => "$segment/index", 'vars' => $vars ];

    } elseif ( $literal and is_dir ( "$dir$segment" ) ) {

      $found = padRouteWalk ( "$dir$segment/", $segments, $vars );

      if ( $found )
        return [ 'page' => "$segment/" . $found ['page'], 'vars' => $found ['vars'] ];

    }

    $names = padRouteNames ( $dir );

    foreach ( $names ['pages'] as $name )
      if ( $last )
        return [ 'page' => "[$name]", 'vars' => $vars + [ $name => $segment ] ];

    foreach ( $names ['dirs'] as $name ) {

      $bound = $vars + [ $name => $segment ];

      if ( $last ) {
        if ( padPageExists ( "{$dir}[$name]/index" ) )
          return [ 'page' => "[$name]/index", 'vars' => $bound ];
        continue;
      }

      $found = padRouteWalk ( "{$dir}[$name]/", $segments, $bound );

      if ( $found )
        return [ 'page' => "[$name]/" . $found ['page'], 'vars' => $found ['vars'] ];

    }

    foreach ( $names ['rests'] as $name )
      return [ 'page' => "[$name+]", 'vars' => $vars + [ $name => implode ( '/', array_merge ( [ $segment ], $segments ) ) ] ];

    return FALSE;

  }

  // The names are sorted, so two brackets side by side - which only the author can tell
  // apart - always resolve the same way. A name that is no variable a request may set, an
  // engine name among them, is not a route.

  function padRouteNames ( $dir ) {

    $names = [ 'pages' => [], 'dirs' => [], 'rests' => [] ];

    if ( ! is_dir ( $dir ) )
      return $names;

    $entries = scandir ( $dir );

    sort ( $entries );

    foreach ( $entries as $entry ) {

      if ( ! preg_match ( '/^\[([a-zA-Z][a-zA-Z0-9_]*)(\+?)\](\.(php|pad|html))?$/D', $entry, $match ) )
        continue;

      if ( ! padValidVar ( $match [1] ) )
        continue;

      $isDir  = is_dir ( "$dir$entry" );
      $isPage = ( ! $isDir && ( $match [3] ?? '' ) !== '' );

      if     ( $match [2] and $isPage ) $names ['rests'] [] = $match [1];
      elseif ( ! $match [2] and $isDir  ) $names ['dirs']  [] = $match [1];
      elseif ( ! $match [2] and $isPage ) $names ['pages'] [] = $match [1];

    }

    foreach ( $names as $kind => $list )
      $names [$kind] = array_values ( array_unique ( $list ) );

    return $names;

  }

  // A name the application keeps in the session is never set from the path, as a request
  // value never sets one (padRequestVar): a page [user].pad bound $user from the URL, the
  // session's own value was then not taken, and the end of the request wrote the path's
  // value into the session. The first bind runs before the configuration is read, so
  // inits/parms.php drops such a name again before it takes the session in.

  function padRouteBind ( $vars ) {

    foreach ( $vars as $name => $value )
      if ( ! in_array ( $name, $GLOBALS ['padSessionVars'] ?? [], TRUE ) )
        $GLOBALS [$name] = $value;

  }

?>
