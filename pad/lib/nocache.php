<?php

  // {nocache}...{/nocache}: a part of a cached page or fragment that renders on every
  // request, the hit included - tags/nocache.php.
  //
  //   {cache 'top-products', ttl=300}            the section is kept five minutes ...
  //     {topProducts}<li>{$name}</li>{/topProducts}
  //     {nocache}In stock: {stock}{/nocache}     ... this part renders on every request
  //   {/cache}
  //
  // While something caches what it renders - the page cache for this request, or a
  // fragment-cache section around it that is rendering - a {nocache} renders as always but
  // puts a marker pair round what it rendered, with its source kept by number. When the
  // cache stores the rendering, padNocacheCut puts a placeholder where each marked part
  // stood and keeps the sources beside the text; the visitor's copy only loses the
  // markers (padNocacheStrip). A hit makes a template of the stored text again
  // (padNocacheTemplate): the stored text protected - it is text, and stays what it was -
  // with a {nocache} pair of each source where its placeholder stands, and renders that.
  //
  // A part inside a loop of what is cached has no loop to render in on a hit - the loop is
  // part of the stored text - so the fields of the rows round it are kept with its source,
  // as they were when it was stored, and given back to it as its own row.
  //
  // In a fragment the parts render where the section stands, with the fields of the page.
  // A page-cache hit runs no PHP - not the page's, nor an _inits.php - so there the parts
  // see what a request has without it: the request and session values, the configuration,
  // the _lib functions and the tags of the application.
  //
  // The live markers carry a token drawn once per request, and the stored placeholders one
  // drawn per entry: a value of the page - text a visitor wrote - cannot hold one, so it
  // can never become a part that runs.

  function padNocacheToken () {

    static $token = NULL;

    if ( $token === NULL )
      $token = bin2hex ( random_bytes ( 8 ) );

    return $token;

  }

  // Whether a {nocache} at this level renders into something a cache keeps: the page cache
  // of this request, or a fragment-cache section below it that is rendering.

  function padNocacheWanted () {

    global $pad, $padCache, $padCacheServerAge, $padFragment, $padFragmentCache;

    if ( $padCache and ( $padCacheServerAge ?? 0 ) )
      return TRUE;

    if ( $padFragmentCache )
      for ( $i = $pad - 1; $i >= 0; $i-- )
        if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] )
          return TRUE;

    return FALSE;

  }

  // The marker pair round the part a {nocache} renders, its source and the fields of the
  // rows round it kept under its number.

  function padNocacheOpen ( $source ) {

    global $pad, $padCurrent, $padNocacheSource;

    $row = [];

    for ( $i = 1; $i < $pad; $i++ )
      if ( is_array ( $padCurrent [$i] ?? NULL ) )
        $row = array_merge ( $row, $padCurrent [$i] );

    $padNocacheSource [] = [ 'source' => $source, 'row' => $row ];

    $number = array_key_last ( $padNocacheSource );
    $token  = padNocacheToken ();

    return [ "<!--padNocache $token $number-->", "<!--/padNocache $token $number-->" ];

  }

  // The text as a cache keeps it: each marked part a placeholder, the sources in $kept as
  // [ key, [ number => source ] ]. A text without a marked part comes back as it is, $kept
  // NULL.

  function padNocacheCut ( $text, &$kept ) {

    global $padNocacheSource;

    $kept  = NULL;
    $token = padNocacheToken ();

    if ( ! is_string ( $text ) or ! str_contains ( $text, "<!--padNocache $token " ) )
      return $text;

    $key     = bin2hex ( random_bytes ( 8 ) );
    $sources = [];

    $text = preg_replace_callback (
      '/<!--padNocache ' . $token . ' (\d+)-->.*?<!--\/padNocache ' . $token . ' \1-->/s',
      function ( $match ) use ( $key, &$sources, $padNocacheSource ) {
        $number             = count ( $sources );
        $sources [$number]  = $padNocacheSource [ (int) $match [1] ] ?? [ 'source' => '', 'row' => [] ];
        return "<!--padNocache $key $number-->";
      },
      $text
    );

    $kept = [ $key, $sources ];

    return $text;

  }

  // What a visitor gets: the text without the markers.

  function padNocacheStrip ( $text ) {

    $token = padNocacheToken ();

    if ( ! is_string ( $text ) or ! str_contains ( $text, "padNocache $token " ) )
      return $text;

    return preg_replace ( '/<!--\/?padNocache ' . $token . ' \d+-->/', '', $text );

  }

  // A stored text made a template again: the text protected, a {nocache} pair of each
  // source where its placeholder stands - named by a key of this entry, under which its row
  // waits in $padNocacheRows for tags/nocache.php.

  function padNocacheTemplate ( $text, $kept ) {

    global $padNocacheRows;

    [ $key, $sources ] = $kept;

    $parts = preg_split ( '/<!--padNocache ' . preg_quote ( $key, '/' ) . ' (\d+)-->/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
    $out   = '';

    foreach ( $parts as $index => $part )
      if ( $index % 2 ) {
        $part = $sources [ (int) $part ] ?? [];
        $name = "$key-" . count ( $padNocacheRows ?? [] );
        $padNocacheRows [$name] = is_array ( $part ['row'] ?? NULL ) ? $part ['row'] : [];
        $out .= "{nocache '$name'}" . ( $part ['source'] ?? '' ) . '{/nocache}';
      } else
        $out .= padProtect ( $part );

    return $out;

  }

  // The page cache keeps a page with parts as one body: a header that no page starts with,
  // the length of the serialized sources, the sources, then the text.

  function padNocachePack ( $text, $kept ) {

    $extra = serialize ( $kept );

    return "\0padNocache\0" . strlen ( $extra ) . "\n" . $extra . $text;

  }

  function padNocacheUnpack ( $body ) {

    if ( ! is_string ( $body ) or ! str_starts_with ( $body, "\0padNocache\0" ) )
      return FALSE;

    $body  = substr ( $body, 12 );
    $split = strpos ( $body, "\n" );

    if ( $split === FALSE )
      return FALSE;

    $size = (int) substr ( $body, 0, $split );
    $kept = @unserialize ( substr ( $body, $split + 1, $size ), [ 'allowed_classes' => FALSE ] );

    if ( ! is_array ( $kept ) or count ( $kept ) != 2 or ! is_array ( $kept [1] ) )
      return FALSE;

    return [ substr ( $body, $split + 1 + $size ), $kept ];

  }

  // The ETag a page with parts is stored under: one no response carries - a response's is
  // the hash of what it sent - so the cache never answers 304 for it from the stored entry
  // alone, the parts unrendered.

  function padNocacheEtag ( $body ) {

    return '~' . substr ( padMD5 ( $body ), 1 );

  }

  function padNocacheEtagIs ( $etag ) {

    return is_string ( $etag ) and str_starts_with ( $etag, '~' );

  }

?>
