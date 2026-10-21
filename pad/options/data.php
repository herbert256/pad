<?php

  // Implements data="name": returns the data array the tag should iterate over, looked up in
  // the data store $padDataStore, then in the sequence store $pqStore, and otherwise handed to
  // padData() so a literal value is converted into PAD data.
  //
  // Included by level/start.php, which assigns the return value to $padData [$pad].

  $padGetName = padTagParm ( 'data' );
  $padCheck   = padTagParm ( 'data' );

  if ( isset ( $padDataStore [$padCheck] ) )
    return $padDataStore [$padCheck];

  if ( isset ( $pqStore [$padCheck] ) )
    return $pqStore [$padCheck];

  // A list - ( 'a', $b, 2 * 3 ) - evaluates every element as an expression. A {data} block
  // reads one from the template; through data= the text is a value, often a field's, so
  // under $padProtectValues a value that reads as a list is refused rather than run.

  if ( $padProtectValues and is_string ( $padCheck ) ) {

    // Judged as padData will read it (padDataText): trimmed and a leading UTF-8 byte order
    // mark taken off, which padData strips before it sniffs the type - a list behind one, or
    // behind white space and one, passed this check and ran.

    $padDataList = padDataText ( $padCheck );

    if ( padContentType ( $padDataList ) == 'list' )
      return padError ( "the data= value reads as a PAD list, whose elements would run as expressions" );

    // An address is fetched as data only when it is written in the template, data=
    // 'https://...': a value - data=$v, or one spliced in at its start, data='{$v}' - had
    // the server connect to any host and port it can reach, its own pages behind an address
    // guard (SELF://) and cloud metadata included, and render the answer into the page.

    if ( padDataFetches ( $padCheck ) and padDataValueAddress ( $padCheck, ! padOptionLiteral ( 'data' ), $padBase [$pad - 1] ?? '' ) )
      return padError ( "the data value reads as an address to fetch - write the address in the template, data='https://...', or fetch it in the page's PHP with padPrefetch or padCurl" );

  }

  return padData ( $padCheck );

?>
