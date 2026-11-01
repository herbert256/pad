<?php

  // padSearch - site search for a page's PHP, the entry of the search subsystem in
  // subs/search/, whose library is included the first time it is called.
  //
  //   $found = padSearch ( 'blog', $q, [ 'fields' => 'title^2, body', 'lang' => 'en' ] );
  //   $found = padSearch ( db ( "ARRAY id, name, about FROM products" ), $q, [ 'name' => 'products' ] );
  //
  // The source is the name of a Markdown collection (_content/blog/), else of a data store,
  // a page's array or a _data file - or the rows themselves, a db() result. The rows found
  // come back the best first, each with its own fields and score and excerpt added (the
  // excerpt HTML, the matches in <mark>). The options: fields (every field with text in a
  // row, title^2, body for a collection), lang (en, nl, de, fr, es, it, pt, sv, da, no -
  // the locale's language, else en), words (the excerpt's length, 30), excerpt (the field it
  // is cut from, the last of fields), first (the best rows only - an excerpt is made for
  // those alone) and name - what the index of rows is kept under in
  // DATA/cache/search/<app>/, 'rows' unless given: rows of one name share one index, made
  // again whenever the fields searched change. The query is what a visitor typed - a list of words, as ?q[]= makes
  // one, is searched for its words. A wrong argument is reported with padError naming the
  // function, which then answers no rows.

  function padSearch ( $source, $query, $options = [] ) {

    include_once PAD . 'subs/search/_lib/search.php';

    if ( ! is_array ( $options ) ) {
      padError ( 'padSearch: the options are an array - [ \'fields\' => \'title^2, body\' ]' );
      return [];
    }

    // Each option is one value: a list met a (string) cast deeper down, and the request
    // ended in PHP's "Array to string conversion" instead of this function's report.

    foreach ( [ 'name', 'fields', 'lang', 'words', 'excerpt', 'first' ] as $option )
      if ( isset ( $options [$option] ) and ! is_scalar ( $options [$option] ) ) {
        padError ( "padSearch: the option $option is a value - not " . get_debug_type ( $options [$option] ) );
        return [];
      }

    if ( is_array ( $source ) )
      $source = [ 'rows:' . ( $options ['name'] ?? 'rows' ), NULL, $source, FALSE ];
    elseif ( is_string ( $source ) )
      $source = padSearchSource ( $source );
    else
      $source = "the source is the name of a collection or data, or the rows - not " . get_debug_type ( $source );

    unset ( $options ['excerpts'] );

    $options ['style'] = TRUE;

    $found = is_string ( $source ) ? $source : padSearchRun ( $source, padSearchQueryText ( $query ), $options );

    if ( is_string ( $found ) ) {
      padError ( "padSearch: $found" );
      return [];
    }

    return $found;

  }

?>
