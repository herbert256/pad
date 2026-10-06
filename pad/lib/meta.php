<?php

  // Page metadata: {meta title='Monthly report', layout='_layouts/print', cache=600} in a
  // page's own template - what a page with PHP says in its .php, said by a plain .pad or a
  // dropped-in .html.
  //
  // The tags are read while the page is assembled (padMetaBuild, from build/page.php), after
  // the PHP of _inits.php and the page itself and before the template renders, and taken
  // out of the text. What they say:
  //
  //   layout   the page is framed by that layout, as {extends} does (lib/layout.php)
  //   cache    the page's own server-cache time in seconds, 0 or false to keep it out of
  //            the cache - in an application that has its page cache on; read from the
  //            file before anything is built (padMetaCache, from inits/cache.php), since a
  //            hit answers before the build
  //   access   kept for the access rules and the sitemap to read with padMeta (todo #30,
  //   sitemap  #36) - nothing enforces them yet
  //   any other name becomes a variable of that name: title= is $title, which the wrapper's
  //   <title>{$title}</title> shows
  //
  // padMeta ( 'access' ) answers for the page being built; padMeta ( 'sitemap', 'reports/
  // monthly' ) reads another page's file - its literal values only, nothing is evaluated.

  const padMetaEngine = [ 'layout', 'cache', 'access', 'sitemap' ];

  // Takes the {meta} tags out of a page's template, evaluates their items and applies them;
  // returns the template without them. What stands between {ignore} tags is set aside.

  function padMetaBuild ( $template ) {

    global $padMeta;

    $padMeta = [];

    if ( ! str_contains ( (string) $template, '{meta' ) )
      return $template;

    $masks = [];
    $text  = padLayoutMask ( $template, $masks );
    $found = padMetaFind ( $text );

    if ( ! $found )
      return $template;

    foreach ( array_reverse ( $found ) as $one )
      $text = substr ( $text, 0, $one ['start'] ) . substr ( $text, $one ['end'] );

    foreach ( $found as $one )
      foreach ( padMetaItems ( $one ['parms'] ) as $name => $expr ) {

        if ( ! padValidVar ( $name ) ) {
          padError ( "the {meta} name '$name' is not one a page can set" );
          continue;
        }

        $padMeta [$name] = padEval ( $expr );

        if ( ! in_array ( $name, padMetaEngine ) )
          $GLOBALS [$name] = $padMeta [$name];

      }

    $template = padLayoutUnmask ( $text, $masks );

    if ( isset ( $padMeta ['layout'] ) and (string) $padMeta ['layout'] !== '' and ! str_contains ( $text, '{extends' ) )
      $template = "{extends '" . str_replace ( "'", '', (string) $padMeta ['layout'] ) . "'}" . $template;

    return $template;

  }

  // The {meta ...} tags standing directly in a text - one with its {ignore} blocks set
  // aside - with their offsets and the text of their items.

  function padMetaFind ( $text ) {

    $found  = [];
    $offset = 0;

    while ( ( $pos = padPairNext ( $text, '{meta', $offset ) ) !== FALSE ) {

      $end = padPairTagEnd ( $text, $pos + 5 );

      if ( $end === FALSE )
        break;

      if ( padPairTopLevel ( $text, $pos ) )
        $found [] = [ 'start' => $pos, 'end' => $end + 1,
                      'parms' => rtrim ( trim ( substr ( $text, $pos + 5, $end - $pos - 5 ) ), '/' ) ];

      $offset = $end + 1;

    }

    return $found;

  }

  // Splits the items of a {meta} at the commas that stand outside quotes and brackets into
  // name => expression. Inside quotes a backslash escapes the character after it, as the
  // expression evaluator has it: title='Herbert\'s report' ended at the escaped quote, and
  // the rest of the tag - sub='x' and every item after it - became part of the title.

  function padMetaItems ( $parms ) {

    $items  = [];
    $parts  = [];
    $now    = '';
    $quote  = '';
    $depth  = 0;
    $escape = FALSE;

    foreach ( mb_str_split ( (string) $parms ) as $char ) {

      if ( $quote !== '' ) {
        $now .= $char;
        if ( $escape )
          $escape = FALSE;
        elseif ( $char == '\\' )
          $escape = TRUE;
        elseif ( $char == $quote )
          $quote = '';
        continue;
      }

      if ( $char == "'" or $char == '"' ) $quote = $char;
      elseif ( in_array ( $char, [ '(', '[', '{' ] ) ) $depth++;
      elseif ( in_array ( $char, [ ')', ']', '}' ] ) ) $depth--;

      if ( $char == ',' and ! $depth ) {
        $parts [] = $now;
        $now      = '';
        continue;
      }

      $now .= $char;

    }

    $parts [] = $now;

    foreach ( $parts as $part ) {

      $part = trim ( $part );

      if ( $part === '' )
        continue;

      if ( preg_match ( '/^([A-Za-z_][A-Za-z0-9_]*)\s*=(?!=)\s*(.*)$/s', $part, $match ) )
        $items [ $match [1] ] = $match [2];
      elseif ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $part ) )
        $items [$part] = 'TRUE';
      elseif ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "a {meta} item is name=value - '$part' is not" );

    }

    return $items;

  }

  // A literal of a {meta} item read from the file - a quoted string, a number, true, false
  // or null - or NULL for anything that would need evaluating. A string's escapes - \' \"
  // \\ \n \r \t - are read as the expression evaluator reads them.

  function padMetaLiteral ( $expr ) {

    $expr = trim ( (string) $expr );

    if ( preg_match ( '/^([\'"])(.*)\1$/s', $expr, $match ) )
      return preg_replace_callback ( '/\\\\(.)/s',
        fn ( $one ) => [ 'n' => "\n", 'r' => "\r", 't' => "\t" ] [ $one [1] ] ?? $one [1], $match [2] );

    if ( is_numeric ( $expr ) )
      return $expr + 0;

    return match ( strtolower ( $expr ) ) {
      'true'  => TRUE,
      'false' => FALSE,
      default => NULL
    };

  }

  // The metadata of a page read from its file, literal values only.

  function padMetaStatic ( $page ) {

    $meta = [];
    $base = APP . padCorrectPath ( (string) $page );

    if ( ! file_exists ( "$base.pad" ) and ! file_exists ( "$base.html" ) )
      return $meta;

    $masks = [];

    foreach ( padMetaFind ( padLayoutMask ( padPageTemplate ( $base ), $masks ) ) as $one )
      foreach ( padMetaItems ( $one ['parms'] ) as $name => $expr )
        $meta [$name] = padMetaLiteral ( $expr );

    return $meta;

  }

  // padMeta ( 'access' ) for the page being built, padMeta ( 'access', 'admin/users' ) for
  // another page of the application; without a name, all of it.

  function padMeta ( $name = NULL, $page = NULL ) {

    global $padMeta;

    $meta = ( $page === NULL ) ? ( $padMeta ?? [] ) : padMetaStatic ( $page );

    return ( $name === NULL ) ? $meta : ( $meta [$name] ?? NULL );

  }

  // {meta cache=...} of the requested page, applied before the cache is looked in: a number
  // of seconds is this page's server-cache time, 0 or false keeps the page out of it. Only
  // an application that has the page cache on - it chose the store - is affected.

  function padMetaCache () {

    if ( ! ( $GLOBALS ['padCache'] ?? FALSE ) )
      return;

    $cache = padMetaStatic ( $GLOBALS ['padPage'] ) ['cache'] ?? NULL;

    if ( $cache === NULL or $cache === TRUE )
      return;

    if ( $cache === FALSE or ( is_numeric ( $cache ) and $cache <= 0 ) )
      $GLOBALS ['padCache'] = FALSE;
    elseif ( is_numeric ( $cache ) )
      $GLOBALS ['padCacheServerAge'] = (int) $cache;

  }

?>
