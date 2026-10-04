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

    $padDataList = $padCheck;

    if ( padContentType ( $padDataList ) == 'list' )
      return padError ( "the data= value reads as a PAD list, whose elements would run as expressions" );

  }

  return padData ( $padCheck );

?>