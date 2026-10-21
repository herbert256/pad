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

  // A page array or an array field by its name, as {chart data='staff'} takes it (tags/
  // _lib/chart.php): data='staff' read the word as a CSV header with no rows and iterated
  // nothing.

  if ( is_string ( $padCheck ) and padValidVar ( $padCheck ) ) {

    if ( isset ( $GLOBALS [$padCheck] ) and is_array ( $GLOBALS [$padCheck] ) )
      return $GLOBALS [$padCheck];

    if ( padFieldCheck ( $padCheck ) and is_array ( $padDataField = padFieldValue ( $padCheck ) ) )
      return $padDataField;

  }

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

    if ( padDataValueRange ( $padCheck ) and padDataValueAddress ( $padCheck, ! padOptionLiteral ( 'data' ) ) )
      return padError ( "the data= value reads as a range of more than $padSeqDefaultTries values - write a range that long in the template" );

    if ( padDataFetches ( $padCheck ) and padDataValueAddress ( $padCheck, ! padOptionLiteral ( 'data' ) ) )
      return padError ( "the data value reads as an address to fetch - write the address in the template, data='https://...', or fetch it in the page's PHP with padPrefetch or padCurl" );

  }

  // A bare word that names nothing - no store, no page array or field, no _data file - is a
  // CSV header with no rows, so a misspelt data='usres' iterated nothing in silence, where
  // {data:usres} is an error. Written in the template it is one under the strict check, as
  // the chart's data= is; a value naming nothing stays silent, and so does a name a
  // {sandbox} hides.

  $padDataWord = is_string ( $padCheck ) ? trim ( $padCheck ) : '';

  if ( $padCheckSyntax and preg_match ( '/^[a-zA-Z_][a-zA-Z0-9_-]*$/D', $padDataWord )
       and padContentType ( $padDataWord ) == 'csv' and padOptionLiteral ( 'data' ) and ! padStrHidden ( $padDataWord ) )
    return padError ( "there is no data named '$padDataWord' - no data store, sequence store, page array or _data file" );

  return padData ( $padCheck );

?>
