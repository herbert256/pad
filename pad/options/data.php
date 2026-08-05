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

  return padData ( $padCheck );

?>