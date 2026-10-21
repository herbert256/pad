<?php

  // Stacks: named lists of rendered text that the parts of a page add to and one place in
  // the page prints - a component declares the script or stylesheet it needs where it is
  // used, and the layout writes them all in its <head>.
  //
  //   {push 'scripts', once='chart'} <script src="chart.js"></script> {/push}
  //   {stack 'scripts'}
  //
  // The page renders from top to bottom, so a {stack} in the <head> comes out before the
  // components further down have pushed anything. It therefore prints a marker, and the
  // marker is replaced by the stack's text once the whole page has rendered - padStackFill,
  // from exits/exits.php. The marker carries a number made from the stack's name rather
  // than the name itself: a pipe on the tag - {stack 'css' | upper} - cannot change it,
  // and the same name makes the same marker on every request, so a {stack} inside a
  // cached fragment still finds its stack. It is sealed (padMarkSeal): a value carrying a
  // marker's characters and the number of a stack is text, where the stack's scripts were
  // written into it a second time.
  //
  // once= keeps the first push of a key and drops the later ones, per stack - a 'chart' in
  // the scripts and a 'chart' in the styles are two different things. once without a key
  // compares the rendered text itself.
  //
  // The pushes live in $padStackStore, one of the stores of padStrSto: a {page} pass adds
  // to the page's stacks, a sandboxed pass leaves no trace in them, as with the content
  // and data stores. A push made inside a fragment-cache section is kept with the cached
  // rendering and made again on every hit - lib/fragment.php - or a cached component
  // would stop declaring its script the moment its rendering came from the cache.

  function padStackMarker ( $name ) {

    $number = crc32 ( (string) $name );

    return "\u{E0F0}" . padMarkSeal ( "stack $number" ) . $number . "\u{E0F1}";

  }

  // Whether a push under this once= key has been made already - asked before the push's
  // content renders, so a component used fifty times renders its script tag once.

  function padStackSeen ( $name, $once ) {

    global $padStackStore;

    if ( ! is_string ( $once ) and ! is_int ( $once ) )
      return FALSE;

    return ( (string) $once !== '' and isset ( $padStackStore [ (string) $name ] ['once'] [$once] ) );

  }

  function padStackPush ( $name, $text, $once = '' ) {

    global $padStackStore, $padFragment, $pad, $padTag, $padType, $padStackNocache;

    $name = (string) $name;
    $text = (string) $text;

    if ( $once === TRUE )
      $once = 'text:' . md5 ( $text );

    if ( $once !== '' and $once !== FALSE and $once !== NULL ) {

      if ( isset ( $padStackStore [$name] ['once'] [$once] ) )
        return FALSE;

      $padStackStore [$name] ['once'] [$once] = TRUE;

    }

    $padStackStore [$name] ['text'] [] = $text;

    // Every fragment-cache section that is rendering around this push records it, so a
    // hit can make it again - up to a {nocache} round the push, which makes it again itself.
    //
    // The page cache keeps a page with its stacks filled in, so a push a {nocache} part
    // makes again on every hit had no stack left to land in: the stack showed the first
    // visitor's push for the entry's whole life. Such a page is not kept (lib/cache.php).

    for ( $i = $pad; $i >= 0; $i-- ) {
      if ( isset ( $padFragment [$i] ) and is_array ( $padFragment [$i] ) and ! $padFragment [$i] ['hit'] )
        $padFragment [$i] ['stacks'] [] = [ $name, $text, $once ];
      if ( ( $padTag [$i] ?? '' ) == 'nocache' and ( $padType [$i] ?? '' ) == 'pad' ) {
        $padStackNocache = TRUE;
        break;
      }
    }

    return TRUE;

  }

  function padStackText ( $name ) {

    global $padStackStore;

    return implode ( '', $padStackStore [ (string) $name ] ['text'] ?? [] );

  }

  function padStackFill ( $output ) {

    global $padStackStore;

    if ( ! is_string ( $output ) or ! str_contains ( $output, "\u{E0F0}" ) )
      return $output;

    $markers = [];

    foreach ( array_keys ( $padStackStore ?? [] ) as $name )
      $markers [ padStackMarker ( $name ) ] = padStackText ( $name );

    // A stack's text can hold the marker of another - a push of head markup with a {stack}
    // of its own: filled again while one is left, as many rounds as there are stacks, so a
    // stack pushed into itself ends. One pass left it to the line below, which took it for
    // a stack nothing pushed to, and the css stack of {push 'head'}{stack 'css'}{/push} was
    // lost.

    for ( $round = 0; $round <= count ( $markers ); $round++ ) {

      $output = strtr ( $output, $markers );

      if ( ! array_filter ( array_keys ( $markers ), fn ( $marker ) => str_contains ( $output, $marker ) ) )
        break;

    }

    // A stack nothing pushed to prints nothing.

    return preg_replace_callback ( '/\x{E0F0}(\d{16})(\d+)\x{E0F1}/u',
      fn ( $match ) => hash_equals ( padMarkSeal ( "stack $match[2]" ), $match [1] ) ? '' : $match [0], $output );

  }

?>
